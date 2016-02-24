<?php


/**
 *
 * @author 229602756@qq.com
 *
 */
class imageCrack {

	public function filterColor($r,$g,$b) {
		return $r<144||$g<143||$b<150;
	}
	
	public function getDots($im) {
		$dots = array();
		
		$img_width=imagesx($im);
		$img_height=imagesy($im);
		
		for ($h=0;$h<$img_height;$h++){
			for ($w=0;$w<$img_width;$w++){
				$rgb=imagecolorat($im,$w,$h);
				$rgbarray=imagecolorsforindex($im,$rgb);
				if($w!=$img_width&&$h!=$img_height&&$this->filterColor($rgbarray['red'],$rgbarray['green'],$rgbarray['blue'])){
					$dots[$h][$w]=1;
				}else {
					$dots[$h][$w]=0;
				}
			}
		}
		
		return array($dots,$w,$h);
	}
	
	public function clean($data,$keep = 3){
		list($dots,$width,$height)=$data;
		unset($data);
		
		for ($h=0;$h<$height;$h++){
			for ($w=0;$w<$width;$w++){
				$c=0;
				if($w-1>0&&$h-1>0)//左上
					if($dots[$h-1][$w-1])$c++;
				if($h-1>0)//上
					if($dots[$h-1][$w])$c++;
				if($w+1<$width&&$h-1>0)//右上
					if($dots[$h-1][$w+1])$c++;
				if($w+1<$width)//右
					if($dots[$h][$w+1])$c++;
				if($w+1<$width&&$h+1<$height)//右下
					if($dots[$h+1][$w+1])$c++;
				if($h+1<$height)//下
					if($dots[$h+1][$w])$c++;
				if($w-1>0&&$h+1<$height)//左下
					if($dots[$h+1][$w-1])$c++;
				if($w-1>0)//左
					if($dots[$h][$w-1])$c++;
				if($c<$keep){
					$dots[$h][$w]=0;
				}
			}
		}
		
		return array($dots,$width,$height);
	}
	
	function cut($data) {
		list($dots,$width,$height)=$data;
		unset($data);
		$smallpics = array();
		$smallpicscount = 0;
		for($w=0;$w<$width;$w++){
			$tmpstr = '';
			$o=0;
			for($h=0;$h<$height;$h++){
				$tmpstr .= $dots[$h][$w];
				if($dots[$h][$w]=='0'){
					$o++;
				}
			}
			if($o==$h){//如果全是0，即空列
				$smallpics[] = '';
				$smallpicscount++;
				continue;
			}else{//若不是空列
				if(!isset($smallpics[$smallpicscount])) {
					$smallpics[$smallpicscount] = '';
				}
				$smallpics[$smallpicscount] .= $tmpstr;
			}
		}
		
		return array_values(array_filter($smallpics));
	}
	
	function _bmpstr2jpg($bmpstr) {
		$prestr = substr($bmpstr,0,14);
		$bmpstr = substr($bmpstr,14);
	
		$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", $prestr);
		if ($FILE['file_type'] != 19778)
			return FALSE;
		$prestr = substr($bmpstr,0,40);
		$bmpstr = substr($bmpstr,40);
		$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', $prestr);
		$BMP['colors'] = pow(2, $BMP['bits_per_pixel']);
		if ($BMP['size_bitmap'] == 0)
			$BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
		$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
		$BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
		$BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
		$BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
		$BMP['decal'] = 4 - (4 * $BMP['decal']);
		if ($BMP['decal'] == 4)
			$BMP['decal'] = 0;
		$PALETTE = array ();
		if ($BMP['colors'] < 16777216) {
			$prestr = substr($bmpstr,0,$BMP['colors'] * 4);
			$bmpstr = substr($bmpstr,$BMP['colors'] * 4);
			$PALETTE = unpack('V' . $BMP['colors'], $prestr);
		}
		$prestr = substr($bmpstr,0,$BMP['size_bitmap']);
		$bmpstr = substr($bmpstr,$BMP['size_bitmap']);
		$IMG = $prestr;
		$VIDE = chr(0);
		$im = imagecreatetruecolor($BMP['width'], $BMP['height']);
		$P = 0;
		$Y = $BMP['height'] - 1;
		while($Y >= 0){
			$X = 0;
			while($X < $BMP['width']){
				if ($BMP['bits_per_pixel'] == 24)
					$COLOR = unpack("V", substr($IMG, $P, 3). $VIDE);
				elseif ($BMP['bits_per_pixel'] == 16) {
					$COLOR = unpack("n", substr($IMG, $P, 2));
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				} elseif ($BMP['bits_per_pixel'] == 8) {
					$COLOR = unpack("n", $VIDE . substr($IMG, $P, 1));
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				} elseif ($BMP['bits_per_pixel'] == 4) {
					$COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
					if (($P * 2) % 2 == 0)
						$COLOR[1] = ($COLOR[1] >> 4);
					else
						$COLOR[1] = ($COLOR[1] & 0x0F);
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				} elseif ($BMP['bits_per_pixel'] == 1) {
					$COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
					if (($P * 8) % 8 == 0)
						$COLOR[1] = $COLOR[1] >> 7;
					elseif (($P * 8) % 8 == 1)
					$COLOR[1] = ($COLOR[1] & 0x40) >> 6;
					elseif (($P * 8) % 8 == 2)
					$COLOR[1] = ($COLOR[1] & 0x20) >> 5;
					elseif (($P * 8) % 8 == 3)
					$COLOR[1] = ($COLOR[1] & 0x10) >> 4;
					elseif (($P * 8) % 8 == 4)
					$COLOR[1] = ($COLOR[1] & 0x8) >> 3;
					elseif (($P * 8) % 8 == 5)
					$COLOR[1] = ($COLOR[1] & 0x4) >> 2;
					elseif (($P * 8) % 8 == 6)
					$COLOR[1] = ($COLOR[1] & 0x2) >> 1;
					elseif (($P * 8) % 8 == 7)
					$COLOR[1] = ($COLOR[1] & 0x1);
					$COLOR[1] = $PALETTE[$COLOR[1] + 1];
				} else
					return FALSE;
				imagesetpixel($im, $X, $Y, $COLOR[1]);
				$X ++;
				$P += $BMP['bytes_per_pixel'];
			}
			$Y --;
			$P += $BMP['decal'];
		}
	
		return $im;
	}
	
	function _bmpstr2jpgstr($bmpstr){
		ob_start();
		$im = $this->_bmpstr2jpg($bmpstr);
		imagejpeg($im);
		$buf = ob_get_contents();
		ob_end_clean();
		return $buf;
	}

}