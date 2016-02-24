<?php
return array(
        array(
          'name'=>'我的面板',
          'link'=> 'index.php?ctl=index&act=default', 
          'item'=>array(
                  array(
                      'name'=> '个人中心',
                      'link'=> 'me',
                      
                      'item'=>array(
                          array(
                               'name' => '修改资料',
                               'link' => 'index.php?ctl=admin&act=edit2',
                          )
                      )
                 ),
               
                 
          )  
        ),    
        array(
          'name'=>'系统',
          'link'=> 'index.php?ctl=site',
          'order'=>2,
          'item'=>array(
                  array(
                      'name'=> '系统设置',
                      'link'=>'menu',                   
                      'item'=>array(
                          array(
                              'name' => '站点配置',
                              'link' => 'index.php?ctl=site',
                          ),
                           array(
                              'name' => 'seo综合配置',
                              'link' => 'index.php?ctl=seo',
                          ),
                          array(
                              'name' => 'uc整合',
                              'link' => 'index.php?ctl=site&act=uc',
                          ),
                          array(
                              'name' => '会员权限',
                              'link' => 'index.php?ctl=site&act=authority',
                          ),
                           array(
                              'name' => '邮件设置',
                              'link' => 'index.php?ctl=site&act=mail',
                          ),
                          array(
                              'name' => '短信设置',
                              'link' => 'index.php?ctl=site&act=sms',
                          ),
                          array(
                              'name' => '水印设置',
                              'link' => 'index.php?ctl=site&act=watermark',
                          )
                      )
                 ),
              array(
                      'name'=> '区域配置',
                      'link'=>'area',                   
                      'item'=>array(
                         
                
                          array(
                              'name' => '区域配置',
                              'link' => 'index.php?ctl=area',
                          ),
                         
                      )
                 ),
                  array(
                      'name'=> '过滤设置',
                      'link'=>'gl',                   
                      'item'=>array(
                         
                          array(
                              'name' => '敏感词管理',
                              'link' => 'index.php?ctl=sensitiveWord',
                          )
                          ,
                          array(
                              'name' => '防注水配置',
                              'link' => 'index.php?ctl=injection',
                          )
                         
                      )
                 ),
                 array(
                      'name'=> '管理员设置',
                      'link'=>'admin',
                      'item'=>array(
                          array(
                              'name' => '管理员角色',
                              'link' => 'index.php?ctl=group',
                          ),
                          array(
                              'name' => '管理员管理',
                              'link' => 'index.php?ctl=admin',
                          ),
                          array(
                              'name' => '权限分类',
                              'link' => 'index.php?ctl=privilegeGroup',
                          ),
                          array(
                              'name' => '权限管理',
                              'link' => 'index.php?ctl=privilege',
                          ),
                      )
                 ),
          )  
        ),
      array(
          'name'=>'推送',
          'link'=> 'index.php?ctl=recommendSign',
          'item'=>array(
                array(
                      'name'=> '推荐管理',
                      'link'=>'recommend',                   
                      'item'=>array(
                          array(
                              'name' => '推荐位分组',
                              'link' => 'index.php?ctl=recommendGroup',
                          ),
                          array(
                              'name' => '推荐位管理',
                              'link' => 'index.php?ctl=recommendSign',
                          ) ,
                          array(
                              'name' => '推荐内容管理',
                              'link' => 'index.php?ctl=recommend',
                          )
                      )
                 ),
           )
        ), 
        array(
          'name'=>'内容',
          'link'=> 'index.php?ctl=content',
          'item'=>array(
               
                 array(
                      'name'=> '文章系统',
                      'link'=>'contents',                   
                      'item'=>array(
                          array(
                              'name' => '分类管理',
                              'link' => 'index.php?ctl=category&category_type=1',
                          ),
                          
                          array(
                              'name' => '文章TAG',
                              'link' => 'index.php?ctl=contentTag',
                          ),
                          array(
                              'name' => '文章管理',
                              'link' => 'index.php?ctl=content',
                          ),
                          array(
                              'name' => '装修日记',
                              'link' => 'index.php?ctl=diary',
                          )
                      )
                 ),
                array(
                      'name'=> '案例系统',
                      'link'=>'case',                   
                      'item'=>array(
                          array(
                              'name' => '分类管理',
                              'link' => 'index.php?ctl=category&category_type=4',
                          ),
                          
                          array(
                              'name' => '案例管理',
                              'link' => 'index.php?ctl=case',
                          )
                      )
                 ),
                 
                
                
                 
            )
        ),
        array(
          'name'=>'营销',
          'link'=> 'index.php?ctl=activity',
          'item'=>array(
                 array(
                      'name'=> '团购系统',
                      'link'=>'activity',                   
                      'item'=>array(
                          array(
                              'name' => '发布团购',
                              'link' => 'index.php?ctl=activity&act=add',
                          ),
                          array(
                              'name' => '团购管理',
                              'link' => 'index.php?ctl=activity',
                          )
                      )
                 ),
              array(
                      'name'=> '优惠信息',
                      'link'=>'activity',                   
                      'item'=>array(
                          array(
                              'name' => '发布优惠信息',
                              'link' => 'index.php?ctl=preferential&act=add',
                          ),
                          array(
                              'name' => '优惠信息管理',
                              'link' => 'index.php?ctl=preferential',
                          )
                      )
                 ),
                 array(
                      'name'=> '友情链接',
                      'link'=>'links',                   
                      'item'=>array(
                          array(
                              'name' => '新增友情',
                              'link' => 'index.php?ctl=links&act=add',
                          ),
                          array(
                              'name' => '友情链接',
                              'link' => 'index.php?ctl=links',
                          ) 
                      )
                ),
                array(
                      'name'=> '广告管理',
                      'link'=>'ads',                   
                      'item'=>array(
                          array(
                              'name' => '广告位配置',
                              'link' => 'index.php?ctl=adSite',
                          ),
                          array(
                              'name' => '广告管理',
                              'link' => 'index.php?ctl=ads',
                          ) 
                      )
                 ),
                 
           )
        ),
        array(
          'name'=>'会员',
          'link'=> 'index.php?ctl=users',
          'item'=>array(
                array(
                      'name'=> '会员管理',
                      'link'=>'user',                   
                      'item'=>array(
                          array(
                              'name' => '会员等级',
                              'link' => 'index.php?ctl=ranks',
                          ),
                          array(
                              'name' => '会员列表',
                              'link' => 'index.php?ctl=users',
                          ),
                          array(
                              'name' => '会员开通日志',
                              'link' => 'index.php?ctl=rankLogs',
                          )
                      )
                 ),
                 
                 array(
                      'name'=> '公司管理',
                      'link'=>'company',                   
                      'item'=>array(
                          array(
                              'name' => '分类管理',
                              'link' => 'index.php?ctl=category&category_type=3',
                          ),
                          array(
                              'name' => '公司列表',
                              'link' => 'index.php?ctl=company',
                          )
                          ,
                          array(
                              'name' => '公司保障',
                              'link' => 'index.php?ctl=security',
                          ),
                          array(
                              'name' => '点评管理',
                              'link' => 'index.php?ctl=dianping',
                          ),
                          array(
                              'name' => '企业图片',
                              'link' => 'index.php?ctl=companyPics',
                          ),
                          array(
                              'name' => '在建工地',
                              'link' => 'index.php?ctl=buildingSite',
                          ),
                          array(
                              'name' => '申请量房管理',
                              'link' => 'index.php?ctl=quantityRoom',
                          ),
                          array(
                              'name' => '申请设计管理',
                              'link' => 'index.php?ctl=bookingDesign',
                          )
                      )
                 ),
                 
                array(
                      'name'=> '设计师',
                      'link'=>'designer',                   
                      'item'=>array(
                          array(
                              'name' => '设计师列表',
                              'link' => 'index.php?ctl=designer',
                          )
                      )
                 ),
                 array(
                      'name'=> '工队',
                      'link'=>'team',                   
                      'item'=>array(
                          array(
                              'name' => '工队列表',
                              'link' => 'index.php?ctl=team',
                          )
                      )
                 ),
               
                 
                 
           )
        ), 
       array(
          'name'=>'招标',
          'link'=> 'index.php?ctl=bidding',
          'item'=>array(
                array(
                      'name'=> '招标管理',
                      'link'=>'bidding',                   
                      'item'=>array(
                          array(
                              'name' => '招标设置',
                              'link' => 'index.php?ctl=bidding&act=setting',
                          ),
                          array(
                              'name' => '分类管理',
                              'link' => 'index.php?ctl=category&category_type=5',
                          ),
                          array(
                              'name' => '招标管理',
                              'link' => 'index.php?ctl=bidding',
                          ),
                          array(
                              'name' => '快捷招标',
                              'link' => 'index.php?ctl=biddingQuick',
                          )
                      )
                 ),
           )
        ), 
        array(
          'name'=>'问吧',
          'link'=> 'index.php?ctl=ask',
          'item'=>array(
                array(
                      'name'=> '问吧管理',
                      'link'=>'ask',                   
                      'item'=>array(
                          array(
                              'name' => '分类管理',
                              'link' => 'index.php?ctl=category&category_type=6',
                          ),
                          array(
                              'name' => '问吧管理',
                              'link' => 'index.php?ctl=ask',
                          ) 
                         
                      )
                 ),
           )
        ), 
      array(
          'name'=>'商城',
          'link'=> 'index.php?ctl=products',
          'item'=>array(
                array(
                      'name'=> '系统配置',
                      'link'=>'case',                   
                      'item'=>array(
                         
                         array(
                              'name' => '品牌管理',
                              'link' => 'index.php?ctl=brand',
                          ),
                          array(
                              'name' => '分类管理',
                              'link' => 'index.php?ctl=category&category_type=2',
                          ),
                      )
                 ),
                array(
                      'name'=> '产品系统',
                      'link'=>'case',                   
                      'item'=>array(
                          
                          array(
                              'name' => '发布产品',
                              'link' => 'index.php?ctl=products&act=add',
                          ),
                          array(
                              'name' => '产品管理',
                              'link' => 'index.php?ctl=products',
                          ), 
                          array(
                              'name' => '建材导购',
                              'link' => 'index.php?ctl=demand',
                          )
                      )
                 ),
           )
        ),
        array(
          'name'=>'积分',
          'link'=> 'index.php?ctl=integralShop',
          'item'=>array(
                array(
                      'name'=> '积分设置',
                      'link'=>'setting',                   
                      'item'=>array(
                          array(
                              'name' => '积分设置',
                              'link' => 'index.php?ctl=integralSetting',
                          )
                      )
                 )
                 ,
                 
                array(
                      'name'=> '积分管理',
                      'link'=>'manage',                   
                      'item'=>array(
                          array(
                              'name' => '积分管理',
                              'link' => 'index.php?ctl=integral',
                          ),
                          array(
                              'name' => '使用日志',
                              'link' => 'index.php?ctl=integralUsed',
                          )
                      )
                 ),
                 
                 array(
                      'name'=> '积分商城',
                      'link'=>'integralShop',                   
                      'item'=>array(
                          array(
                              'name' => '积分产品',
                              'link' => 'index.php?ctl=integralShop',
                          ),
                          array(
                              'name' => '兑换/抽奖管理',
                              'link' => 'index.php?ctl=integralExchange',
                          )
                      )
                 )
              
           )
        ),
         array(
          'name'=>'留言评论',
          'link'=> 'index.php?ctl=message',
          'item'=>array(
                array(
                      'name'=> '用户留言',
                      'link'=>'designer',                   
                      'item'=>array(
                          array(
                              'name' => '留言管理',
                              'link' => 'index.php?ctl=message',
                          )
                      )
                 )
                 ,
                 
                array(
                      'name'=> '评论审核',
                      'link'=>'designer',                   
                      'item'=>array(
                          array(
                              'name' => '评论管理',
                              'link' => 'index.php?ctl=comments',
                          )
                      )
                 )
           )
        ),
        array(
          'name'=>'工具',
          'link'=> 'index.php?ctl=model',
          'item'=>array(
               
                 array(
                      'name'=> '开发者工具',
                      'link'=> 'tools',
                      
                      'item'=>array(
                          array(
                               'name' => '模块生成器',
                               'link' => 'index.php?ctl=model',
                          ),
                          
                      )
                 ),
                 array(
                      'name'=> '日志相关',
                      'link'=> 'rizhi',
                      
                      'item'=>array(
                          array(
                               'name' => '系统日志',
                               'link' => 'index.php?ctl=systemLogs',
                          ),
                          
                      )
                 ),
                
           )
        ),
);
