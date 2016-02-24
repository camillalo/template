/*
Navicat MySQL Data Transfer

Source Server         : shanghai.huijunet.com
Source Server Version : 50173
Source Host           : shanghai.huijunet.com:33306
Source Database       : hj_wiki

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2015-11-25 10:08:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for api
-- ----------------------------
DROP TABLE IF EXISTS `api`;
CREATE TABLE `api` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cid` int(10) NOT NULL DEFAULT '0',
  `uid` int(10) NOT NULL DEFAULT '0',
  `zh_name` varchar(50) NOT NULL COMMENT '中文名称',
  `en_name` varchar(50) NOT NULL COMMENT '英文名称',
  `url` varchar(255) NOT NULL COMMENT '请求地址',
  `api_type` enum('inside','outside') NOT NULL DEFAULT 'inside' COMMENT '是否是内部接口',
  `request_type` enum('soap','rest') NOT NULL DEFAULT 'rest',
  `desc` text COMMENT '接口说明',
  `create_at` int(10) NOT NULL DEFAULT '0',
  `update_at` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `en_name` (`en_name`) USING BTREE,
  KEY `api_type` (`api_type`,`create_at`,`update_at`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of api
-- ----------------------------
INSERT INTO `api` VALUES ('1', '1', '3', '邮件加入发送队列', 'add_email', '', 'inside', 'rest', '将需要发送的邮件加入队列，稍后将以队列形式发送邮件', '1446446244', '1446689059');
INSERT INTO `api` VALUES ('2', '1', '3', '发送邮件', 'send_email', '', 'inside', 'rest', '将邮件队列中发送失败及未发送的邮件进行发送(一般为自动脚本，无需调用)', '1446690764', '1446691110');
INSERT INTO `api` VALUES ('41', '1', '3', '读取邮件', 'read_email', '', 'inside', 'rest', '读取邮件，返回邮件列表', '1446691373', '1446691373');
INSERT INTO `api` VALUES ('42', '2', '3', '百度地图多人线路数据接口', 'map_line', '', 'inside', 'rest', '用于实时显示多人当前位置及移动线路图', '1447737672', '1447737672');

-- ----------------------------
-- Table structure for api_cat
-- ----------------------------
DROP TABLE IF EXISTS `api_cat`;
CREATE TABLE `api_cat` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `position` int(10) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of api_cat
-- ----------------------------
INSERT INTO `api_cat` VALUES ('1', '邮件相关', '', '0');
INSERT INTO `api_cat` VALUES ('2', '地图相关', null, '0');

-- ----------------------------
-- Table structure for api_param
-- ----------------------------
DROP TABLE IF EXISTS `api_param`;
CREATE TABLE `api_param` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `api_id` int(10) NOT NULL COMMENT '接口ID',
  `is_need` enum('yes','no') DEFAULT 'no',
  `param` varchar(50) NOT NULL COMMENT '参数名称',
  `param_type` enum('json','string','float','int') DEFAULT 'string' COMMENT '参数类型',
  `param_desc` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=213 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of api_param
-- ----------------------------
INSERT INTO `api_param` VALUES ('1', '1', 'yes', 'content', 'string', '邮件的内容');
INSERT INTO `api_param` VALUES ('2', '1', 'no', 'fujian', 'string', '邮件的附件，格式为：\r\n	[{\"file_name\":\"1.jpg\",\"data\":$a},{\"file_name\":\"2.zip\",\"data\":$b}]\r\n$a,$b为文件的字节流（即base64转码）\r\n');
INSERT INTO `api_param` VALUES ('3', '1', 'yes', 'title', 'string', '邮件的标题');
INSERT INTO `api_param` VALUES ('4', '1', 'yes', 'send_to', 'string', '收件方邮箱地址');
INSERT INTO `api_param` VALUES ('5', '1', 'yes', 'server', 'string', '邮件服务地址,如smtp.163.com');
INSERT INTO `api_param` VALUES ('6', '1', 'yes', 'server_port', 'string', '服务端口,如25');
INSERT INTO `api_param` VALUES ('7', '1', 'no', 'ssl_port', 'string', '安全端口:如995');
INSERT INTO `api_param` VALUES ('8', '1', 'yes', 'from_name', 'string', '发送人姓名，如不填，默认为收件方地址');
INSERT INTO `api_param` VALUES ('205', '41', 'yes', 'email', 'string', '要读取的邮件地址');
INSERT INTO `api_param` VALUES ('206', '41', 'yes', 'password', 'string', '邮箱密码');
INSERT INTO `api_param` VALUES ('207', '41', 'yes', 'server', 'string', '服务器地址，如：pop.qq.com');
INSERT INTO `api_param` VALUES ('208', '41', 'yes', 'server_port', 'string', '服务器端口，如：110');
INSERT INTO `api_param` VALUES ('209', '41', 'no', 'ssl_port', 'string', '安全端口，如：995');
INSERT INTO `api_param` VALUES ('210', '42', 'yes', 'lat_lng', 'string', '经纬度，中间用 , 隔开如：121.434163,31.230432，前后顺序无关');
INSERT INTO `api_param` VALUES ('211', '42', 'yes', 'member_id', 'string', '用户id');
INSERT INTO `api_param` VALUES ('212', '42', 'yes', 'ip', 'string', '用户ip');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `create_at` int(10) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('2', 'llq', '123', '0');
INSERT INTO `user` VALUES ('3', 'zty', '123', '0');
INSERT INTO `user` VALUES ('4', 'lyx', '123', '0');
INSERT INTO `user` VALUES ('5', 'lpf', '123', '0');
