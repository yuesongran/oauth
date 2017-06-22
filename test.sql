/*
Navicat MySQL Data Transfer

Source Server         : 本地数据库
Source Server Version : 50711
Source Host           : 127.0.0.1:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2017-06-22 11:42:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for oauth_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL COMMENT 'access_token 用户调取API密钥',
  `client_id` varchar(80) NOT NULL COMMENT 'AppId',
  `openID` varchar(255) DEFAULT NULL COMMENT '用户openID',
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '过期时间',
  `scope` varchar(2000) DEFAULT NULL COMMENT '权限范围',
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for oauth_authorization_codes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_authorization_codes`;
CREATE TABLE `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL COMMENT '授权码',
  `client_id` varchar(80) NOT NULL COMMENT 'AppId',
  `openID` varchar(255) DEFAULT NULL COMMENT '用户openID',
  `redirect_uri` varchar(2000) DEFAULT NULL COMMENT '回调url',
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '过期时间',
  `scope` varchar(2000) DEFAULT NULL COMMENT '权限范围',
  PRIMARY KEY (`authorization_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for oauth_clients
-- ----------------------------
DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE `oauth_clients` (
  `client_id` varchar(80) NOT NULL COMMENT 'AppId',
  `client_secret` varchar(80) NOT NULL COMMENT 'AppSecret 密钥',
  `redirect_uri` varchar(2000) NOT NULL COMMENT '回调域名',
  `grant_types` varchar(80) DEFAULT NULL COMMENT '授权类型',
  `scope` varchar(100) DEFAULT NULL COMMENT '权限范围',
  `openID` varchar(80) DEFAULT NULL COMMENT '用户openID',
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for oauth_jwt
-- ----------------------------
DROP TABLE IF EXISTS `oauth_jwt`;
CREATE TABLE `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for oauth_refresh_tokens
-- ----------------------------
DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL COMMENT 'refresh_token刷新token密钥',
  `client_id` varchar(80) NOT NULL COMMENT 'AppId',
  `openID` varchar(28) DEFAULT NULL COMMENT '用户openID',
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '超时时间',
  `scope` varchar(2000) DEFAULT NULL COMMENT '权限范围',
  PRIMARY KEY (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for oauth_scopes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_scopes`;
CREATE TABLE `oauth_scopes` (
  `scope` text COMMENT '权限范围',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '是否为默认'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for oauth_users
-- ----------------------------
DROP TABLE IF EXISTS `oauth_users`;
CREATE TABLE `oauth_users` (
  `openID` varchar(28) NOT NULL COMMENT '用户openID',
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `password` varchar(2000) NOT NULL COMMENT '密码',
  `corpid` bigint(20) NOT NULL COMMENT '关联公司id',
  PRIMARY KEY (`openID`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
