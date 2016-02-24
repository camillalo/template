<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
class mkUrl{
    
    static public function linkTo($ctl,$act = 'main',$params = array()){
        switch($ctl){
             case 'login':
             case 'register':  
             case 'qqlogin':    
             case 'search':
             case 'user':   
             case 'ajax':
                    return self::defaultLinkTo($ctl, $act, $params);
                    break;
             case 'ask':
                    return self::askLintTo($ctl, $act, $params);
                 break;
             case 'designer':
             case 'company':
                 if(IS_REWRITE){
                    return self::companyLinkTo($ctl, $act, $params);
                 }else{
                    return self::defaultLinkTo($ctl, $act, $params);
                 }
                 break;
       
             default:
                 if(IS_REWRITE){
                    return self::rewriteLinkTo($ctl, $act, $params);
                 }else{
                    return self::defaultLinkTo($ctl, $act, $params);
                 }
                 break;
         }
         return;
    }

    static private function  askLintTo($ctl, $act, $params){
        switch ($act){
            case 'list':
            case 'main':
            case 'detail':
                if(IS_REWRITE){
                    return  self::rewriteLinkTo($ctl, $act, $params);
                 }
                 break;
        }
        return self::defaultLinkTo($ctl, $act, $params);
    }
    static private function companyLinkTo($ctl, $act, $params){
        if(!isset($params['id'])) return '###';
        $url = URL.$ctl.$params['id'];
        unset($params['id']);
        if(empty($params)&&$act==='main') return $url;
        $url.= '/'.$act;
        if(!empty($params)){
            $local = array();
            foreach($params as $k=>$v){
                $local[] = $k.'-'.$v;
            }
            $url.= '_'.join('--',$local).'.html';
        }
        return $url;
    }
    
    //解析REWRITE参数
    static public function getRewriteArgument(){
        
        if(!isset($_GET['argument'])) return $_GET;
        $arr = explode('--', $_GET['argument']);
        if(empty($arr)) return $_GET;
        foreach($arr as $v){
            list($key,$val) = explode('-', $v);
            $_GET[$key] = $val;
        }
        return $_GET;
    }
    
    static private function rewriteLinkTo($ctl,$act,$params){
        if($act === 'main' && empty($params)){
            if($ctl === 'index') return URL;
            return  URL.$ctl.'/';
        }
        if(empty($params)) return URL.$ctl.'_'.$act.'.html';
        if(count($params) == 1 && isset($params['id'])) return  URL.$ctl.'_'.$act.'_'.$params['id'].'.html';
        $local = array();
        foreach($params as $k=>$v){
            $local[] = $k.'-'.$v;
        }
        return URL.$ctl.'_'.$act.'_'.join('--',$local).'.html';
    }
    //默认的URL连接不受REWRITE营销
    //不受域的影响
    static private function defaultLinkTo($ctl,$act,$params){
          $url =  URL.'index.php?ctl='.$ctl;  
          if($act!='main') $url.='&act='.$act;
          $local = array();  
          foreach($params as $k=>$v){
              if($k !== 'page') $local[] = $k.'='. urlencode($v);
              else $local[] = $k.'='.$v;
          }  
          if(!empty($local)) $url.='&'.join('&',$local);
          return $url;
    }
  
}