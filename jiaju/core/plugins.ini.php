<?php
/*
 * @前台插件中心
 */
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
//评论
function pluginsComments($type,$type_id){
    return require TEMPLATE_PATH.'plugins/comments.html';
}

function pluginsAdsLunbo($id,$width,$height){

    import::getInt('ads');
    $data = adsInt::getInstance()->load($id);
    if(empty($data)) return;
    $ads = empty($data['item']) ? array() : $data['item'];
    if(empty($ads)) return;
    return require TEMPLATE_PATH.'plugins/lunxian.html';
}

function pluginsRecommendLunBo($id,$width,$height){ 
    if(empty($id)) return;
    $recommend = recommend::getInstance()->load($id,6);
    $id = str_replace('-', '', $id);
    if(empty($recommend)) return;
    return require TEMPLATE_PATH.'plugins/lunxian_recommend.html';
}

//广告插件
function pluginsAds($id,$type = false){
    global $__AD_TYPE;
    import::getInt('ads');
    $data = adsInt::getInstance()->load($id);
    if(empty($data)) return $type ? array() : '';
    if($type === true) return empty($data['item']) ? array() : $data['item'];
    $return = '';
    switch($data['type']){
        
        case $__AD_TYPE['word']:
            foreach($data['item'] as $val){
                $return .='<a target="_blank" href="'.$val['link'].'" title="'.$val['title'].'">'.$val['title'].'</a>';
            }
            break;
        case $__AD_TYPE['pic'] :
            foreach($data['item'] as $val){
                $return .='<a target="_blank" href="'.$val['link'].'" title="'.$val['title'].'"><img alt="'.$val['title'].'" border="0" src="'.URL.$val['pic'].'" /></a>';
            }
            break;
        case $__AD_TYPE['html']:
            foreach($data['item'] as $val){
                $return .= $val['code'];
            }
            break;
    }
    return $return;
}