<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
class  import{
    
    private static $_model;
    
    private static $_core;
    
    private static $_cfg;
    
    private static $_int;


    public static  function getMdl($filename){
        
        if(!isset(self::$_model[$filename])){
            $fullname = BASE_PATH .'core/mdl/'.$filename.'.mdl.php';
            if(file_exists($fullname)) {
                require $fullname;
                self::$_model[$filename] = true;
                return true;
            }
           return false;
        }
        return true;
    }
    
    
    public static  function getInt($filename){
        
        if(!isset(self::$_int[$filename])){
            $fullname = BASE_PATH .'core/interface/'.$filename.'.int.php';
            if(file_exists($fullname)) {
                require $fullname;
                self::$_int[$filename] = true;
                return true;
            }
             return false;
        }
        return true;
    }
    
    
    public static  function getCfg($filename){
        
        if(!isset(self::$_cfg[$filename])){
            $fullname = BASE_PATH .'core/config/'.$filename.'.cfg.php';
            if(file_exists($fullname)) {
              
                self::$_cfg[$filename] =  require $fullname;
                
                return   self::$_cfg[$filename];
            }
            return false;
        }
        return self::$_cfg[$filename];
    }
    
    public static  function getLib($filename){
        
        if(!isset(self::$_core[$filename])){
            $fullname = BASE_PATH .'core/library/'.$filename.'.lib.php';
            if(file_exists($fullname)) {
                self::$_core[$filename] = true;
                require $fullname;
                return true;
            }
              return false;
        }
        return true;
    }
    
}
