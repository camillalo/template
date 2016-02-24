<?php

if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

if($_GET['act'] === 'main'){
    require TEMPLATE_PATH.'bdmap.html';
    die;
}

if($_GET['act'] === 'getResult'){
    $lng = empty($_GET['lng']) ?  0 : (int)($_GET['lng'] * 100000);
    $lat = empty($_GET['lat']) ?  0 : (int)($_GET['lat'] * 100000);
    if(empty($lng) || empty($lat)) dieJsonRight(array());
    import::getMdl('company');
    $result = companyMdl::getInstance()->getCompanyByLngAndLat($lng,$lat);
     $str = 'map.clearOverlays();';
    foreach($result as $k=>$v){
        $result[$k]['longitude'] = $v['longitude'] / 100000;
        $result[$k]['latitude'] = $v['latitude'] / 100000;
        $result[$k]['link'] = mkUrl::linkTo('company','main',array('id'=>$v['uid']));
        $str .= 'var sContent'.$k.'=\'<a target="_blank" href="'.$result[$k]['link'].'">'.$v['company_name'].'</a>\';';
        $str .='var point'.$k.' = new BMap.Point('.$result[$k]['longitude'].', '.$result[$k]['latitude'].');';
        $str .='var marker'.$k.' = new BMap.Marker(point'.$k.');';
        $str .='map.addOverlay(marker'.$k.');';
        $str.=' var infoWindow'.$k.' = new BMap.InfoWindow(sContent'.$k.');';
        $str .='marker'.$k.'.removeEventListener(); ';  
        $str .='marker'.$k.'.addEventListener("click", function(){ ';         
        $str.=' this.openInfoWindow(infoWindow'.$k.');';    
        $str.='});';
    }
    dieJs("$str");
    die;
}