<?php

class recommend{
    
    private static  $instance = null;
    
    private $_cache  =  null;
    
    private $_token  =  'recommend';
    
    private $_datas  =  null;
    
    private $_pageid = null;
    

    private $_writeCache = false; 
    
    public static function getInstance()
    {   
        if (null == self::$instance){

            self::$instance = new recommend();
        }
        
        return self::$instance;
    }
    
    private function __construct() {

        $this->_cache = fileCache::getInstance();

    }
    
    public function __destruct() {
        if($this->_writeCache == true){
            $this->_cache->put($this->_token.'-'.$this->_pageid,  $this->_datas);
        }
    }


    public function load($key,$num = 0){
        
        if(!isset($this->_datas[$key]['data'])) return $this->_load($key, $num);
        $n = count($this->_datas[$key]['data']) ;
        if($n>=$num) return array_slice($this->_datas[$key]['data'],0,$num);
        $n1 = $num - $n;
        return $this->_load($key, $n1);
    }
    
    private function _load($key,$num){
       global $__RECOMMEND_TYPE,$__RECOMMEND_MOLD,$__USER_TYPE,$__CATEGORY_TYPE;
       $this->_datas[$key]['data'] = isset($this->_datas[$key]['data']) ? $this->_datas[$key]['data'] : array();
       if(isset($this->_datas[$key]['lock']) && (int)$this->_datas[$key]['lock'] == 1) 
       return $this->_datas[$key]['data'];
       $this->_datas[$key]['lock'] = 1;
       $this->_writeCache = true; //析构的时候更新缓存
       
       $num = (int)$num;
       $signs = $this->loadSign();
       $sign = array();
       foreach($signs as $val){
           if($val['key'] == $key) {
               $sign = $val;
               break;
           }
       }
       if(empty($sign['type'])) return $this->_datas[$key]['data'];
       $orderby = array();
       $where ['is_show'] = 1;
       switch ($sign['type']){
            case $__RECOMMEND_TYPE['content'] :
                import::getMdl('content');                
                switch ($sign['mold']){
                    case $__RECOMMEND_MOLD['hot']:
                        $orderby = array('pv_num'=>'desc');
                        break;  
                    case $__RECOMMEND_MOLD['hot1']:
                        $orderby = array('pv_num'=>'desc','content_id'=>'desc');
                        break; 
                    default:
                        $orderby = array('content_id'=>'desc');
                        break;
                }
                if(!empty($sign['cate_id'])){
                    import::getInt('category');
                    $lastIds = category::getInstance()->getAllLastChildIds($__CATEGORY_TYPE['contents'],(int)$sign['cate_id']);
                    if(!empty($lastIds)){
                        $where['last_category_id'] = $lastIds;

                    }else{
                        $where['category_id'] = (int)$sign['cate_id'];
                    }
                }
                $datas = contentMdl::getInstance()->getContentList(array(),$where,$orderby,0,$num);
                break; 

            case $__RECOMMEND_TYPE['company'] :
                $where['type'] =$__USER_TYPE['company'];
                 import::getMdl('company');
               
                  switch ($sign['mold']){
                    case $__RECOMMEND_MOLD['hot']:
                        $orderby = array('pv'=>'desc');
                        break;      
                    case $__RECOMMEND_MOLD['hot1']:
                        $orderby = array('`orderby`'=>'desc','pv'=>'desc');
                        break;  
                    default:
                        $orderby = array('`orderby`'=>'desc','uid'=>'desc');
                        break;
                }
                $datas = companyMdl::getInstance()->getCompanyList(array(),$where,$orderby,0,$num);
                break;
            case $__RECOMMEND_TYPE['company2'] :
                $where['type'] =$__USER_TYPE['material'];
                 import::getMdl('company');
               
                  switch ($sign['mold']){
                    case $__RECOMMEND_MOLD['hot']:
                        $orderby = array('pv'=>'desc');
                        break;      
                    case $__RECOMMEND_MOLD['hot1']:
                        $orderby = array('`orderby`'=>'desc','pv'=>'desc');
                        break;  
                    default:
                        $orderby = array('`orderby`'=>'desc','uid'=>'desc');
                        break;
                }
                $datas = companyMdl::getInstance()->getCompanyList(array(),$where,$orderby,0,$num);
                break;
            
            
            case $__RECOMMEND_TYPE['case'] :
                  import::getMdl('case');
                  switch ($sign['mold']){
                    case $__RECOMMEND_MOLD['hot']:
                    case $__RECOMMEND_MOLD['hot1']:   
                        $orderby = array('pv_num'=>'desc');
                        break;      
                    default:
                        $orderby = array('case_id' => 'desc');
                        break;
                }
                $datas = caseMdl::getInstance()->getCaseList(array(),$where,$orderby,0,$num);
                break;

            case $__RECOMMEND_TYPE['activity'] :
                import::getMdl('activity');
                $datas = activityMdl::getInstance()->getActivityList(array(),$where,$orderby,0,$num);
                break;
            case $__RECOMMEND_TYPE['diary']:
                import::getMdl('diary');
                 if(!empty($sign['cate_id'])) $where['cate_id'] = (int)$sign['cate_id'];
                $datas = diaryMdl::getInstance()->getDiaryList(array(),$where,$orderby,0,$num);
                break;
            case $__RECOMMEND_TYPE['preferential']:
                import::getMdl('preferential');
                $datas = preferentialMdl::getInstance()->getPreferentialList(array(),$where,$orderby,0,$num);
                break;
            case $__RECOMMEND_TYPE['products']:
                import::getMdl('products');
                if(!empty($sign['cate_id'])) $where['category_id'] = (int)$sign['cate_id'];
                $datas = productsMdl::getInstance()->getProductsList(array(),$where,$orderby,0,$num); 
                break;
            case $__RECOMMEND_TYPE['ask']:
                import::getMdl('ask');
                switch ($sign['mold']){
                    case $__RECOMMEND_MOLD['hot']:
                        $orderby = array('pv'=>'desc');
                        break;      
                    case $__RECOMMEND_MOLD['hot1']:
                        $orderby = array('`orderby`'=>'desc','pv'=>'desc');
                        break;  
                    default:
                        $orderby = array('`orderby`'=>'desc','uid'=>'desc');
                        break;
                }
                if(!empty($sign['cate_id'])) $where['category_id'] = (int)$sign['cate_id'];
                $datas = askMdl::getInstance()->getAskList(array(),$where,$orderby,0,$num); 
                break;

            case $__RECOMMEND_TYPE['brand']:
                import::getMdl('brand');
                $datas = brandMdl::getInstance()->getBrandList(array(),$where,$orderby,0,$num); 
                break;
            case $__RECOMMEND_TYPE['designer']:
                import::getMdl('designer');
                switch ($sign['mold']){
                    case $__RECOMMEND_MOLD['new']:
                        $orderby = array('id'=>'desc');
                        break;      
                    default:
                        $orderby = array('`orderby`'=>'desc','id'=>'desc');
                        break;
                }
                $datas = designerMdl::getInstance()->getDesignerList(array(),$where,$orderby,0,$num); 
                break;
                default:
                    $datas = array();
                    break;
       }
      
       foreach($datas as $val){
           $val = $this->_dataformat($sign['type'], $val);
           $val['link'] = $this->makeRecommendUrl($val['type'],$val['mdl_id']);
           $this->_datas[$key]['data'][] = $val;
       }
       return $this->_datas[$key]['data'];
    }
    
    public function init($_pageId){
        $this->_pageid = $_pageId;
        $this->_datas = $this->_cache->load($this->_token.'-'.$this->_pageid);
        
        if(empty($this->_datas)){
            $this->_datas = $this->put();
        }
        return $this->_datas;
    }
    
    
    private function  loadSign(){
        $token = 'recommendSign_'.$this->_pageid;
        $data = $this->_cache->load($token);
        if(empty($data)){
            import::getMdl('recommendSign');
            $data = recommendSignMdl::getInstance()->getRecommendSignByGroupId($this->_pageid);
            $this->_cache->put($token,$data);
        }
        return $data;
    }


    public function put(){
        import::getMdl('recommend');
        $signs = $this->loadSign($this->_pageid);
        $recommends = recommendMdl::getInstance()->getRecommendByPageId($this->_pageid);
        $return = array();
        foreach($signs as $val){
            $return[$val['key']]['lock'] = 0;
            $return[$val['key']]['data'] = array();
            if(empty($recommends))  break;
            foreach($recommends as $k=>$v){
                if($v['sign_id'] == $val['id']){
                    $v['link'] = empty($v['link']) ? $this->makeRecommendUrl($v['type'], $v['mdl_id']) : $v['link'];
                    $return[$val['key']]['data'][] = $v;
                    unset($recommends[$k]);
                }
            }
        }
        $this->_cache->put($this->_token.'-'.$this->_pageid,$return);
        return $return;
    }
    
    
    private function _dataformat($type,$data){
        global  $__RECOMMEND_TYPE;
        $return = array( );
        switch($type){ //根据不同的模型 取不同的默认数据 因为代码不是过分的长 哥就不封装成接口了 见谅见谅
            case $__RECOMMEND_TYPE['content'] :
             $return = array(
                    'mdl_id'=>$data['content_id'],
                    'title' => $data['title'],
                    'face_pic'=> $data['face_pic'],
                    'link'   => '',
                    'description' => $data['description'],
                );
            break;    
              case $__RECOMMEND_TYPE['company'] :
              case $__RECOMMEND_TYPE['company2'] :    
                $return = array(
                    'mdl_id'=> $data['uid'],
                    'title' => $data['company_name'],
                    'face_pic'=> $data['logo'],
                    'link'   => '',
                    'description' => $data['description'],
                );
                break;
            case $__RECOMMEND_TYPE['case'] :
                $return = array(
                    'mdl_id'=> $data['case_id'],
                    'title' => $data['title'],
                    'face_pic'=>$data['face_pic'],
                    'link'   => '',
                    'description' => $data['description']
                );
                break;

            case $__RECOMMEND_TYPE['activity'] :

                $return = array(
                    'mdl_id'=> $data['id'],
                    'title' => $data['title'],
                    'face_pic'=>$data['face_pic'],
                    'link'   => '',
                    'description' => $data['coupon']
                );
                break;
            case $__RECOMMEND_TYPE['diary']:
                $return = array(
                    'mdl_id'=> $data['id'],
                    'title' => $data['title'],
                    'face_pic'=>'',
                    'link'   => '',
                    'description' => mb_substr(getValue($data['contents'],true),0,200)
                );

                break;
            case $__RECOMMEND_TYPE['preferential']:
                $return = array(
                    'mdl_id'=> $data['id'],
                    'title' => $data['title'],
                    'face_pic'=>$data['face_pic'],
                    'link'   => '',
                    'description' => mb_substr(getValue($data['content'],true),0,200)
                );
                break;
            case $__RECOMMEND_TYPE['products']:  
                 $return = array(
                     'mdl_id'=> $data['id'],
                    'title' => $data['product_name'],
                    'face_pic'=>$data['face_pic'],
                    'link'   => '',
                    'description' => json_encode(
                                         array(
                                             'market_price'=>$data['market_price'],
                                             'mall_price'=>$data['mall_price'],
                                             'model'=>$data['model'],
                                             )
                                         )
                );

                break;
            case $__RECOMMEND_TYPE['ask']:
                 $return = array(
                     'mdl_id'=> $data['id'],
                    'title' => $data['title'],
                    'face_pic'=>'',
                    'link'   => '',
                    'description' => getValue($data['description'],true)
                );
                break;

            case $__RECOMMEND_TYPE['brand']:

                $return = array(
                    'mdl_id'=> $data['brand_id'],
                    'title' => $data['brand_name'],
                    'face_pic'=>$data['brand_pic'],
                    'link'   => '',
                    'description' => ''
                );

                break;
            case $__RECOMMEND_TYPE['designer']:
   
                $return = array(
                    'mdl_id'=> $data['id'],
                    'title' => $data['name'],
                    'face_pic'=>$data['face_pic'],
                    'link'   => '',
                    'description' => $data['style']
                );
                break;  
        } 
        $return['type'] = $type;
        return $return;
    }
    public function getDataByTypeId($type,$id){
        global  $__RECOMMEND_TYPE;
        switch($type){ //根据不同的模型 取不同的默认数据 因为代码不是过分的长 哥就不封装成接口了 见谅见谅
            case $__RECOMMEND_TYPE['content'] :
                import::getMdl('content');
                $contentInfo = contentMdl::getInstance()->getContent($id);
                $data = $this->_dataformat($type, $contentInfo);
                break; 

            case $__RECOMMEND_TYPE['company'] :
            case $__RECOMMEND_TYPE['company2'] :    
                import::getMdl('company');
                $company = companyMdl::getInstance()->getCompany($id);
                $data = $this->_dataformat($type, $company);
                break;
            case $__RECOMMEND_TYPE['case'] :
                import::getMdl('case');
                $caseInfo = caseMdl::getInstance()->getCase($id);
                $data =  $this->_dataformat($type, $caseInfo);
                break;

            case $__RECOMMEND_TYPE['activity'] :
                import::getMdl('activity');
                $activity = activityMdl::getInstance()->getActivity($id);
                $data = $this->_dataformat($type, $activity);
                break;
            case $__RECOMMEND_TYPE['diary']:
                import::getMdl('diary');
                $diary = diaryMdl::getInstance()->getDiary($id);    
                $data =$this->_dataformat($type, $diary);

                break;
            case $__RECOMMEND_TYPE['preferential']:
                import::getMdl('preferential');
                $preferential = preferentialMdl::getInstance()->getPreferential($id);    
                $data = $this->_dataformat($type, $preferential);
                break;
            case $__RECOMMEND_TYPE['products']:
                import::getMdl('products');
                $product = productsMdl::getInstance()->getProducts($id);    
                $data = $this->_dataformat($type, $product);
                break;
            case $__RECOMMEND_TYPE['ask']:
                import::getMdl('ask');
                $ask = askMdl::getInstance()->getAsk($id);    
                $data = $this->_dataformat($type, $ask);
                break;

            case $__RECOMMEND_TYPE['brand']:
                import::getMdl('brand');
                $brand = brandMdl::getInstance()->getBrand($id);    
                $data = $this->_dataformat($type, $brand);

                break;
            case $__RECOMMEND_TYPE['designer']:
                import::getMdl('designer');
                $designer = designerMdl::getInstance()->getDesigner($id);    
                $data =  $this->_dataformat($type, $designer);
                break;
            default:
                $data = array(
                    'mdl_id'=> $id,
                    'type'  => $type,
                    'title' => '',
                    'face_pic'=>'',
                    'link'   => '',
                    'description' => ''
                );
                break;
         } 
         return  $data;
    }
    
    private  function makeRecommendUrl($type,$id){
            global $__RECOMMEND_TYPE;
            import::getLib('mkurl');
            switch($type){ //根据不同的模型 取不同的默认数据
                case $__RECOMMEND_TYPE['content'] :
                    $url = mkUrl::linkTo('content','detail',array('id'=>$id));
                    break;
                case $__RECOMMEND_TYPE['company2'] :
                case $__RECOMMEND_TYPE['company'] :
                    $url =  mkUrl::linkTo('company','main',array('id'=>$id));
                    break;
                case $__RECOMMEND_TYPE['case'] :
                    $url =  mkUrl::linkTo('case','detail',array('id'=>$id));
                    break;
                case $__RECOMMEND_TYPE['activity'] :
                    $url =  mkUrl::linkTo('activity','detail',array('id'=>$id));
                    break;
                case $__RECOMMEND_TYPE['diary']:
                     $url =  mkUrl::linkTo('content','diaryshow',array('id'=>$id));
                    break;
                case $__RECOMMEND_TYPE['preferential']:
                    $url =  mkUrl::linkTo('preferential','detail',array('id'=>$id));
                    break;
                case $__RECOMMEND_TYPE['products']:
                    $url =  mkUrl::linkTo('mall','detail',array('id'=>$id));
                    break;
                case $__RECOMMEND_TYPE['ask']:
                    $url =  mkUrl::linkTo('ask','detail',array('id'=>$id));
                    break;
                case $__RECOMMEND_TYPE['brand']:
                    $url =  mkUrl::linkTo('mall','list',array('brand'=>$id));
                    break;
                case $__RECOMMEND_TYPE['designer']:
                    $url =  mkUrl::linkTo('designer','main',array('id'=>$id));
                    break;
                default:
                    $url = '';
                    break;
            } 
            return $url;
    }

    
}    