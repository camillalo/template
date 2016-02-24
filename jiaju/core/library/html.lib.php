<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
class html{
    /**
     * 这个参数在搜索的时候如果你发现 0 这个值不好处理的时候替换 
     */
    static public function select($name,$arr=array(),$checked = null,$need = null){
         $str = '<select name="'.$name.'" id="'.$name.'">';   
         
         if($need){
             $str .='<option value="'.$need.'">请选择......</option>';
         }else{
             $str .='<option value="">请选择......</option>';
         }
         foreach($arr as $k=>$val){
            if($checked == $k){
                $str .='<option selected="selected" value="'.$k.'">'.$val.'</option>'; 
            }else{
                $str .='<option value="'.$k.'">'.$val.'</option>';
            }
         }
         $str.='</select>';
         return $str;
    }
    
    static public function radio($name,$arr = array(),$checked = null){
        $str = '';
        foreach($arr as $k=>$val){
            if($checked == $k){
                $str.='&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" checked="checked" name="'.$name.'" id="'.$name.'" value="'.$k.'" />&nbsp;&nbsp;' . $val; 
            }else{
                $str.='&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$name.'" id="'.$name.'" value="'.$k.'" />&nbsp;&nbsp;' . $val;
            }
        }   
        return $str;
    }
    
    static public function checkbox($name,$arr = array(),$checked = array()){
        $str = '';
        foreach($arr as $k=>$val){
            if(in_array($k,$checked)){
                $str.='&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" checked="checked" name="'.$name.'[]" id="'.$name.'[]" value="'.$k.'" />&nbsp;&nbsp;' . $val; 
            }else{
                $str.='&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$name.'[]" id="'.$name.'[]" value="'.$k.'" />&nbsp;&nbsp;' . $val;
            }
        }   
        return $str;
    }
    
    static public  function wordsChangeColor($words,$color = 'red'){
        if(!is_array($words)) return '<span style="color:'.$color.'">'.$word.'</span>';
        foreach($words as $k=> $v){
            $words[$k] = '<span style="color:'.$color.'">'.$v.'</span>';
        }
        return $words;
    }
    
}