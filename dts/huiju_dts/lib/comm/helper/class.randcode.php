<?php


class randcode {
	
	var $code = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
	var $codelen = 0;
	
	function randcode(){
		
	}
	
	function __construct(){
		$this->codelen = strlen($this->code);
	}

	function getrandstr($len = 4){
		$randstr = '';
		while ($len--){
			$i = rand(0, 60);
			$randstr .= $this->code{$i};
		}
		return $randstr;
	}
	
	function showimage($randstr){
		//设置字体大小
		$font_size=26;
		
		$len = strlen($randstr);
		$imstr = array();
		for ($i = 0; $i<$len; $i++){
			$imstr[$i] = array(
				'c'=>$randstr{$i},
				'x'=>12*$i+rand(0,6),
				'y'=>rand(0,20)
			);
		}
		
		$imageY = $len*10+10;
		
		$im = @imagecreatetruecolor($imageY, $font_size+8) or die("建立图像失败");
		//获取背景颜色
		$background_color = imagecolorallocate($im, 255, 255, 255);
		//填充背景颜色(这个东西类似油桶)
		imagefill($im,0,0,$background_color);
		//获取边框颜色
		$border_color = imagecolorallocate($im,180,180,180);
		//画矩形，边框颜色200,200,200
		imagerectangle($im,0,0,$imageY-1,$font_size+7,$border_color);
		
		//逐行炫耀背景，全屏用1或0
		for($i=2;$i<$font_size+6;$i++){
			//获取随机淡色
			$line_color = imagecolorallocate($im,rand(180,255),rand(180,255),rand(180,255));
			//画线
			imageline($im,2,$i,$imageY-3,$i,$line_color);
		}
		
		for($i=0;$i<$len;$i++){
			//获取随机较深颜色
			$text_color = imagecolorallocate($im,rand($imageY,180),rand($imageY,180),rand($imageY,180));
			//画文字
			imagechar($im,$font_size,$imstr[$i]["x"],$imstr[$i]["y"],$imstr[$i]["c"],$text_color);
		}
		
		header("Content-type: image/png");
		//显示图片
		imagepng($im);
		//销毁图片
		imagedestroy($im);
	}

}