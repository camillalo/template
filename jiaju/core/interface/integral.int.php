<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
/*
 * 积分接口
 */
class integralInt{
    
    private static  $instance = null;
       
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new integralInt();
        }
        
        return self::$instance;
    }
    
    private function __construct() { 
      
    }

    public function checkUserIntegral($uid,$num){
        import::getMdl('integral');
        $userHasNum = integralMdl::getInstance()->getSumIntegralByUid($uid);
        return  $userHasNum >= $num ? true : false;
    }
    
    //外层其实需要做事务处理 
    public function useUserIntegral($uid,$num,$type){
        
        if(!$this->checkUserIntegral($uid, $num)) return false;
       // import::getMdl('integral');
        $usedinfo = array(
            'uid'  => (int)$uid,
            'num'  => (int)$num,
            'type' => (int)$type,
            't'    => NOWTIME 
        );
        while (true){
           if(empty($num)) break;
           $user = integralMdl::getInstance()->getIntegralByUid($uid);
           if(empty($user)) break;
           if($user['num'] >=$num){
               $user['num'] = $user['num'] - $num;
               $num =0 ;
           }else{
               $num = $num - $user['num'];
               $user['num'] = 0; 
           }
           integralMdl::getInstance()->replaceIntegral($user); //忽悠报错了谁让你通过了呢
        }
        import::getMdl('integralUsed');
        integralUsedMdl::getInstance()->addIntegralUsed($usedinfo);
        return true;
    }
    
    //获得积分
    public function obtain($type){
        if(empty($type)) return;
        $uid = (int)getUid();
        if(empty($uid)) return;
        $cfg = import::getCfg('integral');
        if(empty($cfg[$type])) return;
        $t = empty($cfg['t']) ? 30 : (int)$cfg['t'];
        import::getMdl('integral');
        $info = array(
            'uid'       => $uid,
            'num'       => (int)$cfg[$type],
            'type'      => (int)$type,
            't'         => NOWTIME,
            'expires_t' => NOWTIME + 86400 * 30
        );
        return integralMdl::getInstance()->addIntegral($info);
    }
    
    
}    