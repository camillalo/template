<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
class  uploadImg {

    private $_saveDir;

    private $_webDir;

    private $_dateSegment;
    
    private static $self = null;

    public static function getInstance(){
        if(null !== self::$self) return self::$self;
        self::$self = new uploadImg();
        return self::$self;
    }

    private function  __construct() {

        $this->_saveDir = IMG_SAVE_PATH;

        $this->_webDir  = IMG_WEB_PATH;

        $this->_dateSegment   = date('Y/m', NOWTIME);

    }

    //只做上传到目标文件
    public function upload($_file){

        if(empty($_FILES[$_file]) ||empty($_FILES[$_file]['tmp_name']) )   throw new Exception('请选择上传的图片');

        if(is_array($_FILES[$_file]['tmp_name'])) {
            return $this->uploadArr($_file);
        }
        return $this->uploadOne($_file);
    }
     /*
        * 功能:PHP图片水印 (水印支持图片或文字)
        */
       public function imageWaterMark($groundImage, $waterImage = "", $waterText = "", $textFont = 3, $textColor = "#FFFFFF") {
               $isWaterImage = FALSE;
               $formatMsg = "暂不支持该文件格式,请用图片处理软件将图片转换为GIF、JPG、PNG格式.";
               //读取水印文件
               if (!empty($waterImage) && file_exists($waterImage)) {
                   $isWaterImage = TRUE;
                   $water_info = getimagesize($waterImage);
                   $water_w = $water_info[0]; //取得水印图片的宽
                   $water_h = $water_info[1]; //取得水印图片的高
                   switch ($water_info[2]) {//取得水印图片的格式
                       case 1:$water_im = imagecreatefromgif($waterImage);
                           break;
                       case 2:$water_im = imagecreatefromjpeg($waterImage);
                           break;
                       case 3:$water_im = imagecreatefrompng($waterImage);
                           break;
                       default: throw new Exception($formatMsg);
                   }
               }
               //读取背景图片
                if (!empty($groundImage) && file_exists($groundImage)) {
                   $ground_info = getimagesize($groundImage);
                   $ground_w = $ground_info[0]; //取得背景图片的宽
                   $ground_h = $ground_info[1]; //取得背景图片的高
                   switch ($ground_info[2]) {//取得背景图片的格式
                       case 1:$ground_im = imagecreatefromgif($groundImage);
                           break;
                       case 2:$ground_im = imagecreatefromjpeg($groundImage);
                           break;
                       case 3:$ground_im = imagecreatefrompng($groundImage);
                           break;
                       default:die($formatMsg);
                   }
               } else {
                   throw new Exception("需要加水印的图片不存在!");
               }
                //水印位置
               if ($isWaterImage) {//图片水印
                   $w = $water_w;
                   $h = $water_h;
                   $label = "图片的";
               } else {//文字水印
                   $temp = imagettfbbox(ceil($textFont * 5.5), 0, BASE_PATH. "statics/font/courbd.ttf", $waterText); //取得使用 TrueType 字体的文本的范围
                   $w = $temp[2] - $temp[6];
                   $h = $temp[3] - $temp[7];
                   unset($temp);
                   $label = "文字区域";
               }
               if (($ground_w < $w) || ($ground_h < $h)) {
                   throw new Exception ("需要加水印的图片的长度或宽度比水印" . $label . "还小,无法生成水印!");
               }
         
            $posX = $ground_w - $w;
            $posY = $ground_h - $h;
                   
           //设定图像的混色模式
               imagealphablending($ground_im, true);
            if ($isWaterImage) {//图片水印
                imagecopy($ground_im, $water_im, $posX, $posY, 0, 0, $water_w, $water_h); //拷贝水印到目标文件
            } else {//文字水印
                if (!empty($textColor) && (strlen($textColor) == 7)) {
                    $R = hexdec(substr($textColor, 1, 2));
                    $G = hexdec(substr($textColor, 3, 2));
                    $B = hexdec(substr($textColor, 5));
                } else {
                    throw new Exception("水印文字颜色格式不正确!");
                }
                imagestring($ground_im, $textFont, $posX, $posY, $waterText, imagecolorallocate($ground_im, $R, $G, $B));
            }
           //生成水印后的图片
               @unlink($groundImage);
               switch ($ground_info[2]) {//取得背景图片的格式
                   case 1:imagegif($ground_im, $groundImage);
                       break;
                   case 2:imagejpeg($ground_im, $groundImage);
                       break;
                   case 3:imagepng($ground_im, $groundImage);
                       break;
                   default:die($errorMsg);
               }
           //释放内存
            if (isset($water_info))  unset($water_info);
            if (isset($water_im))   imagedestroy($water_im);
            unset($ground_info);
            imagedestroy($ground_im);
            return;
    }
    //返回 等比例宽高度
    private function getProportionWH($srcImageFile,$width,$height){
        
        $return = array('w'=>$width,'h'=>$height);
        
        $imginfo = getimagesize($srcImageFile);
        
        $localarr = explode('"',$imginfo[3]);
        
        $w = isset($localarr[1]) ? $localarr[1] : 0;
        $h = isset($localarr[3]) ? $localarr[3] : 0;
        
        if(!$w || !$h) return $return;    
        
        if($w < $width && $h < $height) return  array('w'=>$w,'h'=>$h);
        if($width/$height > $w/$h){//如果新的压缩比例显示我要比原有的 长宽比大 那么 我因为依据 高为基础
            
            $return['h'] = $height;
            
            $return['w'] = $w * $height/$h;
        }else{
            $return['h'] = $h * $width/$w;
            
            $return['w'] = $width;
        }
        
        return $return;
    }


    /*
     * 生成缩略图
     */
    public function resizeImage($srcImageFile, $destImageFile, $width, $height)
    {
        $srcImage   = $this->imageCreateFromFile($srcImageFile);
        if (empty($srcImage))   return false;

        $extName    = $this->getExtFileName($destImageFile);
        if (empty($extName))    return false;

        $extName    = strtolower($extName);
        if ($extName === 'jpg' || $extName === 'jpeg')
        {
            $type   = IMAGETYPE_JPEG;
        } else if ($extName === 'gif') {
            $type   = IMAGETYPE_GIF;
        } else if ($extName === 'png') {
            $type   = IMAGETYPE_PNG;
        } else  return false;
        
        $info = $this->getProportionWH($srcImageFile, $width, $height);
        if($info['w'] < $width && $info['h'] < $height) return;
        
        $width = $info['w'];
        $height= $info['h'];
        
        $destImage  = imagecreatetruecolor($width, $height);
        imagefilledrectangle($destImage, 0, 0, $width, $height, imagecolorallocate($destImage, 255, 255, 255));
        imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $width, $height, imagesx($srcImage), imagesy($srcImage));
        switch($type)
        {
            case IMAGETYPE_PNG:
                return imagepng($destImage, $destImageFile, 2);
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                return imagejpeg($destImage, $destImageFile, 100);
            case IMAGETYPE_GIF:
                return imagegif($destImage, $destImageFile);
        }
        return false;
    }
    private function uploadOne($_file){

        if($_FILES[$_file]['error'] != 0)   throw new Exception('图片上传失败！');
        if(!isImage($_FILES[$_file]['name'])) throw new Exception ('图片类型不合法！');
        $save_dir = $this->getStoreDir();
        $store_file_name = $this->getNoDupFilePath($_FILES[$_file]['name'], $save_dir);
        $web_file_name = $this->_webDir.'/'.$this->_dateSegment.'/'.$store_file_name;
        $save_file = $save_dir.'/'.$store_file_name;
        if(!move_uploaded_file($_FILES[$_file]['tmp_name'], $save_file)) throw new Exception('移动图片失败');
        return array(
                    'web_file_name'     => $web_file_name,
                    'store_file_name'   =>  $save_dir.'/'.$store_file_name
                    );
    }

    private function uploadArr($_file){
        $cnt = count($_FILES[$_file]['tmp_name']);
        $save_dir = $this->getStoreDir();
        $return = array();
        for($i=0;$i<$cnt;$i++){
             if(!empty($_FILES[$_file]['tmp_name'][$i] )){ //群上传的时候 如果 临时文件为空那么就不处理了
                 if($_FILES[$_file]['error'][$i] != 0)   throw new Exception('第'.($i+1).'图片上传失败！');
                 if(!isImage($_FILES[$_file]['name'][$i])) throw new Exception ('第'.($i+1).'图片类型不合法！');
                 $store_file_name = $this->getNoDupFilePath($_FILES[$_file]['name'][$i], $save_dir);
                 $web_file_name = $this->_webDir.'/'.$this->_dateSegment.'/'.$store_file_name;
                 if(!move_uploaded_file($_FILES[$_file]['tmp_name'][$i], $save_dir.'/'.$store_file_name)) throw new Exception('第'.($i+1).'移动图片失败');
                 $return['web_file_name'][$i] = $web_file_name;
                 $return['store_file_name'][$i] =  $save_dir.'/'.$store_file_name;
             }
        }
        return $return;
    }

    private function getStoreDir(){

        $save_dir  = $this->_saveDir . '/' . $this->_dateSegment;

        if (!is_dir($save_dir))    mkdir($save_dir, 0700, true);//如果文件夹不存在，则创建

        return $save_dir;
    }

    private function getUnixMtRandPath($fileName){
        $lastDotPos     = strrpos($fileName, '.');//最后一个 . 的位置
        if ($lastDotPos === false)
        {
            $extName        = '';
        } else {
            $extName        = substr($fileName, $lastDotPos);
        }
        $fileBasePath   = time() . '_'.mt_rand(0, 10000);
        return $fileBasePath . $extName;
    }


    private function getNoDupFilePath($fileName, $dir)
    {
        $fileName = $this->getUnixMtRandPath($fileName);
        $lastDotPos     = strrpos($fileName, '.');//最后一个 . 的位置
        if ($lastDotPos === false)
        {
            $fileBasePath   = $fileName;
            $extName        = '';
        } else {
            $fileBasePath   = substr($fileName, 0, $lastDotPos);
            $extName        = substr($fileName, $lastDotPos);
        }
        while (true)
        {
            $retPath    = $dir . '/' . $fileBasePath . $extName;
            if (file_exists($retPath))
            {
                $fileBasePath   = $fileBasePath . '_' .  mt_rand(0, 1000);
                continue;
            } else {
                return $fileBasePath . $extName;
            }
        }
    }

    private   function getExtFileName($fileName)
    {
        $lastDotPos = strrpos($fileName, '.');
        if ($lastDotPos === false)
        {
            return false;
        }
        return substr($fileName, $lastDotPos + 1);
    }

    private function imageCreateFromFile($imgFile)
    {
        if (!file_exists($imgFile) || !is_readable($imgFile))
        {
            return null;
        }

        $imgInfo    = getimagesize($imgFile);
        if (empty($imgFile))    return null;
        switch ($imgInfo[2])
        {
            case IMAGETYPE_GIF:
                return imagecreatefromgif($imgFile);
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                return imagecreatefromjpeg($imgFile);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($imgFile);
        }
        return null;
    }

}
?>