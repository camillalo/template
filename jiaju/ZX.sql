-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 05 月 30 日 18:14
-- 服务器版本: 5.5.40
-- PHP 版本: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `ceshi5`
--

-- --------------------------------------------------------

--
-- 表的结构 `zx_activity`
--

CREATE TABLE IF NOT EXISTS `zx_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `area_id` int(11) DEFAULT NULL,
  `reg_time` date DEFAULT NULL,
  `bg_time` date DEFAULT NULL,
  `end_time` date DEFAULT NULL,
  `coupon` varchar(1024) DEFAULT NULL,
  `face_pic` varchar(256) DEFAULT NULL,
  `sign_num` int(11) DEFAULT '0' COMMENT '报名人数',
  `tel` varchar(20) DEFAULT NULL,
  `addr` varchar(256) DEFAULT NULL,
  `details` text,
  `lng` int(11) DEFAULT NULL,
  `lat` int(11) DEFAULT NULL,
  `sj` varchar(32) DEFAULT NULL COMMENT '活动具体时间',
  PRIMARY KEY (`id`),
  KEY `city_id` (`area_id`),
  KEY `city_id_2` (`area_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_activity`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_activity_join`
--

CREATE TABLE IF NOT EXISTS `zx_activity_join` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT '0',
  `name` varchar(64) DEFAULT NULL,
  `addr` varchar(64) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `qq` varchar(20) DEFAULT NULL,
  `num` tinyint(4) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_id` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_activity_join`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_admin`
--

CREATE TABLE IF NOT EXISTS `zx_admin` (
  `admin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` char(32) NOT NULL,
  `realname` varchar(64) NOT NULL,
  `email` varchar(128) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `tel` varchar(15) DEFAULT NULL,
  `group_id` int(11) NOT NULL,
  `is_lock` tinyint(1) DEFAULT '0',
  `last_t` datetime DEFAULT NULL,
  `last_ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `zx_admin`
--

INSERT INTO `zx_admin` (`admin_id`, `username`, `password`, `realname`, `email`, `mobile`, `tel`, `group_id`, `is_lock`, `last_t`, `last_ip`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', '管理员', 'admin@admin.com', NULL, NULL, 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `zx_ads`
--

CREATE TABLE IF NOT EXISTS `zx_ads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `pic` varchar(256) DEFAULT NULL,
  `code` text,
  `link` varchar(256) DEFAULT NULL,
  `bg_time` int(11) DEFAULT NULL,
  `end_time` int(11) DEFAULT NULL,
  `orderby` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orderby` (`orderby`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_ads`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_ad_site`
--

CREATE TABLE IF NOT EXISTS `zx_ad_site` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- 转存表中的数据 `zx_ad_site`
--

INSERT INTO `zx_ad_site` (`id`, `type`, `name`) VALUES
(3, 2, '首页横幅广告'),
(8, 2, '商城轮播图片'),
(14, 2, '积分商城BANNER广告'),
(15, 2, '商城首页分类下广告'),
(16, 1, '搜索框下面热门文字搜索广告'),
(17, 2, '问答首页广告'),
(18, 2, '学装修头部广告'),
(19, 2, '学装修一周精彩推荐旁边小广告');

-- --------------------------------------------------------

--
-- 表的结构 `zx_ask`
--

CREATE TABLE IF NOT EXISTS `zx_ask` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `description` text,
  `create_time` int(11) DEFAULT NULL,
  `integral` int(11) DEFAULT NULL,
  `last_time` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '0待解决 1代表已解决',
  `num` int(11) DEFAULT '0',
  `ip` varchar(20) DEFAULT NULL,
  `answer_id` int(11) DEFAULT '0' COMMENT '满意答案ID',
  `orderby` int(11) DEFAULT '0' COMMENT '置顶',
  `pv` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `cate_id` (`cate_id`),
  KEY `status` (`status`),
  KEY `cate_id_2` (`cate_id`,`status`),
  KEY `orderby` (`orderby`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_ask`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_ask_added`
--

CREATE TABLE IF NOT EXISTS `zx_ask_added` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ask_id` int(11) DEFAULT NULL,
  `added` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ask_id` (`ask_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_ask_added`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_ask_answer`
--

CREATE TABLE IF NOT EXISTS `zx_ask_answer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `ask_id` int(11) DEFAULT NULL,
  `content` varchar(2048) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `ask_id` (`ask_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_ask_answer`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_ask_keyword_maps`
--

CREATE TABLE IF NOT EXISTS `zx_ask_keyword_maps` (
  `ask_id` int(11) DEFAULT NULL,
  `keyword_id` int(11) DEFAULT NULL,
  UNIQUE KEY `ask_keyword` (`ask_id`,`keyword_id`),
  UNIQUE KEY `keyword_ask` (`keyword_id`,`ask_id`),
  KEY `ask_id` (`ask_id`),
  KEY `keyword_id` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_ask_keyword_maps`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_bidding`
--

CREATE TABLE IF NOT EXISTS `zx_bidding` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `area_id` int(11) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `sex` tinyint(1) DEFAULT NULL,
  `building_name` varchar(128) DEFAULT NULL,
  `addr` varchar(256) DEFAULT NULL,
  `way` tinyint(4) DEFAULT NULL COMMENT '装修方式',
  `type_root` int(11) DEFAULT '0' COMMENT '跟类型',
  `type_id` int(11) DEFAULT NULL COMMENT '类型',
  `style_id` int(11) DEFAULT NULL COMMENT '风格',
  `budget_id` int(11) DEFAULT NULL COMMENT '预算',
  `area` varchar(10) DEFAULT '0' COMMENT '面积',
  `start_time` varchar(32) DEFAULT NULL,
  `is_key` tinyint(4) DEFAULT NULL COMMENT '是否拿到钥匙',
  `is_supervision` tinyint(4) DEFAULT NULL COMMENT '监理需求',
  `is_material` tinyint(4) DEFAULT NULL COMMENT '是否需要材料需求',
  `demand` varchar(512) DEFAULT NULL,
  `create_ip` varchar(20) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `pv` int(11) DEFAULT '0',
  `bid_num` int(11) DEFAULT '0',
  `bid_id` int(10) DEFAULT '0' COMMENT '1代表结束',
  `feedback` varchar(1024) DEFAULT NULL,
  `is_show` tinyint(4) DEFAULT '0' COMMENT '是否显示在前台',
  `gold` int(11) DEFAULT '0' COMMENT '看此条招标需要的金币数',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_bidding`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_bidding_bid`
--

CREATE TABLE IF NOT EXISTS `zx_bidding_bid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `main_material` int(11) DEFAULT NULL,
  `vice_material` int(11) DEFAULT NULL,
  `artificial` int(11) DEFAULT NULL,
  `management` int(11) DEFAULT NULL,
  `design` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `message` varchar(2048) DEFAULT NULL,
  `detail_pics` text,
  `is_shortlisted` tinyint(1) DEFAULT '0' COMMENT '1',
  `is_win` tinyint(1) DEFAULT '0' COMMENT '1 代表中标 做冗余方便SQL查询提升效率',
  `t` int(11) DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT '0' COMMENT '1代表显示',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_2` (`uid`,`bid`),
  KEY `uid` (`uid`),
  KEY `bid` (`bid`),
  KEY `bid_2` (`bid`,`is_show`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_bidding_bid`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_bidding_look`
--

CREATE TABLE IF NOT EXISTS `zx_bidding_look` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `bidding_id` int(11) DEFAULT NULL,
  `type` tinyint(1) DEFAULT '0' COMMENT '1代表有权限查看用户详细信息 0代表没有权限',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`,`bidding_id`),
  KEY `uid_2` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_bidding_look`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_bidding_quick`
--

CREATE TABLE IF NOT EXISTS `zx_bidding_quick` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `create_ip` varchar(20) DEFAULT NULL,
  `is_check` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_bidding_quick`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_booking_design`
--

CREATE TABLE IF NOT EXISTS `zx_booking_design` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `designer_id` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_booking_design`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_brand`
--

CREATE TABLE IF NOT EXISTS `zx_brand` (
  `brand_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(64) NOT NULL,
  `brand_pic` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `zx_brand`
--

INSERT INTO `zx_brand` (`brand_id`, `brand_name`, `brand_pic`) VALUES
(1, '佛山市天纬陶', ''),
(2, '蒙娜丽莎', ''),
(3, '东鹏陶瓷', ''),
(4, '王者陶瓷', ''),
(5, '创益', 'data/upload/2013/03/1363340075_6322.gif'),
(6, '特地陶瓷', 'data/upload/2013/03/1363340057_7068.gif');

-- --------------------------------------------------------

--
-- 表的结构 `zx_brand_map`
--

CREATE TABLE IF NOT EXISTS `zx_brand_map` (
  `brand_id` int(11) DEFAULT '0',
  `category_id` int(11) DEFAULT '0',
  UNIQUE KEY `category_id_2` (`category_id`,`brand_id`),
  KEY `category_id` (`category_id`),
  KEY `brand_id` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_brand_map`
--

INSERT INTO `zx_brand_map` (`brand_id`, `category_id`) VALUES
(1, 179),
(2, 179),
(3, 179),
(4, 179),
(5, 179),
(6, 179),
(1, 180),
(2, 180),
(3, 180),
(4, 180),
(5, 180),
(6, 180),
(1, 181),
(2, 181),
(3, 181),
(4, 181),
(5, 181),
(6, 181),
(1, 182),
(2, 182),
(3, 182),
(4, 182),
(5, 182),
(6, 182),
(1, 183),
(2, 183),
(3, 183),
(5, 183),
(6, 183),
(6, 278);

-- --------------------------------------------------------

--
-- 表的结构 `zx_building_site`
--

CREATE TABLE IF NOT EXISTS `zx_building_site` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `designer_id` int(11) DEFAULT NULL,
  `area_id` int(11) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `face_pic` varchar(256) DEFAULT NULL,
  `space_id` int(11) DEFAULT NULL,
  `price_id` int(11) DEFAULT NULL,
  `a_id` int(11) DEFAULT NULL COMMENT '面积',
  `style_id` int(11) DEFAULT '0',
  `bg_time` date DEFAULT NULL,
  `description` varchar(512) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT '0' COMMENT '1代表显示',
  `pv` int(11) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0' COMMENT '施工状态',
  `score` tinyint(4) DEFAULT '5' COMMENT '评分',
  `orderby` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `city_id_2` (`area_id`),
  KEY `city_id_3` (`area_id`,`is_show`),
  KEY `city_id_4` (`is_show`),
  KEY `city_id_5` (`is_show`,`orderby`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_building_site`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_building_site_apply`
--

CREATE TABLE IF NOT EXISTS `zx_building_site_apply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `comment` varchar(256) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `uid` (`uid`),
  KEY `site_id_2` (`site_id`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_building_site_apply`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_building_site_status`
--

CREATE TABLE IF NOT EXISTS `zx_building_site_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_building_site_status`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_case`
--

CREATE TABLE IF NOT EXISTS `zx_case` (
  `case_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `designer_id` int(11) DEFAULT NULL,
  `title` varchar(256) NOT NULL,
  `type` tinyint(1) DEFAULT '1',
  `space_id` int(11) DEFAULT '0' COMMENT '空间',
  `style_id` int(11) DEFAULT '0' COMMENT '风格',
  `area_id` int(11) DEFAULT '0' COMMENT '面积',
  `price_id` int(11) DEFAULT '0' COMMENT '价格区间',
  `real_price` int(11) DEFAULT NULL COMMENT '真实价格',
  `real_space` int(11) DEFAULT '0',
  `face_pic` varchar(256) NOT NULL,
  `pv_num` int(11) DEFAULT '0',
  `detail_pics` text NOT NULL,
  `keywords` varchar(256) DEFAULT NULL,
  `description` text,
  `create_time` datetime DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT '0' COMMENT '1代表显示  0 代表在审核',
  PRIMARY KEY (`case_id`),
  KEY `uid` (`uid`),
  KEY `space_id` (`space_id`),
  KEY `style_id` (`style_id`),
  KEY `area_id` (`area_id`),
  KEY `price_id` (`price_id`),
  KEY `designer_id` (`designer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_case`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_case_map`
--

CREATE TABLE IF NOT EXISTS `zx_case_map` (
  `case_id` int(11) DEFAULT NULL,
  `cate_id` int(11) DEFAULT NULL,
  UNIQUE KEY `case_id` (`case_id`,`cate_id`),
  KEY `case_id_2` (`case_id`),
  KEY `cate_id` (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_case_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_category`
--

CREATE TABLE IF NOT EXISTS `zx_category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_type` tinyint(2) NOT NULL,
  `category_name` varchar(64) NOT NULL,
  `parent_id` int(11) DEFAULT '0',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=283 ;

--
-- 转存表中的数据 `zx_category`
--

INSERT INTO `zx_category` (`category_id`, `category_type`, `category_name`, `parent_id`) VALUES
(1, 1, '系统文章', 0),
(2, 5, '空间类型', 0),
(3, 5, '喜欢风格', 0),
(4, 5, '预算范围', 0),
(5, 1, '装修流程', 16),
(6, 4, '空间', 0),
(7, 4, '风格', 0),
(8, 4, '面积', 0),
(9, 4, '价格', 0),
(10, 3, '服务项目', 0),
(11, 3, '公司规模', 0),
(12, 3, '公司产值', 0),
(13, 3, '行业大类', 0),
(14, 1, '公告', 0),
(15, 1, '帮助中心', 0),
(16, 1, '学装修', 0),
(17, 5, '家庭装修', 2),
(18, 5, '办公装修', 2),
(19, 5, '酒店装修', 2),
(20, 5, '商铺装修', 2),
(21, 1, '收房准备中', 5),
(22, 1, '装修准备中', 5),
(23, 1, '拆改/隐蔽工程', 5),
(24, 1, '泥瓦工程', 5),
(25, 1, '木工工程', 5),
(26, 1, '油漆工程', 5),
(27, 1, '安装/收尾工程', 5),
(28, 1, '软装进行中', 5),
(29, 1, '即将入住', 5),
(30, 1, '收房小常识', 21),
(31, 1, '交房流程', 21),
(32, 1, '相关法规文件', 21),
(33, 1, '装修小常识', 22),
(34, 1, '签订装修合同', 22),
(35, 1, '相关法规文件', 22),
(36, 1, '结构拆改', 23),
(37, 1, '水管', 23),
(38, 1, '强弱电/开关插座', 23),
(39, 1, '拆改施工验收', 23),
(40, 1, '瓷砖', 24),
(41, 1, '石材', 24),
(42, 1, '泥瓦施工验收', 24),
(43, 1, '板材', 25),
(44, 1, '龙骨', 25),
(45, 1, '顶角/踢脚线', 25),
(46, 1, '石膏制品', 25),
(47, 1, '胶黏剂/胶水', 25),
(48, 1, '铝合金/不锈钢', 25),
(49, 1, '玻璃', 25),
(50, 1, '铁艺制品', 25),
(51, 1, '扣板', 25),
(52, 1, '门窗', 25),
(53, 1, '木工施工验收', 25),
(54, 1, '壁纸/壁布', 26),
(55, 1, '涂料/油漆', 26),
(56, 1, '油漆施工验收', 26),
(57, 1, '木地板', 27),
(58, 1, '地毯', 27),
(59, 1, '灯具', 27),
(60, 1, '洁具', 27),
(61, 1, '龙头五金配件', 27),
(62, 1, '家电', 27),
(63, 1, '安装施工验收', 27),
(64, 1, '家具', 28),
(65, 1, '布艺', 28),
(66, 1, '壁饰/工艺品', 28),
(67, 1, '花卉', 28),
(68, 1, '环保检测', 29),
(69, 1, '搬家搬场', 29),
(70, 1, '保洁', 29),
(71, 5, '5万以下', 4),
(72, 5, '5-8万', 4),
(73, 5, '8-10万', 4),
(74, 5, '10-15万', 4),
(75, 5, '15-50万', 4),
(76, 5, '50万以上', 4),
(77, 4, '50㎡以下', 8),
(78, 4, '50-70㎡', 8),
(79, 4, '70-90㎡', 8),
(80, 4, '90-130㎡', 8),
(81, 4, '130-200㎡', 8),
(82, 4, '200㎡以上', 8),
(83, 4, '中式', 7),
(84, 4, '日韩', 7),
(85, 4, '欧美', 7),
(86, 4, '东南亚', 7),
(87, 4, '古典', 7),
(88, 4, '田园', 7),
(89, 4, '现代', 7),
(90, 4, '简约', 7),
(91, 4, '豪华', 7),
(92, 4, '时尚', 7),
(93, 4, '温馨', 7),
(94, 5, '中式', 3),
(95, 5, '日韩', 3),
(96, 5, '欧美', 3),
(97, 5, '东南亚', 3),
(98, 5, '古典', 3),
(99, 5, '田园', 3),
(100, 5, '现代', 3),
(101, 5, '简约', 3),
(102, 5, '豪华', 3),
(103, 5, '时尚', 3),
(104, 5, '温馨', 3),
(105, 4, '一室一厅一卫', 6),
(106, 4, '两室一厅一卫', 6),
(107, 4, '两室两厅一卫', 6),
(108, 4, '两室两厅两卫', 6),
(109, 4, '三室一厅一卫', 6),
(110, 4, '三室两厅一卫', 6),
(111, 4, '三室两厅两卫', 6),
(112, 4, '四室两厅两卫', 6),
(113, 4, '复式住宅', 6),
(114, 4, '别墅住宅', 6),
(115, 4, '酒店餐厅', 6),
(116, 4, '健身房', 6),
(117, 4, '营业厅', 6),
(118, 4, '酒吧KTV', 6),
(119, 4, '浴室桑拿房', 6),
(120, 4, '办公室', 6),
(121, 4, '茶楼会所', 6),
(122, 4, '超市', 6),
(123, 4, '学校', 6),
(124, 4, '商场商铺', 6),
(125, 4, '其他', 6),
(126, 4, '5万以下', 9),
(127, 4, '5-6万', 9),
(128, 4, '6-10万', 9),
(129, 4, '10-15万', 9),
(130, 4, '15-50万', 9),
(131, 4, '50万以上', 9),
(132, 3, '中小型', 11),
(133, 3, '大中型', 11),
(134, 3, '全国连锁', 11),
(135, 3, '50万以下', 12),
(136, 3, '50-200万', 12),
(137, 3, '200-500万', 12),
(138, 3, '500-2000万', 12),
(139, 3, '2000万以上', 12),
(140, 3, '油漆', 13),
(141, 3, '五金', 13),
(142, 3, '灯饰', 13),
(143, 3, '软装', 13),
(144, 3, '地板', 13),
(145, 3, '家庭装修', 10),
(146, 3, '酒店装修', 10),
(147, 3, '办公装修', 10),
(148, 4, '在建工地', 6),
(149, 4, '设计作品', 6),
(150, 1, '经典家装', 16),
(151, 1, '风格潮流', 16),
(152, 1, '装饰搭配', 16),
(153, 1, '家居风水', 16),
(154, 1, '家品导购', 16),
(155, 1, '定设计/装修方案', 22),
(156, 1, '选择装修公司', 22),
(157, 1, '配置资金预算', 22),
(158, 6, '家装设计', 0),
(159, 6, '装修流程', 0),
(160, 6, '家居产品', 0),
(161, 6, '装饰材料', 0),
(162, 6, '公装设计', 0),
(163, 6, '装修风水', 158),
(164, 6, '装修风格', 158),
(165, 6, '客厅', 158),
(166, 2, '瓷砖', 0),
(167, 2, '地板', 0),
(168, 2, '涂料油漆', 0),
(169, 2, '卫浴洁具', 0),
(170, 2, '厨房设备', 0),
(171, 2, '五金', 0),
(172, 2, '吊顶', 0),
(173, 2, '门窗', 0),
(174, 2, '电工灯具', 0),
(175, 2, '软装', 0),
(176, 2, '墙纸', 0),
(177, 2, '家具', 0),
(178, 2, '智能家居', 0),
(179, 2, '外墙砖', 166),
(180, 2, '花片砖', 166),
(181, 2, '腰线砖', 166),
(182, 2, '地面砖', 166),
(183, 2, '内墙砖', 166),
(184, 5, '一室一厅', 17),
(186, 1, '装饰搭配', 0),
(187, 1, '风格潮流', 0),
(188, 6, '装饰材料', 161),
(189, 6, '玄关', 158),
(190, 6, '过道', 158),
(191, 6, '卧室', 158),
(192, 6, '书房', 158),
(193, 6, '餐厅', 158),
(194, 6, '厨房', 158),
(195, 6, '阳台', 158),
(196, 6, '花园', 158),
(197, 6, '卫生间', 158),
(198, 6, '儿童房', 158),
(199, 6, '衣帽间', 158),
(200, 6, '别墅', 158),
(201, 6, '小户型', 158),
(202, 6, '二手房装修', 158),
(203, 6, '复式', 158),
(204, 6, '样板间', 158),
(205, 6, '装饰柜', 158),
(206, 6, '其他', 158),
(207, 6, '新房验收', 159),
(208, 6, '收费咨询', 159),
(209, 6, '装修预算', 159),
(210, 6, '装修合同', 159),
(211, 6, '施工工艺', 159),
(212, 6, '业主维权', 159),
(213, 6, '环保检测', 159),
(214, 6, '装修公司', 159),
(215, 6, '完工验收', 159),
(216, 6, '水电改造', 159),
(217, 6, '维护保养', 159),
(218, 6, '其它', 159),
(219, 6, '家具', 160),
(220, 6, '卫浴设备', 160),
(221, 6, '灯具/照明', 160),
(222, 6, '儿童家具/用品', 160),
(223, 6, '家用电器', 160),
(224, 6, '橱柜', 160),
(225, 6, '厨卫电器', 160),
(226, 6, '厨房用具', 160),
(227, 6, '地毯', 160),
(228, 6, '饰品', 160),
(229, 6, '庭院/花园', 160),
(230, 6, '暖气设备', 160),
(231, 6, '家庭影院', 160),
(232, 6, '壁炉/火炉', 160),
(233, 6, '天花板/吊顶', 160),
(234, 6, '床上用品', 160),
(235, 6, '窗帘/配件', 160),
(236, 6, '日杂用品', 160),
(238, 6, '夹板木材', 161),
(239, 6, '地板', 161),
(240, 6, '涂料/油漆', 161),
(241, 6, '瓷砖/石材', 161),
(242, 6, '门窗', 161),
(243, 6, '五金', 161),
(244, 6, '壁纸', 161),
(245, 6, '水龙头', 161),
(246, 6, '建筑构件', 161),
(247, 6, '管材管件', 161),
(248, 6, '其他', 161),
(249, 6, '办公楼', 162),
(250, 6, '商场', 162),
(251, 6, '专卖店', 162),
(252, 6, '酒店宾馆/客房', 162),
(253, 6, '餐饮酒吧', 162),
(254, 6, '歌舞迪厅', 162),
(255, 6, '休闲健身', 162),
(256, 6, '体育场管', 162),
(257, 6, '会展剧场', 162),
(258, 6, '博物馆', 162),
(259, 6, '图书馆', 162),
(260, 6, '学校', 162),
(261, 6, '医院', 162),
(262, 6, '机场车船站', 162),
(263, 6, '工厂', 162),
(264, 6, '公园广场', 162),
(265, 6, '会所', 162),
(266, 6, '园林', 162),
(267, 6, '其它', 162),
(268, 5, '两室一厅一卫', 17),
(269, 5, '两室两厅一卫', 17),
(270, 5, '两室两厅两卫', 17),
(271, 5, '三室一厅一卫', 17),
(272, 5, '三室两厅一卫', 17),
(273, 5, '三室两厅两卫', 17),
(274, 5, '四室两厅两卫', 17),
(275, 5, '复式住宅', 17),
(276, 5, '别墅住宅', 17),
(278, 2, '其它瓷砖', 166),
(279, 1, '行业新闻', 0),
(280, 1, '装修视频', 0),
(281, 1, '养护秘籍', 0),
(282, 1, '家居健康', 0);

-- --------------------------------------------------------

--
-- 表的结构 `zx_city_areas`
--

CREATE TABLE IF NOT EXISTS `zx_city_areas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `area_name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_city_areas`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_comments`
--

CREATE TABLE IF NOT EXISTS `zx_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `uid` int(10) NOT NULL DEFAULT '0',
  `comments` varchar(512) DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT '0' COMMENT '1显示0代表不显示',
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`type_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_comments`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_company`
--

CREATE TABLE IF NOT EXISTS `zx_company` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `area_id` int(11) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `company_name` varchar(128) DEFAULT NULL,
  `description` varchar(128) DEFAULT NULL,
  `logo` varchar(128) DEFAULT NULL,
  `banner` varchar(256) DEFAULT NULL,
  `founding_year` mediumint(4) DEFAULT NULL COMMENT '成立年份 ',
  `scale_id` int(11) DEFAULT '0' COMMENT '公司规模',
  `output_id` int(11) DEFAULT '0' COMMENT '产值',
  `qq_id` int(11) DEFAULT '0' COMMENT '默认客服QQ ID',
  `addr_id` int(11) DEFAULT '0' COMMENT '默认地址ID',
  `comment_num` int(11) DEFAULT '0' COMMENT '点评数冗余',
  `average_score` int(11) DEFAULT '50' COMMENT '平均评价分数',
  `free_room` tinyint(1) DEFAULT '0' COMMENT '是否免费量房',
  `introduce` text,
  `orderby` int(11) DEFAULT '0',
  `pv` int(11) DEFAULT '0',
  `longitude` int(11) DEFAULT NULL,
  `latitude` int(11) DEFAULT NULL,
  `template_id` tinyint(3) DEFAULT '0',
  `is_show` tinyint(1) DEFAULT '1' COMMENT '0 代表不显示',
  PRIMARY KEY (`uid`),
  KEY `city_id_2` (`area_id`),
  KEY `orderby` (`orderby`) USING BTREE,
  KEY `city_id_3` (`area_id`,`orderby`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_company`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_company_addrs`
--

CREATE TABLE IF NOT EXISTS `zx_company_addrs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `addr` varchar(256) DEFAULT NULL,
  `tel` varchar(128) DEFAULT NULL,
  `fax` varchar(128) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `contact` varchar(32) DEFAULT NULL,
  `pic` varchar(256) DEFAULT NULL COMMENT '周边地图贴图',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_company_addrs`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_company_area`
--

CREATE TABLE IF NOT EXISTS `zx_company_area` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `area_id` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `uid_2` (`uid`,`area_id`),
  KEY `area_id` (`area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_company_area`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_company_dianping`
--

CREATE TABLE IF NOT EXISTS `zx_company_dianping` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `company_id` int(11) DEFAULT '0',
  `process` tinyint(1) DEFAULT NULL,
  `service` tinyint(1) DEFAULT NULL,
  `design` tinyint(1) DEFAULT NULL,
  `sales` tinyint(1) DEFAULT NULL,
  `dianping` varchar(512) DEFAULT NULL,
  `project` varchar(512) DEFAULT NULL,
  `contact` varchar(32) DEFAULT NULL,
  `realname` varchar(32) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `revert` varchar(512) DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT '0' COMMENT '1代表显示',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`,`company_id`),
  KEY `uid_2` (`uid`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_company_dianping`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_company_industry`
--

CREATE TABLE IF NOT EXISTS `zx_company_industry` (
  `uid` int(11) DEFAULT NULL,
  `industry_id` int(11) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`industry_id`),
  KEY `uid_2` (`uid`),
  KEY `industry_id` (`industry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_company_industry`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_company_keyword_maps`
--

CREATE TABLE IF NOT EXISTS `zx_company_keyword_maps` (
  `uid` int(11) DEFAULT NULL,
  `keyword_id` int(11) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`keyword_id`),
  UNIQUE KEY `keyword_id` (`keyword_id`,`uid`),
  KEY `uid_2` (`uid`),
  KEY `keyword_id_2` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_company_keyword_maps`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_company_material`
--

CREATE TABLE IF NOT EXISTS `zx_company_material` (
  `uid` int(11) DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  UNIQUE KEY `uid_2` (`uid`,`material_id`),
  KEY `uid` (`uid`),
  KEY `material_id` (`material_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_company_material`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_company_pics`
--

CREATE TABLE IF NOT EXISTS `zx_company_pics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `pic` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `type` (`type`),
  KEY `uid_2` (`uid`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_company_pics`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_company_project`
--

CREATE TABLE IF NOT EXISTS `zx_company_project` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `project_id` int(11) DEFAULT NULL,
  UNIQUE KEY `uid` (`uid`,`project_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_company_project`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_company_qqs`
--

CREATE TABLE IF NOT EXISTS `zx_company_qqs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `qq` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_company_qqs`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_company_security`
--

CREATE TABLE IF NOT EXISTS `zx_company_security` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `money1` int(11) DEFAULT '0' COMMENT '固定保障金',
  `money2` int(11) DEFAULT '0' COMMENT '活动保障金',
  `special` text,
  `after_sales` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_company_security`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_content`
--

CREATE TABLE IF NOT EXISTS `zx_content` (
  `content_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `category_id` int(11) NOT NULL,
  `source` varchar(64) DEFAULT NULL,
  `author` varchar(64) DEFAULT NULL,
  `face_pic` varchar(256) DEFAULT NULL,
  `create_time` datetime NOT NULL,
  `contents` text NOT NULL,
  `keywords` varchar(80) DEFAULT NULL,
  `like_num` int(11) DEFAULT '0',
  `pv_num` int(11) DEFAULT '0',
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`content_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_content`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_content_keyword_maps`
--

CREATE TABLE IF NOT EXISTS `zx_content_keyword_maps` (
  `content_id` int(11) DEFAULT NULL,
  `keyword_id` int(11) DEFAULT NULL,
  UNIQUE KEY `content_keyword` (`content_id`,`keyword_id`),
  UNIQUE KEY `keyword_content` (`keyword_id`,`content_id`),
  KEY `content_id` (`content_id`),
  KEY `keyword_id` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_content_keyword_maps`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_content_tag`
--

CREATE TABLE IF NOT EXISTS `zx_content_tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_content_tag`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_content_tagmap`
--

CREATE TABLE IF NOT EXISTS `zx_content_tagmap` (
  `tag_id` int(11) DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  UNIQUE KEY `content_id_2` (`content_id`,`tag_id`) USING BTREE,
  UNIQUE KEY `tag_id_2` (`tag_id`,`content_id`),
  KEY `content_id` (`content_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_content_tagmap`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_demand`
--

CREATE TABLE IF NOT EXISTS `zx_demand` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `demand` varchar(1024) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_demand`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_designer`
--

CREATE TABLE IF NOT EXISTS `zx_designer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `area_id` int(11) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `face_pic` varchar(256) DEFAULT NULL,
  `position` varchar(32) DEFAULT NULL,
  `school` varchar(64) DEFAULT NULL,
  `from_time` int(11) DEFAULT NULL,
  `style` varchar(128) DEFAULT NULL,
  `about` varchar(1024) DEFAULT NULL,
  `qq` varchar(20) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `is_gold` tinyint(1) DEFAULT '0' COMMENT '1代表金牌设计师',
  `orderby` int(11) DEFAULT '0' COMMENT '数字越大排名越高',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `city_id_2` (`area_id`),
  KEY `city_id_4` (`area_id`,`orderby`),
  KEY `orderby` (`orderby`),
  KEY `city_id_3` (`orderby`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_designer`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_diary`
--

CREATE TABLE IF NOT EXISTS `zx_diary` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `cate_id` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `contents` text,
  `create_time` int(11) DEFAULT NULL,
  `is_show` tinyint(4) DEFAULT '1' COMMENT '1代表显示',
  PRIMARY KEY (`id`),
  KEY `cate_id` (`cate_id`),
  KEY `uid` (`uid`),
  KEY `cate_id_2` (`cate_id`,`is_show`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_diary`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_group`
--

CREATE TABLE IF NOT EXISTS `zx_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `zx_group`
--

INSERT INTO `zx_group` (`group_id`, `group_name`) VALUES
(1, '超级管理员'),
(2, '普通编辑');

-- --------------------------------------------------------

--
-- 表的结构 `zx_group_map`
--

CREATE TABLE IF NOT EXISTS `zx_group_map` (
  `group_id` int(11) DEFAULT NULL,
  `privilege_id` int(11) DEFAULT NULL,
  UNIQUE KEY `group_id` (`group_id`,`privilege_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `zx_group_map`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_injection`
--

CREATE TABLE IF NOT EXISTS `zx_injection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `ctl` varchar(20) DEFAULT NULL,
  `act` varchar(20) DEFAULT NULL,
  `t` int(11) DEFAULT '86400',
  `num` int(11) DEFAULT '1000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ctl` (`ctl`,`act`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `zx_injection`
--

INSERT INTO `zx_injection` (`id`, `name`, `ctl`, `act`, `t`, `num`) VALUES
(1, '招标', 'tenders', 'save', 600, 3),
(2, '快速招标（只填手机号码的）', 'tenders', 'save2', 10, 3);

-- --------------------------------------------------------

--
-- 表的结构 `zx_injection_info`
--

CREATE TABLE IF NOT EXISTS `zx_injection_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token` char(32) DEFAULT '0',
  `last_t` int(11) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_injection_info`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_integral`
--

CREATE TABLE IF NOT EXISTS `zx_integral` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` tinyint(2) DEFAULT '0' COMMENT '获得途径',
  `num` int(11) DEFAULT '0',
  `expires_t` int(11) DEFAULT NULL,
  `t` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_integral`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_integral_exchange`
--

CREATE TABLE IF NOT EXISTS `zx_integral_exchange` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT '0' COMMENT '0兑换1为抽奖',
  `integral` int(11) DEFAULT '0' COMMENT '花费积分',
  `name` varchar(32) DEFAULT NULL COMMENT '联系人',
  `tel` varchar(32) DEFAULT NULL COMMENT '联系方式',
  `description` varchar(1024) DEFAULT NULL,
  `t` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '1代表已经处理',
  `note` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `product_id` (`product_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_integral_exchange`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_integral_shop`
--

CREATE TABLE IF NOT EXISTS `zx_integral_shop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(256) DEFAULT NULL,
  `face_pic` varchar(256) DEFAULT NULL,
  `num` int(11) DEFAULT '0',
  `market_price` int(11) DEFAULT '0',
  `exchange_integral` int(11) DEFAULT '0',
  `lottery_integral` int(11) DEFAULT '0',
  `lottery_probability` int(11) DEFAULT '0',
  `is_show` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_integral_shop`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_integral_used`
--

CREATE TABLE IF NOT EXISTS `zx_integral_used` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `t` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_integral_used`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_keywords`
--

CREATE TABLE IF NOT EXISTS `zx_keywords` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_keywords`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_links`
--

CREATE TABLE IF NOT EXISTS `zx_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `link_name` varchar(32) DEFAULT NULL,
  `link_url` varchar(256) DEFAULT NULL,
  `link_pic` varchar(256) DEFAULT NULL,
  `link_order` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `zx_links`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_message`
--

CREATE TABLE IF NOT EXISTS `zx_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `tel` varchar(64) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `content` varchar(1024) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_message`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_outtoin`
--

CREATE TABLE IF NOT EXISTS `zx_outtoin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `out` varchar(40) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `out` (`out`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_outtoin`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_preferential`
--

CREATE TABLE IF NOT EXISTS `zx_preferential` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `area_id` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT '0',
  `title` varchar(256) DEFAULT NULL,
  `face_pic` varchar(256) DEFAULT NULL,
  `content` text,
  `create_time` int(11) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT '0' COMMENT '1代表显示',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `city_d_2` (`area_id`),
  KEY `city_id` (`area_id`,`is_show`),
  KEY `city_id_2` (`is_show`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_preferential`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_preferential_keyword_maps`
--

CREATE TABLE IF NOT EXISTS `zx_preferential_keyword_maps` (
  `preferential_id` int(11) DEFAULT NULL,
  `keyword_id` int(11) DEFAULT NULL,
  UNIQUE KEY `preferential_keyword` (`preferential_id`,`keyword_id`),
  UNIQUE KEY `keyword_preferential` (`keyword_id`,`preferential_id`),
  KEY `preferential_id` (`preferential_id`),
  KEY `keyword_id` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_preferential_keyword_maps`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_privilege`
--

CREATE TABLE IF NOT EXISTS `zx_privilege` (
  `privilege_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `privilege_name` varchar(64) DEFAULT NULL,
  `privilege_key` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`privilege_id`),
  UNIQUE KEY `privilege_key` (`privilege_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=253 ;

--
-- 转存表中的数据 `zx_privilege`
--

INSERT INTO `zx_privilege` (`privilege_id`, `group_id`, `privilege_name`, `privilege_key`) VALUES
(1, 1, '角色列表', 'group_main'),
(2, 1, '新增角色', 'group_add'),
(3, 1, '角色修改', 'group_edit'),
(4, 1, '删除角色', 'group_del'),
(5, 1, '设置权限', 'group_privilege'),
(6, 3, '新增管理员', 'admin_add'),
(7, 3, '修改管理员', 'admin_edit'),
(8, 3, '锁定管理员', 'admin_lock'),
(9, 3, '解锁管理员', 'admin_unlock'),
(10, 4, '修改资料', 'admin_edit2'),
(11, 3, '管理员列表', 'admin_main'),
(15, 5, '权限组列表', 'privilegeGroup_main'),
(16, 5, '权限组新增', 'privilegeGroup_add'),
(17, 5, '权限组修改', 'privilegeGroup_edit'),
(18, 5, '权限组删除', 'privilegeGroup_del'),
(19, 5, '权限列表', 'privilege_main'),
(20, 5, '新增权限', 'privilege_add'),
(21, 5, '修改权限', 'privilege_edit'),
(22, 5, '删除权限', 'privilege_del'),
(23, 6, '站点设置', 'site_main'),
(24, 6, '城市列表', 'city_main'),
(25, 6, '城市修改', 'city_edit'),
(26, 6, '城市删除', 'city_del'),
(27, 6, '城市新增', 'city_add'),
(28, 6, '区域列表', 'area_main'),
(29, 6, '区域新增', 'area_add'),
(30, 6, '区域修改', 'area_edit'),
(31, 6, '区域删除', 'area_del'),
(32, 7, '分类列表', 'category_main'),
(33, 7, '分类修改', 'category_edit'),
(34, 7, '分类删除', 'category_del'),
(35, 7, '分类新增', 'category_add'),
(36, 8, '文章TAG列表', 'contentTag_main'),
(37, 8, '文章TAG删除', 'contentTag_del'),
(38, 8, '文章列表', 'content_main'),
(39, 8, '文章新增', 'content_add'),
(40, 8, '文章修改', 'content_edit'),
(41, 8, '文章删除', 'content_del'),
(42, 8, '装修日记列表', 'diary_main'),
(43, 8, '新增装修日记', 'diary_add'),
(44, 8, '修改装修日记', 'diary_edit'),
(45, 8, '装修日记删除', 'diary_del'),
(46, 9, '案例列表', 'case_main'),
(47, 9, '新增案例', 'case_add'),
(48, 9, '案例修改', 'case_edit'),
(49, 9, '案例删除', 'case_del'),
(50, 9, '案例显示', 'case_show'),
(51, 9, '案例不显示', 'case_unshow'),
(52, 10, '招标列表', 'bidding_main'),
(53, 10, '招标修改', 'bidding_edit'),
(54, 10, '招标查看', 'bidding_view'),
(55, 10, '招标删除', 'bidding_del'),
(56, 10, '快捷招标列表', 'biddingQuick_main'),
(57, 10, '快捷招标删除', 'biddingQuick_del'),
(58, 10, '快捷招标确认', 'biddingQuick_change'),
(59, 11, '问吧列表', 'ask_main'),
(60, 11, '问吧编辑', 'ask_edit'),
(61, 11, '问吧删除', 'ask_del'),
(62, 11, '问吧置顶', 'ask_orderUp'),
(63, 11, '问吧取消置顶', 'ask_orderDown'),
(64, 11, '问题补充列表', 'askAdded_main'),
(65, 11, '问题补充增加', 'askAdded_add'),
(66, 11, '问题补充修改', 'askAdded_edit'),
(67, 11, '问题补充删除', 'askAdded_del'),
(68, 11, '问题回答列表', 'askAnswer_main'),
(69, 11, '问题回答新增', 'askAnswer_add'),
(70, 11, '问题回答修改', 'askAnswer_edit'),
(71, 11, '问题回答删除', 'askAnswer_del'),
(72, 11, '推荐位分组列表', 'recommendGroup_main'),
(73, 12, '推荐位分组新增', 'recommendGroup_add'),
(74, 12, '推荐位分组删除', 'recommendGroup_del'),
(75, 12, '推荐位分组修改', 'recommendGroup_edit'),
(76, 12, '推荐位列表', 'recommendSign_main'),
(77, 12, '推荐位新增', 'recommendSign_add'),
(78, 12, '推荐位修改', 'recommendSign_edit'),
(79, 12, '推荐位删除', 'recommendSign_del'),
(80, 12, '推荐内容列表', 'recommend_main'),
(81, 12, '推荐内容新增', 'recommend_add'),
(82, 12, '推荐内容修改', 'recommend_edit'),
(83, 12, '推荐内容删除', 'recommend_del'),
(84, 12, '推荐内容更新', 'recommend_update'),
(85, 13, '活动列表', 'activity_main'),
(86, 13, '活动新增', 'activity_add'),
(87, 13, '活动修改', 'activity_edit'),
(88, 13, '活动删除', 'activity_del'),
(89, 13, '活动报名查看', 'activity_join'),
(90, 14, '优惠信息列表', 'preferential_main'),
(91, 14, '优惠信息新增', 'preferential_add'),
(92, 14, '优惠信息删除', 'preferential_del'),
(93, 13, '优惠信息编辑', 'preferential_edit'),
(94, 14, '优惠信息查看', 'preferential_view'),
(95, 15, '友情链接列表', 'links_main'),
(96, 14, '友情链接新增', 'links_add'),
(97, 15, '友情链接修改', 'links_edit'),
(98, 15, '友情链接删除', 'links_del'),
(99, 16, '广告位列表', 'adSite_main'),
(100, 16, '广告位新增', 'adSite_add'),
(101, 16, '广告位修改', 'adSite_edit'),
(102, 16, '广告位删除', 'adSite_del'),
(103, 16, '广告列表', 'ads_main'),
(104, 16, '广告新增', 'ads_add'),
(105, 16, '广告修改', 'ads_edit'),
(106, 16, '广告删除', 'ads_del'),
(107, 17, '会员等级列表', 'ranks_main'),
(108, 17, '会员等级新增', 'ranks_add'),
(109, 17, '会员等级修改', 'ranks_edit'),
(110, 17, '会员等级删除', 'ranks_del'),
(111, 17, '会员等级查看', 'ranks_view'),
(112, 17, '会员列表', 'users_main'),
(113, 17, '会员注册', 'users_add'),
(114, 17, '会员编辑', 'users_edit'),
(115, 17, '会员删除', 'users_del'),
(116, 17, '会员查看', 'users_view'),
(117, 17, '开通会员', 'users_rank'),
(118, 17, '会员开通日志列表', 'rankLogs_main'),
(119, 18, '公司列表', 'company_main'),
(120, 18, '公司删除', 'company_del'),
(121, 18, '公司认证', 'company_authentication'),
(122, 18, '公司点评列表', 'companyDianping_main'),
(123, 18, '公司点评修改', 'companyDianping_edit'),
(124, 18, '公司点评删除', 'companyDianping_del'),
(125, 18, '企业图片列表', 'companyPics_main'),
(126, 18, '企业图片新增', 'companyPics_add'),
(127, 18, '企业图片编辑', 'companyPics_edit'),
(128, 18, '企业图片查看', 'companyPics_view'),
(129, 17, '企业图片删除', 'companyPics_del'),
(130, 17, '设计师列表', 'designer_main'),
(131, 17, '设计师新增', 'designer_add'),
(132, 17, '设计师修改', 'designer_edit'),
(133, 17, '设计师删除', 'designer_del'),
(134, 17, '会员留言列表', 'message_main'),
(135, 17, '新增会员留言', 'message_add'),
(136, 17, '会员留言修改', 'message_edit'),
(137, 17, '会员留言查看', 'message_view'),
(138, 17, '会员留言删除', 'message_del'),
(139, 19, '品牌列表', 'brand_main'),
(140, 19, '品牌新增', 'brand_add'),
(141, 19, '品牌修改', 'brand_edit'),
(142, 19, '品牌删除', 'brand_del'),
(143, 19, '品牌取消绑定分类', 'brand_delMap'),
(144, 19, '品牌绑定分类', 'brand_getMap'),
(145, 19, '产品列表', 'products_main'),
(146, 19, '产品新增', 'products_add'),
(147, 19, '产品修改', 'products_edit'),
(148, 19, '产品删除', 'products_del'),
(149, 19, '产品显示', 'products_show'),
(150, 19, '产品不显示', 'products_unshow'),
(151, 19, '产品导购列表', 'demand_main'),
(152, 19, '产品导购删除', 'demand_del'),
(153, 18, '在建工地列表', 'buildingSite_main'),
(154, 18, '新增在建工地', 'buildingSite_add'),
(155, 18, '修改在建工地', 'buildingSite_edit'),
(156, 18, '删除在建工地', 'buildingSite_del'),
(157, 18, '在建工地预约查看', 'buildingSiteApply_main'),
(158, 10, '招标设置', 'bidding_setting'),
(159, 18, '公司列表排序推广管理', 'company_orderby'),
(160, 20, '申请量房列表', 'quantityRoom_main'),
(161, 20, '新增申请量房', 'quantityRoom_add'),
(162, 20, '修改申请量房', 'quantityRoom_edit'),
(163, 20, '删除申请量房', 'quantityRoom_del'),
(164, 21, '公司保障列表', 'security_main'),
(165, 21, '新增公司保障', 'security_add'),
(166, 21, '修改公司保障', 'security_edit'),
(167, 21, '删除公司保障', 'security_del'),
(168, 22, '敏感词管理列表', 'sensitiveWord_main'),
(169, 22, '新增敏感词管理', 'sensitiveWord_add'),
(170, 22, '修改敏感词管理', 'sensitiveWord_edit'),
(171, 22, '删除敏感词管理', 'sensitiveWord_del'),
(172, 23, '防注水设置列表', 'injection_main'),
(173, 23, '新增防注水设置', 'injection_add'),
(174, 23, '修改防注水设置', 'injection_edit'),
(175, 23, '删除防注水设置', 'injection_del'),
(176, 24, '申请设计列表', 'bookingDesign_main'),
(177, 24, '新增申请设计', 'bookingDesign_add'),
(178, 24, '修改申请设计', 'bookingDesign_edit'),
(179, 24, '删除申请设计', 'bookingDesign_del'),
(180, 25, '竞标管理列表', 'biddingBid_main'),
(181, 25, '新增竞标管理', 'biddingBid_add'),
(182, 25, '修改竞标管理', 'biddingBid_edit'),
(183, 25, '删除竞标管理', 'biddingBid_del'),
(184, 17, '设计师认证', 'designer_authentication'),
(185, 17, '设计师推广', 'designer_promotion'),
(186, 17, '设计师金牌设置', 'designer_gold'),
(187, 25, '设为入围标', 'biddingBid_setRw'),
(188, 25, '设置中标', 'biddingBid_setWin'),
(189, 6, '会员权限', 'site_authority'),
(190, 6, 'uc整合设置', 'site_uc'),
(191, 6, '邮件设置', 'site_mail'),
(192, 6, '短信设置', 'site_sms'),
(193, 26, '会员积分列表', 'integral_main'),
(194, 26, '新增会员积分', 'integral_add'),
(195, 26, '修改会员积分', 'integral_edit'),
(196, 26, '删除会员积分', 'integral_del'),
(197, 27, '积分使用列表', 'integralUsed_main'),
(198, 26, '积分设置', 'integralSetting_main'),
(200, 28, '积分产品列表', 'integralShop_main'),
(201, 28, '新增积分产品', 'integralShop_add'),
(202, 28, '修改积分产品', 'integralShop_edit'),
(232, 28, '产品上下架处理', 'integralShop_show'),
(233, 36, '积分兑换物品列表', 'integralExchange_main'),
(234, 36, '新增积分兑换物品', 'integralExchange_add'),
(235, 36, '修改积分兑换物品', 'integralExchange_edit'),
(245, 40, '系统日志列表', 'systemLogs_main'),
(246, 40, '新增系统日志', 'systemLogs_add'),
(247, 12, '批量推荐', 'recommend_save'),
(248, 41, '工地流程列表', 'buildingSiteStatus_main'),
(249, 41, '新增工地流程', 'buildingSiteStatus_add'),
(250, 41, '修改工地流程', 'buildingSiteStatus_edit'),
(251, 41, '删除工地流程', 'buildingSiteStatus_del'),
(252, 41, '查看工地流程', 'buildingSiteStatus_view');

-- --------------------------------------------------------

--
-- 表的结构 `zx_privilege_group`
--

CREATE TABLE IF NOT EXISTS `zx_privilege_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- 转存表中的数据 `zx_privilege_group`
--

INSERT INTO `zx_privilege_group` (`group_id`, `group_name`) VALUES
(4, '个人中心'),
(14, '优惠信息'),
(26, '会员积分'),
(17, '会员管理'),
(21, '公司保障'),
(18, '公司管理'),
(7, '分类管理'),
(15, '友情链接'),
(19, '商城系统'),
(41, '工地流程'),
(16, '广告管理'),
(10, '招标管理'),
(12, '推荐管理'),
(22, '敏感词管理'),
(8, '文章系统'),
(5, '权限管理'),
(9, '案例系统'),
(13, '活动系统'),
(24, '申请设计'),
(20, '申请量房'),
(28, '积分产品'),
(27, '积分使用'),
(36, '积分兑换物品'),
(25, '竞标管理'),
(3, '管理员管理'),
(40, '系统日志'),
(6, '系统设置'),
(1, '角色配置'),
(11, '问吧管理'),
(23, '防注水设置');

-- --------------------------------------------------------

--
-- 表的结构 `zx_products`
--

CREATE TABLE IF NOT EXISTS `zx_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(128) DEFAULT NULL,
  `model` varchar(64) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT '0',
  `face_pic` varchar(256) DEFAULT NULL,
  `market_price` int(11) DEFAULT NULL,
  `mall_price` int(11) DEFAULT NULL,
  `detail_pics` text,
  `description` text,
  `is_show` tinyint(1) DEFAULT '1' COMMENT '0 代表下降',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `brand_id` (`brand_id`),
  KEY `company_id` (`company_id`),
  KEY `category_id_2` (`category_id`,`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_products`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_product_keyword_maps`
--

CREATE TABLE IF NOT EXISTS `zx_product_keyword_maps` (
  `product_id` int(11) DEFAULT NULL,
  `keyword_id` int(11) DEFAULT NULL,
  UNIQUE KEY `product_id_2` (`product_id`,`keyword_id`),
  UNIQUE KEY `keyword_id_2` (`keyword_id`,`product_id`),
  KEY `product_id` (`product_id`),
  KEY `keyword_id` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_product_keyword_maps`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_quantity_room`
--

CREATE TABLE IF NOT EXISTS `zx_quantity_room` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_quantity_room`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_recommend`
--

CREATE TABLE IF NOT EXISTS `zx_recommend` (
  `recommend_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `page_id` int(11) NOT NULL,
  `sign_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `mdl_id` int(11) NOT NULL,
  `face_pic` varchar(256) DEFAULT NULL,
  `link` varchar(256) DEFAULT NULL,
  `order` int(11) DEFAULT '100',
  `description` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`recommend_id`),
  KEY `city_id` (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_recommend`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_recommend_group`
--

CREATE TABLE IF NOT EXISTS `zx_recommend_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `zx_recommend_group`
--

INSERT INTO `zx_recommend_group` (`group_id`, `group_name`) VALUES
(2, '优惠信息'),
(5, '商城'),
(3, '学装修'),
(6, '案例'),
(4, '问吧'),
(1, '首页');

-- --------------------------------------------------------

--
-- 表的结构 `zx_recommend_sign`
--

CREATE TABLE IF NOT EXISTS `zx_recommend_sign` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `key` varchar(64) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `mold` tinyint(4) DEFAULT NULL COMMENT '比如最新 最热 人气最高 等等 模型',
  `cate_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=90 ;

--
-- 转存表中的数据 `zx_recommend_sign`
--

INSERT INTO `zx_recommend_sign` (`id`, `group_id`, `name`, `key`, `type`, `mold`, `cate_id`) VALUES
(4, 2, '优惠信息右侧推荐位', 'service-right-001', NULL, NULL, NULL),
(5, 3, '装修日记左侧精品日记推荐', 'content-diary-001', NULL, NULL, NULL),
(6, 3, '装修日记左侧装修经验推荐', 'content-diary-002', NULL, NULL, NULL),
(15, 3, '学装修活动推荐', 'content-activity-001', NULL, NULL, NULL),
(21, 3, '学装修轮播推荐', 'content-lunbo-001', NULL, NULL, NULL),
(22, 3, '学装修一周精彩推荐', 'content-hot-001', NULL, NULL, NULL),
(23, 4, '问吧精彩问题', 'ask-001', NULL, NULL, NULL),
(24, 4, '问吧公告', 'ask-002', NULL, NULL, NULL),
(25, 4, '问吧企业推荐', 'ask-003', NULL, NULL, NULL),
(26, 4, '问吧设计师推荐', 'ask-004', NULL, NULL, NULL),
(28, 5, '商城首页右侧品牌推荐', 'mall-brand-001', NULL, NULL, NULL),
(34, 5, '商城首页第一楼商品推荐', 'mall-floor-001', NULL, NULL, NULL),
(40, 5, '行业新闻推荐', 'mall-hynew-001', NULL, NULL, NULL),
(41, 5, '商城右侧商家推荐', 'mall-right-company001', NULL, NULL, NULL),
(42, 5, '商城广告下面商铺推荐', 'mall-company-002', NULL, NULL, NULL),
(43, 5, '商城首页第二批产品推荐', 'mall-floor-002', NULL, NULL, NULL),
(44, 6, '案例热门案例', 'case-hot-001', NULL, NULL, NULL),
(45, 3, '学装修行业资讯', 'content-hynew-001', NULL, NULL, NULL),
(46, 3, '装修准备左边大图推荐位', 'content-zbleft-001', NULL, NULL, NULL),
(47, 3, '学装修装修准备风格推荐', 'content-zbfg-001', NULL, NULL, NULL),
(48, 3, '学装修装修准备中注意事项', 'content-zbsx-001', NULL, NULL, NULL),
(49, 3, '学装修装修准备视频学装修', 'content-zbsp-001', NULL, NULL, NULL),
(50, 3, '学装修家装进行中左边大图', 'content-zingleft-001', NULL, NULL, NULL),
(51, 3, '学装修装修进行中装修常识', 'content-zingcs-001', NULL, NULL, NULL),
(52, 3, '学装修装修进行中材料选购', 'content-zingcl-001', NULL, NULL, NULL),
(53, 3, '学装修装修进行中装修风水', 'content-zingfs-001', NULL, NULL, NULL),
(54, 3, '学装修装修结束左边大图', 'content-zoverleft-001', NULL, NULL, NULL),
(55, 3, '学装修装修结束养护秘籍', 'content-zxover-miji', NULL, NULL, NULL),
(56, 3, '学装修装修结束家居健康', 'content-zxover-jk', NULL, NULL, NULL),
(57, 3, '学装修装修结束家具篇', 'content-zxover-jiajup', NULL, NULL, NULL),
(58, 3, '学装修装修结束软装篇', 'content-zxover-rz', NULL, NULL, NULL),
(59, 3, '学装修装修结束品牌电器', 'content-zxover-dq', NULL, NULL, NULL),
(60, 3, '学装修 首页装修日记推荐', 'content-index-diary', NULL, NULL, NULL),
(61, 3, '学装修首页问答推荐', 'content-index-ask', NULL, NULL, NULL),
(62, 1, '首页第一屏轮播文章推荐', 'index-lunbo-001', NULL, NULL, NULL),
(63, 1, '首页第一屏热点新闻推送', 'index-hot-news01', NULL, NULL, NULL),
(64, 1, '首页热点下面公司推荐', 'index-company-001', NULL, NULL, NULL),
(65, 1, '首页行业资讯推荐', 'index-hynews-001', NULL, NULL, NULL),
(66, 1, '网站公告推荐', 'index-gg-001', NULL, NULL, NULL),
(67, 1, '首页第一屏专家导购', 'index-zjdg-001', NULL, NULL, NULL),
(68, 1, '首页明星商家推荐', 'index-mx-company', NULL, NULL, NULL),
(69, 1, '首页招标下公司推荐', 'index-company-zhaobiao', NULL, NULL, NULL),
(70, 1, '首页公司左侧大图案例推荐', 'index-company-left01', NULL, NULL, NULL),
(71, 1, '最新加盟公司推荐', 'index-company-new01', NULL, NULL, NULL),
(72, 1, '首页口碑商家中的最新资讯', 'index-company-zx001', NULL, NULL, NULL),
(73, 1, '首页口碑装修公司推荐', 'index-company-kb01', NULL, NULL, NULL),
(74, 1, '首页设计师推荐', 'index-designer-001', NULL, NULL, NULL),
(75, 1, '首页材料公司 最新加盟公司', 'index-material-zx01', NULL, NULL, NULL),
(76, 1, '首页材料商商家资讯', 'index-material-sjzx', NULL, NULL, NULL),
(77, 1, '首页材料资讯下材料商推荐', 'index-material-zxxgs', NULL, NULL, NULL),
(78, 1, '首页材料商活动商家', 'index-material-huodong', NULL, NULL, NULL),
(79, 1, '首页材料产品推荐左侧', 'index-products-01', NULL, NULL, NULL),
(80, 1, '首页正在做活动的商品', 'index-products-02', NULL, NULL, NULL),
(81, 1, '首页装修学堂左侧文章推荐', 'index-xt-left', NULL, NULL, NULL),
(82, 1, '首页装修学堂中间内容推荐', 'index-xt-zj', NULL, NULL, NULL),
(83, 1, '首页工具下面的几条新闻推荐', 'index-tooldown-01', NULL, NULL, NULL),
(84, 1, '首页案例菜单栏目推荐', 'index-casemenu-01', NULL, NULL, NULL),
(85, 1, '首页案例左边大图轮播', 'index-case-left01', NULL, NULL, NULL),
(86, 1, '首页案例推荐位右侧', 'index-case-right01', NULL, NULL, NULL),
(87, 1, '首页底部风水推荐', 'index-down-news1', NULL, NULL, NULL),
(88, 1, '首页底部装修流程推荐', 'index-down-news2', NULL, NULL, NULL),
(89, 1, '首页底部问答推荐', 'index-down-ask', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `zx_sensitive_word`
--

CREATE TABLE IF NOT EXISTS `zx_sensitive_word` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `zx_sensitive_word`
--

INSERT INTO `zx_sensitive_word` (`id`, `word`) VALUES
(1, '习近平'),
(2, '共产党');

-- --------------------------------------------------------

--
-- 表的结构 `zx_system_logs`
--

CREATE TABLE IF NOT EXISTS `zx_system_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `url` varchar(1024) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `raw_data` text,
  `processed_data` text,
  `ip` varchar(20) DEFAULT NULL,
  `t` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=70 ;

--
-- 转存表中的数据 `zx_system_logs`
--

INSERT INTO `zx_system_logs` (`id`, `username`, `url`, `title`, `raw_data`, `processed_data`, `ip`, `t`) VALUES
(1, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432979678),
(2, 'admin', '/admin/index.php?ctl=seo', '查看了SEO配置', 'null', 'null', '127.0.0.1', 1432979680),
(3, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432979681),
(4, 'admin', '/admin/index.php?ctl=seo', '查看了SEO配置', 'null', 'null', '127.0.0.1', 1432979681),
(5, 'admin', '/admin/index.php?ctl=site&act=uc', '查看了UC整合设置', 'null', 'null', '127.0.0.1', 1432979685),
(6, 'admin', '/admin/index.php?ctl=site&act=authority', '查看了会员权限设置', 'null', 'null', '127.0.0.1', 1432979686),
(7, 'admin', '/admin/index.php?ctl=site&act=mail', '查看了EMAIL设置', 'null', 'null', '127.0.0.1', 1432979686),
(8, 'admin', '/admin/index.php?ctl=site&act=sms', '查看了短信设置', 'null', 'null', '127.0.0.1', 1432979686),
(9, 'admin', '/admin/index.php?ctl=site&act=watermark', '查看了水印设置', 'null', 'null', '127.0.0.1', 1432979687),
(10, 'admin', '/admin/index.php?ctl=area', '查看了区域列表', 'null', 'null', '127.0.0.1', 1432979688),
(11, 'admin', '/admin/index.php?ctl=sensitiveWord', '查看了敏感词列表', 'null', 'null', '127.0.0.1', 1432979689),
(12, 'admin', '/admin/index.php?ctl=injection', '查看了防注水设置列表', 'null', 'null', '127.0.0.1', 1432979690),
(13, 'admin', '/admin/index.php?ctl=group', '查看了管理员角色列表', 'null', 'null', '127.0.0.1', 1432979691),
(14, 'admin', '/admin/index.php?ctl=admin', '查看了管理员列表', 'null', 'null', '127.0.0.1', 1432979692),
(15, 'admin', '/admin/index.php?ctl=privilegeGroup', '查看了权限组列表', 'null', 'null', '127.0.0.1', 1432979693),
(16, 'admin', '/admin/index.php?ctl=privilege', '查看了权限列表', 'null', 'null', '127.0.0.1', 1432979694),
(17, 'admin', '/admin/index.php?ctl=model', '打开了模块生成器', 'null', 'null', '127.0.0.1', 1432979696),
(18, 'admin', '/admin/index.php?ctl=model', '打开了模块生成器', 'null', 'null', '127.0.0.1', 1432979697),
(19, 'admin', '/admin/index.php?ctl=message', '查看了用户留言投诉列表', 'null', 'null', '127.0.0.1', 1432979698),
(20, 'admin', '/admin/index.php?ctl=integralShop', '查看了积分商城列表', 'null', 'null', '127.0.0.1', 1432979699),
(21, 'admin', '/admin/index.php?ctl=products', '查看了产品列表', 'null', 'null', '127.0.0.1', 1432979700),
(22, 'admin', '/admin/index.php?ctl=ask', '查看了问答列表', 'null', 'null', '127.0.0.1', 1432979701),
(23, 'admin', '/admin/index.php?ctl=bidding', '查看了招标列表', 'null', 'null', '127.0.0.1', 1432979701),
(24, 'admin', '/admin/index.php?ctl=users', '查看了用户列表', 'null', 'null', '127.0.0.1', 1432979704),
(25, 'admin', '/admin/index.php?ctl=activity', '查看了团购列表', 'null', 'null', '127.0.0.1', 1432979706),
(26, 'admin', '/admin/index.php?ctl=content', '查看了文章列表', 'null', 'null', '127.0.0.1', 1432979707),
(27, 'admin', '/admin/index.php?ctl=recommendSign', '查看了推荐位', 'null', 'null', '127.0.0.1', 1432979709),
(28, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432979710),
(29, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432979711),
(30, 'admin', '/admin/index.php?ctl=seo', '查看了SEO配置', 'null', 'null', '127.0.0.1', 1432979722),
(31, 'admin', '/admin/index.php?ctl=site&act=uc', '查看了UC整合设置', 'null', 'null', '127.0.0.1', 1432979723),
(32, 'admin', '/admin/index.php?ctl=seo', '查看了SEO配置', 'null', 'null', '127.0.0.1', 1432979725),
(33, 'admin', '/admin/index.php?ctl=admin&act=edit2', '打开了个人资料编辑页面', 'null', 'null', '127.0.0.1', 1432979751),
(34, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432979753),
(35, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432979754),
(36, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432979755),
(37, 'admin', '/admin/index.php?ctl=seo', '查看了SEO配置', 'null', 'null', '127.0.0.1', 1432979755),
(38, 'admin', '/admin/index.php?ctl=seo', '查看了SEO配置', 'null', 'null', '127.0.0.1', 1432979756),
(39, 'admin', '/admin/index.php?ctl=seo', '查看了SEO配置', 'null', 'null', '127.0.0.1', 1432979756),
(40, 'admin', '/admin/index.php?ctl=site&act=uc', '查看了UC整合设置', 'null', 'null', '127.0.0.1', 1432979761),
(41, 'admin', '/admin/index.php?ctl=site&act=authority', '查看了会员权限设置', 'null', 'null', '127.0.0.1', 1432979762),
(42, 'admin', '/admin/index.php?ctl=site&act=mail', '查看了EMAIL设置', 'null', 'null', '127.0.0.1', 1432979763),
(43, 'admin', '/admin/index.php?ctl=site&act=uc', '查看了UC整合设置', 'null', 'null', '127.0.0.1', 1432979763),
(44, 'admin', '/admin/index.php?ctl=site&act=authority', '查看了会员权限设置', 'null', 'null', '127.0.0.1', 1432979764),
(45, 'admin', '/admin/index.php?ctl=site&act=sms', '查看了短信设置', 'null', 'null', '127.0.0.1', 1432979766),
(46, 'admin', '/admin/index.php?ctl=site&act=watermark', '查看了水印设置', 'null', 'null', '127.0.0.1', 1432979766),
(47, 'admin', '/admin/index.php?ctl=activity', '查看了团购列表', 'null', 'null', '127.0.0.1', 1432979768),
(48, 'admin', '/admin/index.php?ctl=content', '查看了文章列表', 'null', 'null', '127.0.0.1', 1432979769),
(49, 'admin', '/admin/index.php?ctl=recommendSign', '查看了推荐位', 'null', 'null', '127.0.0.1', 1432979770),
(50, 'admin', '/admin/index.php?ctl=content', '查看了文章列表', 'null', 'null', '127.0.0.1', 1432979771),
(51, 'admin', '/admin/index.php?ctl=activity', '查看了团购列表', 'null', 'null', '127.0.0.1', 1432979772),
(52, 'admin', '/admin/index.php?ctl=links', '查看友情链接列表', 'null', 'null', '127.0.0.1', 1432979774),
(53, 'admin', '/admin/index.php?ctl=links', '查看友情链接列表', 'null', 'null', '127.0.0.1', 1432979775),
(54, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432980308),
(55, 'admin', '/admin/index.php?ctl=recommendSign', '查看了推荐位', 'null', 'null', '127.0.0.1', 1432980308),
(56, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432980309),
(57, 'admin', '/admin/index.php?ctl=recommendSign', '查看了推荐位', 'null', 'null', '127.0.0.1', 1432980326),
(58, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432980327),
(59, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432980702),
(60, 'admin', '/admin/index.php?ctl=recommendSign', '查看了推荐位', 'null', 'null', '127.0.0.1', 1432980702),
(61, 'admin', '/admin/index.php?ctl=site', '查看了站点配置', 'null', 'null', '127.0.0.1', 1432980704),
(62, 'admin', '/admin/index.php?ctl=recommendSign', '查看了推荐位', 'null', 'null', '127.0.0.1', 1432980706),
(63, 'admin', '/admin/index.php?ctl=content', '查看了文章列表', 'null', 'null', '127.0.0.1', 1432980706),
(64, 'admin', '/admin/index.php?ctl=activity', '查看了团购列表', 'null', 'null', '127.0.0.1', 1432980707),
(65, 'admin', '/admin/index.php?ctl=links', '查看友情链接列表', 'null', 'null', '127.0.0.1', 1432980710),
(66, 'admin', '/admin/index.php?ctl=links&act=del&id=2&back_url=index.php%3Fctl%3Dlinks%26act%3Dmain%26page%3D1', '删除了友情链接', '{"id":"2","link_name":"PHPu5bb6u5c45u5b98u65b9","link_url":"http://www.haoid.cn","link_pic":"","link_order":"2"}', '[]', '127.0.0.1', 1432980850),
(67, 'admin', '/admin/index.php?ctl=links&act=main&page=1', '查看友情链接列表', 'null', 'null', '127.0.0.1', 1432980851),
(68, 'admin', '/admin/index.php?ctl=links&act=del&id=1&back_url=index.php%3Fctl%3Dlinks%26act%3Dmain%26page%3D1', '删除了友情链接', '{"id":"1","link_name":"PHPu5bb6u5c45u8bbau575b","link_url":"http://www.haoid.cn","link_pic":"","link_order":"1"}', '[]', '127.0.0.1', 1432980852),
(69, 'admin', '/admin/index.php?ctl=links&act=main&page=1', '查看友情链接列表', 'null', 'null', '127.0.0.1', 1432980853);

-- --------------------------------------------------------

--
-- 表的结构 `zx_team`
--

CREATE TABLE IF NOT EXISTS `zx_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `face_pic` varchar(256) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `addr` varchar(256) DEFAULT NULL,
  `info` text,
  `orderby` int(11) DEFAULT NULL,
  `is_security` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_team`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_users`
--

CREATE TABLE IF NOT EXISTS `zx_users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `realname` varchar(32) DEFAULT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `sex` tinyint(1) DEFAULT '1',
  `type` tinyint(4) DEFAULT '0' COMMENT '会员类型',
  `rank_id` int(11) DEFAULT '1',
  `day` int(11) DEFAULT '0',
  `gold` int(11) DEFAULT '0' COMMENT '可看招标的金币数',
  `num` int(11) DEFAULT '0',
  `reg_t` int(11) DEFAULT NULL,
  `reg_ip` varchar(20) DEFAULT NULL,
  `last_t` int(11) DEFAULT NULL,
  `last_ip` varchar(20) DEFAULT NULL,
  `fail_num` tinyint(4) DEFAULT '0',
  `lock_t` int(11) DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_users`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_users_ex`
--

CREATE TABLE IF NOT EXISTS `zx_users_ex` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `face_pic` varchar(128) DEFAULT NULL,
  `is_authentication` tinyint(1) DEFAULT '0' COMMENT '1代表已经验证',
  `certificate` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `zx_users_ex`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_user_ranks`
--

CREATE TABLE IF NOT EXISTS `zx_user_ranks` (
  `rank_id` int(10) NOT NULL AUTO_INCREMENT,
  `rank_name` varchar(32) DEFAULT NULL,
  `num` mediumint(9) unsigned DEFAULT '0' COMMENT '可以看招标的数量',
  `day` smallint(6) unsigned DEFAULT '0' COMMENT '可以看招标的天数',
  `gold` int(11) DEFAULT '0' COMMENT '可看招标的金币数',
  `icon` varchar(255) DEFAULT NULL COMMENT '等级图标',
  `icon1` varchar(255) DEFAULT NULL COMMENT '到期图标',
  `prices` int(11) DEFAULT '0' COMMENT '开通价格',
  PRIMARY KEY (`rank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_user_ranks`
--


-- --------------------------------------------------------

--
-- 表的结构 `zx_user_rank_logs`
--

CREATE TABLE IF NOT EXISTS `zx_user_rank_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `rank_id` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `zx_user_rank_logs`
--

