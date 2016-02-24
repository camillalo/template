<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
$__SETTING = import::getCfg('setting');
define('AUTH_KEY',$__SETTING['cookie']);
//图片保存的路径
define('IMG_SAVE_PATH',BASE_PATH.'/data/upload');
//存入数据库的路径
define('IMG_WEB_PATH','data/upload');
define('IS_REWRITE', $__SETTING['is_rewrite']);

define('QQ_APPID',$__SETTING['qqappid']);
define('QQ_KEY',$__SETTING['qqappkey']);


$__ABC = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

$__CATEGORY_TYPE = array(
    'contents'    => 1,
    'products'    => 2,
    'company'     => 3,
    'case'        => 4,
    'bidding'     => 5,
    'ask'         => 6  
);

$__CATEGORY_TYPE_MEAN = array(
    1       => '文章分类',
    2       => '商城分类',
    3       => '装修公司分类',
    4       => '案例分类',
    5       => '招标分类',
    6       => '问吧分类'
);


$__USER_TYPE = array(
    'owner'     => 1,
    'company'   => 2,
    'material'  => 3,
    'designer'  => 4,
);

$__USER_TYPE_MEAN = array(
    1 => '业主',
    2 => '装修公司',
    3 => '材料商',
    4 => '设计师'
);

$__SEX = array(
    1 => '先生',
    2 => '女士',
);

//装修类型
$__DECORATE_TYPE = array(
    0 => '不限',
    1 => '半包',
    2 => '全包',
    3 => '清包',
);

$__WHETHER = array(
    1 => '是',
    2 => '否',
);

$__HAVE = array(
    1 => '有',
    2 => '无'
);

$__SHOW = array(
    0 => '不显示',
    1 => '显示'
);
//装修招标的跟分类
$__BIDDING_ROOT = array(
    'type'   => 2,
    'style'  => 3,
    'budget' => 4    
);

//频道跟分类 
$__PINDAO_ROOT = array(
    'jj'    => 16, //家居学堂
    'gd'    => 148,//在建工地
    'zp'    => 149,//设计作品
    'gg'    => 14,
    'system'=> 1,
    'lc'    => 5,//装修流程
);

//案例分类的 默认跟分类
$__CASE_CATEGORY = array(
    'space'  => 6,
    'style'  => 7,
    'area'   => 8,
    'price'  => 9
);

$__COMMENTS_TYPE = array(
    'contents' => 1,
    'case'     => 2,
    'products' => 3,
    'diary'    => 4,
    'preferential'=>5,
);

$__COMMENTS_TYPE_MEAN = array(
    1 => '文章评论',
    2 => '案例评论',
    3 => '商品评论',
    4 => '日记评论',
    5 => '优惠评论',
);

//装修公司分类根目录
$__COMPANY_CATEGORY_ROOT = array(
    'project' => 10,
    'scale'   => 11,
    'output'  => 12,
    'industry'  => 13  
);

$__DIANPING_MEANS = array(
    1 => '1',
    2 => '2',
    3 => '3',
    4 => '4',
    5 => '5',
);

$__COMPANY_PIC_TYPE = array(
    'pics'      => 1, //企业图片
    'credit'    => 2,
);
$__COMPANY_PIC_TYPE_MEANS = array(
    1 => '公司相册',
    2 => '荣誉资质',
);

$__WORK = array(
    1 => '实习',
    2 => '1-3年',
    3 => '3-5年',
    4 => '5-8年',
    5 => '8年以上'
);


$__ACTIVITY_TYPE = array(
    'decorate' => 1,
    'materials'=> 2,
    'house'    => 3,
    'household' => 4
);

$__ACTIVITY_TYPE_MEAN = array(
    1 => '装修装潢',
    2 => '建材购买',
    3 => '预约看房',
    4 => '家居体验',
);

$__AD_TYPE = array(
    'word'   => 1,
    'pic'    => 2,
    'html'   => 3 
);

$__AD_TYPE_MEAN = array(
    1 => '文字广告',
    2 => '图片广告',
    3 => '代码广告'
);
$__RECOMMEND_TYPE = array(
    'case'      => 1,
    'content'   => 2,
    'company'   => 3,
    'activity'  => 4,
    'diary'     => 5,
    'preferential'=> 6,
    'products'   => 7,
    'ask'        => 8,
    'brand'      => 9,
    'designer'  =>10,
    'company2'  => 11
);

$__RECOMMEND_TYPE_MEAN = array(
    1 => '案例',
    2 => '文章',
    3 => '公司',
    11 => '材料商',
    4 => '活动',
    5 => '日记',
    6 => '优惠信息',
    7 => '产品',
    8 => '问吧',
    9 => '品牌',
    10 => '设计师',
);
$__RECOMMEND_MOLD = array(
    'new'  => 1,
    'hot'  => 2,
    'new1' => 3,
    'hot1' => 4
);
$__RECOMMEND_MOLD_MEAN = array(
    1 => '最新',
    2 => '最热门',
    3 => '推广优先最新',
    4 => '推广优先最热',
);




$__ASK_TYPE = array(
    'wait' => 0,
    'ok'   => 1 
);

$__ASK_TYPE_MEAN = array(
    0 => '待解决',
    1 => '已解决',
);

$__SITE_AUTHORITY = array(
    'no'   => 0,
    'yes1' => 50,
    'yes2' => 100
);

$__SITE_AUTHORITY_MEAN = array(
    0   => '不允许',
    50  => '允许需要审核',
    100 => '不需要审核',
);

$__INTEGRAL_GAIN_MEAN = array(
    1 => '发布案例',
    2 => '发布日记',
    3 => '回答问题',
    4 => '发布产品',
    5 => '发布优惠信息',
    0 => '系统赠送' 
);

$__INTEGRAL_GAIN = array(
    'case'  => 1,
    'diary' => 2,
    'answer'   => 3,
    'product' => 4,
    'preferential'=>5,
    'other'  => 0
);

$__INTEGRAL_USED = array(
    'ask'        =>  1,
    'exchange'   =>  2,
    'lottery'    =>  3,
    'other'      =>  0 //其他原因   
);

$__INTEGRAL_USED_MEAN = array(
    0 => '其他原因',
    1 => '悬赏问题',
    2 => '积分兑换',
    3 => '积分抽奖',
);

$__SITE_STATUS = array(
    'kg' => 1,
    'sd' => 2,
    'nw' => 3,
    'mg' => 4,
    'yq' => 5,
    'az' => 6,
    'ok' => 7
);

$__SITE_STATUS_MEANS = array(
    1 => '开工大吉',
    2 => '水电施工',
    3 => '泥瓦工阶段',
    4 => '木工阶段',
    5 => '油漆阶段',
    6 => '安装阶段',
    7 => '完工验收'
);