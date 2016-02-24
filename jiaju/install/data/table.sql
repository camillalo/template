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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_ad_site`
--

CREATE TABLE IF NOT EXISTS `zx_ad_site` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_brand`
--

CREATE TABLE IF NOT EXISTS `zx_brand` (
  `brand_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(64) NOT NULL,
  `brand_pic` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_city_areas`
--

CREATE TABLE IF NOT EXISTS `zx_city_areas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `area_name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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

-- --------------------------------------------------------

--
-- 表的结构 `zx_content_tag`
--

CREATE TABLE IF NOT EXISTS `zx_content_tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_group`
--

CREATE TABLE IF NOT EXISTS `zx_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_group_map`
--

CREATE TABLE IF NOT EXISTS `zx_group_map` (
  `group_id` int(11) DEFAULT NULL,
  `privilege_id` int(11) DEFAULT NULL,
  UNIQUE KEY `group_id` (`group_id`,`privilege_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_keywords`
--

CREATE TABLE IF NOT EXISTS `zx_keywords` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyword` (`keyword`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_privilege_group`
--

CREATE TABLE IF NOT EXISTS `zx_privilege_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_recommend_group`
--

CREATE TABLE IF NOT EXISTS `zx_recommend_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zx_sensitive_word`
--

CREATE TABLE IF NOT EXISTS `zx_sensitive_word` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
