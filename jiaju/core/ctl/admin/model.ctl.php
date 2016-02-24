<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
if($_GET['act'] === 'main'){
    logsInt::getInstance()->systemLogs('打开了模块生成器');
    require TEMPLATE_PATH.'model/main.html';
    die;
}
if($_GET['act'] === 'make'){
    logsInt::getInstance()->systemLogs('使用了模块生成器');
    $cnname = empty($_POST['cnname']) ? errorAlert('请填写中文名称') : $_POST['cnname'];
    $mdlname = empty($_POST['mdlname']) ? errorAlert('请填写英文名称') : $_POST['mdlname'];
    $dbname = empty($_POST['dbname']) ? errorAlert('请填写英文名称') : $_POST['dbname'];
    $index  = empty($_POST['index'])  ? errorAlert('请填写主键') : $_POST['index'];
    $mdl = empty($_POST['mdl']) ? array() : $_POST['mdl'];
    $funmname = ucfirst($mdlname);
    $info = empty($_POST['info']) ? array() : $_POST['info'];
    if(empty($info['enname'])) errorAlert ('英文名称不能为空');
    $picArr = array();
    foreach($info['enname'] as $k => $v){
        if(!isset($info['cnname'][$k]) || empty($info['cnname'][$k]) ) errorAlert ('中文名称不能为空');
        if(!isset($info['type'][$k]) || empty($info['type'][$k]) ) errorAlert ('类型不能为空');
        if((int)$info['type'][$k] === 4) $picArr[$k] = $v;
    }
    //偷懒把权限写入进去
    if(in_array(2,$mdl) ||in_array(3,$mdl) ||in_array(4,$mdl) ||in_array(5,$mdl)  ){
        import::getMdl('privilegeGroup');
        privilegeGroupMdl::getInstance()->replacePrivilegeGroup(array('group_name'=>$cnname));
        $group_id = privilegeGroupMdl::getInstance()->getPrivilegeGroupIdByName($cnname);
        if($group_id){
            import::getMdl('privilege');
                privilegeMdl::getInstance()->replacePrivilege(array('group_id'=>$group_id,'privilege_name'=>$cnname.'列表','privilege_key'=>$mdlname.'_main'));
            if(in_array(2,$mdl)){
                privilegeMdl::getInstance()->replacePrivilege(array('group_id'=>$group_id,'privilege_name'=>'新增'.$cnname,'privilege_key'=>$mdlname.'_add'));
            }
            if(in_array(3,$mdl)){
                privilegeMdl::getInstance()->replacePrivilege(array('group_id'=>$group_id,'privilege_name'=>'修改'.$cnname,'privilege_key'=>$mdlname.'_edit'));
            }
            if(in_array(4,$mdl)){
                privilegeMdl::getInstance()->replacePrivilege(array('group_id'=>$group_id,'privilege_name'=>'删除'.$cnname,'privilege_key'=>$mdlname.'_del'));
            }
            if(in_array(5,$mdl)){
                privilegeMdl::getInstance()->replacePrivilege(array('group_id'=>$group_id,'privilege_name'=>'查看'.$cnname,'privilege_key'=>$mdlname.'_view'));
            }
            
        }
        
    }
    
if(in_array(1,$mdl)){    
    /*********第一步开始生成MODEL*********/
$str='<?php
if ( !defined ( \'BASE_PATH\') )
{
    exit (\'Access Denied\' );
}

class '.$mdlname.'Mdl{
    
    private  $_db;
    
    private static  $instance = null;
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new '.$mdlname.'Mdl();
        }
        
        return self::$instance;
    }
    
    private function __construct() {
        
        $this->_db = mysql::getInstance();
        
    }
    
    public  function add'.$funmname.'($info){
        
        if(empty($info)) return false;
        
        return $this->_db->insert(DB_FIX . \''.$dbname.'\',$info);
    } 
    
    public function update'.$funmname.'($id,$info){
        
        if(empty($info)) return false;
        
        $id = (int)$id;
        
        return $this->_db->update(DB_FIX.\''.$dbname.'\',$info," '.$index.' = {$id} ");
    }
    
    public function get'.$funmname.'($id){
        
        $id = (int)$id;
        
        return $this->_db->fetchRow("select  * from `".DB_FIX."'.$dbname.'` where '.$index.' ={$id} limit 1 ");
    }
    

    
    public function del'.$funmname.'($id){
         $id = (int)$id;
         return $this->_db->delete(DB_FIX."'.$dbname.'"," '.$index.' = {$id} ");
    }
    
    
    public function get'.$funmname.'List($col,$where,$order,$start=0,$limit=10){
        
        if(empty($col)){
            $colstr =\'*\';
        }else{
            $colstr = join(\',\',$col);
        }
        $wherestr  = $this->getWhere($where);
        if(empty($order)){
            $orderby = \' order by  '.$index.'  desc \';
        }else{
            $local = array();
            foreach($order  as $k => $v){
                $local [] = " {$k} {$v} ";
            }
            $orderby = \' order by \'.join(\',\',$local);
        }
        return $this->_db->fetchAll(" select {$colstr} from  `".DB_FIX."'.$dbname.'` {$wherestr} {$orderby} limit {$start},{$limit} ");
    }
    
    public function get'.$funmname.'Count($where){
        
        $wherestr  = $this->getWhere($where);
        
        return $this->_db->fetchOne("  select count(1) from  `".DB_FIX."'.$dbname.'` {$wherestr}");
    }
    
    
    private function getWhere($where){
         $local = array();
         if(isset($where[\'keyword\'])){
             $where[\'keyword\'] = $this->_db->quote($where[\'keyword\']);'."\r\n";
if(!empty($info['keyword'])){ 
    $local = array();
    foreach($info['keyword'] as $k => $v){
        if(!empty($v)){
            $local[] = $info['enname'][$k] .' like  \'%{$where[\'keyword\']}%\'  ';
        }
    }
    if(!empty($local)){
        $str.='             $local[] = " ( '.join(' or ' , $local).' ) ";'."\r\n";
    }   
}
$str.='         }'."\r\n";
if(!empty($info['search'])){
    $local = array();
    foreach($info['search'] as $k => $v){
       if(!empty($v)){
$str.='         if(isset($where[\''.$info['enname'][$k].'\'])){'."\r\n";           
switch($info['type'][$k]){
     case  1:
$str.='             $where[\''.$info['enname'][$k].'\'] = (int)$where[\''.$info['enname'][$k].'\'];'."\r\n";
$str.='             $local[] = " '.$info['enname'][$k].' = {$where[\''.$info['enname'][$k].'\']} ";'."\r\n";
        break;
    default:
$str.='             $where[\''.$info['enname'][$k].'\'] = $this->_db->quote($where[\''.$info['enname'][$k].'\']);'."\r\n";
$str.='             $local[] = " '.$info['enname'][$k].' = \'{$where[\''.$info['enname'][$k].'\']}\' ";'."\r\n";        
        break;
}
$str.='         }'."\r\n";
       } 
    }
}
$str.='         
         $wherestr = \'\';
         if(!empty($local)){
             $wherestr = " WHERE  ". join(\' and \',$local);
         }
         return $wherestr;
    }
    
    
}';    

$filename = BASE_PATH.'core/mdl/'.$mdlname.'.mdl.php';
file_put_contents($filename, $str);
}

if(in_array(2,$mdl) ||in_array(3,$mdl) || in_array(4,$mdl) || in_array(5,$mdl) ){

$str ='<?php
if ( !defined ( \'BASE_PATH\') )
{
	exit ( \'Access Denied\' );
}
session_write_close();
define(\'PAGE_SIZE\',30);//分页大小
import::getMdl(\''.$mdlname.'\');
if($_GET[\'act\'] === \'main\'){
    $url = \'index.php?ctl='.$mdlname.'&act=main\'; 
    $_GET[\'keyword\'] = empty($_GET[\'keyword\']) ? \'\' : htmlspecialchars($_GET[\'keyword\'],ENT_QUOTES,\'UTF-8\');
    $where  = array();
    if(!empty($_GET[\'keyword\'])){
        $url.=\'&keyword=\'.  urlencode($_GET[\'keyword\']);
        $where[\'keyword\'] = $_GET[\'keyword\'];
    }
    $totalnum = '.$mdlname.'Mdl::getInstance()->get'.$funmname.'Count($where);
    $_GET[\'page\'] = empty($_GET[\'page\']) ? 1 : (int)$_GET[\'page\']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET[\'page\'] > $pageMax) $_GET[\'page\'] = $pageMax;
    if($_GET[\'page\']<=0) $_GET[\'page\'] = 1;   
    $begin = ($_GET[\'page\'] -1) * PAGE_SIZE;
    
    $orderby = array(\''.$index.'\'=>\'DESC\');';
    $col = array('\'`'.$index.'`\'');
    if(!empty($info['is_show'])){
        foreach($info['is_show'] as $k=> $v){
            if(!empty($v)){
                $col[] = '\'`'.$info['enname'][$k].'`\'';
            }
        }
     
    }
$str.='    $col = array('.join(',',$col).'); ';   
$str.='    
    $datas = '.$mdlname.'Mdl::getInstance()->get'.$funmname.'List($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.\'&page=%d\', PAGE_SIZE, $_GET[\'page\'], $totalnum);
    logsInt::getInstance()->systemLogs(\'查看了'.$cnname.'列表\');
    require TEMPLATE_PATH.\''.$mdlname.'/main.html\';
    die;
}
';
if(in_array(2,$mdl)){
$str.='
if($_GET[\'act\'] === \'add\'){
    if($_SERVER[\'REQUEST_METHOD\'] === \'POST\'){'."\r\n";
    
foreach($info['enname'] as $k=>$v){
        switch($info['type'][$k]){
            case 1:
                $str.='        $info[\''.$v.'\'] =empty($_POST[\''.$v.'\']) ? 0: (int)$_POST[\''.$v.'\'];'."\r\n";
                if(!isset($info['is_null'][$k])|| empty($info['is_null'][$k])){
                    $str.='        if(empty($info[\''.$v.'\'])) errorAlert(\''.$info['cnname'][$k].'不能为空\');'."\r\n";
                }
                break;
            case 2:
            case 5:   
                $str.='        $info[\''.$v.'\'] = empty($_POST[\''.$v.'\']) ? \'\': trim(htmlspecialchars($_POST[\''.$v.'\'],ENT_QUOTES,\'UTF-8\'));'."\r\n";
                if(!isset($info['is_null'][$k])|| empty($info['is_null'][$k])){
                    $str.='        if(empty($info[\''.$v.'\'])) errorAlert(\''.$info['cnname'][$k].'不能为空\');'."\r\n";
                }
                break;
            case 3:
                $str.='        $info[\''.$v.'\'] = empty($_POST[\''.$v.'\']) ? \'\': getValue($_POST[\''.$v.'\']);'."\r\n";
                if(!isset($info['is_null'][$k])|| empty($info['is_null'][$k])){
                    $str.='        if(empty($info[\''.$v.'\'])) errorAlert(\''.$info['cnname'][$k].'不能为空\');'."\r\n";
                }
                break;
            case 4:
                $str.='        $info[\''.$v.'\'] = \'\';'."\r\n";
                break;
        }
}
if(!empty($picArr)){
$str.='
        try{
            import::getLib(\'uploadimg\');
';          
            foreach($picArr as $k=>$v){
                if(isset($info['is_null'][$k])&&!empty($info['is_null'][$k])){
$str.='           if(!empty($_FILES[\''.$v.'\'][\'tmp_name\'])){ '."\r\n";                    
                }
$str.='            $'.$v.' = uploadImg::getInstance()->upload(\''.$v.'\');'."\r\n";
$str.='            if(!empty($'.$v.'[\'web_file_name\'])) $info[\''.$v.'\'] = $'.$v.'[\'web_file_name\'];'."\r\n";
                
                if(isset($info['is_null'][$k])&&!empty($info['is_null'][$k])){
$str.='           } '."\r\n";                    
                }
            }
$str.='            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
';
}

$str.='        
        if(!'.$mdlname.'Mdl::getInstance()->add'.$funmname.'($info)) errorAlert (\'操作失败\');
        logsInt::getInstance()->systemLogs(\'新增了'.$cnname.'\',$info,array());    
        echoJs("alert(\'操作成功\');parent.location=\'index.php?ctl='.$mdlname.'&act=add\'");
        die;
    } 

    require TEMPLATE_PATH.\''.$mdlname.'/add.html\';
    die;
}
';
}
if(in_array(3, $mdl)){ 
$str.='
if($_GET[\'act\'] === \'edit\'){
    $'.$index.' = empty ($_GET[\''.$index.'\']) ? errorAlert(\'参数错误\') : (int)$_GET[\''.$index.'\'];    
    $data = '.$mdlname.'Mdl::getInstance()->get'.$funmname.'($'.$index.');    
    if(empty($data)) errorAlert (\'参数出错\');
    
    if($_SERVER[\'REQUEST_METHOD\'] === \'POST\'){'."\r\n";
    
foreach($info['enname'] as $k=>$v){
        switch($info['type'][$k]){
            case 1:
                $str.='        $info[\''.$v.'\'] =empty($_POST[\''.$v.'\']) ? 0: (int)$_POST[\''.$v.'\'];'."\r\n";
                if(!isset($info['is_null'][$k])|| empty($info['is_null'][$k])){
                    $str.='        if(empty($info[\''.$v.'\'])) errorAlert(\''.$info['cnname'][$k].'不能为空\');'."\r\n";
                }
                break;
            case 2:
            case 5:    
                $str.='        $info[\''.$v.'\'] = empty($_POST[\''.$v.'\']) ? \'\': trim(htmlspecialchars($_POST[\''.$v.'\'],ENT_QUOTES,\'UTF-8\'));'."\r\n";
                if(!isset($info['is_null'][$k])|| empty($info['is_null'][$k])){
                    $str.='        if(empty($info[\''.$v.'\'])) errorAlert(\''.$info['cnname'][$k].'不能为空\');'."\r\n";
                }
                break;
            case 3:
                $str.='        $info[\''.$v.'\'] = empty($_POST[\''.$v.'\']) ? \'\': getValue($_POST[\''.$v.'\']);'."\r\n";
                if(!isset($info['is_null'][$k])|| empty($info['is_null'][$k])){
                    $str.='        if(empty($info[\''.$v.'\'])) errorAlert(\''.$info['cnname'][$k].'不能为空\');'."\r\n";
                }
                break;
            case 4:
                $str.='        $info[\''.$v.'\'] = $data[\''.$v.'\'];'."\r\n";
                break;
        }
}
if(!empty($picArr)){
$str.=' 
        $delpics = array();
        try{
            import::getLib(\'uploadimg\');
';          
            foreach($picArr as $k=>$v){
                if(isset($info['is_null'][$k])&&!empty($info['is_null'][$k]) ){
$str.='           if(!empty($_FILES[\''.$v.'\'][\'tmp_name\'])){ '."\r\n";                    
                }
$str.='            $'.$v.' = uploadImg::getInstance()->upload(\''.$v.'\');'."\r\n";
$str.='            if(!empty($'.$v.'[\'web_file_name\'])) {
                    $info[\''.$v.'\'] = $'.$v.'[\'web_file_name\'];
                    $delpics[] = $data[\''.$v.'\'];    
                }'."\r\n";
                
                if(isset($info['is_null'][$k])&&!empty($info['is_null'][$k])){
$str.='           } '."\r\n";                    
                }
            }
$str.='            
        }  catch (Exception $e){
            if(empty($data[\''.$v.'\'])){
                errorAlert($e->getMessage());
            }
        }
';
}

$str.='        
        if(false === '.$mdlname.'Mdl::getInstance()->update'.$funmname.'($'.$index.',$info)) errorAlert (\'操作失败\');
';     
if(!empty($picArr)){
$str.='        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
     ';   
}        
$str.='
        logsInt::getInstance()->systemLogs(\'修改了'.$cnname.'\',$data,$info);   
        echoJs("alert(\'操作成功\');parent.location=\'index.php?ctl='.$mdlname.'&act=edit&'.$index.'=".$'.$index.'."\'");
        die;
    } 
     logsInt::getInstance()->systemLogs(\'打开了'.$cnname.'编辑模块\');
    require TEMPLATE_PATH.\''.$mdlname.'/edit.html\';
    die;
        
}
';
}
if(in_array(5,$mdl)){
$str.='
if($_GET[\'act\'] === \'view\'){    
    $'.$index.' = empty ($_GET[\''.$index.'\']) ? errorAlert(\'参数错误\') : (int)$_GET[\''.$index.'\'];    
    $data = '.$mdlname.'Mdl::getInstance()->get'.$funmname.'($'.$index.');    
    if(empty($data)) errorAlert (\'参数出错\');
     logsInt::getInstance()->systemLogs(\'查看了'.$cnname.'详情\',$data,array());
    require TEMPLATE_PATH.\''.$mdlname.'/view.html\';
    die;
}
';
    
}

if(in_array(4,$mdl)){
$str.='
if($_GET[\'act\'] === \'del\'){
    $'.$index.' = empty ($_GET[\''.$index.'\']) ? errorAlert(\'参数错误\') : (int)$_GET[\''.$index.'\'];    
    $data = '.$mdlname.'Mdl::getInstance()->get'.$funmname.'($'.$index.');    
    if(empty($data)) errorAlert (\'参数出错\');
    $back_url = empty($_GET[\'back_url\']) ? \'index.php?ctl='.$mdlname.'\' : $_GET[\'back_url\'];
    if(false !== '.$mdlname.'Mdl::getInstance()->del'.$funmname.'($'.$index.')) {
';
foreach($picArr as $v){
$str.='        if(!empty($data[\''.$v.'\'])){
            if(file_exists(BASE_PATH.$data[\''.$v.'\'])) unlink(BASE_PATH.$data[\''.$v.'\']);
        }
';        
}        
$str.=' 
        logsInt::getInstance()->systemLogs(\'删除了'.$cnname.'\',$data,array());
        dieJs(\'alert("操作成功");parent.location="\'.$back_url.\'"\');
    }
    errorAlert(\'操作失败\');
    die;
}

';
}
$filename = BASE_PATH.'core/ctl/admin/'.$mdlname.'.ctl.php';
file_put_contents($filename, $str);    

/***************模版开始************/
$str ='<?php require TEMPLATE_PATH.\'header.html\';?>
<body>
<iframe name="hiden_frm"  style="display:none;"></iframe>    
    <div class="subnav">
        <div class="content-menu ib-a blue line-x"> 
            <a href="index.php?ctl='.$mdlname.'" class="on"><em>'.$cnname.'列表</em></a>
             ';   
if(in_array(2,$mdl)){ 
$str.='                
            <span>|</span>
            <a href="index.php?ctl='.$mdlname.'&act=add"><em>新增'.$cnname.'</em></a>
';
}
$str.='                
        </div>
    </div>
    <div class="pad-lr-10">
         <form method="get" action="index.php" name="searchform">
            <input type="hidden" name="ctl" value="'.$mdlname.'">
            <table width="100%" cellspacing="0" class="search-form">
                <tbody>
                    <tr>
                        <td>
                            <div class="explain-col">
                                <input type="text" size="10" name="keyword" class="input-text" value="<?php echo $_GET[\'keyword\'];?>">
                                <input type="submit" name="dosubmit" class="button" value="确定搜索">
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <div class="table-list">
            <table width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="10%">序号</th>'."\r\n";
foreach($info['is_show'] as $k=>$v){
    $str.='                      <th align="center" >'.$info['cnname'][$k].'</th>'."\r\n";
}

$str.='                        <th width="15%">管理操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php  foreach($datas as $val){ ?>
                    <tr>
                        <td width="10%" align="center"><?php echo $val[\''.$index.'\'];?></td>'."\r\n";
foreach($info['is_show'] as $k=>$v){
    if((int)$info['type'][$k] === 4){
         $str.='                        <td align="center" ><?php if(!empty($val[\''.$info['enname'][$k].'\'])){ ?><img width="80" height="80" src="<?php echo URL.$val[\''.$info['enname'][$k].'\'];?>" /><?php }?></td>'."\r\n";
    }else{
        $str.='                        <td align="center" ><?php echo $val[\''.$info['enname'][$k].'\'];?></td>'."\r\n";
    }
}                            

$str.='                        <td width="15%" align="center">
';
if(in_array(3,$mdl)){ 
$str.='                           <font color="#cccccc">
                                <a href="index.php?ctl='.$mdlname.'&act=edit&'.$index.'=<?php echo $val[\''.$index.'\'];?>">修改</a>
                            </font> | 
                            ';
}
if(in_array(5,$mdl)){
$str.='                           <font color="#cccccc">
                                <a href="index.php?ctl='.$mdlname.'&act=view&'.$index.'=<?php echo $val[\''.$index.'\'];?>">查看</a>
                            </font> | 
                        ';
}
if(in_array(4,$mdl)){ 
 $str.='                           <font color="#cccccc">
                              <a target="hiden_frm" href="index.php?ctl='.$mdlname.'&act=del&'.$index.'=<?php echo $val[\''.$index.'\'];?>&back_url=<?php echo urlencode($url.\'&page=\'.$_GET[\'page\']);?>">删除</a>
                            </font>
  ';                          
}    
$str.='                            
                        </td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
            <div id="pages"><?php echo $links;?></div>
        </div>
    </div>
</body>
</html>
    
';
$dir = BASE_PATH.'themes/admin/'.$mdlname.'/';
if(!is_dir($dir)) mkdir ($dir,0700,true);
$filename = $dir.'main.html';
file_put_contents($filename, $str);
}
if(in_array(2,$mdl)){ 
$str='<?php require TEMPLATE_PATH.\'header.html\';?>
<body>
    <iframe name="hiden_frm"  style="display:none;"></iframe>   
    <div class="subnav">
        <div class="content-menu ib-a blue line-x"> 
            <a href="index.php?ctl='.$mdlname.'" ><em>'.$cnname.'列表</em></a>
            <span>|</span>
            <a href="index.php?ctl='.$mdlname.'&act=add" class="on"><em>新增'.$cnname.'</em></a>
        </div>
    </div>  
    <div class="pad_10">
     <form ';
if(!empty($picArr)){
 $str .=' enctype="multipart/form-data" ';
}    
$str .='   action="index.php?ctl='.$mdlname.'&act=add" target="hiden_frm" name="form1" id="myform" method="post">
            <table width="100%" cellspacing="0" cellpadding="0" class="table_form">
                <tbody>
';
foreach($info['enname'] as $k=>$v){
    switch ($info['type'][$k]){ 
        case 1:
        case 2:    
    
    $str.='                    <tr>
                        <th >'.$info['cnname'][$k].':</th>
                        <td><input type="text" value="" name="'.$v.'" class="input-text"> </td>
                    </tr>
';
            break;
        case 5:
             $str.='                    <tr>
                        <th >'.$info['cnname'][$k].':</th>
                        <td><textarea name="'.$v.'" cols="50" rows="10"></textarea></td>
                    </tr>
';
            break;
        case 3:
$str.='
            <tr>
                <th >'.$info['cnname'][$k].':</th>
                <td>
                     <script type="text/plain" id="'.$v.'_edit" style="width:700px"></script>
                     <script>

                         var editor_a'.$v.' = new baidu.editor.ui.Editor({
                                //focus时自动清空初始化时的内容
                                autoClearinitialContent:true,
                                //关闭字数统计
                                wordCount:false,
                                //关闭elementPath
                                elementPathEnabled:false,
                                textarea:\''.$v.'\'
                                //更多其他参数，请参考editor_config.js中的配置项
                         });
                         editor_a'.$v.'.render(\''.$v.'_edit\');
                     </script>
                </td>
            </tr>    
';            
            break;
        case 4:
$str.='
                    <tr>
                        <th >'.$info['cnname'][$k].':</th>
                        <td><input type="file"  name="'.$v.'" > </td>
                    </tr>
';            
            break;
    }
}
$str.='                   
                </tbody>
            </table>
            <div class="btn">
                <input type="submit" class="button" value="确认保存">
            </div>
        </form>
    </div>

</body>
</html>
    

';
$dir = BASE_PATH.'themes/admin/'.$mdlname.'/';
if(!is_dir($dir)) mkdir ($dir,0700,true);
$filename = $dir.'add.html';
file_put_contents($filename, $str);
}

if(in_array(3,$mdl)){ 
$str='<?php require TEMPLATE_PATH.\'header.html\';?>
<body>
    <iframe name="hiden_frm"  style="display:none;"></iframe>   
    <div class="subnav">
        <div class="content-menu ib-a blue line-x"> 
            <a href="index.php?ctl='.$mdlname.'" ><em>'.$cnname.'列表</em></a>
            <span>|</span>
            <a class="on" href="###"><em>编辑'.$cnname.'</em></a>
        </div>
    </div>  
    <div class="pad_10">
        <form ';
if(!empty($picArr)){
 $str .=' enctype="multipart/form-data" ';
}    
$str .=' action="index.php?ctl='.$mdlname.'&act=edit&'.$index.'=<?php echo $'.$index.';?>" target="hiden_frm" name="form1" id="myform" method="post">
            <table width="100%" cellspacing="0" cellpadding="0" class="table_form">
                <tbody>
                ';
foreach($info['enname'] as $k=>$v){
    switch ($info['type'][$k]){ 
        case 1:
        case 2:    
    
    $str.='                    <tr>
                        <th >'.$info['cnname'][$k].':</th>
                        <td><input type="text" value="<?php echo $data[\''.$v.'\']?>" name="'.$v.'" class="input-text"> </td>
                    </tr>
';
            break;
        case 5:
             $str.='                    <tr>
                        <th >'.$info['cnname'][$k].':</th>
                        <td><textarea name="'.$v.'" cols="50" rows="10"><?php echo $data[\''.$v.'\']?></textarea></td>
                    </tr>
';
            break;
        case 3:
$str.='
            <tr>
                <th >'.$info['cnname'][$k].':</th>
                <td>
                     <script type="text/plain" id="'.$v.'_edit" style="width:700px"><?php echo $data[\''.$v.'\']?></script>
                     <script>

                         var editor_a'.$v.' = new baidu.editor.ui.Editor({
                                //关闭字数统计
                                wordCount:false,
                                //关闭elementPath
                                elementPathEnabled:false,
                                textarea:\''.$v.'\'
                                //更多其他参数，请参考editor_config.js中的配置项
                         });
                         editor_a'.$v.'.render(\''.$v.'_edit\');
                     </script>
                </td>
            </tr>    
';            
            break;
        case 4:
$str.='
                    <tr>
                        <th >'.$info['cnname'][$k].':</th>
                        <td><input type="file"  name="'.$v.'" ><?php if(!empty( $data[\''.$v.'\'])) { ?> <img width="80" height="80" src="<?php echo URL.$data[\''.$v.'\']?>" /> <?php }?></td>
                    </tr>
';            
            break;
    }
}
                            
$str.='
            </tbody>
            </table>
            <div class="btn">
                <input type="submit" class="button" value="确认修改">
            </div>
        </form>
    </div>

</body>
</html>
    
';
$dir = BASE_PATH.'themes/admin/'.$mdlname.'/';
if(!is_dir($dir)) mkdir ($dir,0700,true);
$filename = $dir.'edit.html';
file_put_contents($filename, $str);
}

if(in_array(5,$mdl)){
$str='<?php require TEMPLATE_PATH.\'header.html\';?>
<body>
    <iframe name="hiden_frm"  style="display:none;"></iframe>   
    <div class="subnav">
        <div class="content-menu ib-a blue line-x"> 
            <a href="index.php?ctl='.$mdlname.'" ><em>'.$cnname.'列表</em></a>
            <span>|</span>
            <a class="on" href="###"><em>查看'.$cnname.'</em></a>
        </div>
    </div>  
    <div class="pad_10">

            <table width="100%" cellspacing="0" cellpadding="0" class="table_form">
                <tbody>
                ';
foreach($info['enname'] as $k=>$v){
    switch ($info['type'][$k]){ 
        case 1:
        case 2:    
    
    $str.='                    <tr>
                        <th >'.$info['cnname'][$k].':</th>
                        <td><?php echo $data[\''.$v.'\']?></td>
                    </tr>
';
            break;
        case 5:
             $str.='                    <tr>
                        <th >'.$info['cnname'][$k].':</th>
                        <td><?php echo $data[\''.$v.'\']?></td>
                    </tr>
';
            break;
        case 3:
$str.='
            <tr>
                <th >'.$info['cnname'][$k].':</th>
                <td>
                  <?php echo $data[\''.$v.'\']?>
                    
                </td>
            </tr>    
';            
            break;
        case 4:
$str.='
                    <tr>
                        <th >'.$info['cnname'][$k].':</th>
                        <td><?php if(!empty( $data[\''.$v.'\'])) { ?> <img width="80" height="80" src="<?php echo URL.$data[\''.$v.'\']?>" /> <?php }?></td>
                    </tr>
';            
            break;
    }
}
                            
$str.='
            </tbody>
            </table>


    </div>

</body>
</html>
    
';
$dir = BASE_PATH.'themes/admin/'.$mdlname.'/';
if(!is_dir($dir)) mkdir ($dir,0700,true);
$filename = $dir.'view.html';
file_put_contents($filename, $str);
    
}

    die;
}