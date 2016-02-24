<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
/*
 * 分类的接口类
 */
class category{
    
    private static  $instance = null;
    
    private $_cache;
    
    private $_token = 'category';
    
    private $_datas =  null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new category();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_cache = fileCache::getInstance();
        
        $this->load();
    }
    
    public function getCategory($category_type,$id){
        
        return isset($this->_datas[$category_type][$id]) ? $this->_datas[$category_type][$id] : array(); 
        
    }
    
    public function getCategoryName($category_type,$id){
        if(isset($this->_datas[$category_type][$id])){
            return $this->_datas[$category_type][$id]['category_name'];
        }
        return null;
    }
    
    //getRoot 一级分类 $category_id 等于什么代表排除掉哪个跟父类
    public function getRoot($category_type,$category_id = 0){        
         $local = isset($this->_datas[$category_type]) ? $this->_datas[$category_type] : array();
         $return = array();
         foreach($local as $val){
             if((int)$val['parent_id'] === 0 && (int)$val['category_id']!= $category_id){
                 $return [] = $val;
             }
         }
         return $return;
    }
    //getChild 取子分类
    public function getChild($category_type,$parent_id){
        if((int)$parent_id === 0) return $this->getRoot ($category_type);
        $local = isset($this->_datas[$category_type]) ? $this->_datas[$category_type] : array();  
        $return = array();
        if(isset($local[$parent_id]['sub'])){
            foreach($local[$parent_id]['sub'] as $id){
                $return[]  = $local[$id];
            }
        }
        return $return;
    }
    
    public function getRootCol($category_type,$category_id = 0){
        $local = isset($this->_datas[$category_type]) ? $this->_datas[$category_type] : array();
         $return = array();
         foreach($local as $val){
             if((int)$val['parent_id'] === 0 && (int)$val['category_id']!= $category_id){
                 $return [$val['category_id']] = $val['category_name'];
             }
         }
         return $return;
        
    }
    public function getChildCol($category_type,$parent_id){
        if((int)$parent_id === 0) return $this->getRootCol ($category_type);
        $local = isset($this->_datas[$category_type]) ? $this->_datas[$category_type] : array();  
        $return = array();
        if(isset($local[$parent_id]['sub'])){
            foreach($local[$parent_id]['sub'] as $id){
                $return[$local[$id]['category_id']]  = $local[$id]['category_name'];
            }
        }
        return $return;
    }
    
    
    public function getAllLastChildIds($category_type,$parent_id){
         $ret = $local = isset($this->_datas[$category_type][$parent_id]['sub']) ? $this->_datas[$category_type][$parent_id]['sub'] : array();  
         $return = array();
         while(true){
             $localArr = array();
             foreach( $local as $v){
                
                 if(isset($this->_datas[$category_type][$v]['sub']) && !empty( $this->_datas[$category_type][$v]['sub'])){
                      $localArr = array_merge($localArr,$this->_datas[$category_type][$v]['sub']);
                 }else{
                      $return [] = $v;
                 }
             }
             if(empty($localArr)) break;
             $local = $localArr;
         }
         if(empty($return)) $return = $ret;
         return $return;
    }
    
    
    public function getSelect($category_type,$category_id,$type = false){
        //首先自己为空
        if((int)$category_id === 0) $this->makeCategorySelect($this->getRoot($category_type), $category_id);      
        $category_info = $this->getCategory($category_type, $category_id);
        if(empty($category_info)) return $this->makeCategorySelect($this->getRoot($category_type));
        //检查父类 如果直接是0 那么就回去
        $parent_id = (int)$category_info['parent_id'];      
 
        if($parent_id === 0 ){
            return $this->makeCategorySelect($this->getRoot($category_type,$type ?  0 : $category_id), $category_id); 
        }
        //这个也是啦
        $local = isset($this->_datas[$category_type][$parent_id]) ? $this->_datas[$category_type][$parent_id] : array();  
        if(empty($local)){
            return $this->makeCategorySelect($this->getRoot($category_type), $parent_id);
        }
        $pid  = $local['parent_id'];
        $str    = '';
        if($type){
            $str = $this->makeCategorySelect( $this->getChild($category_type, $category_info['parent_id']),$category_id);
        } 
        //如果父者的父者也为空那么 只需要把父者那一级显示出来 因为他肯定是第一级
        if((int)$pid === 0) return $this->makeCategorySelect($this->getRoot($category_type), $parent_id).$str;
        while(true){
            $data = array();
            if((int)$pid === 0){
                $str = $this->makeCategorySelect($this->getRoot($category_type), $parent_id).$str;
                break;
            }
            foreach($this->_datas[$category_type][$pid]['sub'] as $v){
                $data[]  = $this->_datas[$category_type][$v];
            }
            $str = $this->makeCategorySelect($data,$parent_id) .$str;
            $parent_id = $pid;
            $pid = $this->_datas[$category_type][$pid]['parent_id'];
        } 
             
        return $str;
    }
    
    
    private function makeCategorySelect($data,$selected = 0){
        $str ='<select name="cates[]" id="cates[]"><option value="0" >请选择.....</option>';
        foreach($data as $v){
            $str .='<option ';
            if($selected == $v['category_id']) $str .= '  selected = "selected" ';
            $str.=' value="'.$v['category_id'].'" >'.$v['category_name'].'</option>';
        }                        
        $str.='</select>';
        return $str;
    }
    
    private function load(){
        if(empty($this->_datas)){
           // $this->_datas = $this->_cache->load($this->_token);
            if(empty($this->_datas)) return $this->put();
        }
        return $this->_datas;
    }
    //直接更新缓存分类设置
    public  function put(){
        import::getMdl('category');
        $categories = categoryMdl::getInstance()->getAllCategory();
        $local  = array();
        foreach( $categories as $val){
           $local[$val['category_type']][$val['category_id']]['category_id']   = $val['category_id']; 
           $local[$val['category_type']][$val['category_id']]['category_type'] = $val['category_type']; 
           $local[$val['category_type']][$val['category_id']]['category_name'] = $val['category_name']; 
           $local[$val['category_type']][$val['category_id']]['parent_id']     = $val['parent_id']; 
           if($val['parent_id'] > 0){
               $local[$val['category_type']][$val['parent_id']]['sub'][]  = $val['category_id']; 
           }
        }
        $this->_cache->put($this->_token,$local,86400*365); //将缓存时间设置成一年
        $this->_datas = $local;
        return $this->_datas;
    }
        
}