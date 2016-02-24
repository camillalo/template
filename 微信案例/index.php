<?php
header("content-type:text/html;charset=utf-8");
require_once("weixin.class.php");

$appid = "wx5fdc8233e9678fdf"; 
$appsecret = "ecfbc6a335c7856e3e331330ba15cd57";

$weixin = new class_weixin_adv($appid, $appsecret);  //实类化
$click_href = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=";
$tail_href = "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
    
$xml_array2=simplexml_load_file('config.xml');
	/*--菜单栏--start--*/
    foreach ($xml_array2->navigations as $data) {
        foreach ($data->menu as $data2) {
            $type = $data2->type;
            if($type == "click"){ 
                foreach ($data2->content as $data3) {
                    $html_click = '{  
                      "type":"click",
                      "name":"'.$data3->name.'",
                      "key":"'.$data3->key.'"
                    },';
                }

            }else if($type == "view"){ 
                foreach ($data2->content as $data3) {
                    $html_view = '{  
                      "type":"view",
                      "name":"'.$data3->name.'",
                      "url":"'.$click_href.$data3->url.$tail_href.'"
                    },';
                }
            }else if($type == "sub_botton"){ 
                $html_sub_botton ='{
                                   "name":"'.$data2->name.'",
                                   "sub_button":[';

                foreach ($data2->sub_botton as $data3) {
                    $type = $data3->type;
                    if($type == "click"){ 
                        foreach ($data3->content as $data4) {
                            $html_sub_botton .= '{  
                              "type":"click",
                              "name":"'.$data4->name.'",
                              "key":"'.$data4->key.'"
                            },';
                        }
                    }else if($type == "view"){ 
                        foreach ($data3->content as $data4) {
                            $html_sub_botton .= '{  
                              "type":"view",
                              "name":"'.$data4->name.'",
                              "url":"'.$click_href.$data4->url.$tail_href.'"
                            },';
                        }
                    }
                }
                $html_sub_botton .= ']}';
            }
        }
    }
    $html = $html_click.$html_view.$html_sub_botton;
    $data3 ='{
            "button":['.$html.']
    }';

//var_dump($weixin->create_menu($data3));
/*--菜单栏--end--*/



/*--自动回复--start--*/
$replays_data = array();
foreach ($xml_array2->replays as $arr) { 
	foreach ($arr->menu as $arr2) {
		$replays_data[] = (array)$arr2;
	}
}

/*--自动回复--end--*/

//关键字回复
$weixin->responseMsg($replays_data);

?>