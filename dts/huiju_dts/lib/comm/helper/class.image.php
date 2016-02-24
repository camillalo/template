<?php
class image {
	    const ROOT_PATH = './';
	    const FAIL_WRITE_DATA = 'Fail to write data';
	    //没有数据流
	    const NO_STREAM_DATA = 'The post data is empty';
	   //图片类型不正确
	    const NOT_CORRECT_TYPE = 'Not a correct image type';
	    //不能创建文件
	    const CAN_NOT_CREATE_FILE = 'Can not create file';
	    //上传图片名称
	    public $image_name;
	    //图片保存名称
	    public $save_name;
	    //图片保存路径
	    public $save_dir;
	    //目录+图片完整路径
	    public $save_fullpath;
	
	    /**
	     * 构造函数
	     * @param String $save_name 保存图片名称
	     * @param String $save_dir 保存路径名称
	     */
		 /*
	    public function __construct($save_name, $save_dir) {
		        //set_error_handler ( $this->error_handler () );
		
		        //设置保存图片名称，若未设置，则随机产生一个唯一文件名
		        $this->save_name = $save_name ? $save_name : md5 ( mt_rand (), uniqid () );
		        //设置保存图片路径，若未设置，则使用年/月/日格式进行目录存储
		        $this->save_dir =  $save_dir ? $save_dir : date ( 'Y/m/d' );
		
		        //创建文件夹
		        @$this->create_dir ( $this->save_dir );
		        //设置目录+图片完整路径
		        $this->save_fullpath = $this->save_dir . '/' . $this->save_name;
		    }
		*/
		public function prepare($save_name, $save_dir) {
		        //set_error_handler ( $this->error_handler () );
		
		        //设置保存图片名称，若未设置，则随机产生一个唯一文件名
		        $this->save_name = $save_name ? $save_name : md5 ( mt_rand (), uniqid () );
		        //设置保存图片路径，若未设置，则使用年/月/日格式进行目录存储
		        $this->save_dir =  $save_dir ? $save_dir : date ( 'Y/m/d' );
		
		        //创建文件夹
		        @$this->create_dir ( $this->save_dir );
		        //设置目录+图片完整路径
		        $this->save_fullpath = $this->save_dir . '/' . $this->save_name;
		    }

			
		    public function stream2Image($imageStream) {
		    		//$data = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDABsSFBcUERsXFhceHBsgKEIrKCUlKFE6PTBCYFVlZF9VXVtqeJmBanGQc1tdhbWGkJ6jq62rZ4C8ybqmx5moq6T/2wBDARweHigjKE4rK06kbl1upKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKSkpKT/wAARCAA6ADoDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwC3o+kw29uruoMhHJ71pfZ4v7n6mi2/1C/j/OpaYiL7PF/c/U0fZ4v7n6mpaKVwsRfZ4v7n6mj7PF/cA/U1LRRcLFO7063uIirRg8cZrlX0WcOwQgqCcE+ldtWXTWonoX7b/AFC/j/Opaz2jPkrIM46Hr6/UU2NwiyS54jUt1/8Ar1Dk+a1jZQXJzXNKiucsHG95nlk3xKz7cnafTPzepqOG6eOGZMvmQAZyeBzn+Kt/ZGHMdPRXOvI8cFvC0josgMjNk556d/TtV+1SQ2KASGcMxIIzwPTk5qZx5VcuPvOxp1l1NDG4lUmNgM9cf/XqGog7jqRUXoy/bDNuoPTn+dVru1la3kjhIJdh95sYH5VZtv8AUL+P86lp7O4k9LGQLC7Fi0GYwSQAA3QD3x60TaVIwk8vaNxVV56KBz27mteiq9oyeVGVdWF28zLFIPIYAAFvuj2GK0beEW8CRKchRjPrUlFJybVhpWCsutSsuhCkWNMuo7i0RkYcjpVyuJ0J3F0UDMFIyRniuipWuF7GpRWXRTsHMalFZdFFg5jRkkWNCzEDFcy+txK7KFLAEgEd6Ned1gAVmAJ');
		    		$data = base64_decode($imageStream);
		    		//二进制数据流
			       // $data = file_get_contents ( 'php://input' ) ? file_get_contents ( 'php://input' ) : gzuncompress ( $GLOBALS ['HTTP_RAW_POST_DATA'] );
			        //数据流不为空，则进行保存操作
			        if (! empty($data )) {
				            //创建并写入数据流，然后保存文件
				            if (@$fp = fopen ( $this->save_fullpath, 'w+' )) {
				                fwrite ( $fp, $data );
				                fclose ( $fp );
				                $baseurl = $this->save_fullpath;
				                return true;
				                //$baseurl = "http://" . $_SERVER ["SERVER_NAME"] . ":" . $_SERVER ["SERVER_PORT"] . dirname ( $_SERVER ["SCRIPT_NAME"] ) . '/' . $this->save_name;
				                /*
				                if ( $this->getimageInfo ( $baseurl )) {
					                    echo $baseurl;
					               } else {
						                    echo ( self::NOT_CORRECT_TYPE  );
						             }*/
							  } else {
								
								     }
							  return false;
						} else {
					            //没有接收到数据流
					           // echo ( self::NO_STREAM_DATA );
					           return false;
							}
			 }
		    /**
		     * 创建文件夹
		     * @param String $dirName 文件夹路径名
		     */
		    public function create_dir($dirName, $recursive = 1,$mode=0777) {
			        ! is_dir ( $dirName ) && mkdir ( $dirName,$mode,$recursive );
			    }
		    /**
		     * 获取图片信息，返回图片的宽、高、类型、大小、图片mine类型
		     * @param String $imageName 图片名称
		     */
		    public function getimageInfo($imageName = '') {
			        $imageInfo = getimagesize ( $imageName );
			        if ($imageInfo !== false) {
				            $imageType = strtolower ( substr ( image_type_to_extension ( $imageInfo [2] ), 1 ) );
				            $imageSize = filesize ( $imageInfo );
				            return $info = array ('width' => $imageInfo [0], 'height' => $imageInfo [1], 'type' => $imageType, 'size' => $imageSize, 'mine' => $imageInfo ['mine'] );
				     } else {
					            //不是合法的图片
					            return false;
					 }
					
		    }
														
		    /*private function error_handler($a, $b) {
		        echo $a, $b;
		    }*/
														
	}
