<?php

class template {
	// public configuration variables
	var $left_tag			= "<%";		
	var $right_tag			= "%>";		
	var $template_dir		= "template";
	var $compile_dir		= "template_c";

	// private internal variables
	var $_vars		= array();	
	var $_linenum		= 0;		
    var $_file		= "";		
	var $_compile_obj	= null;
	var $_sl_md5		= '39fc70570b8b60cbc1b85839bf242aff';

	function assign($key, $value = null) {
		if (is_array($key)) {
			foreach($key as $var => $val)
				if ($var != "" && !is_numeric($var))
					$this->_vars[$var] = $val;
		} else {
			if ($key != "" && !is_numeric($key))
				$this->_vars[$key] = $value;
		}
	}

	function clear($key = null) {
		if ($key == null) {
			$this->_vars = array();
		} else {
			if (is_array($key)) {
				foreach($id as $index => $value)
					if (in_array($value, $this->_vars))
						unset($this->_vars[$index]);
			} else {
				if (in_array($key, $this->_vars))
					unset($this->_vars[$index]);
			}
		}
	}

	function &get_vars($key = null) {
		if ($key == null) {
			return $this->_vars;
		} else {
			if (isset($this->_vars[$key]))
				return $this->_vars[$key];
			else
				return null;
		}
	}
	
	function clear_compiled($file = null) {
		$this->_destroy_dir($file, null, $this->_get_dir($this->compile_dir));
	}

	function template_exists($file) {
		if (file_exists($this->_get_dir($this->template_dir).$file))
			return true;
		else
			return false;
	}

	function display($file) {
		
		$this->fetch($file,  true);
	}

	function fetch($file, $display = false) {

		$this->template_dir = $this->_get_dir($this->template_dir);
		$this->compile_dir = $this->_get_dir($this->compile_dir);
		$this->_error_level = error_reporting(error_reporting() & ~E_NOTICE);
		$output = $this->_fetch_compile($file, $cache_id);

		error_reporting($this->_error_level);
		if ($display)
			echo $output;
		else
			return $output;
	}

	function _fetch_compile($file) {
		$this->template_dir = $this->_get_dir($this->template_dir);
		$name = md5($this->template_dir.$file).'.php';

		if (file_exists($this->compile_dir.'c_'.$name) && (filemtime($this->compile_dir.'c_'.$name) > filemtime($this->template_dir.$file))) {
			ob_start();
			include($this->compile_dir.'c_'.$name);
			$output = ob_get_contents();
			ob_end_clean();
			error_reporting($this->_error_level);
			return $output;
		}

		if ($this->template_exists($file)) {
			$f = fopen($this->template_dir.$file, "r");
			$size = filesize($this->template_dir.$file);
			if ($size > 0) {
				$file_contents = fread($f, filesize($this->template_dir.$file));
			} else {
				$file_contents = "";
			}
			$this->_file = $file;
			fclose($f);
		} else {
			$this->trigger_error("file '$file' does not exist", E_USER_ERROR);
		}

		if (!is_object($this->_compile_obj)) {
			require_once("class.compiler.php");
			$this->_compile_obj = new compiler;
		}
		$this->_compile_obj->left_tag = $this->left_tag;
		$this->_compile_obj->right_tag = $this->right_tag;
		$this->_compile_obj->template_dir = &$this->template_dir;
		$this->_compile_obj->_vars = &$this->_vars;
		$this->_compile_obj->_linenum = &$this->_linenum;
		$this->_compile_obj->_file = &$this->_file;
		$output = $this->_compile_obj->_compile_file($file_contents);

		$f = fopen($this->compile_dir.'c_'.$name, "w");
		fwrite($f, $output);
		fclose($f);
		ob_start();
		eval(' ?>' . $output . '<?php ');
		$output = ob_get_contents();
		ob_end_clean();
		error_reporting($this->_error_level);
		return $output;
	}

	function _get_dir($dir, $id = null) {
		if (empty($dir))
			$dir = '.';
		if (substr($dir, -1) != DIRECTORY_SEPARATOR)
			$dir .= DIRECTORY_SEPARATOR;
		if (!empty($id)) {
			$_args = explode('|', $id);
			if (count($_args) == 1 && empty($_args[0]))
				return $dir;
			foreach($_args as $value)
				$dir .= $value.DIRECTORY_SEPARATOR;
		}
		return $dir;
	}

	function _build_dir($dir, $id) {
		$_args = explode('|', $id);
		if (count($_args) == 1 && empty($_args[0]))
			return $this->_get_dir($dir);
		umask(0000);
		$_result = $this->_get_dir($dir);
		foreach($_args as $value) {
			$_result .= $value.DIRECTORY_SEPARATOR;
			@mkdir($_result, 0777);
		}
		return $_result;
	}

	function _destroy_dir($file, $id, $dir) {
		if ($file == null && $id == null) {
			if (is_dir($dir))
				if($d = opendir($dir))
					while(($f = readdir($d)) !== false)
						if ($f != '.' && $f != '..')
							$this->_rm_dir($dir.$f.DIRECTORY_SEPARATOR);
		} else {
			if ($id == null) {
				$this->template_dir = $this->_get_dir($this->template_dir);
				@unlink($dir.md5($this->template_dir.$file).'.php');
			} else {
				$_args = "";
				foreach(explode('|', $id) as $value)
					$_args .= $value.DIRECTORY_SEPARATOR;
				$this->_rm_dir($dir.DIRECTORY_SEPARATOR.$_args);
			}
		}
	}

	function _rm_dir($dir) {
		if ($d = opendir($dir)) {
			while(($f = readdir($d)) !== false) {
				if ($f != '.' && $f != '..') {
					if (is_dir($dir.$f))
						$this->_rm_dir($dir.$f.DIRECTORY_SEPARATOR);
					if (is_file($dir.$f))
						@unlink($dir.$f);
				}
			}
			@rmdir($dir.$f);
		}
	}

	function trigger_error($error_msg, $error_type = E_USER_ERROR, $file = null, $line = null) {
		if(isset($file) && isset($line))
			$info = ' ('.basename($file).", line $line)";
		else
			$info = null;
		trigger_error('TPL: [in ' . $this->_file . ' line ' . $this->_linenum . "]: syntax error: $error_msg$info", $error_type);
	}
}