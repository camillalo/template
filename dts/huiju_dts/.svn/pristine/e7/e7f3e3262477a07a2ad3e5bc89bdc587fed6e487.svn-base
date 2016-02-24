<?php


/**
 *
 * @author 229602756@qq.com
 *
 */
class String {
	
	public static function unicode_encode($string){
		$string = iconv('UTF-8', 'UCS-2', $string);
		$len = strlen($string);
		$str = '';
		for ($i = 0; $i < $len - 1; $i = $i + 2){
			$c = $string[$i];
			$c2 = $string[$i + 1];
			if (ord($c) > 0){// 若是两个字节的文字
				$str .= '\u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
			}else{
				$str .= $c2;
			}
		}
		return $str;
	}
	
	public static function unicode_decode($string){
		// 转换编码，将Unicode编码转换成可以浏览的utf-8编码
		$pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
		preg_match_all($pattern, $string, $matches);
		if (!empty($matches)){
			$string = '';
			//for ($j = 0; $j < count($matches[0]); $j++){
			for ($j = 0, $count = count($matches[0]); $j < $count; $j++){
				$str = $matches[0][$j];
				if (strpos($str, '\\u') === 0){
					$code = base_convert(substr($str, 2, 2), 16, 10);
					$code2 = base_convert(substr($str, 4), 16, 10);
					$c = chr($code).chr($code2);
					$c = iconv('UCS-2', 'UTF-8', $c);
					$string .= $c;
				}else{
					$string .= $str;
				}
			}
		}
		return $string;
	}
	
	//输入必须是utf-8字串
	public static function escape($str){
		preg_match_all("/[\xc2-\xdf][\x80-\xbf]+|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]+/e",$str,$r);
		//匹配utf-8字符，
		$str = $r[0];
		$l = count($str);
		for($i=0; $i <$l; $i++){
			$value = ord($str[$i][0]);
			if($value < 223){
				$str[$i] = rawurlencode(utf8_decode($str[$i]));
				//先将utf8编码转换为ISO-8859-1编码的单字节字符，urlencode单字节字符.
				//utf8_decode()的作用相当于iconv("UTF-8","CP1252",$v)。
			}else{
				$str[$i] = "%u".strtoupper(bin2hex(iconv("UTF-8","UCS-2",$str[$i])));
			}
		}
		return join("",$str);
	}
	
	public static function unescape($str) {
		$str = rawurldecode($str);
		preg_match_all("/%u.{4}|&#x.{4};|&#d+;|.+/U",$str,$r);
		$ar = $r[0];
		foreach($ar as $k=>$v) {
			if(substr($v,0,2) == "%u")
				$ar[$k] = mb_convert_encoding(pack("H4",substr($v,-4)),"gb2312","UCS-2");
			elseif(substr($v,0,3) == "&#x")
			$ar[$k] = mb_convert_encoding(pack("H4",substr($v,3,-1)),"gb2312","UCS-2");
			elseif(substr($v,0,2) == "&#") {
				$ar[$k] = mb_convert_encoding(pack("H4",substr($v,2,-1)),"gb2312","UCS-2");
			}
		}
		return join("",$ar);
	}
	
	public static function utf8char2unicode($char) {
		switch(strlen($char)) {
			case 1:
				return ord($char);
			case 2:
				$n = (ord($char[0]) & 0x3f) << 6;
				$n += ord($char[1]) & 0x3f;
				return $n;
			case 3:
				$n = (ord($char[0]) & 0x1f) << 12;
				$n += (ord($char[1]) & 0x3f) << 6;
				$n += ord($char[2]) & 0x3f;
				return $n;
			case 4:
				$n = (ord($char[0]) & 0x0f) << 18;
				$n += (ord($char[1]) & 0x3f) << 12;
				$n += (ord($char[2]) & 0x3f) << 6;
				$n += ord($char[3]) & 0x3f;
				return $n;
			default:
				die('invalid input for function "utf8char2unicode"');
		}
	}
	
	public static function is_utf8($string){
		return preg_match('%^(?:
			 [\x09\x0A\x0D\x20-\x7E]            # ASCII
				| [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
				|  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
				| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
				|  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
				|  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
				| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
				|  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
		)*$%xs', $string);
	}
	
	public static function is_email($email) {
		return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
	}
	
	public static function substr($str, $start=0, $length, $charset="utf-8", $suffix='…'){
		if(strlen($str)<$length+1) return $str;
		if(function_exists("mb_substr")){
			$slice = mb_substr($str, $start, $length, $charset);
			return $slice.$suffix;
		}elseif(function_exists('iconv_substr')){
			$slice = iconv_substr($str,$start,$length,$charset);
			return $slice.$suffix;
		}
		$re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']	  = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']	  = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
		return $slice.$suffix;
	}
	
	public static function make_semiangle($str){
		$arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
				'５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
				'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
				'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
				'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
				'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
				'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
				'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
				'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
				'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
				'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
				'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
				'ｙ' => 'y', 'ｚ' => 'z',
				'（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
				'】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
				'‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
				'》' => '>',
				'％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
				'：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
				'；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
				'”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
				'　' => ' ');
		return strtr($str, $arr);
	}
	
	public static function is_serialized($data) {
		if ( !is_string($data) )
			return false;
		$data = trim($data);
		if ( 'N;' == $data )
			return true;
		if ( !preg_match('/^([adObis]):/', $data, $badions) )
			return false;
		switch ( $badions[1] ) :
		case 'a' :
		case 'O' :
		case 's' :
			if ( preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
			return true;
		break;
		case 'b' :
		case 'i' :
		case 'd' :
			if ( preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
				return true;
			break;
			endswitch;
			return false;
	}
	
	public static function getcharset($string) {
		$re = array(
				'utf-8'=>"/^(?:[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3})*$/xs",
				'gb2312'=>"/^(?:[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe])*$/xs",
				'gbk'=>"/^(?:[\x01-\x7f]|[\x81-\xfe][\x40-\xfe])*$/xs",
				'big5'=>"/^(?:[\x01-\x7f]|[\x81-\xfe](?:[\x40-\x7e]|\xa1-\xfe]))*$/xs",
		);
		
		foreach($re as $k=>$v){
			if(preg_match($v,$string)){
				return $k;
			}
		}
		
		return false;
	}
	
}