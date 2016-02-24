<?php
	/**
*@ 
*例如：$thumb_image = new thumb_image(300,200,ROOT."/upload/".$image_name,ROOT."/images/");       
*     $thumb_image = $thumb_image->Canvas();
*生成缩略图
*/
class thumb_image
{
  private $new_width;//需要生成的图片宽度
  private $new_height;//需要生成的图片高度
  private $save_path;//需要生成的图片的保存路径
  private $img_path;//图片的路径

  //初始化输入数据
  function __construct($new_width,$new_height,$img_path,$save_path=null)
  {
        $this->new_width = $new_width;
        $this->new_height = $new_height;
        $this->save_path = $save_path;
        $this->img_path = $img_path;
  }

 public function Canvas(){ 
    $new_width = $this->new_width;
    $new_height = $this->new_height;
    $img_path = $this->img_path;
    $save_path = $this->save_path;

    //判断文件是否存在
    if(!is_file($img_path)){ 
      echo "<script>alert('上传的文件不存在！')</script>";
      exit();
    }

    if(!is_dir($save_path)){ 
        mkdir($save_path);
    }

    $image_info = getimagesize($img_path);
    //生成缩略
    switch ($image_info['mime']) {
      case 'image/jpeg':
      case 'image/pjpeg':
        $old_img = imagecreatefromjpeg($img_path);
        break;
      case 'image/png':
      case 'image/x-png':
        $old_img = imagecreatefrompng($img_path);
        break;
      case 'image/gif':
        $old_img = imagecreatefromgif($img_path);
        break;
      default:
        echo "<script>alert('上传的图片格式有误')</script>";
        break;
    }

    $old_img_width = imagesx($old_img);
    $old_img_height = imagesy($old_img);

    //图片实际大小，跟标准进行对比
    if($new_width > $old_img_width && $new_height < $old_img_height){ 
    	//标准宽度大
    	$new_height_2 = $old_img_height*($new_width/$old_img_width);
      	$new_width_2 = $new_width;
    }else if($new_width < $old_img_width && $new_height > $old_img_height){ 
    	//标准高度大
    	$new_height_2 = $new_height;
      	$new_width_2 = $new_width*($new_height/$old_img_height);
    }else{ 
    	//标准高度小，标准宽度小
    	$state_h = $new_height/$old_img_height;
    	$state_w = $new_width/$old_img_width;
    	if($state_h >= $state_w){ 
    		$new_height_2 = $new_height;
      		$new_width_2 = $old_img_width*$state_h;
    	}else{ 
    		$new_height_2 = $old_img_height*$state_w;
      		$new_width_2 = $new_width;
    	}
    }

   //进行放大,缩小
    $dim = imagecreatetruecolor($new_width_2, $new_height_2);
    imagecopyresampled ($dim,$old_img,0,0,0,0,$new_width_2,$new_width_2,$old_img_width,$old_img_width);

    $temp_height = $new_height;
	$temp_width = $new_width;

	//进行剪切
	$tmpImage = imagecreatetruecolor($temp_width, $temp_height);
	imagecopyresampled($tmpImage, $dim, 0, 0, 0, 50, $temp_width, $temp_height, $new_width_2, $new_height_2/2);

	//保存图片
    switch ($image_info['mime']) {
      case 'image/jpeg':
      case 'image/pjpeg':
        $thumb_image_name = time().".jpg";
        imagejpeg($tmpImage,$save_path.$thumb_image_name,100);
        return $thumb_image_name;
        break;
      case 'image/png':
      case 'image/x-png':
        $thumb_image_name = time().".png";
        imagepng($tmpImage,$save_path.$thumb_image_name);
        return $thumb_image_name;
        break;
      case 'image/gif':
        $thumb_image_name = time().".gif";
        imagegif($tmpImage,$save_path.$thumb_image_name);
        return $thumb_image_name;
        break;
      default:
        echo "<script>alert('上传的图片格式有误')</script>";
        break;
    }
 }

}

	  $thumb_image = new thumb_image(960,285,"upload/5.jpg","images/");       
      $thumb_image = $thumb_image->Canvas();
?>