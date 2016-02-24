<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
$_posibleChars  = '23456789QWERTYUPASDFGHJKLZXCVBNM';
$_charLength    = 4;
$_scodeWidth    = 75;
$_socdeHeight   = 25;
$_font          = BASE_PATH . 'statics/font/courbd.ttf';
$_fontSize      = 16;
$_fontSizeDelta = 3;
$_padding       = 5;
$_margin        = 8;
$_baseLine      = 19;
$_noiseColorNum = 6;
$_noiseNum      = 140;

################### 生成验证码并存于Session中 ######################
$_SESSION['scode']  = $scode   = getRandomStr($_posibleChars, $_charLength);
session_write_close();

################################################################
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");
header("Cache-control: private");
header('Content-Type: image/png');

$image  = imagecreatetruecolor($_scodeWidth, $_socdeHeight);

$gdWidth    = $_scodeWidth - 1;
$gdHeight   = $_socdeHeight - 1;

imagefilledrectangle($image, 0, 0, $gdWidth, $gdHeight,
        imagecolorallocate($image, mt_rand(225, 255), mt_rand(225, 255), mt_rand(225, 255)));

//开始绘制杂点
$noiseColor = array();
for ($i = 0; $i < $_noiseColorNum; ++ $i)
{
    $noiseColor[]   = imagecolorallocate($image, mt_rand(0, 50), mt_rand(0, 50), mt_rand(0, 50));
}
for (; $_noiseNum > 0; -- $_noiseNum)
{
    imagesetpixel($image, mt_rand(0, $gdWidth), mt_rand(0, $gdHeight), $noiseColor[$_noiseNum % $_noiseColorNum]);
}

########## 开始绘制文字 ##############################
$scodeLen   = strlen($scode);
for ($i = 0; $i < $scodeLen; ++ $i)
{
    $fontSize   = mt_rand($_fontSize - $_fontSizeDelta, $_fontSize + $_fontSizeDelta);
    $angle      = mt_rand(-36, 36);
    $color      = imagecolorallocate($image, mt_rand(0, 50), mt_rand(10, 100), mt_rand(20, 150));
    imagettftext(   $image,
                    $fontSize,
                    $angle,
                    $_margin,
                    mt_rand($_baseLine - 2, $_baseLine + 2),
                    $color, $_font,
                    substr($scode, $i, 1));
    $_margin    +=  (imagefontwidth($fontSize) + $_padding);
}

imagepng($image);
imagedestroy($image);

################ 验证码结束 ########################################


function getRandomStr($possibleChars, $length = 4)
{
    $charNum    = strlen($possibleChars) - 1;
    $ret        = '';
    for ($i = 0; $i < $length; ++ $i)
    {
        $ret    .= $possibleChars[mt_rand(0, $charNum)];
    }
    return $ret;
}
?>