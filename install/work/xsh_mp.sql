/*
Navicat MySQL Data Transfer

Source Server         : develop
Source Server Version : 50711
Source Host           : 120.24.70.73:3306
Source Database       : xsh_mp

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2017-12-07 21:10:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `advert_base`
-- ----------------------------
DROP TABLE IF EXISTS `advert_base`;
CREATE TABLE `advert_base` (
  `tag` varchar(8) CHARACTER SET utf8 NOT NULL COMMENT '标识',
  `title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '标题',
  `desc` text CHARACTER SET utf8 NOT NULL COMMENT '描述',
  `start_time` int(11) unsigned NOT NULL COMMENT '开始时间戳',
  `end_time` int(11) unsigned NOT NULL COMMENT '结束时间戳',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='广告基本信息表';

-- ----------------------------
-- Table structure for `advert_content`
-- ----------------------------
DROP TABLE IF EXISTS `advert_content`;
CREATE TABLE `advert_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(8) CHARACTER SET utf8 NOT NULL COMMENT '广告标识',
  `provider_tag` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '服务商标识',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '用户ID',
  `title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '内容',
  `image` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '图片链接',
  `url` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '外部链接',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序字段,数字越大越在前',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  KEY `tag` (`tag`),
  KEY `uid` (`uid`),
  KEY `ptag` (`provider_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='广告内容表';

-- ----------------------------
-- Table structure for `attachment_base`
-- ----------------------------
DROP TABLE IF EXISTS `attachment_base`;
CREATE TABLE `attachment_base` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `sha1` varchar(64) NOT NULL COMMENT '附件SHA1值',
  `mime_type` varchar(45) NOT NULL COMMENT '附件MIME类型',
  `size` int(11) unsigned NOT NULL COMMENT '附件大小,以B为单位',
  `path` varchar(255) NOT NULL COMMENT '存放路径',
  `refer_num` int(11) NOT NULL DEFAULT '1' COMMENT '引用次数',
  `created` int(11) unsigned NOT NULL COMMENT '上传时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sha1` (`sha1`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='附件基础信息表';

-- ----------------------------
-- Table structure for `attachment_refer`
-- ----------------------------
DROP TABLE IF EXISTS `attachment_refer`;
CREATE TABLE `attachment_refer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '引用ID',
  `upload_name` varchar(255) NOT NULL COMMENT '上传文件名',
  `attach_id` int(10) unsigned NOT NULL COMMENT '附件ID',
  `uid` varchar(32) NOT NULL COMMENT '上传用户ID',
  `created` int(10) unsigned NOT NULL COMMENT '引用时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='附件引用表';

-- ----------------------------
-- Table structure for `bargain_joins`
-- ----------------------------
DROP TABLE IF EXISTS `bargain_joins`;
CREATE TABLE `bargain_joins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '产品ID',
  `tag` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '发起标识',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `money_now` int(11) unsigned NOT NULL,
  `money_decrease` int(11) unsigned NOT NULL COMMENT '砍下的金额,单位为分',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  KEY `tag` (`tag`),
  KEY `uid` (`uid`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='砍价参与记录表';

-- ----------------------------
-- Table structure for `bargain_sponsor`
-- ----------------------------
DROP TABLE IF EXISTS `bargain_sponsor`;
CREATE TABLE `bargain_sponsor` (
  `tag` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '标识',
  `pid` int(11) unsigned NOT NULL COMMENT '产品ID',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '砍价标题',
  `price_origin` int(11) unsigned NOT NULL COMMENT '商品原价,单位为分',
  `price_now` int(11) unsigned NOT NULL COMMENT '当前价格,单位为分',
  `price_min` int(11) unsigned NOT NULL COMMENT '最小价格,单位为分',
  `interval_time` int(11) unsigned NOT NULL COMMENT '每次砍价间隔时间,单位为秒',
  `decrease_min` int(11) unsigned NOT NULL COMMENT '单次最低砍价金额,单位为分',
  `decrease_max` int(11) unsigned NOT NULL COMMENT '单次最高砍价金额,单位为分',
  `join_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '参与砍价的次数',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `link_man` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '联系人名称',
  `link_tel` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '联系人电话',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`tag`),
  KEY `pid` (`pid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='砍价发起记录表';

-- ----------------------------
-- Table structure for `config`
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置项名',
  `data` text COMMENT '配置数据，json格式',
  `updated` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='配置数据表';

-- ----------------------------
-- Table structure for `day_stat_money`
-- ----------------------------
DROP TABLE IF EXISTS `day_stat_money`;
CREATE TABLE `day_stat_money` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) NOT NULL COMMENT '用户ID',
  `type` tinyint(3) unsigned NOT NULL COMMENT '统计类型',
  `money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '金额,单位为分',
  `created` int(11) unsigned NOT NULL COMMENT '当日零点时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nunique` (`uid`,`type`,`created`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每日金额统计表';

-- ----------------------------
-- Table structure for `day_stat_order`
-- ----------------------------
DROP TABLE IF EXISTS `day_stat_order`;
CREATE TABLE `day_stat_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '卖家用户ID',
  `num` int(11) unsigned NOT NULL COMMENT '数量',
  `created` int(11) unsigned NOT NULL COMMENT '当天0点时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dayunique` (`type`,`uid`,`created`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单数量日统计表';

-- ----------------------------
-- Table structure for `day_stat_product`
-- ----------------------------
DROP TABLE IF EXISTS `day_stat_product`;
CREATE TABLE `day_stat_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) NOT NULL COMMENT '用户ID',
  `type` tinyint(3) unsigned NOT NULL COMMENT '统计类型',
  `ptype` tinyint(3) unsigned NOT NULL COMMENT '产品类型',
  `num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  `created` int(11) unsigned NOT NULL COMMENT '当日零点时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nunique` (`uid`,`type`,`ptype`,`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每日产品数量统计表';

-- ----------------------------
-- Table structure for `feedback`
-- ----------------------------
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型',
  `content_id` varchar(32) NOT NULL DEFAULT '' COMMENT '内容ID',
  `content` text NOT NULL COMMENT '内容',
  `images` text NOT NULL COMMENT '图片ID',
  `uid` char(32) NOT NULL DEFAULT '' COMMENT '用户ID',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系方式',
  `status` tinyint(3) unsigned NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `reply_content` text NOT NULL COMMENT '回复内容',
  `reply_uid` char(32) NOT NULL DEFAULT '0' COMMENT '回复者ID',
  `reply_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '回复时间戳',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='信息反馈表';

-- ----------------------------
-- Table structure for `group_joins`
-- ----------------------------
DROP TABLE IF EXISTS `group_joins`;
CREATE TABLE `group_joins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '团标识',
  `pid` int(11) unsigned NOT NULL COMMENT '产品ID',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `utype` tinyint(3) unsigned NOT NULL COMMENT '用户类型 1:团长 2:团员',
  `pnum` int(11) unsigned NOT NULL COMMENT '商品数量',
  `order_sn` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '订单单号',
  `pay_sn` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '支付单号',
  `refund_sn` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '退款单号',
  `refund_money` int(11) NOT NULL DEFAULT '0' COMMENT '退款金额,单位为分',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_sn` (`order_sn`),
  UNIQUE KEY `pay_sn` (`pay_sn`),
  KEY `tag` (`tag`),
  KEY `pid` (`pid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for `group_sponsors`
-- ----------------------------
DROP TABLE IF EXISTS `group_sponsors`;
CREATE TABLE `group_sponsors` (
  `tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '标识',
  `pid` int(11) unsigned NOT NULL COMMENT '产品ID',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `title` text CHARACTER SET utf8mb4 NOT NULL COMMENT '拼团标题',
  `price_origin` int(11) unsigned NOT NULL COMMENT '拼团原价,单位为分',
  `price` int(11) unsigned NOT NULL COMMENT '参团价格,单位为分',
  `ptnum` int(11) unsigned NOT NULL COMMENT '拼团商品总数量',
  `effective_times` int(11) unsigned NOT NULL COMMENT '团有效时间,单位为秒',
  `total_num` int(11) unsigned NOT NULL COMMENT '参团总人数',
  `remaining_num` int(11) NOT NULL COMMENT '参团剩余人数',
  `payed_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成支付人数',
  `expired` int(11) unsigned NOT NULL COMMENT '团过期时间戳',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`tag`),
  KEY `uid` (`uid`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='拼团发起记录表';

-- ----------------------------
-- Table structure for `logistics_base`
-- ----------------------------
DROP TABLE IF EXISTS `logistics_base`;
CREATE TABLE `logistics_base` (
  `sn` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '物流单号',
  `object_id` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '关联对象ID',
  `company_tag` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '物流公司标识',
  `company_name` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '物流公司名称',
  `company_tel` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '物流公司电话',
  `company_sn` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '物流公司单号',
  `link_man` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '联系人',
  `link_tel` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '联系电话',
  `link_address` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '联系地址',
  `exp_info` text CHARACTER SET utf8 NOT NULL COMMENT '物流详情',
  `latest_search` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最新查询时间戳',
  `status` tinyint(3) NOT NULL COMMENT '物流状态',
  `autoconsume_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '自动消费状态 0:未处理 1:已处理',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`sn`),
  UNIQUE KEY `objectid` (`object_id`) USING BTREE,
  KEY `companysn` (`company_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='物流基本信息表';

-- ----------------------------
-- Table structure for `logistics_company`
-- ----------------------------
DROP TABLE IF EXISTS `logistics_company`;
CREATE TABLE `logistics_company` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '标识',
  `title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '名称',
  `image` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '公司图片',
  `phone` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '电话',
  `url` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '网址',
  `remark` text CHARACTER SET utf8 NOT NULL COMMENT '备注',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态 0:无效 1:有效',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='物流公司信息表';

-- ----------------------------
-- Table structure for `logistics_company_user`
-- ----------------------------
DROP TABLE IF EXISTS `logistics_company_user`;
CREATE TABLE `logistics_company_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `company_tag` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '物流公司标识',
  `use_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用次数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uidtag` (`uid`,`company_tag`),
  KEY `uid` (`uid`),
  KEY `comtag` (`company_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户物流公司关联表';

-- ----------------------------
-- Table structure for `order_base`
-- ----------------------------
DROP TABLE IF EXISTS `order_base`;
CREATE TABLE `order_base` (
  `sn` varchar(100) NOT NULL COMMENT '订单单号',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '订单类型 1:微品商品',
  `uid` varchar(32) NOT NULL COMMENT '购买者用户ID',
  `belong_id` varchar(32) NOT NULL COMMENT '所属者ID',
  `object_id` int(11) unsigned NOT NULL COMMENT '对象ID',
  `object_name` text NOT NULL COMMENT '对象名称',
  `object_price` int(11) unsigned NOT NULL COMMENT '对象单价,价格为分',
  `total_num` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '对象总数量',
  `use_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用数量',
  `auth_code` varchar(15) NOT NULL DEFAULT '' COMMENT '交易授权码',
  `verification_code` varchar(10) NOT NULL DEFAULT '' COMMENT '核销码',
  `verification_sign` varchar(64) DEFAULT NULL COMMENT '核销码签名',
  `link_man` varchar(100) NOT NULL DEFAULT '' COMMENT '联系人',
  `link_tel` varchar(50) NOT NULL DEFAULT '' COMMENT '联系方式',
  `link_address` varchar(200) NOT NULL DEFAULT '' COMMENT '联系地址',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '订单状态：0关闭，1未付款，2已付款，3已确认付款，4已过期',
  `comment_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '评论状态 0:未评论 1:已评论',
  `show_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '前端显示状态 0:不显示 1:显示',
  `examine_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '账务审查状态',
  `balance_money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '余额金额，单位为分',
  `refund_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '退款状态 0:未退款 1:已申请退款',
  `refund_sn` varchar(100) NOT NULL DEFAULT '' COMMENT '退款单号',
  `refund_money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '退款金额,单位为分',
  `pay_way` tinyint(3) unsigned NOT NULL COMMENT '支付方式 0:线下 1:微信 2:支付宝',
  `pay_sn` varchar(100) NOT NULL COMMENT '支付单号',
  `pay_money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '线上支付金额，单位为分',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态 1 已支付 0未支付',
  `remark` text NOT NULL COMMENT '备注信息',
  `desc` text NOT NULL COMMENT '描述信息',
  `end_time` int(11) unsigned NOT NULL COMMENT '订单失效时间',
  `partner_id` varchar(32) NOT NULL DEFAULT '' COMMENT '合伙人ID',
  `tag` varchar(64) NOT NULL DEFAULT '' COMMENT '标识',
  `logistics_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '物流状态 0:不需要 1:需要',
  `logistics_sn` varchar(32) NOT NULL DEFAULT '' COMMENT '物流单号',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`sn`),
  UNIQUE KEY `paysn` (`pay_sn`),
  UNIQUE KEY `verifysign` (`verification_sign`) USING BTREE,
  KEY `uid` (`uid`),
  KEY `belongid` (`belong_id`),
  KEY `object_id` (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单基本信息表';

-- ----------------------------
-- Table structure for `order_history`
-- ----------------------------
DROP TABLE IF EXISTS `order_history`;
CREATE TABLE `order_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(100) NOT NULL COMMENT '订单单号',
  `uid` varchar(32) NOT NULL COMMENT '操作者用户ID',
  `option_type` tinyint(3) unsigned NOT NULL COMMENT '操作类型',
  `option_title` varchar(64) NOT NULL COMMENT '操作名称',
  `option_content` text NOT NULL COMMENT '操作内容,json字符串',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单操作记录表';

-- ----------------------------
-- Table structure for `order_system`
-- ----------------------------
DROP TABLE IF EXISTS `order_system`;
CREATE TABLE `order_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '单号',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型',
  `title` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '标题',
  `price` int(11) unsigned NOT NULL COMMENT '价格,单位为分',
  `pay_sn` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '支付单号',
  `pay_money` int(11) unsigned NOT NULL COMMENT '支付金额,单位为分',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态',
  `expire_time` int(11) unsigned NOT NULL COMMENT '订单过期时间戳',
  `remark` text CHARACTER SET utf8 NOT NULL COMMENT '订单备注',
  `desc` text CHARACTER SET utf8 NOT NULL COMMENT '订单描述',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`),
  UNIQUE KEY `paysn` (`pay_sn`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统付费订单表';

-- ----------------------------
-- Table structure for `pay_history`
-- ----------------------------
DROP TABLE IF EXISTS `pay_history`;
CREATE TABLE `pay_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '交易类型',
  `trade_sn` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '交易单号',
  `out_sn` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '商户单号',
  `money` int(11) unsigned NOT NULL COMMENT '交易金额,单位为分',
  `attach` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '附加数据',
  `content` text CHARACTER SET utf8 NOT NULL COMMENT '支付回调数据,json格式',
  `status` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '交易状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `uid` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '下单用户id',
  `openid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '支付者openid',
  PRIMARY KEY (`id`),
  UNIQUE KEY `otn` (`out_sn`),
  UNIQUE KEY `trade_sn` (`trade_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='支付记录表';

-- ----------------------------
-- Table structure for `product_bargain`
-- ----------------------------
DROP TABLE IF EXISTS `product_bargain`;
CREATE TABLE `product_bargain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '产品ID',
  `price_min` int(11) unsigned NOT NULL COMMENT '最低价格,单位为分',
  `interval_time` int(11) unsigned NOT NULL COMMENT '用户每次砍价间隔时间,单位为秒',
  `bargain_times` int(11) unsigned NOT NULL COMMENT '用户可发起的次数,0为不限制',
  `decrease_min` int(11) unsigned NOT NULL COMMENT '单次最低砍价金额,单位为分',
  `decrease_max` int(11) unsigned NOT NULL COMMENT '单次最高砍价金额,单位为分',
  `decrease_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '砍价总次数',
  `decrease_moneys` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '砍价累计金额,单位为分',
  `join_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '参与砍价的次数',
  `status` tinyint(3) NOT NULL COMMENT '砍价状态,0:取消 1:开启',
  `scollect_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '发起砍价收集状态 0:不收集 1:收集发起者信息',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='砍价详情表';

-- ----------------------------
-- Table structure for `product_base`
-- ----------------------------
DROP TABLE IF EXISTS `product_base`;
CREATE TABLE `product_base` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) NOT NULL COMMENT '所有者ID',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '产品类型',
  `title` text NOT NULL COMMENT '标题',
  `strip_title` text NOT NULL COMMENT '去除html标签后的标题',
  `template_id` int(11) unsigned NOT NULL COMMENT '模板ID',
  `template_content` text NOT NULL COMMENT '模板内容',
  `template_json` text NOT NULL COMMENT '模板参数json',
  `share_image` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '分享图片',
  `inventory_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '库存数量',
  `total_num` int(11) unsigned NOT NULL COMMENT '总数量',
  `limit_num` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '限购数量,0为不限制',
  `buy_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `consume_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '消费数量',
  `price` int(11) unsigned NOT NULL COMMENT '价格,单位为分',
  `start_time` int(11) unsigned NOT NULL COMMENT '开始时间',
  `end_time` int(11) unsigned NOT NULL COMMENT '结束时间',
  `scan_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '图片定时任务扫描时间戳',
  `lng` varchar(32) NOT NULL DEFAULT '' COMMENT '经度',
  `lat` varchar(32) NOT NULL DEFAULT '' COMMENT '纬度',
  `hits_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `ip_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总IP访问量',
  `share_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享次数',
  `pay_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付的订单总数',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态 0:无效 1:有效 -1:删除',
  `logistics_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '物流状态 0:不需要 1:需要',
  `order_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单量',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序,数字越大越在前',
  `barrage_status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '弹幕状态 0:关闭 1:开启',
  `unreal_hitsnum` int(11) NOT NULL DEFAULT '0' COMMENT '虚假浏览量',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL COMMENT '修改时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='产品基本信息表';

-- ----------------------------
-- Table structure for `product_buys`
-- ----------------------------
DROP TABLE IF EXISTS `product_buys`;
CREATE TABLE `product_buys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `product_id` int(11) unsigned NOT NULL COMMENT '产品ID',
  `num` int(11) NOT NULL COMMENT '数量',
  PRIMARY KEY (`id`),
  UNIQUE KEY `up` (`uid`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='产品购买数量表';

-- ----------------------------
-- Table structure for `product_goods`
-- ----------------------------
DROP TABLE IF EXISTS `product_goods`;
CREATE TABLE `product_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '父ID,即基础信息表ID',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL COMMENT '修改时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品扩展信息表';

-- ----------------------------
-- Table structure for `product_group`
-- ----------------------------
DROP TABLE IF EXISTS `product_group`;
CREATE TABLE `product_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '产品ID',
  `join_price` int(11) unsigned NOT NULL COMMENT '参团价格,单位为分',
  `join_user_num` int(11) unsigned NOT NULL COMMENT '参团人数',
  `join_product_num` int(11) NOT NULL COMMENT '参团商品数量',
  `effective_times` int(11) unsigned NOT NULL COMMENT '团有效时间,单位为秒',
  `user_limit` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '用户开团数,0为不限制开团',
  `sponsor_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发起拼团的次数',
  `complete_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成拼团的次数',
  `complete_product_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '完成拼团的商品数量',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='拼团详情表';

-- ----------------------------
-- Table structure for `product_vote`
-- ----------------------------
DROP TABLE IF EXISTS `product_vote`;
CREATE TABLE `product_vote` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '产品ID',
  `type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '投票类型,1:每个活动在间隔时间内只允许投票一次 2:每个活动每个报名者在间隔时间内允许投票一次',
  `interval_time` int(11) unsigned NOT NULL COMMENT '投票间隔时间,单位为秒',
  `enter_status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '报名状态 0:不允许报名 1:允许报名',
  `enter_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '报名总人数',
  `enter_etime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '报名截止时间戳',
  `enter_review` tinyint(3) NOT NULL DEFAULT '1' COMMENT '用户报名审核状态 0:不需要审核 1:需要审核',
  `vote_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '投票总数',
  `vote_money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '投票总金额,单位为分',
  `gift_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '礼物总数',
  `gift_status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '礼物状态',
  `gift_showstatus` tinyint(3) NOT NULL DEFAULT '1' COMMENT '礼物显示状态 0:不显示 1:显示',
  `exchange_limit` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '奖品兑换总次数,0为不限制',
  `commission` decimal(5,2) unsigned NOT NULL DEFAULT '20.00' COMMENT '提成比例',
  `bulletin_content` text CHARACTER SET utf8mb4 COMMENT '公告内容',
  `bulletin_status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '公告状态',
  `indexshare_title` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '首页分享标题',
  `indexshare_desc` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '首页分享描述',
  `indexshare_image` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '首页分享图片',
  `freelimit_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '免费投票次数限制,0为不限制',
  `freelimit_time` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '免费投票时间限制,单位为分钟',
  `freelimit_area` mediumtext CHARACTER SET utf8 COMMENT '免费投票地区限制',
  `concern_status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '关注状态',
  `concern_desc` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '关注描述',
  `concern_logo` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '关注logo',
  `concern_qr` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '关注二维码',
  `unreal_enternum` int(11) NOT NULL DEFAULT '0' COMMENT '虚假报名数',
  `unreal_votenum` int(11) NOT NULL DEFAULT '0' COMMENT '虚假投票数',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投票详情表';

-- ----------------------------
-- Table structure for `product_vote_gift`
-- ----------------------------
DROP TABLE IF EXISTS `product_vote_gift`;
CREATE TABLE `product_vote_gift` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(8) CHARACTER SET utf8 NOT NULL COMMENT '标识',
  `pid` int(11) unsigned NOT NULL COMMENT '产品ID',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '名称',
  `image` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '图片链接',
  `price` int(11) unsigned NOT NULL COMMENT '价格,单位为分',
  `vote_num` int(11) unsigned NOT NULL COMMENT '票数',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序,数字越小越在前',
  `use_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用数量',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ptag` (`tag`,`pid`),
  KEY `tag` (`tag`),
  KEY `pid` (`pid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投票礼物表';

-- ----------------------------
-- Table structure for `provider_apply`
-- ----------------------------
DROP TABLE IF EXISTS `provider_apply`;
CREATE TABLE `provider_apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '申请类型',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '申请人用户ID',
  `provider_uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '服务商用户ID',
  `link_man` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '联系人',
  `link_tel` varchar(30) CHARACTER SET utf8 NOT NULL COMMENT '联系人电话',
  `remark` text CHARACTER SET utf8 NOT NULL COMMENT '备注信息',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `puid` (`provider_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务商活动申请记录表';

-- ----------------------------
-- Table structure for `provider_base`
-- ----------------------------
DROP TABLE IF EXISTS `provider_base`;
CREATE TABLE `provider_base` (
  `tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '标识',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '服务商名称',
  `address` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '地址',
  `link_man` varchar(80) CHARACTER SET utf8 NOT NULL COMMENT '联系人名称',
  `link_tel` varchar(30) CHARACTER SET utf8 NOT NULL COMMENT '联系人电话',
  `tstyle_path` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT 'template1' COMMENT '模板风格路径',
  `hits_num` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `share_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享量',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `vip_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'vip状态',
  `vip_stime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'vip开通时间戳',
  `vip_etime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'vip到期时间戳',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间戳',
  PRIMARY KEY (`tag`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务商基本信息表';

-- ----------------------------
-- Table structure for `provider_configs`
-- ----------------------------
DROP TABLE IF EXISTS `provider_configs`;
CREATE TABLE `provider_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '服务商标识',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '服务商用户ID',
  `customer_qrimage` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '客服二维码图片链接',
  `plat_qrimage` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '平台二维码链接',
  `plat_image` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '平台logo',
  `share_image` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '分享图片链接',
  `share_title` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分享标题',
  `share_desc` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分享描述',
  `shop_sitetitle` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '商城网站名称',
  `shop_customtel` varchar(200) CHARACTER SET utf8 NOT NULL COMMENT '商城客服电话',
  `shop_customwx` varchar(200) CHARACTER SET utf8 NOT NULL COMMENT '商城客服微信号',
  `svip_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '商家vip开通状态 0:关闭 1:开通',
  `svip_welfare` mediumtext CHARACTER SET utf8mb4 COMMENT '商家vip福利',
  `svip_welfarefront` mediumtext CHARACTER SET utf8mb4 COMMENT '前端商家vip福利',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`),
  KEY `ptag` (`provider_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务商配置表';

-- ----------------------------
-- Table structure for `provider_products`
-- ----------------------------
DROP TABLE IF EXISTS `provider_products`;
CREATE TABLE `provider_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL COMMENT '产品ID',
  `product_type` tinyint(3) unsigned NOT NULL COMMENT '产品类型',
  `product_uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '卖家用户ID',
  `provider_tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '服务商标识',
  `provider_uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '服务商用户ID',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序字段,数字越大越在前',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  KEY `pid` (`product_id`),
  KEY `ptype` (`product_type`),
  KEY `puid` (`product_uid`),
  KEY `provideruid` (`provider_uid`),
  KEY `providertag` (`provider_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务商产品表';

-- ----------------------------
-- Table structure for `provider_renew`
-- ----------------------------
DROP TABLE IF EXISTS `provider_renew`;
CREATE TABLE `provider_renew` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型',
  `title` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '标题',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `pay_sn` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '支付单号',
  `pay_money` int(11) unsigned NOT NULL COMMENT '支付金额,单位为分',
  `continue_time` int(11) unsigned NOT NULL COMMENT '持续时间,单位为秒',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `paysn` (`pay_sn`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务商vip续费记录表';

-- ----------------------------
-- Table structure for `provider_sellers`
-- ----------------------------
DROP TABLE IF EXISTS `provider_sellers`;
CREATE TABLE `provider_sellers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '服务商标识',
  `provider_uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '服务商用户ID',
  `seller_uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '卖家用户ID',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `psuid` (`provider_uid`,`seller_uid`),
  KEY `puid` (`provider_uid`),
  KEY `suid` (`seller_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务商下属卖家用户表';

-- ----------------------------
-- Table structure for `provider_svip_price`
-- ----------------------------
DROP TABLE IF EXISTS `provider_svip_price`;
CREATE TABLE `provider_svip_price` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `provider_tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '服务商标识',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型',
  `money` int(11) unsigned NOT NULL COMMENT '金额,单位为分',
  `continue_time` int(11) unsigned NOT NULL COMMENT '延续时间,单位为秒',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tagtype` (`provider_tag`,`type`),
  KEY `ptag` (`provider_tag`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务商商家vip价格表';

-- ----------------------------
-- Table structure for `provider_svip_renew`
-- ----------------------------
DROP TABLE IF EXISTS `provider_svip_renew`;
CREATE TABLE `provider_svip_renew` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `provider_tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '服务商标识',
  `type` tinyint(3) unsigned NOT NULL COMMENT '续费类型',
  `pay_sn` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '支付单号',
  `pay_money` int(11) unsigned NOT NULL COMMENT '支付金额,单位为分',
  `system_money` int(11) unsigned NOT NULL COMMENT '系统收入金额,单位为分',
  `provider_money` int(11) unsigned NOT NULL COMMENT '服务商收入金额,单位为分',
  `continue_time` int(11) unsigned NOT NULL COMMENT '续费时间,单位为秒',
  `referee` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '推荐人',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `ptag` (`provider_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务商商家vip续费记录表';

-- ----------------------------
-- Table structure for `refund_base`
-- ----------------------------
DROP TABLE IF EXISTS `refund_base`;
CREATE TABLE `refund_base` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL COMMENT '退款类型',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `object_id` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '退款对象ID',
  `object_bid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '退款对象所属者ID',
  `object_name` text CHARACTER SET utf8 NOT NULL COMMENT '退款对象名称',
  `object_price` int(11) unsigned NOT NULL COMMENT '对象单价,单位为分',
  `object_num` int(11) unsigned NOT NULL COMMENT '对象总数量',
  `object_rnum` int(11) unsigned NOT NULL COMMENT '对象退款数量',
  `pay_sn` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '付款单号',
  `pay_money` int(11) unsigned NOT NULL COMMENT '支付金额,单位为分',
  `refund_sn` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '退款单号',
  `refund_money` int(11) unsigned NOT NULL COMMENT '退款金额,单位为分',
  `remark` text CHARACTER SET utf8 NOT NULL COMMENT '备注',
  `status` tinyint(3) unsigned NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `refundsn` (`refund_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for `refund_history`
-- ----------------------------
DROP TABLE IF EXISTS `refund_history`;
CREATE TABLE `refund_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refund_sn` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '退款单号',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `option_type` tinyint(3) unsigned NOT NULL COMMENT '操作类型',
  `option_title` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '操作标题',
  `option_content` text CHARACTER SET utf8 NOT NULL COMMENT '操作内容,json字符串',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  KEY `refundsn` (`refund_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for `regions`
-- ----------------------------
DROP TABLE IF EXISTS `regions`;
CREATE TABLE `regions` (
  `tag` varchar(32) NOT NULL COMMENT '标识',
  `level` tinyint(3) unsigned NOT NULL COMMENT '级别',
  `title` varchar(150) NOT NULL COMMENT '名称',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户收货地址区域表';

INSERT INTO `regions` VALUES ('001', '1', '北京', '0');
INSERT INTO `regions` VALUES ('001001', '2', '北京市', '0');
INSERT INTO `regions` VALUES ('001001001', '3', '东城区', '0');
INSERT INTO `regions` VALUES ('001001002', '3', '西城区', '0');
INSERT INTO `regions` VALUES ('001001003', '3', '朝阳区', '0');
INSERT INTO `regions` VALUES ('001001004', '3', '丰台区', '0');
INSERT INTO `regions` VALUES ('001001005', '3', '石景山区', '0');
INSERT INTO `regions` VALUES ('001001006', '3', '海淀区', '0');
INSERT INTO `regions` VALUES ('001001007', '3', '门头沟区', '0');
INSERT INTO `regions` VALUES ('001001008', '3', '房山区', '0');
INSERT INTO `regions` VALUES ('001001009', '3', '通州区', '0');
INSERT INTO `regions` VALUES ('001001010', '3', '顺义区', '0');
INSERT INTO `regions` VALUES ('001001011', '3', '昌平区', '0');
INSERT INTO `regions` VALUES ('001001012', '3', '大兴区', '0');
INSERT INTO `regions` VALUES ('001001013', '3', '怀柔区', '0');
INSERT INTO `regions` VALUES ('001001014', '3', '平谷区', '0');
INSERT INTO `regions` VALUES ('001001015', '3', '密云县', '0');
INSERT INTO `regions` VALUES ('001001016', '3', '延庆县', '0');
INSERT INTO `regions` VALUES ('002', '1', '天津', '0');
INSERT INTO `regions` VALUES ('002001', '2', '天津市', '0');
INSERT INTO `regions` VALUES ('002001001', '3', '和平区', '0');
INSERT INTO `regions` VALUES ('002001002', '3', '河东区', '0');
INSERT INTO `regions` VALUES ('002001003', '3', '河西区', '0');
INSERT INTO `regions` VALUES ('002001004', '3', '南开区', '0');
INSERT INTO `regions` VALUES ('002001005', '3', '河北区', '0');
INSERT INTO `regions` VALUES ('002001006', '3', '红桥区', '0');
INSERT INTO `regions` VALUES ('002001007', '3', '东丽区', '0');
INSERT INTO `regions` VALUES ('002001008', '3', '西青区', '0');
INSERT INTO `regions` VALUES ('002001009', '3', '津南区', '0');
INSERT INTO `regions` VALUES ('002001010', '3', '北辰区', '0');
INSERT INTO `regions` VALUES ('002001011', '3', '武清区', '0');
INSERT INTO `regions` VALUES ('002001012', '3', '宝坻区', '0');
INSERT INTO `regions` VALUES ('002001013', '3', '滨海新区', '0');
INSERT INTO `regions` VALUES ('002001014', '3', '宁河县', '0');
INSERT INTO `regions` VALUES ('002001015', '3', '静海县', '0');
INSERT INTO `regions` VALUES ('002001016', '3', '蓟县', '0');
INSERT INTO `regions` VALUES ('003', '1', '河北省', '0');
INSERT INTO `regions` VALUES ('003001', '2', '石家庄市', '0');
INSERT INTO `regions` VALUES ('003001001', '3', '长安区', '0');
INSERT INTO `regions` VALUES ('003001002', '3', '桥西区', '0');
INSERT INTO `regions` VALUES ('003001003', '3', '新华区', '0');
INSERT INTO `regions` VALUES ('003001004', '3', '井陉矿区', '0');
INSERT INTO `regions` VALUES ('003001005', '3', '裕华区', '0');
INSERT INTO `regions` VALUES ('003001006', '3', '藁城区', '0');
INSERT INTO `regions` VALUES ('003001007', '3', '鹿泉区', '0');
INSERT INTO `regions` VALUES ('003001008', '3', '栾城区', '0');
INSERT INTO `regions` VALUES ('003001009', '3', '井陉县', '0');
INSERT INTO `regions` VALUES ('003001010', '3', '正定县', '0');
INSERT INTO `regions` VALUES ('003001011', '3', '行唐县', '0');
INSERT INTO `regions` VALUES ('003001012', '3', '灵寿县', '0');
INSERT INTO `regions` VALUES ('003001013', '3', '高邑县', '0');
INSERT INTO `regions` VALUES ('003001014', '3', '深泽县', '0');
INSERT INTO `regions` VALUES ('003001015', '3', '赞皇县', '0');
INSERT INTO `regions` VALUES ('003001016', '3', '无极县', '0');
INSERT INTO `regions` VALUES ('003001017', '3', '平山县', '0');
INSERT INTO `regions` VALUES ('003001018', '3', '元氏县', '0');
INSERT INTO `regions` VALUES ('003001019', '3', '赵县', '0');
INSERT INTO `regions` VALUES ('003001020', '3', '辛集市', '0');
INSERT INTO `regions` VALUES ('003001021', '3', '晋州市', '0');
INSERT INTO `regions` VALUES ('003001022', '3', '新乐市', '0');
INSERT INTO `regions` VALUES ('003002', '2', '唐山市', '0');
INSERT INTO `regions` VALUES ('003002001', '3', '路南区', '0');
INSERT INTO `regions` VALUES ('003002002', '3', '路北区', '0');
INSERT INTO `regions` VALUES ('003002003', '3', '古冶区', '0');
INSERT INTO `regions` VALUES ('003002004', '3', '开平区', '0');
INSERT INTO `regions` VALUES ('003002005', '3', '丰南区', '0');
INSERT INTO `regions` VALUES ('003002006', '3', '丰润区', '0');
INSERT INTO `regions` VALUES ('003002007', '3', '曹妃甸区', '0');
INSERT INTO `regions` VALUES ('003002008', '3', '滦县', '0');
INSERT INTO `regions` VALUES ('003002009', '3', '滦南县', '0');
INSERT INTO `regions` VALUES ('003002010', '3', '乐亭县', '0');
INSERT INTO `regions` VALUES ('003002011', '3', '迁西县', '0');
INSERT INTO `regions` VALUES ('003002012', '3', '玉田县', '0');
INSERT INTO `regions` VALUES ('003002013', '3', '遵化市', '0');
INSERT INTO `regions` VALUES ('003002014', '3', '迁安市', '0');
INSERT INTO `regions` VALUES ('003003', '2', '秦皇岛市', '0');
INSERT INTO `regions` VALUES ('003003001', '3', '海港区', '0');
INSERT INTO `regions` VALUES ('003003002', '3', '山海关区', '0');
INSERT INTO `regions` VALUES ('003003003', '3', '北戴河区', '0');
INSERT INTO `regions` VALUES ('003003004', '3', '青龙满族自治县', '0');
INSERT INTO `regions` VALUES ('003003005', '3', '昌黎县', '0');
INSERT INTO `regions` VALUES ('003003006', '3', '抚宁县', '0');
INSERT INTO `regions` VALUES ('003003007', '3', '卢龙县', '0');
INSERT INTO `regions` VALUES ('003004', '2', '邯郸市', '0');
INSERT INTO `regions` VALUES ('003004001', '3', '邯山区', '0');
INSERT INTO `regions` VALUES ('003004002', '3', '丛台区', '0');
INSERT INTO `regions` VALUES ('003004003', '3', '复兴区', '0');
INSERT INTO `regions` VALUES ('003004004', '3', '峰峰矿区', '0');
INSERT INTO `regions` VALUES ('003004005', '3', '邯郸县', '0');
INSERT INTO `regions` VALUES ('003004006', '3', '临漳县', '0');
INSERT INTO `regions` VALUES ('003004007', '3', '成安县', '0');
INSERT INTO `regions` VALUES ('003004008', '3', '大名县', '0');
INSERT INTO `regions` VALUES ('003004009', '3', '涉县', '0');
INSERT INTO `regions` VALUES ('003004010', '3', '磁县', '0');
INSERT INTO `regions` VALUES ('003004011', '3', '肥乡县', '0');
INSERT INTO `regions` VALUES ('003004012', '3', '永年县', '0');
INSERT INTO `regions` VALUES ('003004013', '3', '邱县', '0');
INSERT INTO `regions` VALUES ('003004014', '3', '鸡泽县', '0');
INSERT INTO `regions` VALUES ('003004015', '3', '广平县', '0');
INSERT INTO `regions` VALUES ('003004016', '3', '馆陶县', '0');
INSERT INTO `regions` VALUES ('003004017', '3', '魏县', '0');
INSERT INTO `regions` VALUES ('003004018', '3', '曲周县', '0');
INSERT INTO `regions` VALUES ('003004019', '3', '武安市', '0');
INSERT INTO `regions` VALUES ('003005', '2', '邢台市', '0');
INSERT INTO `regions` VALUES ('003005001', '3', '桥东区', '0');
INSERT INTO `regions` VALUES ('003005002', '3', '桥西区', '0');
INSERT INTO `regions` VALUES ('003005003', '3', '邢台县', '0');
INSERT INTO `regions` VALUES ('003005004', '3', '临城县', '0');
INSERT INTO `regions` VALUES ('003005005', '3', '内丘县', '0');
INSERT INTO `regions` VALUES ('003005006', '3', '柏乡县', '0');
INSERT INTO `regions` VALUES ('003005007', '3', '隆尧县', '0');
INSERT INTO `regions` VALUES ('003005008', '3', '任县', '0');
INSERT INTO `regions` VALUES ('003005009', '3', '南和县', '0');
INSERT INTO `regions` VALUES ('003005010', '3', '宁晋县', '0');
INSERT INTO `regions` VALUES ('003005011', '3', '巨鹿县', '0');
INSERT INTO `regions` VALUES ('003005012', '3', '新河县', '0');
INSERT INTO `regions` VALUES ('003005013', '3', '广宗县', '0');
INSERT INTO `regions` VALUES ('003005014', '3', '平乡县', '0');
INSERT INTO `regions` VALUES ('003005015', '3', '威县', '0');
INSERT INTO `regions` VALUES ('003005016', '3', '清河县', '0');
INSERT INTO `regions` VALUES ('003005017', '3', '临西县', '0');
INSERT INTO `regions` VALUES ('003005018', '3', '南宫市', '0');
INSERT INTO `regions` VALUES ('003005019', '3', '沙河市', '0');
INSERT INTO `regions` VALUES ('003006', '2', '保定市', '0');
INSERT INTO `regions` VALUES ('003006001', '3', '新市区', '0');
INSERT INTO `regions` VALUES ('003006002', '3', '北市区', '0');
INSERT INTO `regions` VALUES ('003006003', '3', '南市区', '0');
INSERT INTO `regions` VALUES ('003006004', '3', '满城县', '0');
INSERT INTO `regions` VALUES ('003006005', '3', '清苑县', '0');
INSERT INTO `regions` VALUES ('003006006', '3', '涞水县', '0');
INSERT INTO `regions` VALUES ('003006007', '3', '阜平县', '0');
INSERT INTO `regions` VALUES ('003006008', '3', '徐水县', '0');
INSERT INTO `regions` VALUES ('003006009', '3', '定兴县', '0');
INSERT INTO `regions` VALUES ('003006010', '3', '唐县', '0');
INSERT INTO `regions` VALUES ('003006011', '3', '高阳县', '0');
INSERT INTO `regions` VALUES ('003006012', '3', '容城县', '0');
INSERT INTO `regions` VALUES ('003006013', '3', '涞源县', '0');
INSERT INTO `regions` VALUES ('003006014', '3', '望都县', '0');
INSERT INTO `regions` VALUES ('003006015', '3', '安新县', '0');
INSERT INTO `regions` VALUES ('003006016', '3', '易县', '0');
INSERT INTO `regions` VALUES ('003006017', '3', '曲阳县', '0');
INSERT INTO `regions` VALUES ('003006018', '3', '蠡县', '0');
INSERT INTO `regions` VALUES ('003006019', '3', '顺平县', '0');
INSERT INTO `regions` VALUES ('003006020', '3', '博野县', '0');
INSERT INTO `regions` VALUES ('003006021', '3', '雄县', '0');
INSERT INTO `regions` VALUES ('003006022', '3', '涿州市', '0');
INSERT INTO `regions` VALUES ('003006023', '3', '定州市', '0');
INSERT INTO `regions` VALUES ('003006024', '3', '安国市', '0');
INSERT INTO `regions` VALUES ('003006025', '3', '高碑店市', '0');
INSERT INTO `regions` VALUES ('003007', '2', '张家口市', '0');
INSERT INTO `regions` VALUES ('003007001', '3', '桥东区', '0');
INSERT INTO `regions` VALUES ('003007002', '3', '桥西区', '0');
INSERT INTO `regions` VALUES ('003007003', '3', '宣化区', '0');
INSERT INTO `regions` VALUES ('003007004', '3', '下花园区', '0');
INSERT INTO `regions` VALUES ('003007005', '3', '宣化县', '0');
INSERT INTO `regions` VALUES ('003007006', '3', '张北县', '0');
INSERT INTO `regions` VALUES ('003007007', '3', '康保县', '0');
INSERT INTO `regions` VALUES ('003007008', '3', '沽源县', '0');
INSERT INTO `regions` VALUES ('003007009', '3', '尚义县', '0');
INSERT INTO `regions` VALUES ('003007010', '3', '蔚县', '0');
INSERT INTO `regions` VALUES ('003007011', '3', '阳原县', '0');
INSERT INTO `regions` VALUES ('003007012', '3', '怀安县', '0');
INSERT INTO `regions` VALUES ('003007013', '3', '万全县', '0');
INSERT INTO `regions` VALUES ('003007014', '3', '怀来县', '0');
INSERT INTO `regions` VALUES ('003007015', '3', '涿鹿县', '0');
INSERT INTO `regions` VALUES ('003007016', '3', '赤城县', '0');
INSERT INTO `regions` VALUES ('003007017', '3', '崇礼县', '0');
INSERT INTO `regions` VALUES ('003008', '2', '承德市', '0');
INSERT INTO `regions` VALUES ('003008001', '3', '双桥区', '0');
INSERT INTO `regions` VALUES ('003008002', '3', '双滦区', '0');
INSERT INTO `regions` VALUES ('003008003', '3', '鹰手营子矿区', '0');
INSERT INTO `regions` VALUES ('003008004', '3', '承德县', '0');
INSERT INTO `regions` VALUES ('003008005', '3', '兴隆县', '0');
INSERT INTO `regions` VALUES ('003008006', '3', '平泉县', '0');
INSERT INTO `regions` VALUES ('003008007', '3', '滦平县', '0');
INSERT INTO `regions` VALUES ('003008008', '3', '隆化县', '0');
INSERT INTO `regions` VALUES ('003008009', '3', '丰宁满族自治县', '0');
INSERT INTO `regions` VALUES ('003008010', '3', '宽城满族自治县', '0');
INSERT INTO `regions` VALUES ('003008011', '3', '围场满族蒙古族自治县', '0');
INSERT INTO `regions` VALUES ('003009', '2', '沧州市', '0');
INSERT INTO `regions` VALUES ('003009001', '3', '新华区', '0');
INSERT INTO `regions` VALUES ('003009002', '3', '运河区', '0');
INSERT INTO `regions` VALUES ('003009003', '3', '沧县', '0');
INSERT INTO `regions` VALUES ('003009004', '3', '青县', '0');
INSERT INTO `regions` VALUES ('003009005', '3', '东光县', '0');
INSERT INTO `regions` VALUES ('003009006', '3', '海兴县', '0');
INSERT INTO `regions` VALUES ('003009007', '3', '盐山县', '0');
INSERT INTO `regions` VALUES ('003009008', '3', '肃宁县', '0');
INSERT INTO `regions` VALUES ('003009009', '3', '南皮县', '0');
INSERT INTO `regions` VALUES ('003009010', '3', '吴桥县', '0');
INSERT INTO `regions` VALUES ('003009011', '3', '献县', '0');
INSERT INTO `regions` VALUES ('003009012', '3', '孟村回族自治县', '0');
INSERT INTO `regions` VALUES ('003009013', '3', '泊头市', '0');
INSERT INTO `regions` VALUES ('003009014', '3', '任丘市', '0');
INSERT INTO `regions` VALUES ('003009015', '3', '黄骅市', '0');
INSERT INTO `regions` VALUES ('003009016', '3', '河间市', '0');
INSERT INTO `regions` VALUES ('003010', '2', '廊坊市', '0');
INSERT INTO `regions` VALUES ('003010001', '3', '安次区', '0');
INSERT INTO `regions` VALUES ('003010002', '3', '广阳区', '0');
INSERT INTO `regions` VALUES ('003010003', '3', '固安县', '0');
INSERT INTO `regions` VALUES ('003010004', '3', '永清县', '0');
INSERT INTO `regions` VALUES ('003010005', '3', '香河县', '0');
INSERT INTO `regions` VALUES ('003010006', '3', '大城县', '0');
INSERT INTO `regions` VALUES ('003010007', '3', '文安县', '0');
INSERT INTO `regions` VALUES ('003010008', '3', '大厂回族自治县', '0');
INSERT INTO `regions` VALUES ('003010009', '3', '霸州市', '0');
INSERT INTO `regions` VALUES ('003010010', '3', '三河市', '0');
INSERT INTO `regions` VALUES ('003011', '2', '衡水市', '0');
INSERT INTO `regions` VALUES ('003011001', '3', '桃城区', '0');
INSERT INTO `regions` VALUES ('003011002', '3', '枣强县', '0');
INSERT INTO `regions` VALUES ('003011003', '3', '武邑县', '0');
INSERT INTO `regions` VALUES ('003011004', '3', '武强县', '0');
INSERT INTO `regions` VALUES ('003011005', '3', '饶阳县', '0');
INSERT INTO `regions` VALUES ('003011006', '3', '安平县', '0');
INSERT INTO `regions` VALUES ('003011007', '3', '故城县', '0');
INSERT INTO `regions` VALUES ('003011008', '3', '景县', '0');
INSERT INTO `regions` VALUES ('003011009', '3', '阜城县', '0');
INSERT INTO `regions` VALUES ('003011010', '3', '冀州市', '0');
INSERT INTO `regions` VALUES ('003011011', '3', '深州市', '0');
INSERT INTO `regions` VALUES ('004', '1', '山西省', '0');
INSERT INTO `regions` VALUES ('004001', '2', '太原市', '0');
INSERT INTO `regions` VALUES ('004001001', '3', '小店区', '0');
INSERT INTO `regions` VALUES ('004001002', '3', '迎泽区', '0');
INSERT INTO `regions` VALUES ('004001003', '3', '杏花岭区', '0');
INSERT INTO `regions` VALUES ('004001004', '3', '尖草坪区', '0');
INSERT INTO `regions` VALUES ('004001005', '3', '万柏林区', '0');
INSERT INTO `regions` VALUES ('004001006', '3', '晋源区', '0');
INSERT INTO `regions` VALUES ('004001007', '3', '清徐县', '0');
INSERT INTO `regions` VALUES ('004001008', '3', '阳曲县', '0');
INSERT INTO `regions` VALUES ('004001009', '3', '娄烦县', '0');
INSERT INTO `regions` VALUES ('004001010', '3', '古交市', '0');
INSERT INTO `regions` VALUES ('004002', '2', '大同市', '0');
INSERT INTO `regions` VALUES ('004002001', '3', '城区', '0');
INSERT INTO `regions` VALUES ('004002002', '3', '矿区', '0');
INSERT INTO `regions` VALUES ('004002003', '3', '南郊区', '0');
INSERT INTO `regions` VALUES ('004002004', '3', '新荣区', '0');
INSERT INTO `regions` VALUES ('004002005', '3', '阳高县', '0');
INSERT INTO `regions` VALUES ('004002006', '3', '天镇县', '0');
INSERT INTO `regions` VALUES ('004002007', '3', '广灵县', '0');
INSERT INTO `regions` VALUES ('004002008', '3', '灵丘县', '0');
INSERT INTO `regions` VALUES ('004002009', '3', '浑源县', '0');
INSERT INTO `regions` VALUES ('004002010', '3', '左云县', '0');
INSERT INTO `regions` VALUES ('004002011', '3', '大同县', '0');
INSERT INTO `regions` VALUES ('004003', '2', '阳泉市', '0');
INSERT INTO `regions` VALUES ('004003001', '3', '城区', '0');
INSERT INTO `regions` VALUES ('004003002', '3', '矿区', '0');
INSERT INTO `regions` VALUES ('004003003', '3', '郊区', '0');
INSERT INTO `regions` VALUES ('004003004', '3', '平定县', '0');
INSERT INTO `regions` VALUES ('004003005', '3', '盂县', '0');
INSERT INTO `regions` VALUES ('004004', '2', '长治市', '0');
INSERT INTO `regions` VALUES ('004004001', '3', '城区', '0');
INSERT INTO `regions` VALUES ('004004002', '3', '郊区', '0');
INSERT INTO `regions` VALUES ('004004003', '3', '长治县', '0');
INSERT INTO `regions` VALUES ('004004004', '3', '襄垣县', '0');
INSERT INTO `regions` VALUES ('004004005', '3', '屯留县', '0');
INSERT INTO `regions` VALUES ('004004006', '3', '平顺县', '0');
INSERT INTO `regions` VALUES ('004004007', '3', '黎城县', '0');
INSERT INTO `regions` VALUES ('004004008', '3', '壶关县', '0');
INSERT INTO `regions` VALUES ('004004009', '3', '长子县', '0');
INSERT INTO `regions` VALUES ('004004010', '3', '武乡县', '0');
INSERT INTO `regions` VALUES ('004004011', '3', '沁县', '0');
INSERT INTO `regions` VALUES ('004004012', '3', '沁源县', '0');
INSERT INTO `regions` VALUES ('004004013', '3', '潞城市', '0');
INSERT INTO `regions` VALUES ('004005', '2', '晋城市', '0');
INSERT INTO `regions` VALUES ('004005001', '3', '城区', '0');
INSERT INTO `regions` VALUES ('004005002', '3', '沁水县', '0');
INSERT INTO `regions` VALUES ('004005003', '3', '阳城县', '0');
INSERT INTO `regions` VALUES ('004005004', '3', '陵川县', '0');
INSERT INTO `regions` VALUES ('004005005', '3', '泽州县', '0');
INSERT INTO `regions` VALUES ('004005006', '3', '高平市', '0');
INSERT INTO `regions` VALUES ('004006', '2', '朔州市', '0');
INSERT INTO `regions` VALUES ('004006001', '3', '朔城区', '0');
INSERT INTO `regions` VALUES ('004006002', '3', '平鲁区', '0');
INSERT INTO `regions` VALUES ('004006003', '3', '山阴县', '0');
INSERT INTO `regions` VALUES ('004006004', '3', '应县', '0');
INSERT INTO `regions` VALUES ('004006005', '3', '右玉县', '0');
INSERT INTO `regions` VALUES ('004006006', '3', '怀仁县', '0');
INSERT INTO `regions` VALUES ('004007', '2', '晋中市', '0');
INSERT INTO `regions` VALUES ('004007001', '3', '榆次区', '0');
INSERT INTO `regions` VALUES ('004007002', '3', '榆社县', '0');
INSERT INTO `regions` VALUES ('004007003', '3', '左权县', '0');
INSERT INTO `regions` VALUES ('004007004', '3', '和顺县', '0');
INSERT INTO `regions` VALUES ('004007005', '3', '昔阳县', '0');
INSERT INTO `regions` VALUES ('004007006', '3', '寿阳县', '0');
INSERT INTO `regions` VALUES ('004007007', '3', '太谷县', '0');
INSERT INTO `regions` VALUES ('004007008', '3', '祁县', '0');
INSERT INTO `regions` VALUES ('004007009', '3', '平遥县', '0');
INSERT INTO `regions` VALUES ('004007010', '3', '灵石县', '0');
INSERT INTO `regions` VALUES ('004007011', '3', '介休市', '0');
INSERT INTO `regions` VALUES ('004008', '2', '运城市', '0');
INSERT INTO `regions` VALUES ('004008001', '3', '盐湖区', '0');
INSERT INTO `regions` VALUES ('004008002', '3', '临猗县', '0');
INSERT INTO `regions` VALUES ('004008003', '3', '万荣县', '0');
INSERT INTO `regions` VALUES ('004008004', '3', '闻喜县', '0');
INSERT INTO `regions` VALUES ('004008005', '3', '稷山县', '0');
INSERT INTO `regions` VALUES ('004008006', '3', '新绛县', '0');
INSERT INTO `regions` VALUES ('004008007', '3', '绛县', '0');
INSERT INTO `regions` VALUES ('004008008', '3', '垣曲县', '0');
INSERT INTO `regions` VALUES ('004008009', '3', '夏县', '0');
INSERT INTO `regions` VALUES ('004008010', '3', '平陆县', '0');
INSERT INTO `regions` VALUES ('004008011', '3', '芮城县', '0');
INSERT INTO `regions` VALUES ('004008012', '3', '永济市', '0');
INSERT INTO `regions` VALUES ('004008013', '3', '河津市', '0');
INSERT INTO `regions` VALUES ('004009', '2', '忻州市', '0');
INSERT INTO `regions` VALUES ('004009001', '3', '忻府区', '0');
INSERT INTO `regions` VALUES ('004009002', '3', '定襄县', '0');
INSERT INTO `regions` VALUES ('004009003', '3', '五台县', '0');
INSERT INTO `regions` VALUES ('004009004', '3', '代县', '0');
INSERT INTO `regions` VALUES ('004009005', '3', '繁峙县', '0');
INSERT INTO `regions` VALUES ('004009006', '3', '宁武县', '0');
INSERT INTO `regions` VALUES ('004009007', '3', '静乐县', '0');
INSERT INTO `regions` VALUES ('004009008', '3', '神池县', '0');
INSERT INTO `regions` VALUES ('004009009', '3', '五寨县', '0');
INSERT INTO `regions` VALUES ('004009010', '3', '岢岚县', '0');
INSERT INTO `regions` VALUES ('004009011', '3', '河曲县', '0');
INSERT INTO `regions` VALUES ('004009012', '3', '保德县', '0');
INSERT INTO `regions` VALUES ('004009013', '3', '偏关县', '0');
INSERT INTO `regions` VALUES ('004009014', '3', '原平市', '0');
INSERT INTO `regions` VALUES ('004010', '2', '临汾市', '0');
INSERT INTO `regions` VALUES ('004010001', '3', '尧都区', '0');
INSERT INTO `regions` VALUES ('004010002', '3', '曲沃县', '0');
INSERT INTO `regions` VALUES ('004010003', '3', '翼城县', '0');
INSERT INTO `regions` VALUES ('004010004', '3', '襄汾县', '0');
INSERT INTO `regions` VALUES ('004010005', '3', '洪洞县', '0');
INSERT INTO `regions` VALUES ('004010006', '3', '古县', '0');
INSERT INTO `regions` VALUES ('004010007', '3', '安泽县', '0');
INSERT INTO `regions` VALUES ('004010008', '3', '浮山县', '0');
INSERT INTO `regions` VALUES ('004010009', '3', '吉县', '0');
INSERT INTO `regions` VALUES ('004010010', '3', '乡宁县', '0');
INSERT INTO `regions` VALUES ('004010011', '3', '大宁县', '0');
INSERT INTO `regions` VALUES ('004010012', '3', '隰县', '0');
INSERT INTO `regions` VALUES ('004010013', '3', '永和县', '0');
INSERT INTO `regions` VALUES ('004010014', '3', '蒲县', '0');
INSERT INTO `regions` VALUES ('004010015', '3', '汾西县', '0');
INSERT INTO `regions` VALUES ('004010016', '3', '侯马市', '0');
INSERT INTO `regions` VALUES ('004010017', '3', '霍州市', '0');
INSERT INTO `regions` VALUES ('004011', '2', '吕梁市', '0');
INSERT INTO `regions` VALUES ('004011001', '3', '离石区', '0');
INSERT INTO `regions` VALUES ('004011002', '3', '文水县', '0');
INSERT INTO `regions` VALUES ('004011003', '3', '交城县', '0');
INSERT INTO `regions` VALUES ('004011004', '3', '兴县', '0');
INSERT INTO `regions` VALUES ('004011005', '3', '临县', '0');
INSERT INTO `regions` VALUES ('004011006', '3', '柳林县', '0');
INSERT INTO `regions` VALUES ('004011007', '3', '石楼县', '0');
INSERT INTO `regions` VALUES ('004011008', '3', '岚县', '0');
INSERT INTO `regions` VALUES ('004011009', '3', '方山县', '0');
INSERT INTO `regions` VALUES ('004011010', '3', '中阳县', '0');
INSERT INTO `regions` VALUES ('004011011', '3', '交口县', '0');
INSERT INTO `regions` VALUES ('004011012', '3', '孝义市', '0');
INSERT INTO `regions` VALUES ('004011013', '3', '汾阳市', '0');
INSERT INTO `regions` VALUES ('005', '1', '内蒙古自治区', '0');
INSERT INTO `regions` VALUES ('005001', '2', '呼和浩特市', '0');
INSERT INTO `regions` VALUES ('005001001', '3', '新城区', '0');
INSERT INTO `regions` VALUES ('005001002', '3', '回民区', '0');
INSERT INTO `regions` VALUES ('005001003', '3', '玉泉区', '0');
INSERT INTO `regions` VALUES ('005001004', '3', '赛罕区', '0');
INSERT INTO `regions` VALUES ('005001005', '3', '土默特左旗', '0');
INSERT INTO `regions` VALUES ('005001006', '3', '托克托县', '0');
INSERT INTO `regions` VALUES ('005001007', '3', '和林格尔县', '0');
INSERT INTO `regions` VALUES ('005001008', '3', '清水河县', '0');
INSERT INTO `regions` VALUES ('005001009', '3', '武川县', '0');
INSERT INTO `regions` VALUES ('005002', '2', '包头市', '0');
INSERT INTO `regions` VALUES ('005002001', '3', '东河区', '0');
INSERT INTO `regions` VALUES ('005002002', '3', '昆都仑区', '0');
INSERT INTO `regions` VALUES ('005002003', '3', '青山区', '0');
INSERT INTO `regions` VALUES ('005002004', '3', '石拐区', '0');
INSERT INTO `regions` VALUES ('005002005', '3', '白云鄂博矿区', '0');
INSERT INTO `regions` VALUES ('005002006', '3', '九原区', '0');
INSERT INTO `regions` VALUES ('005002007', '3', '土默特右旗', '0');
INSERT INTO `regions` VALUES ('005002008', '3', '固阳县', '0');
INSERT INTO `regions` VALUES ('005002009', '3', '达尔罕茂明安联合旗', '0');
INSERT INTO `regions` VALUES ('005003', '2', '乌海市', '0');
INSERT INTO `regions` VALUES ('005003001', '3', '海勃湾区', '0');
INSERT INTO `regions` VALUES ('005003002', '3', '海南区', '0');
INSERT INTO `regions` VALUES ('005003003', '3', '乌达区', '0');
INSERT INTO `regions` VALUES ('005004', '2', '赤峰市', '0');
INSERT INTO `regions` VALUES ('005004001', '3', '红山区', '0');
INSERT INTO `regions` VALUES ('005004002', '3', '元宝山区', '0');
INSERT INTO `regions` VALUES ('005004003', '3', '松山区', '0');
INSERT INTO `regions` VALUES ('005004004', '3', '阿鲁科尔沁旗', '0');
INSERT INTO `regions` VALUES ('005004005', '3', '巴林左旗', '0');
INSERT INTO `regions` VALUES ('005004006', '3', '巴林右旗', '0');
INSERT INTO `regions` VALUES ('005004007', '3', '林西县', '0');
INSERT INTO `regions` VALUES ('005004008', '3', '克什克腾旗', '0');
INSERT INTO `regions` VALUES ('005004009', '3', '翁牛特旗', '0');
INSERT INTO `regions` VALUES ('005004010', '3', '喀喇沁旗', '0');
INSERT INTO `regions` VALUES ('005004011', '3', '宁城县', '0');
INSERT INTO `regions` VALUES ('005004012', '3', '敖汉旗', '0');
INSERT INTO `regions` VALUES ('005005', '2', '通辽市', '0');
INSERT INTO `regions` VALUES ('005005001', '3', '科尔沁区', '0');
INSERT INTO `regions` VALUES ('005005002', '3', '科尔沁左翼中旗', '0');
INSERT INTO `regions` VALUES ('005005003', '3', '科尔沁左翼后旗', '0');
INSERT INTO `regions` VALUES ('005005004', '3', '开鲁县', '0');
INSERT INTO `regions` VALUES ('005005005', '3', '库伦旗', '0');
INSERT INTO `regions` VALUES ('005005006', '3', '奈曼旗', '0');
INSERT INTO `regions` VALUES ('005005007', '3', '扎鲁特旗', '0');
INSERT INTO `regions` VALUES ('005005008', '3', '霍林郭勒市', '0');
INSERT INTO `regions` VALUES ('005006', '2', '鄂尔多斯市', '0');
INSERT INTO `regions` VALUES ('005006001', '3', '东胜区', '0');
INSERT INTO `regions` VALUES ('005006002', '3', '达拉特旗', '0');
INSERT INTO `regions` VALUES ('005006003', '3', '准格尔旗', '0');
INSERT INTO `regions` VALUES ('005006004', '3', '鄂托克前旗', '0');
INSERT INTO `regions` VALUES ('005006005', '3', '鄂托克旗', '0');
INSERT INTO `regions` VALUES ('005006006', '3', '杭锦旗', '0');
INSERT INTO `regions` VALUES ('005006007', '3', '乌审旗', '0');
INSERT INTO `regions` VALUES ('005006008', '3', '伊金霍洛旗', '0');
INSERT INTO `regions` VALUES ('005007', '2', '呼伦贝尔市', '0');
INSERT INTO `regions` VALUES ('005007001', '3', '海拉尔区', '0');
INSERT INTO `regions` VALUES ('005007002', '3', '扎赉诺尔区', '0');
INSERT INTO `regions` VALUES ('005007003', '3', '阿荣旗', '0');
INSERT INTO `regions` VALUES ('005007004', '3', '莫力达瓦达斡尔族自治旗', '0');
INSERT INTO `regions` VALUES ('005007005', '3', '鄂伦春自治旗', '0');
INSERT INTO `regions` VALUES ('005007006', '3', '鄂温克族自治旗', '0');
INSERT INTO `regions` VALUES ('005007007', '3', '陈巴尔虎旗', '0');
INSERT INTO `regions` VALUES ('005007008', '3', '新巴尔虎左旗', '0');
INSERT INTO `regions` VALUES ('005007009', '3', '新巴尔虎右旗', '0');
INSERT INTO `regions` VALUES ('005007010', '3', '满洲里市', '0');
INSERT INTO `regions` VALUES ('005007011', '3', '牙克石市', '0');
INSERT INTO `regions` VALUES ('005007012', '3', '扎兰屯市', '0');
INSERT INTO `regions` VALUES ('005007013', '3', '额尔古纳市', '0');
INSERT INTO `regions` VALUES ('005007014', '3', '根河市', '0');
INSERT INTO `regions` VALUES ('005008', '2', '巴彦淖尔市', '0');
INSERT INTO `regions` VALUES ('005008001', '3', '临河区', '0');
INSERT INTO `regions` VALUES ('005008002', '3', '五原县', '0');
INSERT INTO `regions` VALUES ('005008003', '3', '磴口县', '0');
INSERT INTO `regions` VALUES ('005008004', '3', '乌拉特前旗', '0');
INSERT INTO `regions` VALUES ('005008005', '3', '乌拉特中旗', '0');
INSERT INTO `regions` VALUES ('005008006', '3', '乌拉特后旗', '0');
INSERT INTO `regions` VALUES ('005008007', '3', '杭锦后旗', '0');
INSERT INTO `regions` VALUES ('005009', '2', '乌兰察布市', '0');
INSERT INTO `regions` VALUES ('005009001', '3', '集宁区', '0');
INSERT INTO `regions` VALUES ('005009002', '3', '卓资县', '0');
INSERT INTO `regions` VALUES ('005009003', '3', '化德县', '0');
INSERT INTO `regions` VALUES ('005009004', '3', '商都县', '0');
INSERT INTO `regions` VALUES ('005009005', '3', '兴和县', '0');
INSERT INTO `regions` VALUES ('005009006', '3', '凉城县', '0');
INSERT INTO `regions` VALUES ('005009007', '3', '察哈尔右翼前旗', '0');
INSERT INTO `regions` VALUES ('005009008', '3', '察哈尔右翼中旗', '0');
INSERT INTO `regions` VALUES ('005009009', '3', '察哈尔右翼后旗', '0');
INSERT INTO `regions` VALUES ('005009010', '3', '四子王旗', '0');
INSERT INTO `regions` VALUES ('005009011', '3', '丰镇市', '0');
INSERT INTO `regions` VALUES ('005010', '2', '兴安盟', '0');
INSERT INTO `regions` VALUES ('005010001', '3', '乌兰浩特市', '0');
INSERT INTO `regions` VALUES ('005010002', '3', '阿尔山市', '0');
INSERT INTO `regions` VALUES ('005010003', '3', '科尔沁右翼前旗', '0');
INSERT INTO `regions` VALUES ('005010004', '3', '科尔沁右翼中旗', '0');
INSERT INTO `regions` VALUES ('005010005', '3', '扎赉特旗', '0');
INSERT INTO `regions` VALUES ('005010006', '3', '突泉县', '0');
INSERT INTO `regions` VALUES ('005011', '2', '锡林郭勒盟', '0');
INSERT INTO `regions` VALUES ('005011001', '3', '二连浩特市', '0');
INSERT INTO `regions` VALUES ('005011002', '3', '锡林浩特市', '0');
INSERT INTO `regions` VALUES ('005011003', '3', '阿巴嘎旗', '0');
INSERT INTO `regions` VALUES ('005011004', '3', '苏尼特左旗', '0');
INSERT INTO `regions` VALUES ('005011005', '3', '苏尼特右旗', '0');
INSERT INTO `regions` VALUES ('005011006', '3', '东乌珠穆沁旗', '0');
INSERT INTO `regions` VALUES ('005011007', '3', '西乌珠穆沁旗', '0');
INSERT INTO `regions` VALUES ('005011008', '3', '太仆寺旗', '0');
INSERT INTO `regions` VALUES ('005011009', '3', '镶黄旗', '0');
INSERT INTO `regions` VALUES ('005011010', '3', '正镶白旗', '0');
INSERT INTO `regions` VALUES ('005011011', '3', '正蓝旗', '0');
INSERT INTO `regions` VALUES ('005011012', '3', '多伦县', '0');
INSERT INTO `regions` VALUES ('005012', '2', '阿拉善盟', '0');
INSERT INTO `regions` VALUES ('005012001', '3', '阿拉善左旗', '0');
INSERT INTO `regions` VALUES ('005012002', '3', '阿拉善右旗', '0');
INSERT INTO `regions` VALUES ('005012003', '3', '额济纳旗', '0');
INSERT INTO `regions` VALUES ('006', '1', '辽宁省', '0');
INSERT INTO `regions` VALUES ('006001', '2', '沈阳市', '0');
INSERT INTO `regions` VALUES ('006001001', '3', '和平区', '0');
INSERT INTO `regions` VALUES ('006001002', '3', '沈河区', '0');
INSERT INTO `regions` VALUES ('006001003', '3', '大东区', '0');
INSERT INTO `regions` VALUES ('006001004', '3', '皇姑区', '0');
INSERT INTO `regions` VALUES ('006001005', '3', '铁西区', '0');
INSERT INTO `regions` VALUES ('006001006', '3', '苏家屯区', '0');
INSERT INTO `regions` VALUES ('006001007', '3', '浑南区', '0');
INSERT INTO `regions` VALUES ('006001008', '3', '沈北新区', '0');
INSERT INTO `regions` VALUES ('006001009', '3', '于洪区', '0');
INSERT INTO `regions` VALUES ('006001010', '3', '辽中县', '0');
INSERT INTO `regions` VALUES ('006001011', '3', '康平县', '0');
INSERT INTO `regions` VALUES ('006001012', '3', '法库县', '0');
INSERT INTO `regions` VALUES ('006001013', '3', '新民市', '0');
INSERT INTO `regions` VALUES ('006002', '2', '大连市', '0');
INSERT INTO `regions` VALUES ('006002001', '3', '中山区', '0');
INSERT INTO `regions` VALUES ('006002002', '3', '西岗区', '0');
INSERT INTO `regions` VALUES ('006002003', '3', '沙河口区', '0');
INSERT INTO `regions` VALUES ('006002004', '3', '甘井子区', '0');
INSERT INTO `regions` VALUES ('006002005', '3', '旅顺口区', '0');
INSERT INTO `regions` VALUES ('006002006', '3', '金州区', '0');
INSERT INTO `regions` VALUES ('006002007', '3', '长海县', '0');
INSERT INTO `regions` VALUES ('006002008', '3', '瓦房店市', '0');
INSERT INTO `regions` VALUES ('006002009', '3', '普兰店市', '0');
INSERT INTO `regions` VALUES ('006002010', '3', '庄河市', '0');
INSERT INTO `regions` VALUES ('006003', '2', '鞍山市', '0');
INSERT INTO `regions` VALUES ('006003001', '3', '铁东区', '0');
INSERT INTO `regions` VALUES ('006003002', '3', '铁西区', '0');
INSERT INTO `regions` VALUES ('006003003', '3', '立山区', '0');
INSERT INTO `regions` VALUES ('006003004', '3', '千山区', '0');
INSERT INTO `regions` VALUES ('006003005', '3', '台安县', '0');
INSERT INTO `regions` VALUES ('006003006', '3', '岫岩满族自治县', '0');
INSERT INTO `regions` VALUES ('006003007', '3', '海城市', '0');
INSERT INTO `regions` VALUES ('006004', '2', '抚顺市', '0');
INSERT INTO `regions` VALUES ('006004001', '3', '新抚区', '0');
INSERT INTO `regions` VALUES ('006004002', '3', '东洲区', '0');
INSERT INTO `regions` VALUES ('006004003', '3', '望花区', '0');
INSERT INTO `regions` VALUES ('006004004', '3', '顺城区', '0');
INSERT INTO `regions` VALUES ('006004005', '3', '抚顺县', '0');
INSERT INTO `regions` VALUES ('006004006', '3', '新宾满族自治县', '0');
INSERT INTO `regions` VALUES ('006004007', '3', '清原满族自治县', '0');
INSERT INTO `regions` VALUES ('006005', '2', '本溪市', '0');
INSERT INTO `regions` VALUES ('006005001', '3', '平山区', '0');
INSERT INTO `regions` VALUES ('006005002', '3', '溪湖区', '0');
INSERT INTO `regions` VALUES ('006005003', '3', '明山区', '0');
INSERT INTO `regions` VALUES ('006005004', '3', '南芬区', '0');
INSERT INTO `regions` VALUES ('006005005', '3', '本溪满族自治县', '0');
INSERT INTO `regions` VALUES ('006005006', '3', '桓仁满族自治县', '0');
INSERT INTO `regions` VALUES ('006006', '2', '丹东市', '0');
INSERT INTO `regions` VALUES ('006006001', '3', '元宝区', '0');
INSERT INTO `regions` VALUES ('006006002', '3', '振兴区', '0');
INSERT INTO `regions` VALUES ('006006003', '3', '振安区', '0');
INSERT INTO `regions` VALUES ('006006004', '3', '宽甸满族自治县', '0');
INSERT INTO `regions` VALUES ('006006005', '3', '东港市', '0');
INSERT INTO `regions` VALUES ('006006006', '3', '凤城市', '0');
INSERT INTO `regions` VALUES ('006007', '2', '锦州市', '0');
INSERT INTO `regions` VALUES ('006007001', '3', '古塔区', '0');
INSERT INTO `regions` VALUES ('006007002', '3', '凌河区', '0');
INSERT INTO `regions` VALUES ('006007003', '3', '太和区', '0');
INSERT INTO `regions` VALUES ('006007004', '3', '黑山县', '0');
INSERT INTO `regions` VALUES ('006007005', '3', '义县', '0');
INSERT INTO `regions` VALUES ('006007006', '3', '凌海市', '0');
INSERT INTO `regions` VALUES ('006007007', '3', '北镇市', '0');
INSERT INTO `regions` VALUES ('006008', '2', '营口市', '0');
INSERT INTO `regions` VALUES ('006008001', '3', '站前区', '0');
INSERT INTO `regions` VALUES ('006008002', '3', '西市区', '0');
INSERT INTO `regions` VALUES ('006008003', '3', '鲅鱼圈区', '0');
INSERT INTO `regions` VALUES ('006008004', '3', '老边区', '0');
INSERT INTO `regions` VALUES ('006008005', '3', '盖州市', '0');
INSERT INTO `regions` VALUES ('006008006', '3', '大石桥市', '0');
INSERT INTO `regions` VALUES ('006009', '2', '阜新市', '0');
INSERT INTO `regions` VALUES ('006009001', '3', '海州区', '0');
INSERT INTO `regions` VALUES ('006009002', '3', '新邱区', '0');
INSERT INTO `regions` VALUES ('006009003', '3', '太平区', '0');
INSERT INTO `regions` VALUES ('006009004', '3', '清河门区', '0');
INSERT INTO `regions` VALUES ('006009005', '3', '细河区', '0');
INSERT INTO `regions` VALUES ('006009006', '3', '阜新蒙古族自治县', '0');
INSERT INTO `regions` VALUES ('006009007', '3', '彰武县', '0');
INSERT INTO `regions` VALUES ('006010', '2', '辽阳市', '0');
INSERT INTO `regions` VALUES ('006010001', '3', '白塔区', '0');
INSERT INTO `regions` VALUES ('006010002', '3', '文圣区', '0');
INSERT INTO `regions` VALUES ('006010003', '3', '宏伟区', '0');
INSERT INTO `regions` VALUES ('006010004', '3', '弓长岭区', '0');
INSERT INTO `regions` VALUES ('006010005', '3', '太子河区', '0');
INSERT INTO `regions` VALUES ('006010006', '3', '辽阳县', '0');
INSERT INTO `regions` VALUES ('006010007', '3', '灯塔市', '0');
INSERT INTO `regions` VALUES ('006011', '2', '盘锦市', '0');
INSERT INTO `regions` VALUES ('006011001', '3', '双台子区', '0');
INSERT INTO `regions` VALUES ('006011002', '3', '兴隆台区', '0');
INSERT INTO `regions` VALUES ('006011003', '3', '大洼县', '0');
INSERT INTO `regions` VALUES ('006011004', '3', '盘山县', '0');
INSERT INTO `regions` VALUES ('006012', '2', '铁岭市', '0');
INSERT INTO `regions` VALUES ('006012001', '3', '银州区', '0');
INSERT INTO `regions` VALUES ('006012002', '3', '清河区', '0');
INSERT INTO `regions` VALUES ('006012003', '3', '铁岭县', '0');
INSERT INTO `regions` VALUES ('006012004', '3', '西丰县', '0');
INSERT INTO `regions` VALUES ('006012005', '3', '昌图县', '0');
INSERT INTO `regions` VALUES ('006012006', '3', '调兵山市', '0');
INSERT INTO `regions` VALUES ('006012007', '3', '开原市', '0');
INSERT INTO `regions` VALUES ('006013', '2', '朝阳市', '0');
INSERT INTO `regions` VALUES ('006013001', '3', '双塔区', '0');
INSERT INTO `regions` VALUES ('006013002', '3', '龙城区', '0');
INSERT INTO `regions` VALUES ('006013003', '3', '朝阳县', '0');
INSERT INTO `regions` VALUES ('006013004', '3', '建平县', '0');
INSERT INTO `regions` VALUES ('006013005', '3', '喀喇沁左翼蒙古族自治县', '0');
INSERT INTO `regions` VALUES ('006013006', '3', '北票市', '0');
INSERT INTO `regions` VALUES ('006013007', '3', '凌源市', '0');
INSERT INTO `regions` VALUES ('006014', '2', '葫芦岛市', '0');
INSERT INTO `regions` VALUES ('006014001', '3', '连山区', '0');
INSERT INTO `regions` VALUES ('006014002', '3', '龙港区', '0');
INSERT INTO `regions` VALUES ('006014003', '3', '南票区', '0');
INSERT INTO `regions` VALUES ('006014004', '3', '绥中县', '0');
INSERT INTO `regions` VALUES ('006014005', '3', '建昌县', '0');
INSERT INTO `regions` VALUES ('006014006', '3', '兴城市', '0');
INSERT INTO `regions` VALUES ('006015', '2', '金普新区', '0');
INSERT INTO `regions` VALUES ('006015001', '3', '金州新区', '0');
INSERT INTO `regions` VALUES ('006015002', '3', '普湾新区', '0');
INSERT INTO `regions` VALUES ('006015003', '3', '保税区', '0');
INSERT INTO `regions` VALUES ('007', '1', '吉林省', '0');
INSERT INTO `regions` VALUES ('007001', '2', '长春市', '0');
INSERT INTO `regions` VALUES ('007001001', '3', '南关区', '0');
INSERT INTO `regions` VALUES ('007001002', '3', '宽城区', '0');
INSERT INTO `regions` VALUES ('007001003', '3', '朝阳区', '0');
INSERT INTO `regions` VALUES ('007001004', '3', '二道区', '0');
INSERT INTO `regions` VALUES ('007001005', '3', '绿园区', '0');
INSERT INTO `regions` VALUES ('007001006', '3', '双阳区', '0');
INSERT INTO `regions` VALUES ('007001007', '3', '九台区', '0');
INSERT INTO `regions` VALUES ('007001008', '3', '农安县', '0');
INSERT INTO `regions` VALUES ('007001009', '3', '榆树市', '0');
INSERT INTO `regions` VALUES ('007001010', '3', '德惠市', '0');
INSERT INTO `regions` VALUES ('007002', '2', '吉林市', '0');
INSERT INTO `regions` VALUES ('007002001', '3', '昌邑区', '0');
INSERT INTO `regions` VALUES ('007002002', '3', '龙潭区', '0');
INSERT INTO `regions` VALUES ('007002003', '3', '船营区', '0');
INSERT INTO `regions` VALUES ('007002004', '3', '丰满区', '0');
INSERT INTO `regions` VALUES ('007002005', '3', '永吉县', '0');
INSERT INTO `regions` VALUES ('007002006', '3', '蛟河市', '0');
INSERT INTO `regions` VALUES ('007002007', '3', '桦甸市', '0');
INSERT INTO `regions` VALUES ('007002008', '3', '舒兰市', '0');
INSERT INTO `regions` VALUES ('007002009', '3', '磐石市', '0');
INSERT INTO `regions` VALUES ('007003', '2', '四平市', '0');
INSERT INTO `regions` VALUES ('007003001', '3', '铁西区', '0');
INSERT INTO `regions` VALUES ('007003002', '3', '铁东区', '0');
INSERT INTO `regions` VALUES ('007003003', '3', '梨树县', '0');
INSERT INTO `regions` VALUES ('007003004', '3', '伊通满族自治县', '0');
INSERT INTO `regions` VALUES ('007003005', '3', '公主岭市', '0');
INSERT INTO `regions` VALUES ('007003006', '3', '双辽市', '0');
INSERT INTO `regions` VALUES ('007004', '2', '辽源市', '0');
INSERT INTO `regions` VALUES ('007004001', '3', '龙山区', '0');
INSERT INTO `regions` VALUES ('007004002', '3', '西安区', '0');
INSERT INTO `regions` VALUES ('007004003', '3', '东丰县', '0');
INSERT INTO `regions` VALUES ('007004004', '3', '东辽县', '0');
INSERT INTO `regions` VALUES ('007005', '2', '通化市', '0');
INSERT INTO `regions` VALUES ('007005001', '3', '东昌区', '0');
INSERT INTO `regions` VALUES ('007005002', '3', '二道江区', '0');
INSERT INTO `regions` VALUES ('007005003', '3', '通化县', '0');
INSERT INTO `regions` VALUES ('007005004', '3', '辉南县', '0');
INSERT INTO `regions` VALUES ('007005005', '3', '柳河县', '0');
INSERT INTO `regions` VALUES ('007005006', '3', '梅河口市', '0');
INSERT INTO `regions` VALUES ('007005007', '3', '集安市', '0');
INSERT INTO `regions` VALUES ('007006', '2', '白山市', '0');
INSERT INTO `regions` VALUES ('007006001', '3', '浑江区', '0');
INSERT INTO `regions` VALUES ('007006002', '3', '江源区', '0');
INSERT INTO `regions` VALUES ('007006003', '3', '抚松县', '0');
INSERT INTO `regions` VALUES ('007006004', '3', '靖宇县', '0');
INSERT INTO `regions` VALUES ('007006005', '3', '长白朝鲜族自治县', '0');
INSERT INTO `regions` VALUES ('007006006', '3', '临江市', '0');
INSERT INTO `regions` VALUES ('007007', '2', '松原市', '0');
INSERT INTO `regions` VALUES ('007007001', '3', '宁江区', '0');
INSERT INTO `regions` VALUES ('007007002', '3', '前郭尔罗斯蒙古族自治县', '0');
INSERT INTO `regions` VALUES ('007007003', '3', '长岭县', '0');
INSERT INTO `regions` VALUES ('007007004', '3', '乾安县', '0');
INSERT INTO `regions` VALUES ('007007005', '3', '扶余市', '0');
INSERT INTO `regions` VALUES ('007008', '2', '白城市', '0');
INSERT INTO `regions` VALUES ('007008001', '3', '洮北区', '0');
INSERT INTO `regions` VALUES ('007008002', '3', '镇赉县', '0');
INSERT INTO `regions` VALUES ('007008003', '3', '通榆县', '0');
INSERT INTO `regions` VALUES ('007008004', '3', '洮南市', '0');
INSERT INTO `regions` VALUES ('007008005', '3', '大安市', '0');
INSERT INTO `regions` VALUES ('007009', '2', '延边朝鲜族自治州', '0');
INSERT INTO `regions` VALUES ('007009001', '3', '延吉市', '0');
INSERT INTO `regions` VALUES ('007009002', '3', '图们市', '0');
INSERT INTO `regions` VALUES ('007009003', '3', '敦化市', '0');
INSERT INTO `regions` VALUES ('007009004', '3', '珲春市', '0');
INSERT INTO `regions` VALUES ('007009005', '3', '龙井市', '0');
INSERT INTO `regions` VALUES ('007009006', '3', '和龙市', '0');
INSERT INTO `regions` VALUES ('007009007', '3', '汪清县', '0');
INSERT INTO `regions` VALUES ('007009008', '3', '安图县', '0');
INSERT INTO `regions` VALUES ('008', '1', '黑龙江省', '0');
INSERT INTO `regions` VALUES ('008001', '2', '哈尔滨市', '0');
INSERT INTO `regions` VALUES ('008001001', '3', '道里区', '0');
INSERT INTO `regions` VALUES ('008001002', '3', '南岗区', '0');
INSERT INTO `regions` VALUES ('008001003', '3', '道外区', '0');
INSERT INTO `regions` VALUES ('008001004', '3', '平房区', '0');
INSERT INTO `regions` VALUES ('008001005', '3', '松北区', '0');
INSERT INTO `regions` VALUES ('008001006', '3', '香坊区', '0');
INSERT INTO `regions` VALUES ('008001007', '3', '呼兰区', '0');
INSERT INTO `regions` VALUES ('008001008', '3', '阿城区', '0');
INSERT INTO `regions` VALUES ('008001009', '3', '双城区', '0');
INSERT INTO `regions` VALUES ('008001010', '3', '依兰县', '0');
INSERT INTO `regions` VALUES ('008001011', '3', '方正县', '0');
INSERT INTO `regions` VALUES ('008001012', '3', '宾县', '0');
INSERT INTO `regions` VALUES ('008001013', '3', '巴彦县', '0');
INSERT INTO `regions` VALUES ('008001014', '3', '木兰县', '0');
INSERT INTO `regions` VALUES ('008001015', '3', '通河县', '0');
INSERT INTO `regions` VALUES ('008001016', '3', '延寿县', '0');
INSERT INTO `regions` VALUES ('008001017', '3', '尚志市', '0');
INSERT INTO `regions` VALUES ('008001018', '3', '五常市', '0');
INSERT INTO `regions` VALUES ('008002', '2', '齐齐哈尔市', '0');
INSERT INTO `regions` VALUES ('008002001', '3', '龙沙区', '0');
INSERT INTO `regions` VALUES ('008002002', '3', '建华区', '0');
INSERT INTO `regions` VALUES ('008002003', '3', '铁锋区', '0');
INSERT INTO `regions` VALUES ('008002004', '3', '昂昂溪区', '0');
INSERT INTO `regions` VALUES ('008002005', '3', '富拉尔基区', '0');
INSERT INTO `regions` VALUES ('008002006', '3', '碾子山区', '0');
INSERT INTO `regions` VALUES ('008002007', '3', '梅里斯达斡尔族区', '0');
INSERT INTO `regions` VALUES ('008002008', '3', '龙江县', '0');
INSERT INTO `regions` VALUES ('008002009', '3', '依安县', '0');
INSERT INTO `regions` VALUES ('008002010', '3', '泰来县', '0');
INSERT INTO `regions` VALUES ('008002011', '3', '甘南县', '0');
INSERT INTO `regions` VALUES ('008002012', '3', '富裕县', '0');
INSERT INTO `regions` VALUES ('008002013', '3', '克山县', '0');
INSERT INTO `regions` VALUES ('008002014', '3', '克东县', '0');
INSERT INTO `regions` VALUES ('008002015', '3', '拜泉县', '0');
INSERT INTO `regions` VALUES ('008002016', '3', '讷河市', '0');
INSERT INTO `regions` VALUES ('008003', '2', '鸡西市', '0');
INSERT INTO `regions` VALUES ('008003001', '3', '鸡冠区', '0');
INSERT INTO `regions` VALUES ('008003002', '3', '恒山区', '0');
INSERT INTO `regions` VALUES ('008003003', '3', '滴道区', '0');
INSERT INTO `regions` VALUES ('008003004', '3', '梨树区', '0');
INSERT INTO `regions` VALUES ('008003005', '3', '城子河区', '0');
INSERT INTO `regions` VALUES ('008003006', '3', '麻山区', '0');
INSERT INTO `regions` VALUES ('008003007', '3', '鸡东县', '0');
INSERT INTO `regions` VALUES ('008003008', '3', '虎林市', '0');
INSERT INTO `regions` VALUES ('008003009', '3', '密山市', '0');
INSERT INTO `regions` VALUES ('008004', '2', '鹤岗市', '0');
INSERT INTO `regions` VALUES ('008004001', '3', '向阳区', '0');
INSERT INTO `regions` VALUES ('008004002', '3', '工农区', '0');
INSERT INTO `regions` VALUES ('008004003', '3', '南山区', '0');
INSERT INTO `regions` VALUES ('008004004', '3', '兴安区', '0');
INSERT INTO `regions` VALUES ('008004005', '3', '东山区', '0');
INSERT INTO `regions` VALUES ('008004006', '3', '兴山区', '0');
INSERT INTO `regions` VALUES ('008004007', '3', '萝北县', '0');
INSERT INTO `regions` VALUES ('008004008', '3', '绥滨县', '0');
INSERT INTO `regions` VALUES ('008005', '2', '双鸭山市', '0');
INSERT INTO `regions` VALUES ('008005001', '3', '尖山区', '0');
INSERT INTO `regions` VALUES ('008005002', '3', '岭东区', '0');
INSERT INTO `regions` VALUES ('008005003', '3', '四方台区', '0');
INSERT INTO `regions` VALUES ('008005004', '3', '宝山区', '0');
INSERT INTO `regions` VALUES ('008005005', '3', '集贤县', '0');
INSERT INTO `regions` VALUES ('008005006', '3', '友谊县', '0');
INSERT INTO `regions` VALUES ('008005007', '3', '宝清县', '0');
INSERT INTO `regions` VALUES ('008005008', '3', '饶河县', '0');
INSERT INTO `regions` VALUES ('008006', '2', '大庆市', '0');
INSERT INTO `regions` VALUES ('008006001', '3', '萨尔图区', '0');
INSERT INTO `regions` VALUES ('008006002', '3', '龙凤区', '0');
INSERT INTO `regions` VALUES ('008006003', '3', '让胡路区', '0');
INSERT INTO `regions` VALUES ('008006004', '3', '红岗区', '0');
INSERT INTO `regions` VALUES ('008006005', '3', '大同区', '0');
INSERT INTO `regions` VALUES ('008006006', '3', '肇州县', '0');
INSERT INTO `regions` VALUES ('008006007', '3', '肇源县', '0');
INSERT INTO `regions` VALUES ('008006008', '3', '林甸县', '0');
INSERT INTO `regions` VALUES ('008006009', '3', '杜尔伯特蒙古族自治县', '0');
INSERT INTO `regions` VALUES ('008007', '2', '伊春市', '0');
INSERT INTO `regions` VALUES ('008007001', '3', '伊春区', '0');
INSERT INTO `regions` VALUES ('008007002', '3', '南岔区', '0');
INSERT INTO `regions` VALUES ('008007003', '3', '友好区', '0');
INSERT INTO `regions` VALUES ('008007004', '3', '西林区', '0');
INSERT INTO `regions` VALUES ('008007005', '3', '翠峦区', '0');
INSERT INTO `regions` VALUES ('008007006', '3', '新青区', '0');
INSERT INTO `regions` VALUES ('008007007', '3', '美溪区', '0');
INSERT INTO `regions` VALUES ('008007008', '3', '金山屯区', '0');
INSERT INTO `regions` VALUES ('008007009', '3', '五营区', '0');
INSERT INTO `regions` VALUES ('008007010', '3', '乌马河区', '0');
INSERT INTO `regions` VALUES ('008007011', '3', '汤旺河区', '0');
INSERT INTO `regions` VALUES ('008007012', '3', '带岭区', '0');
INSERT INTO `regions` VALUES ('008007013', '3', '乌伊岭区', '0');
INSERT INTO `regions` VALUES ('008007014', '3', '红星区', '0');
INSERT INTO `regions` VALUES ('008007015', '3', '上甘岭区', '0');
INSERT INTO `regions` VALUES ('008007016', '3', '嘉荫县', '0');
INSERT INTO `regions` VALUES ('008007017', '3', '铁力市', '0');
INSERT INTO `regions` VALUES ('008008', '2', '佳木斯市', '0');
INSERT INTO `regions` VALUES ('008008001', '3', '向阳区', '0');
INSERT INTO `regions` VALUES ('008008002', '3', '前进区', '0');
INSERT INTO `regions` VALUES ('008008003', '3', '东风区', '0');
INSERT INTO `regions` VALUES ('008008004', '3', '郊区', '0');
INSERT INTO `regions` VALUES ('008008005', '3', '桦南县', '0');
INSERT INTO `regions` VALUES ('008008006', '3', '桦川县', '0');
INSERT INTO `regions` VALUES ('008008007', '3', '汤原县', '0');
INSERT INTO `regions` VALUES ('008008008', '3', '抚远县', '0');
INSERT INTO `regions` VALUES ('008008009', '3', '同江市', '0');
INSERT INTO `regions` VALUES ('008008010', '3', '富锦市', '0');
INSERT INTO `regions` VALUES ('008009', '2', '七台河市', '0');
INSERT INTO `regions` VALUES ('008009001', '3', '新兴区', '0');
INSERT INTO `regions` VALUES ('008009002', '3', '桃山区', '0');
INSERT INTO `regions` VALUES ('008009003', '3', '茄子河区', '0');
INSERT INTO `regions` VALUES ('008009004', '3', '勃利县', '0');
INSERT INTO `regions` VALUES ('008010', '2', '牡丹江市', '0');
INSERT INTO `regions` VALUES ('008010001', '3', '东安区', '0');
INSERT INTO `regions` VALUES ('008010002', '3', '阳明区', '0');
INSERT INTO `regions` VALUES ('008010003', '3', '爱民区', '0');
INSERT INTO `regions` VALUES ('008010004', '3', '西安区', '0');
INSERT INTO `regions` VALUES ('008010005', '3', '东宁县', '0');
INSERT INTO `regions` VALUES ('008010006', '3', '林口县', '0');
INSERT INTO `regions` VALUES ('008010007', '3', '绥芬河市', '0');
INSERT INTO `regions` VALUES ('008010008', '3', '海林市', '0');
INSERT INTO `regions` VALUES ('008010009', '3', '宁安市', '0');
INSERT INTO `regions` VALUES ('008010010', '3', '穆棱市', '0');
INSERT INTO `regions` VALUES ('008011', '2', '黑河市', '0');
INSERT INTO `regions` VALUES ('008011001', '3', '爱辉区', '0');
INSERT INTO `regions` VALUES ('008011002', '3', '嫩江县', '0');
INSERT INTO `regions` VALUES ('008011003', '3', '逊克县', '0');
INSERT INTO `regions` VALUES ('008011004', '3', '孙吴县', '0');
INSERT INTO `regions` VALUES ('008011005', '3', '北安市', '0');
INSERT INTO `regions` VALUES ('008011006', '3', '五大连池市', '0');
INSERT INTO `regions` VALUES ('008012', '2', '绥化市', '0');
INSERT INTO `regions` VALUES ('008012001', '3', '北林区', '0');
INSERT INTO `regions` VALUES ('008012002', '3', '望奎县', '0');
INSERT INTO `regions` VALUES ('008012003', '3', '兰西县', '0');
INSERT INTO `regions` VALUES ('008012004', '3', '青冈县', '0');
INSERT INTO `regions` VALUES ('008012005', '3', '庆安县', '0');
INSERT INTO `regions` VALUES ('008012006', '3', '明水县', '0');
INSERT INTO `regions` VALUES ('008012007', '3', '绥棱县', '0');
INSERT INTO `regions` VALUES ('008012008', '3', '安达市', '0');
INSERT INTO `regions` VALUES ('008012009', '3', '肇东市', '0');
INSERT INTO `regions` VALUES ('008012010', '3', '海伦市', '0');
INSERT INTO `regions` VALUES ('008013', '2', '大兴安岭地区', '0');
INSERT INTO `regions` VALUES ('008013001', '3', '加格达奇区', '0');
INSERT INTO `regions` VALUES ('008013002', '3', '新林区', '0');
INSERT INTO `regions` VALUES ('008013003', '3', '松岭区', '0');
INSERT INTO `regions` VALUES ('008013004', '3', '呼中区', '0');
INSERT INTO `regions` VALUES ('008013005', '3', '呼玛县', '0');
INSERT INTO `regions` VALUES ('008013006', '3', '塔河县', '0');
INSERT INTO `regions` VALUES ('008013007', '3', '漠河县', '0');
INSERT INTO `regions` VALUES ('009', '1', '上海', '0');
INSERT INTO `regions` VALUES ('009001', '2', '上海市', '0');
INSERT INTO `regions` VALUES ('009001001', '3', '黄浦区', '0');
INSERT INTO `regions` VALUES ('009001002', '3', '徐汇区', '0');
INSERT INTO `regions` VALUES ('009001003', '3', '长宁区', '0');
INSERT INTO `regions` VALUES ('009001004', '3', '静安区', '0');
INSERT INTO `regions` VALUES ('009001005', '3', '普陀区', '0');
INSERT INTO `regions` VALUES ('009001006', '3', '闸北区', '0');
INSERT INTO `regions` VALUES ('009001007', '3', '虹口区', '0');
INSERT INTO `regions` VALUES ('009001008', '3', '杨浦区', '0');
INSERT INTO `regions` VALUES ('009001009', '3', '闵行区', '0');
INSERT INTO `regions` VALUES ('009001010', '3', '宝山区', '0');
INSERT INTO `regions` VALUES ('009001011', '3', '嘉定区', '0');
INSERT INTO `regions` VALUES ('009001012', '3', '浦东新区', '0');
INSERT INTO `regions` VALUES ('009001013', '3', '金山区', '0');
INSERT INTO `regions` VALUES ('009001014', '3', '松江区', '0');
INSERT INTO `regions` VALUES ('009001015', '3', '青浦区', '0');
INSERT INTO `regions` VALUES ('009001016', '3', '奉贤区', '0');
INSERT INTO `regions` VALUES ('009001017', '3', '崇明县', '0');
INSERT INTO `regions` VALUES ('010', '1', '江苏省', '0');
INSERT INTO `regions` VALUES ('010001', '2', '南京市', '0');
INSERT INTO `regions` VALUES ('010001001', '3', '玄武区', '0');
INSERT INTO `regions` VALUES ('010001002', '3', '秦淮区', '0');
INSERT INTO `regions` VALUES ('010001003', '3', '建邺区', '0');
INSERT INTO `regions` VALUES ('010001004', '3', '鼓楼区', '0');
INSERT INTO `regions` VALUES ('010001005', '3', '浦口区', '0');
INSERT INTO `regions` VALUES ('010001006', '3', '栖霞区', '0');
INSERT INTO `regions` VALUES ('010001007', '3', '雨花台区', '0');
INSERT INTO `regions` VALUES ('010001008', '3', '江宁区', '0');
INSERT INTO `regions` VALUES ('010001009', '3', '六合区', '0');
INSERT INTO `regions` VALUES ('010001010', '3', '溧水区', '0');
INSERT INTO `regions` VALUES ('010001011', '3', '高淳区', '0');
INSERT INTO `regions` VALUES ('010002', '2', '无锡市', '0');
INSERT INTO `regions` VALUES ('010002001', '3', '崇安区', '0');
INSERT INTO `regions` VALUES ('010002002', '3', '南长区', '0');
INSERT INTO `regions` VALUES ('010002003', '3', '北塘区', '0');
INSERT INTO `regions` VALUES ('010002004', '3', '锡山区', '0');
INSERT INTO `regions` VALUES ('010002005', '3', '惠山区', '0');
INSERT INTO `regions` VALUES ('010002006', '3', '滨湖区', '0');
INSERT INTO `regions` VALUES ('010002007', '3', '江阴市', '0');
INSERT INTO `regions` VALUES ('010002008', '3', '宜兴市', '0');
INSERT INTO `regions` VALUES ('010003', '2', '徐州市', '0');
INSERT INTO `regions` VALUES ('010003001', '3', '鼓楼区', '0');
INSERT INTO `regions` VALUES ('010003002', '3', '云龙区', '0');
INSERT INTO `regions` VALUES ('010003003', '3', '贾汪区', '0');
INSERT INTO `regions` VALUES ('010003004', '3', '泉山区', '0');
INSERT INTO `regions` VALUES ('010003005', '3', '铜山区', '0');
INSERT INTO `regions` VALUES ('010003006', '3', '丰县', '0');
INSERT INTO `regions` VALUES ('010003007', '3', '沛县', '0');
INSERT INTO `regions` VALUES ('010003008', '3', '睢宁县', '0');
INSERT INTO `regions` VALUES ('010003009', '3', '新沂市', '0');
INSERT INTO `regions` VALUES ('010003010', '3', '邳州市', '0');
INSERT INTO `regions` VALUES ('010004', '2', '常州市', '0');
INSERT INTO `regions` VALUES ('010004001', '3', '天宁区', '0');
INSERT INTO `regions` VALUES ('010004002', '3', '钟楼区', '0');
INSERT INTO `regions` VALUES ('010004003', '3', '戚墅堰区', '0');
INSERT INTO `regions` VALUES ('010004004', '3', '新北区', '0');
INSERT INTO `regions` VALUES ('010004005', '3', '武进区', '0');
INSERT INTO `regions` VALUES ('010004006', '3', '溧阳市', '0');
INSERT INTO `regions` VALUES ('010004007', '3', '金坛市', '0');
INSERT INTO `regions` VALUES ('010005', '2', '苏州市', '0');
INSERT INTO `regions` VALUES ('010005001', '3', '虎丘区', '0');
INSERT INTO `regions` VALUES ('010005002', '3', '吴中区', '0');
INSERT INTO `regions` VALUES ('010005003', '3', '相城区', '0');
INSERT INTO `regions` VALUES ('010005004', '3', '姑苏区', '0');
INSERT INTO `regions` VALUES ('010005005', '3', '吴江区', '0');
INSERT INTO `regions` VALUES ('010005006', '3', '常熟市', '0');
INSERT INTO `regions` VALUES ('010005007', '3', '张家港市', '0');
INSERT INTO `regions` VALUES ('010005008', '3', '昆山市', '0');
INSERT INTO `regions` VALUES ('010005009', '3', '太仓市', '0');
INSERT INTO `regions` VALUES ('010006', '2', '南通市', '0');
INSERT INTO `regions` VALUES ('010006001', '3', '崇川区', '0');
INSERT INTO `regions` VALUES ('010006002', '3', '港闸区', '0');
INSERT INTO `regions` VALUES ('010006003', '3', '通州区', '0');
INSERT INTO `regions` VALUES ('010006004', '3', '海安县', '0');
INSERT INTO `regions` VALUES ('010006005', '3', '如东县', '0');
INSERT INTO `regions` VALUES ('010006006', '3', '启东市', '0');
INSERT INTO `regions` VALUES ('010006007', '3', '如皋市', '0');
INSERT INTO `regions` VALUES ('010006008', '3', '海门市', '0');
INSERT INTO `regions` VALUES ('010007', '2', '连云港市', '0');
INSERT INTO `regions` VALUES ('010007001', '3', '连云区', '0');
INSERT INTO `regions` VALUES ('010007002', '3', '海州区', '0');
INSERT INTO `regions` VALUES ('010007003', '3', '赣榆区', '0');
INSERT INTO `regions` VALUES ('010007004', '3', '东海县', '0');
INSERT INTO `regions` VALUES ('010007005', '3', '灌云县', '0');
INSERT INTO `regions` VALUES ('010007006', '3', '灌南县', '0');
INSERT INTO `regions` VALUES ('010008', '2', '淮安市', '0');
INSERT INTO `regions` VALUES ('010008001', '3', '清河区', '0');
INSERT INTO `regions` VALUES ('010008002', '3', '淮安区', '0');
INSERT INTO `regions` VALUES ('010008003', '3', '淮阴区', '0');
INSERT INTO `regions` VALUES ('010008004', '3', '清浦区', '0');
INSERT INTO `regions` VALUES ('010008005', '3', '涟水县', '0');
INSERT INTO `regions` VALUES ('010008006', '3', '洪泽县', '0');
INSERT INTO `regions` VALUES ('010008007', '3', '盱眙县', '0');
INSERT INTO `regions` VALUES ('010008008', '3', '金湖县', '0');
INSERT INTO `regions` VALUES ('010009', '2', '盐城市', '0');
INSERT INTO `regions` VALUES ('010009001', '3', '亭湖区', '0');
INSERT INTO `regions` VALUES ('010009002', '3', '盐都区', '0');
INSERT INTO `regions` VALUES ('010009003', '3', '响水县', '0');
INSERT INTO `regions` VALUES ('010009004', '3', '滨海县', '0');
INSERT INTO `regions` VALUES ('010009005', '3', '阜宁县', '0');
INSERT INTO `regions` VALUES ('010009006', '3', '射阳县', '0');
INSERT INTO `regions` VALUES ('010009007', '3', '建湖县', '0');
INSERT INTO `regions` VALUES ('010009008', '3', '东台市', '0');
INSERT INTO `regions` VALUES ('010009009', '3', '大丰市', '0');
INSERT INTO `regions` VALUES ('010010', '2', '扬州市', '0');
INSERT INTO `regions` VALUES ('010010001', '3', '广陵区', '0');
INSERT INTO `regions` VALUES ('010010002', '3', '邗江区', '0');
INSERT INTO `regions` VALUES ('010010003', '3', '江都区', '0');
INSERT INTO `regions` VALUES ('010010004', '3', '宝应县', '0');
INSERT INTO `regions` VALUES ('010010005', '3', '仪征市', '0');
INSERT INTO `regions` VALUES ('010010006', '3', '高邮市', '0');
INSERT INTO `regions` VALUES ('010011', '2', '镇江市', '0');
INSERT INTO `regions` VALUES ('010011001', '3', '京口区', '0');
INSERT INTO `regions` VALUES ('010011002', '3', '润州区', '0');
INSERT INTO `regions` VALUES ('010011003', '3', '丹徒区', '0');
INSERT INTO `regions` VALUES ('010011004', '3', '丹阳市', '0');
INSERT INTO `regions` VALUES ('010011005', '3', '扬中市', '0');
INSERT INTO `regions` VALUES ('010011006', '3', '句容市', '0');
INSERT INTO `regions` VALUES ('010012', '2', '泰州市', '0');
INSERT INTO `regions` VALUES ('010012001', '3', '海陵区', '0');
INSERT INTO `regions` VALUES ('010012002', '3', '高港区', '0');
INSERT INTO `regions` VALUES ('010012003', '3', '姜堰区', '0');
INSERT INTO `regions` VALUES ('010012004', '3', '兴化市', '0');
INSERT INTO `regions` VALUES ('010012005', '3', '靖江市', '0');
INSERT INTO `regions` VALUES ('010012006', '3', '泰兴市', '0');
INSERT INTO `regions` VALUES ('010013', '2', '宿迁市', '0');
INSERT INTO `regions` VALUES ('010013001', '3', '宿城区', '0');
INSERT INTO `regions` VALUES ('010013002', '3', '宿豫区', '0');
INSERT INTO `regions` VALUES ('010013003', '3', '沭阳县', '0');
INSERT INTO `regions` VALUES ('010013004', '3', '泗阳县', '0');
INSERT INTO `regions` VALUES ('010013005', '3', '泗洪县', '0');
INSERT INTO `regions` VALUES ('011', '1', '浙江省', '0');
INSERT INTO `regions` VALUES ('011001', '2', '杭州市', '0');
INSERT INTO `regions` VALUES ('011001001', '3', '上城区', '0');
INSERT INTO `regions` VALUES ('011001002', '3', '下城区', '0');
INSERT INTO `regions` VALUES ('011001003', '3', '江干区', '0');
INSERT INTO `regions` VALUES ('011001004', '3', '拱墅区', '0');
INSERT INTO `regions` VALUES ('011001005', '3', '西湖区', '0');
INSERT INTO `regions` VALUES ('011001006', '3', '滨江区', '0');
INSERT INTO `regions` VALUES ('011001007', '3', '萧山区', '0');
INSERT INTO `regions` VALUES ('011001008', '3', '余杭区', '0');
INSERT INTO `regions` VALUES ('011001009', '3', '桐庐县', '0');
INSERT INTO `regions` VALUES ('011001010', '3', '淳安县', '0');
INSERT INTO `regions` VALUES ('011001011', '3', '建德市', '0');
INSERT INTO `regions` VALUES ('011001012', '3', '富阳区', '0');
INSERT INTO `regions` VALUES ('011001013', '3', '临安市', '0');
INSERT INTO `regions` VALUES ('011002', '2', '宁波市', '0');
INSERT INTO `regions` VALUES ('011002001', '3', '海曙区', '0');
INSERT INTO `regions` VALUES ('011002002', '3', '江东区', '0');
INSERT INTO `regions` VALUES ('011002003', '3', '江北区', '0');
INSERT INTO `regions` VALUES ('011002004', '3', '北仑区', '0');
INSERT INTO `regions` VALUES ('011002005', '3', '镇海区', '0');
INSERT INTO `regions` VALUES ('011002006', '3', '鄞州区', '0');
INSERT INTO `regions` VALUES ('011002007', '3', '象山县', '0');
INSERT INTO `regions` VALUES ('011002008', '3', '宁海县', '0');
INSERT INTO `regions` VALUES ('011002009', '3', '余姚市', '0');
INSERT INTO `regions` VALUES ('011002010', '3', '慈溪市', '0');
INSERT INTO `regions` VALUES ('011002011', '3', '奉化市', '0');
INSERT INTO `regions` VALUES ('011003', '2', '温州市', '0');
INSERT INTO `regions` VALUES ('011003001', '3', '鹿城区', '0');
INSERT INTO `regions` VALUES ('011003002', '3', '龙湾区', '0');
INSERT INTO `regions` VALUES ('011003003', '3', '瓯海区', '0');
INSERT INTO `regions` VALUES ('011003004', '3', '洞头县', '0');
INSERT INTO `regions` VALUES ('011003005', '3', '永嘉县', '0');
INSERT INTO `regions` VALUES ('011003006', '3', '平阳县', '0');
INSERT INTO `regions` VALUES ('011003007', '3', '苍南县', '0');
INSERT INTO `regions` VALUES ('011003008', '3', '文成县', '0');
INSERT INTO `regions` VALUES ('011003009', '3', '泰顺县', '0');
INSERT INTO `regions` VALUES ('011003010', '3', '瑞安市', '0');
INSERT INTO `regions` VALUES ('011003011', '3', '乐清市', '0');
INSERT INTO `regions` VALUES ('011004', '2', '嘉兴市', '0');
INSERT INTO `regions` VALUES ('011004001', '3', '南湖区', '0');
INSERT INTO `regions` VALUES ('011004002', '3', '秀洲区', '0');
INSERT INTO `regions` VALUES ('011004003', '3', '嘉善县', '0');
INSERT INTO `regions` VALUES ('011004004', '3', '海盐县', '0');
INSERT INTO `regions` VALUES ('011004005', '3', '海宁市', '0');
INSERT INTO `regions` VALUES ('011004006', '3', '平湖市', '0');
INSERT INTO `regions` VALUES ('011004007', '3', '桐乡市', '0');
INSERT INTO `regions` VALUES ('011005', '2', '湖州市', '0');
INSERT INTO `regions` VALUES ('011005001', '3', '吴兴区', '0');
INSERT INTO `regions` VALUES ('011005002', '3', '南浔区', '0');
INSERT INTO `regions` VALUES ('011005003', '3', '德清县', '0');
INSERT INTO `regions` VALUES ('011005004', '3', '长兴县', '0');
INSERT INTO `regions` VALUES ('011005005', '3', '安吉县', '0');
INSERT INTO `regions` VALUES ('011006', '2', '绍兴市', '0');
INSERT INTO `regions` VALUES ('011006001', '3', '越城区', '0');
INSERT INTO `regions` VALUES ('011006002', '3', '柯桥区', '0');
INSERT INTO `regions` VALUES ('011006003', '3', '上虞区', '0');
INSERT INTO `regions` VALUES ('011006004', '3', '新昌县', '0');
INSERT INTO `regions` VALUES ('011006005', '3', '诸暨市', '0');
INSERT INTO `regions` VALUES ('011006006', '3', '嵊州市', '0');
INSERT INTO `regions` VALUES ('011007', '2', '金华市', '0');
INSERT INTO `regions` VALUES ('011007001', '3', '婺城区', '0');
INSERT INTO `regions` VALUES ('011007002', '3', '金东区', '0');
INSERT INTO `regions` VALUES ('011007003', '3', '武义县', '0');
INSERT INTO `regions` VALUES ('011007004', '3', '浦江县', '0');
INSERT INTO `regions` VALUES ('011007005', '3', '磐安县', '0');
INSERT INTO `regions` VALUES ('011007006', '3', '兰溪市', '0');
INSERT INTO `regions` VALUES ('011007007', '3', '义乌市', '0');
INSERT INTO `regions` VALUES ('011007008', '3', '东阳市', '0');
INSERT INTO `regions` VALUES ('011007009', '3', '永康市', '0');
INSERT INTO `regions` VALUES ('011008', '2', '衢州市', '0');
INSERT INTO `regions` VALUES ('011008001', '3', '柯城区', '0');
INSERT INTO `regions` VALUES ('011008002', '3', '衢江区', '0');
INSERT INTO `regions` VALUES ('011008003', '3', '常山县', '0');
INSERT INTO `regions` VALUES ('011008004', '3', '开化县', '0');
INSERT INTO `regions` VALUES ('011008005', '3', '龙游县', '0');
INSERT INTO `regions` VALUES ('011008006', '3', '江山市', '0');
INSERT INTO `regions` VALUES ('011009', '2', '舟山市', '0');
INSERT INTO `regions` VALUES ('011009001', '3', '定海区', '0');
INSERT INTO `regions` VALUES ('011009002', '3', '普陀区', '0');
INSERT INTO `regions` VALUES ('011009003', '3', '岱山县', '0');
INSERT INTO `regions` VALUES ('011009004', '3', '嵊泗县', '0');
INSERT INTO `regions` VALUES ('011010', '2', '台州市', '0');
INSERT INTO `regions` VALUES ('011010001', '3', '椒江区', '0');
INSERT INTO `regions` VALUES ('011010002', '3', '黄岩区', '0');
INSERT INTO `regions` VALUES ('011010003', '3', '路桥区', '0');
INSERT INTO `regions` VALUES ('011010004', '3', '玉环县', '0');
INSERT INTO `regions` VALUES ('011010005', '3', '三门县', '0');
INSERT INTO `regions` VALUES ('011010006', '3', '天台县', '0');
INSERT INTO `regions` VALUES ('011010007', '3', '仙居县', '0');
INSERT INTO `regions` VALUES ('011010008', '3', '温岭市', '0');
INSERT INTO `regions` VALUES ('011010009', '3', '临海市', '0');
INSERT INTO `regions` VALUES ('011011', '2', '丽水市', '0');
INSERT INTO `regions` VALUES ('011011001', '3', '莲都区', '0');
INSERT INTO `regions` VALUES ('011011002', '3', '青田县', '0');
INSERT INTO `regions` VALUES ('011011003', '3', '缙云县', '0');
INSERT INTO `regions` VALUES ('011011004', '3', '遂昌县', '0');
INSERT INTO `regions` VALUES ('011011005', '3', '松阳县', '0');
INSERT INTO `regions` VALUES ('011011006', '3', '云和县', '0');
INSERT INTO `regions` VALUES ('011011007', '3', '庆元县', '0');
INSERT INTO `regions` VALUES ('011011008', '3', '景宁畲族自治县', '0');
INSERT INTO `regions` VALUES ('011011009', '3', '龙泉市', '0');
INSERT INTO `regions` VALUES ('011012', '2', '舟山群岛新区', '0');
INSERT INTO `regions` VALUES ('011012001', '3', '金塘岛', '0');
INSERT INTO `regions` VALUES ('011012002', '3', '六横岛', '0');
INSERT INTO `regions` VALUES ('011012003', '3', '衢山岛', '0');
INSERT INTO `regions` VALUES ('011012004', '3', '舟山本岛西北部', '0');
INSERT INTO `regions` VALUES ('011012005', '3', '岱山岛西南部', '0');
INSERT INTO `regions` VALUES ('011012006', '3', '泗礁岛', '0');
INSERT INTO `regions` VALUES ('011012007', '3', '朱家尖岛', '0');
INSERT INTO `regions` VALUES ('011012008', '3', '洋山岛', '0');
INSERT INTO `regions` VALUES ('011012009', '3', '长涂岛', '0');
INSERT INTO `regions` VALUES ('011012010', '3', '虾峙岛', '0');
INSERT INTO `regions` VALUES ('012', '1', '安徽省', '0');
INSERT INTO `regions` VALUES ('012001', '2', '合肥市', '0');
INSERT INTO `regions` VALUES ('012001001', '3', '瑶海区', '0');
INSERT INTO `regions` VALUES ('012001002', '3', '庐阳区', '0');
INSERT INTO `regions` VALUES ('012001003', '3', '蜀山区', '0');
INSERT INTO `regions` VALUES ('012001004', '3', '包河区', '0');
INSERT INTO `regions` VALUES ('012001005', '3', '长丰县', '0');
INSERT INTO `regions` VALUES ('012001006', '3', '肥东县', '0');
INSERT INTO `regions` VALUES ('012001007', '3', '肥西县', '0');
INSERT INTO `regions` VALUES ('012001008', '3', '庐江县', '0');
INSERT INTO `regions` VALUES ('012001009', '3', '巢湖市', '0');
INSERT INTO `regions` VALUES ('012002', '2', '芜湖市', '0');
INSERT INTO `regions` VALUES ('012002001', '3', '镜湖区', '0');
INSERT INTO `regions` VALUES ('012002002', '3', '弋江区', '0');
INSERT INTO `regions` VALUES ('012002003', '3', '鸠江区', '0');
INSERT INTO `regions` VALUES ('012002004', '3', '三山区', '0');
INSERT INTO `regions` VALUES ('012002005', '3', '芜湖县', '0');
INSERT INTO `regions` VALUES ('012002006', '3', '繁昌县', '0');
INSERT INTO `regions` VALUES ('012002007', '3', '南陵县', '0');
INSERT INTO `regions` VALUES ('012002008', '3', '无为县', '0');
INSERT INTO `regions` VALUES ('012003', '2', '蚌埠市', '0');
INSERT INTO `regions` VALUES ('012003001', '3', '龙子湖区', '0');
INSERT INTO `regions` VALUES ('012003002', '3', '蚌山区', '0');
INSERT INTO `regions` VALUES ('012003003', '3', '禹会区', '0');
INSERT INTO `regions` VALUES ('012003004', '3', '淮上区', '0');
INSERT INTO `regions` VALUES ('012003005', '3', '怀远县', '0');
INSERT INTO `regions` VALUES ('012003006', '3', '五河县', '0');
INSERT INTO `regions` VALUES ('012003007', '3', '固镇县', '0');
INSERT INTO `regions` VALUES ('012004', '2', '淮南市', '0');
INSERT INTO `regions` VALUES ('012004001', '3', '大通区', '0');
INSERT INTO `regions` VALUES ('012004002', '3', '田家庵区', '0');
INSERT INTO `regions` VALUES ('012004003', '3', '谢家集区', '0');
INSERT INTO `regions` VALUES ('012004004', '3', '八公山区', '0');
INSERT INTO `regions` VALUES ('012004005', '3', '潘集区', '0');
INSERT INTO `regions` VALUES ('012004006', '3', '凤台县', '0');
INSERT INTO `regions` VALUES ('012005', '2', '马鞍山市', '0');
INSERT INTO `regions` VALUES ('012005001', '3', '花山区', '0');
INSERT INTO `regions` VALUES ('012005002', '3', '雨山区', '0');
INSERT INTO `regions` VALUES ('012005003', '3', '博望区', '0');
INSERT INTO `regions` VALUES ('012005004', '3', '当涂县', '0');
INSERT INTO `regions` VALUES ('012005005', '3', '含山县', '0');
INSERT INTO `regions` VALUES ('012005006', '3', '和县', '0');
INSERT INTO `regions` VALUES ('012006', '2', '淮北市', '0');
INSERT INTO `regions` VALUES ('012006001', '3', '杜集区', '0');
INSERT INTO `regions` VALUES ('012006002', '3', '相山区', '0');
INSERT INTO `regions` VALUES ('012006003', '3', '烈山区', '0');
INSERT INTO `regions` VALUES ('012006004', '3', '濉溪县', '0');
INSERT INTO `regions` VALUES ('012007', '2', '铜陵市', '0');
INSERT INTO `regions` VALUES ('012007001', '3', '铜官山区', '0');
INSERT INTO `regions` VALUES ('012007002', '3', '狮子山区', '0');
INSERT INTO `regions` VALUES ('012007003', '3', '郊区', '0');
INSERT INTO `regions` VALUES ('012007004', '3', '铜陵县', '0');
INSERT INTO `regions` VALUES ('012008', '2', '安庆市', '0');
INSERT INTO `regions` VALUES ('012008001', '3', '迎江区', '0');
INSERT INTO `regions` VALUES ('012008002', '3', '大观区', '0');
INSERT INTO `regions` VALUES ('012008003', '3', '宜秀区', '0');
INSERT INTO `regions` VALUES ('012008004', '3', '怀宁县', '0');
INSERT INTO `regions` VALUES ('012008005', '3', '枞阳县', '0');
INSERT INTO `regions` VALUES ('012008006', '3', '潜山县', '0');
INSERT INTO `regions` VALUES ('012008007', '3', '太湖县', '0');
INSERT INTO `regions` VALUES ('012008008', '3', '宿松县', '0');
INSERT INTO `regions` VALUES ('012008009', '3', '望江县', '0');
INSERT INTO `regions` VALUES ('012008010', '3', '岳西县', '0');
INSERT INTO `regions` VALUES ('012008011', '3', '桐城市', '0');
INSERT INTO `regions` VALUES ('012009', '2', '黄山市', '0');
INSERT INTO `regions` VALUES ('012009001', '3', '屯溪区', '0');
INSERT INTO `regions` VALUES ('012009002', '3', '黄山区', '0');
INSERT INTO `regions` VALUES ('012009003', '3', '徽州区', '0');
INSERT INTO `regions` VALUES ('012009004', '3', '歙县', '0');
INSERT INTO `regions` VALUES ('012009005', '3', '休宁县', '0');
INSERT INTO `regions` VALUES ('012009006', '3', '黟县', '0');
INSERT INTO `regions` VALUES ('012009007', '3', '祁门县', '0');
INSERT INTO `regions` VALUES ('012010', '2', '滁州市', '0');
INSERT INTO `regions` VALUES ('012010001', '3', '琅琊区', '0');
INSERT INTO `regions` VALUES ('012010002', '3', '南谯区', '0');
INSERT INTO `regions` VALUES ('012010003', '3', '来安县', '0');
INSERT INTO `regions` VALUES ('012010004', '3', '全椒县', '0');
INSERT INTO `regions` VALUES ('012010005', '3', '定远县', '0');
INSERT INTO `regions` VALUES ('012010006', '3', '凤阳县', '0');
INSERT INTO `regions` VALUES ('012010007', '3', '天长市', '0');
INSERT INTO `regions` VALUES ('012010008', '3', '明光市', '0');
INSERT INTO `regions` VALUES ('012011', '2', '阜阳市', '0');
INSERT INTO `regions` VALUES ('012011001', '3', '颍州区', '0');
INSERT INTO `regions` VALUES ('012011002', '3', '颍东区', '0');
INSERT INTO `regions` VALUES ('012011003', '3', '颍泉区', '0');
INSERT INTO `regions` VALUES ('012011004', '3', '临泉县', '0');
INSERT INTO `regions` VALUES ('012011005', '3', '太和县', '0');
INSERT INTO `regions` VALUES ('012011006', '3', '阜南县', '0');
INSERT INTO `regions` VALUES ('012011007', '3', '颍上县', '0');
INSERT INTO `regions` VALUES ('012011008', '3', '界首市', '0');
INSERT INTO `regions` VALUES ('012012', '2', '宿州市', '0');
INSERT INTO `regions` VALUES ('012012001', '3', '埇桥区', '0');
INSERT INTO `regions` VALUES ('012012002', '3', '砀山县', '0');
INSERT INTO `regions` VALUES ('012012003', '3', '萧县', '0');
INSERT INTO `regions` VALUES ('012012004', '3', '灵璧县', '0');
INSERT INTO `regions` VALUES ('012012005', '3', '泗县', '0');
INSERT INTO `regions` VALUES ('012013', '2', '六安市', '0');
INSERT INTO `regions` VALUES ('012013001', '3', '金安区', '0');
INSERT INTO `regions` VALUES ('012013002', '3', '裕安区', '0');
INSERT INTO `regions` VALUES ('012013003', '3', '寿县', '0');
INSERT INTO `regions` VALUES ('012013004', '3', '霍邱县', '0');
INSERT INTO `regions` VALUES ('012013005', '3', '舒城县', '0');
INSERT INTO `regions` VALUES ('012013006', '3', '金寨县', '0');
INSERT INTO `regions` VALUES ('012013007', '3', '霍山县', '0');
INSERT INTO `regions` VALUES ('012014', '2', '亳州市', '0');
INSERT INTO `regions` VALUES ('012014001', '3', '谯城区', '0');
INSERT INTO `regions` VALUES ('012014002', '3', '涡阳县', '0');
INSERT INTO `regions` VALUES ('012014003', '3', '蒙城县', '0');
INSERT INTO `regions` VALUES ('012014004', '3', '利辛县', '0');
INSERT INTO `regions` VALUES ('012015', '2', '池州市', '0');
INSERT INTO `regions` VALUES ('012015001', '3', '贵池区', '0');
INSERT INTO `regions` VALUES ('012015002', '3', '东至县', '0');
INSERT INTO `regions` VALUES ('012015003', '3', '石台县', '0');
INSERT INTO `regions` VALUES ('012015004', '3', '青阳县', '0');
INSERT INTO `regions` VALUES ('012016', '2', '宣城市', '0');
INSERT INTO `regions` VALUES ('012016001', '3', '宣州区', '0');
INSERT INTO `regions` VALUES ('012016002', '3', '郎溪县', '0');
INSERT INTO `regions` VALUES ('012016003', '3', '广德县', '0');
INSERT INTO `regions` VALUES ('012016004', '3', '泾县', '0');
INSERT INTO `regions` VALUES ('012016005', '3', '绩溪县', '0');
INSERT INTO `regions` VALUES ('012016006', '3', '旌德县', '0');
INSERT INTO `regions` VALUES ('012016007', '3', '宁国市', '0');
INSERT INTO `regions` VALUES ('013', '1', '福建省', '0');
INSERT INTO `regions` VALUES ('013001', '2', '福州市', '0');
INSERT INTO `regions` VALUES ('013001001', '3', '鼓楼区', '0');
INSERT INTO `regions` VALUES ('013001002', '3', '台江区', '0');
INSERT INTO `regions` VALUES ('013001003', '3', '仓山区', '0');
INSERT INTO `regions` VALUES ('013001004', '3', '马尾区', '0');
INSERT INTO `regions` VALUES ('013001005', '3', '晋安区', '0');
INSERT INTO `regions` VALUES ('013001006', '3', '闽侯县', '0');
INSERT INTO `regions` VALUES ('013001007', '3', '连江县', '0');
INSERT INTO `regions` VALUES ('013001008', '3', '罗源县', '0');
INSERT INTO `regions` VALUES ('013001009', '3', '闽清县', '0');
INSERT INTO `regions` VALUES ('013001010', '3', '永泰县', '0');
INSERT INTO `regions` VALUES ('013001011', '3', '平潭县', '0');
INSERT INTO `regions` VALUES ('013001012', '3', '福清市', '0');
INSERT INTO `regions` VALUES ('013001013', '3', '长乐市', '0');
INSERT INTO `regions` VALUES ('013002', '2', '厦门市', '0');
INSERT INTO `regions` VALUES ('013002001', '3', '思明区', '0');
INSERT INTO `regions` VALUES ('013002002', '3', '海沧区', '0');
INSERT INTO `regions` VALUES ('013002003', '3', '湖里区', '0');
INSERT INTO `regions` VALUES ('013002004', '3', '集美区', '0');
INSERT INTO `regions` VALUES ('013002005', '3', '同安区', '0');
INSERT INTO `regions` VALUES ('013002006', '3', '翔安区', '0');
INSERT INTO `regions` VALUES ('013003', '2', '莆田市', '0');
INSERT INTO `regions` VALUES ('013003001', '3', '城厢区', '0');
INSERT INTO `regions` VALUES ('013003002', '3', '涵江区', '0');
INSERT INTO `regions` VALUES ('013003003', '3', '荔城区', '0');
INSERT INTO `regions` VALUES ('013003004', '3', '秀屿区', '0');
INSERT INTO `regions` VALUES ('013003005', '3', '仙游县', '0');
INSERT INTO `regions` VALUES ('013004', '2', '三明市', '0');
INSERT INTO `regions` VALUES ('013004001', '3', '梅列区', '0');
INSERT INTO `regions` VALUES ('013004002', '3', '三元区', '0');
INSERT INTO `regions` VALUES ('013004003', '3', '明溪县', '0');
INSERT INTO `regions` VALUES ('013004004', '3', '清流县', '0');
INSERT INTO `regions` VALUES ('013004005', '3', '宁化县', '0');
INSERT INTO `regions` VALUES ('013004006', '3', '大田县', '0');
INSERT INTO `regions` VALUES ('013004007', '3', '尤溪县', '0');
INSERT INTO `regions` VALUES ('013004008', '3', '沙县', '0');
INSERT INTO `regions` VALUES ('013004009', '3', '将乐县', '0');
INSERT INTO `regions` VALUES ('013004010', '3', '泰宁县', '0');
INSERT INTO `regions` VALUES ('013004011', '3', '建宁县', '0');
INSERT INTO `regions` VALUES ('013004012', '3', '永安市', '0');
INSERT INTO `regions` VALUES ('013005', '2', '泉州市', '0');
INSERT INTO `regions` VALUES ('013005001', '3', '鲤城区', '0');
INSERT INTO `regions` VALUES ('013005002', '3', '丰泽区', '0');
INSERT INTO `regions` VALUES ('013005003', '3', '洛江区', '0');
INSERT INTO `regions` VALUES ('013005004', '3', '泉港区', '0');
INSERT INTO `regions` VALUES ('013005005', '3', '惠安县', '0');
INSERT INTO `regions` VALUES ('013005006', '3', '安溪县', '0');
INSERT INTO `regions` VALUES ('013005007', '3', '永春县', '0');
INSERT INTO `regions` VALUES ('013005008', '3', '德化县', '0');
INSERT INTO `regions` VALUES ('013005009', '3', '金门县', '0');
INSERT INTO `regions` VALUES ('013005010', '3', '石狮市', '0');
INSERT INTO `regions` VALUES ('013005011', '3', '晋江市', '0');
INSERT INTO `regions` VALUES ('013005012', '3', '南安市', '0');
INSERT INTO `regions` VALUES ('013006', '2', '漳州市', '0');
INSERT INTO `regions` VALUES ('013006001', '3', '芗城区', '0');
INSERT INTO `regions` VALUES ('013006002', '3', '龙文区', '0');
INSERT INTO `regions` VALUES ('013006003', '3', '云霄县', '0');
INSERT INTO `regions` VALUES ('013006004', '3', '漳浦县', '0');
INSERT INTO `regions` VALUES ('013006005', '3', '诏安县', '0');
INSERT INTO `regions` VALUES ('013006006', '3', '长泰县', '0');
INSERT INTO `regions` VALUES ('013006007', '3', '东山县', '0');
INSERT INTO `regions` VALUES ('013006008', '3', '南靖县', '0');
INSERT INTO `regions` VALUES ('013006009', '3', '平和县', '0');
INSERT INTO `regions` VALUES ('013006010', '3', '华安县', '0');
INSERT INTO `regions` VALUES ('013006011', '3', '龙海市', '0');
INSERT INTO `regions` VALUES ('013007', '2', '南平市', '0');
INSERT INTO `regions` VALUES ('013007001', '3', '延平区', '0');
INSERT INTO `regions` VALUES ('013007002', '3', '建阳区', '0');
INSERT INTO `regions` VALUES ('013007003', '3', '顺昌县', '0');
INSERT INTO `regions` VALUES ('013007004', '3', '浦城县', '0');
INSERT INTO `regions` VALUES ('013007005', '3', '光泽县', '0');
INSERT INTO `regions` VALUES ('013007006', '3', '松溪县', '0');
INSERT INTO `regions` VALUES ('013007007', '3', '政和县', '0');
INSERT INTO `regions` VALUES ('013007008', '3', '邵武市', '0');
INSERT INTO `regions` VALUES ('013007009', '3', '武夷山市', '0');
INSERT INTO `regions` VALUES ('013007010', '3', '建瓯市', '0');
INSERT INTO `regions` VALUES ('013008', '2', '龙岩市', '0');
INSERT INTO `regions` VALUES ('013008001', '3', '新罗区', '0');
INSERT INTO `regions` VALUES ('013008002', '3', '长汀县', '0');
INSERT INTO `regions` VALUES ('013008003', '3', '永定区', '0');
INSERT INTO `regions` VALUES ('013008004', '3', '上杭县', '0');
INSERT INTO `regions` VALUES ('013008005', '3', '武平县', '0');
INSERT INTO `regions` VALUES ('013008006', '3', '连城县', '0');
INSERT INTO `regions` VALUES ('013008007', '3', '漳平市', '0');
INSERT INTO `regions` VALUES ('013009', '2', '宁德市', '0');
INSERT INTO `regions` VALUES ('013009001', '3', '蕉城区', '0');
INSERT INTO `regions` VALUES ('013009002', '3', '霞浦县', '0');
INSERT INTO `regions` VALUES ('013009003', '3', '古田县', '0');
INSERT INTO `regions` VALUES ('013009004', '3', '屏南县', '0');
INSERT INTO `regions` VALUES ('013009005', '3', '寿宁县', '0');
INSERT INTO `regions` VALUES ('013009006', '3', '周宁县', '0');
INSERT INTO `regions` VALUES ('013009007', '3', '柘荣县', '0');
INSERT INTO `regions` VALUES ('013009008', '3', '福安市', '0');
INSERT INTO `regions` VALUES ('013009009', '3', '福鼎市', '0');
INSERT INTO `regions` VALUES ('014', '1', '江西省', '0');
INSERT INTO `regions` VALUES ('014001', '2', '南昌市', '0');
INSERT INTO `regions` VALUES ('014001001', '3', '东湖区', '0');
INSERT INTO `regions` VALUES ('014001002', '3', '西湖区', '0');
INSERT INTO `regions` VALUES ('014001003', '3', '青云谱区', '0');
INSERT INTO `regions` VALUES ('014001004', '3', '湾里区', '0');
INSERT INTO `regions` VALUES ('014001005', '3', '青山湖区', '0');
INSERT INTO `regions` VALUES ('014001006', '3', '南昌县', '0');
INSERT INTO `regions` VALUES ('014001007', '3', '新建县', '0');
INSERT INTO `regions` VALUES ('014001008', '3', '安义县', '0');
INSERT INTO `regions` VALUES ('014001009', '3', '进贤县', '0');
INSERT INTO `regions` VALUES ('014002', '2', '景德镇市', '0');
INSERT INTO `regions` VALUES ('014002001', '3', '昌江区', '0');
INSERT INTO `regions` VALUES ('014002002', '3', '珠山区', '0');
INSERT INTO `regions` VALUES ('014002003', '3', '浮梁县', '0');
INSERT INTO `regions` VALUES ('014002004', '3', '乐平市', '0');
INSERT INTO `regions` VALUES ('014003', '2', '萍乡市', '0');
INSERT INTO `regions` VALUES ('014003001', '3', '安源区', '0');
INSERT INTO `regions` VALUES ('014003002', '3', '湘东区', '0');
INSERT INTO `regions` VALUES ('014003003', '3', '莲花县', '0');
INSERT INTO `regions` VALUES ('014003004', '3', '上栗县', '0');
INSERT INTO `regions` VALUES ('014003005', '3', '芦溪县', '0');
INSERT INTO `regions` VALUES ('014004', '2', '九江市', '0');
INSERT INTO `regions` VALUES ('014004001', '3', '庐山区', '0');
INSERT INTO `regions` VALUES ('014004002', '3', '浔阳区', '0');
INSERT INTO `regions` VALUES ('014004003', '3', '九江县', '0');
INSERT INTO `regions` VALUES ('014004004', '3', '武宁县', '0');
INSERT INTO `regions` VALUES ('014004005', '3', '修水县', '0');
INSERT INTO `regions` VALUES ('014004006', '3', '永修县', '0');
INSERT INTO `regions` VALUES ('014004007', '3', '德安县', '0');
INSERT INTO `regions` VALUES ('014004008', '3', '星子县', '0');
INSERT INTO `regions` VALUES ('014004009', '3', '都昌县', '0');
INSERT INTO `regions` VALUES ('014004010', '3', '湖口县', '0');
INSERT INTO `regions` VALUES ('014004011', '3', '彭泽县', '0');
INSERT INTO `regions` VALUES ('014004012', '3', '瑞昌市', '0');
INSERT INTO `regions` VALUES ('014004013', '3', '共青城市', '0');
INSERT INTO `regions` VALUES ('014005', '2', '新余市', '0');
INSERT INTO `regions` VALUES ('014005001', '3', '渝水区', '0');
INSERT INTO `regions` VALUES ('014005002', '3', '分宜县', '0');
INSERT INTO `regions` VALUES ('014006', '2', '鹰潭市', '0');
INSERT INTO `regions` VALUES ('014006001', '3', '月湖区', '0');
INSERT INTO `regions` VALUES ('014006002', '3', '余江县', '0');
INSERT INTO `regions` VALUES ('014006003', '3', '贵溪市', '0');
INSERT INTO `regions` VALUES ('014007', '2', '赣州市', '0');
INSERT INTO `regions` VALUES ('014007001', '3', '章贡区', '0');
INSERT INTO `regions` VALUES ('014007002', '3', '南康区', '0');
INSERT INTO `regions` VALUES ('014007003', '3', '赣县', '0');
INSERT INTO `regions` VALUES ('014007004', '3', '信丰县', '0');
INSERT INTO `regions` VALUES ('014007005', '3', '大余县', '0');
INSERT INTO `regions` VALUES ('014007006', '3', '上犹县', '0');
INSERT INTO `regions` VALUES ('014007007', '3', '崇义县', '0');
INSERT INTO `regions` VALUES ('014007008', '3', '安远县', '0');
INSERT INTO `regions` VALUES ('014007009', '3', '龙南县', '0');
INSERT INTO `regions` VALUES ('014007010', '3', '定南县', '0');
INSERT INTO `regions` VALUES ('014007011', '3', '全南县', '0');
INSERT INTO `regions` VALUES ('014007012', '3', '宁都县', '0');
INSERT INTO `regions` VALUES ('014007013', '3', '于都县', '0');
INSERT INTO `regions` VALUES ('014007014', '3', '兴国县', '0');
INSERT INTO `regions` VALUES ('014007015', '3', '会昌县', '0');
INSERT INTO `regions` VALUES ('014007016', '3', '寻乌县', '0');
INSERT INTO `regions` VALUES ('014007017', '3', '石城县', '0');
INSERT INTO `regions` VALUES ('014007018', '3', '瑞金市', '0');
INSERT INTO `regions` VALUES ('014008', '2', '吉安市', '0');
INSERT INTO `regions` VALUES ('014008001', '3', '吉州区', '0');
INSERT INTO `regions` VALUES ('014008002', '3', '青原区', '0');
INSERT INTO `regions` VALUES ('014008003', '3', '吉安县', '0');
INSERT INTO `regions` VALUES ('014008004', '3', '吉水县', '0');
INSERT INTO `regions` VALUES ('014008005', '3', '峡江县', '0');
INSERT INTO `regions` VALUES ('014008006', '3', '新干县', '0');
INSERT INTO `regions` VALUES ('014008007', '3', '永丰县', '0');
INSERT INTO `regions` VALUES ('014008008', '3', '泰和县', '0');
INSERT INTO `regions` VALUES ('014008009', '3', '遂川县', '0');
INSERT INTO `regions` VALUES ('014008010', '3', '万安县', '0');
INSERT INTO `regions` VALUES ('014008011', '3', '安福县', '0');
INSERT INTO `regions` VALUES ('014008012', '3', '永新县', '0');
INSERT INTO `regions` VALUES ('014008013', '3', '井冈山市', '0');
INSERT INTO `regions` VALUES ('014009', '2', '宜春市', '0');
INSERT INTO `regions` VALUES ('014009001', '3', '袁州区', '0');
INSERT INTO `regions` VALUES ('014009002', '3', '奉新县', '0');
INSERT INTO `regions` VALUES ('014009003', '3', '万载县', '0');
INSERT INTO `regions` VALUES ('014009004', '3', '上高县', '0');
INSERT INTO `regions` VALUES ('014009005', '3', '宜丰县', '0');
INSERT INTO `regions` VALUES ('014009006', '3', '靖安县', '0');
INSERT INTO `regions` VALUES ('014009007', '3', '铜鼓县', '0');
INSERT INTO `regions` VALUES ('014009008', '3', '丰城市', '0');
INSERT INTO `regions` VALUES ('014009009', '3', '樟树市', '0');
INSERT INTO `regions` VALUES ('014009010', '3', '高安市', '0');
INSERT INTO `regions` VALUES ('014010', '2', '抚州市', '0');
INSERT INTO `regions` VALUES ('014010001', '3', '临川区', '0');
INSERT INTO `regions` VALUES ('014010002', '3', '南城县', '0');
INSERT INTO `regions` VALUES ('014010003', '3', '黎川县', '0');
INSERT INTO `regions` VALUES ('014010004', '3', '南丰县', '0');
INSERT INTO `regions` VALUES ('014010005', '3', '崇仁县', '0');
INSERT INTO `regions` VALUES ('014010006', '3', '乐安县', '0');
INSERT INTO `regions` VALUES ('014010007', '3', '宜黄县', '0');
INSERT INTO `regions` VALUES ('014010008', '3', '金溪县', '0');
INSERT INTO `regions` VALUES ('014010009', '3', '资溪县', '0');
INSERT INTO `regions` VALUES ('014010010', '3', '东乡县', '0');
INSERT INTO `regions` VALUES ('014010011', '3', '广昌县', '0');
INSERT INTO `regions` VALUES ('014011', '2', '上饶市', '0');
INSERT INTO `regions` VALUES ('014011001', '3', '信州区', '0');
INSERT INTO `regions` VALUES ('014011002', '3', '上饶县', '0');
INSERT INTO `regions` VALUES ('014011003', '3', '广丰县', '0');
INSERT INTO `regions` VALUES ('014011004', '3', '玉山县', '0');
INSERT INTO `regions` VALUES ('014011005', '3', '铅山县', '0');
INSERT INTO `regions` VALUES ('014011006', '3', '横峰县', '0');
INSERT INTO `regions` VALUES ('014011007', '3', '弋阳县', '0');
INSERT INTO `regions` VALUES ('014011008', '3', '余干县', '0');
INSERT INTO `regions` VALUES ('014011009', '3', '鄱阳县', '0');
INSERT INTO `regions` VALUES ('014011010', '3', '万年县', '0');
INSERT INTO `regions` VALUES ('014011011', '3', '婺源县', '0');
INSERT INTO `regions` VALUES ('014011012', '3', '德兴市', '0');
INSERT INTO `regions` VALUES ('015', '1', '山东省', '0');
INSERT INTO `regions` VALUES ('015001', '2', '济南市', '0');
INSERT INTO `regions` VALUES ('015001001', '3', '历下区', '0');
INSERT INTO `regions` VALUES ('015001002', '3', '市中区', '0');
INSERT INTO `regions` VALUES ('015001003', '3', '槐荫区', '0');
INSERT INTO `regions` VALUES ('015001004', '3', '天桥区', '0');
INSERT INTO `regions` VALUES ('015001005', '3', '历城区', '0');
INSERT INTO `regions` VALUES ('015001006', '3', '长清区', '0');
INSERT INTO `regions` VALUES ('015001007', '3', '平阴县', '0');
INSERT INTO `regions` VALUES ('015001008', '3', '济阳县', '0');
INSERT INTO `regions` VALUES ('015001009', '3', '商河县', '0');
INSERT INTO `regions` VALUES ('015001010', '3', '章丘市', '0');
INSERT INTO `regions` VALUES ('015002', '2', '青岛市', '0');
INSERT INTO `regions` VALUES ('015002001', '3', '市南区', '0');
INSERT INTO `regions` VALUES ('015002002', '3', '市北区', '0');
INSERT INTO `regions` VALUES ('015002003', '3', '黄岛区', '0');
INSERT INTO `regions` VALUES ('015002004', '3', '崂山区', '0');
INSERT INTO `regions` VALUES ('015002005', '3', '李沧区', '0');
INSERT INTO `regions` VALUES ('015002006', '3', '城阳区', '0');
INSERT INTO `regions` VALUES ('015002007', '3', '胶州市', '0');
INSERT INTO `regions` VALUES ('015002008', '3', '即墨市', '0');
INSERT INTO `regions` VALUES ('015002009', '3', '平度市', '0');
INSERT INTO `regions` VALUES ('015002010', '3', '莱西市', '0');
INSERT INTO `regions` VALUES ('015002011', '3', '西海岸新区', '0');
INSERT INTO `regions` VALUES ('015003', '2', '淄博市', '0');
INSERT INTO `regions` VALUES ('015003001', '3', '淄川区', '0');
INSERT INTO `regions` VALUES ('015003002', '3', '张店区', '0');
INSERT INTO `regions` VALUES ('015003003', '3', '博山区', '0');
INSERT INTO `regions` VALUES ('015003004', '3', '临淄区', '0');
INSERT INTO `regions` VALUES ('015003005', '3', '周村区', '0');
INSERT INTO `regions` VALUES ('015003006', '3', '桓台县', '0');
INSERT INTO `regions` VALUES ('015003007', '3', '高青县', '0');
INSERT INTO `regions` VALUES ('015003008', '3', '沂源县', '0');
INSERT INTO `regions` VALUES ('015004', '2', '枣庄市', '0');
INSERT INTO `regions` VALUES ('015004001', '3', '市中区', '0');
INSERT INTO `regions` VALUES ('015004002', '3', '薛城区', '0');
INSERT INTO `regions` VALUES ('015004003', '3', '峄城区', '0');
INSERT INTO `regions` VALUES ('015004004', '3', '台儿庄区', '0');
INSERT INTO `regions` VALUES ('015004005', '3', '山亭区', '0');
INSERT INTO `regions` VALUES ('015004006', '3', '滕州市', '0');
INSERT INTO `regions` VALUES ('015005', '2', '东营市', '0');
INSERT INTO `regions` VALUES ('015005001', '3', '东营区', '0');
INSERT INTO `regions` VALUES ('015005002', '3', '河口区', '0');
INSERT INTO `regions` VALUES ('015005003', '3', '垦利县', '0');
INSERT INTO `regions` VALUES ('015005004', '3', '利津县', '0');
INSERT INTO `regions` VALUES ('015005005', '3', '广饶县', '0');
INSERT INTO `regions` VALUES ('015006', '2', '烟台市', '0');
INSERT INTO `regions` VALUES ('015006001', '3', '芝罘区', '0');
INSERT INTO `regions` VALUES ('015006002', '3', '福山区', '0');
INSERT INTO `regions` VALUES ('015006003', '3', '牟平区', '0');
INSERT INTO `regions` VALUES ('015006004', '3', '莱山区', '0');
INSERT INTO `regions` VALUES ('015006005', '3', '长岛县', '0');
INSERT INTO `regions` VALUES ('015006006', '3', '龙口市', '0');
INSERT INTO `regions` VALUES ('015006007', '3', '莱阳市', '0');
INSERT INTO `regions` VALUES ('015006008', '3', '莱州市', '0');
INSERT INTO `regions` VALUES ('015006009', '3', '蓬莱市', '0');
INSERT INTO `regions` VALUES ('015006010', '3', '招远市', '0');
INSERT INTO `regions` VALUES ('015006011', '3', '栖霞市', '0');
INSERT INTO `regions` VALUES ('015006012', '3', '海阳市', '0');
INSERT INTO `regions` VALUES ('015007', '2', '潍坊市', '0');
INSERT INTO `regions` VALUES ('015007001', '3', '潍城区', '0');
INSERT INTO `regions` VALUES ('015007002', '3', '寒亭区', '0');
INSERT INTO `regions` VALUES ('015007003', '3', '坊子区', '0');
INSERT INTO `regions` VALUES ('015007004', '3', '奎文区', '0');
INSERT INTO `regions` VALUES ('015007005', '3', '临朐县', '0');
INSERT INTO `regions` VALUES ('015007006', '3', '昌乐县', '0');
INSERT INTO `regions` VALUES ('015007007', '3', '青州市', '0');
INSERT INTO `regions` VALUES ('015007008', '3', '诸城市', '0');
INSERT INTO `regions` VALUES ('015007009', '3', '寿光市', '0');
INSERT INTO `regions` VALUES ('015007010', '3', '安丘市', '0');
INSERT INTO `regions` VALUES ('015007011', '3', '高密市', '0');
INSERT INTO `regions` VALUES ('015007012', '3', '昌邑市', '0');
INSERT INTO `regions` VALUES ('015008', '2', '济宁市', '0');
INSERT INTO `regions` VALUES ('015008001', '3', '任城区', '0');
INSERT INTO `regions` VALUES ('015008002', '3', '兖州区', '0');
INSERT INTO `regions` VALUES ('015008003', '3', '微山县', '0');
INSERT INTO `regions` VALUES ('015008004', '3', '鱼台县', '0');
INSERT INTO `regions` VALUES ('015008005', '3', '金乡县', '0');
INSERT INTO `regions` VALUES ('015008006', '3', '嘉祥县', '0');
INSERT INTO `regions` VALUES ('015008007', '3', '汶上县', '0');
INSERT INTO `regions` VALUES ('015008008', '3', '泗水县', '0');
INSERT INTO `regions` VALUES ('015008009', '3', '梁山县', '0');
INSERT INTO `regions` VALUES ('015008010', '3', '曲阜市', '0');
INSERT INTO `regions` VALUES ('015008011', '3', '邹城市', '0');
INSERT INTO `regions` VALUES ('015009', '2', '泰安市', '0');
INSERT INTO `regions` VALUES ('015009001', '3', '泰山区', '0');
INSERT INTO `regions` VALUES ('015009002', '3', '岱岳区', '0');
INSERT INTO `regions` VALUES ('015009003', '3', '宁阳县', '0');
INSERT INTO `regions` VALUES ('015009004', '3', '东平县', '0');
INSERT INTO `regions` VALUES ('015009005', '3', '新泰市', '0');
INSERT INTO `regions` VALUES ('015009006', '3', '肥城市', '0');
INSERT INTO `regions` VALUES ('015010', '2', '威海市', '0');
INSERT INTO `regions` VALUES ('015010001', '3', '环翠区', '0');
INSERT INTO `regions` VALUES ('015010002', '3', '文登区', '0');
INSERT INTO `regions` VALUES ('015010003', '3', '荣成市', '0');
INSERT INTO `regions` VALUES ('015010004', '3', '乳山市', '0');
INSERT INTO `regions` VALUES ('015011', '2', '日照市', '0');
INSERT INTO `regions` VALUES ('015011001', '3', '东港区', '0');
INSERT INTO `regions` VALUES ('015011002', '3', '岚山区', '0');
INSERT INTO `regions` VALUES ('015011003', '3', '五莲县', '0');
INSERT INTO `regions` VALUES ('015011004', '3', '莒县', '0');
INSERT INTO `regions` VALUES ('015012', '2', '莱芜市', '0');
INSERT INTO `regions` VALUES ('015012001', '3', '莱城区', '0');
INSERT INTO `regions` VALUES ('015012002', '3', '钢城区', '0');
INSERT INTO `regions` VALUES ('015013', '2', '临沂市', '0');
INSERT INTO `regions` VALUES ('015013001', '3', '兰山区', '0');
INSERT INTO `regions` VALUES ('015013002', '3', '罗庄区', '0');
INSERT INTO `regions` VALUES ('015013003', '3', '河东区', '0');
INSERT INTO `regions` VALUES ('015013004', '3', '沂南县', '0');
INSERT INTO `regions` VALUES ('015013005', '3', '郯城县', '0');
INSERT INTO `regions` VALUES ('015013006', '3', '沂水县', '0');
INSERT INTO `regions` VALUES ('015013007', '3', '兰陵县', '0');
INSERT INTO `regions` VALUES ('015013008', '3', '费县', '0');
INSERT INTO `regions` VALUES ('015013009', '3', '平邑县', '0');
INSERT INTO `regions` VALUES ('015013010', '3', '莒南县', '0');
INSERT INTO `regions` VALUES ('015013011', '3', '蒙阴县', '0');
INSERT INTO `regions` VALUES ('015013012', '3', '临沭县', '0');
INSERT INTO `regions` VALUES ('015014', '2', '德州市', '0');
INSERT INTO `regions` VALUES ('015014001', '3', '德城区', '0');
INSERT INTO `regions` VALUES ('015014002', '3', '陵城区', '0');
INSERT INTO `regions` VALUES ('015014003', '3', '宁津县', '0');
INSERT INTO `regions` VALUES ('015014004', '3', '庆云县', '0');
INSERT INTO `regions` VALUES ('015014005', '3', '临邑县', '0');
INSERT INTO `regions` VALUES ('015014006', '3', '齐河县', '0');
INSERT INTO `regions` VALUES ('015014007', '3', '平原县', '0');
INSERT INTO `regions` VALUES ('015014008', '3', '夏津县', '0');
INSERT INTO `regions` VALUES ('015014009', '3', '武城县', '0');
INSERT INTO `regions` VALUES ('015014010', '3', '乐陵市', '0');
INSERT INTO `regions` VALUES ('015014011', '3', '禹城市', '0');
INSERT INTO `regions` VALUES ('015015', '2', '聊城市', '0');
INSERT INTO `regions` VALUES ('015015001', '3', '东昌府区', '0');
INSERT INTO `regions` VALUES ('015015002', '3', '阳谷县', '0');
INSERT INTO `regions` VALUES ('015015003', '3', '莘县', '0');
INSERT INTO `regions` VALUES ('015015004', '3', '茌平县', '0');
INSERT INTO `regions` VALUES ('015015005', '3', '东阿县', '0');
INSERT INTO `regions` VALUES ('015015006', '3', '冠县', '0');
INSERT INTO `regions` VALUES ('015015007', '3', '高唐县', '0');
INSERT INTO `regions` VALUES ('015015008', '3', '临清市', '0');
INSERT INTO `regions` VALUES ('015016', '2', '滨州市', '0');
INSERT INTO `regions` VALUES ('015016001', '3', '滨城区', '0');
INSERT INTO `regions` VALUES ('015016002', '3', '沾化区', '0');
INSERT INTO `regions` VALUES ('015016003', '3', '惠民县', '0');
INSERT INTO `regions` VALUES ('015016004', '3', '阳信县', '0');
INSERT INTO `regions` VALUES ('015016005', '3', '无棣县', '0');
INSERT INTO `regions` VALUES ('015016006', '3', '博兴县', '0');
INSERT INTO `regions` VALUES ('015016007', '3', '邹平县', '0');
INSERT INTO `regions` VALUES ('015016008', '3', '北海新区', '0');
INSERT INTO `regions` VALUES ('015017', '2', '菏泽市', '0');
INSERT INTO `regions` VALUES ('015017001', '3', '牡丹区', '0');
INSERT INTO `regions` VALUES ('015017002', '3', '曹县', '0');
INSERT INTO `regions` VALUES ('015017003', '3', '单县', '0');
INSERT INTO `regions` VALUES ('015017004', '3', '成武县', '0');
INSERT INTO `regions` VALUES ('015017005', '3', '巨野县', '0');
INSERT INTO `regions` VALUES ('015017006', '3', '郓城县', '0');
INSERT INTO `regions` VALUES ('015017007', '3', '鄄城县', '0');
INSERT INTO `regions` VALUES ('015017008', '3', '定陶县', '0');
INSERT INTO `regions` VALUES ('015017009', '3', '东明县', '0');
INSERT INTO `regions` VALUES ('016', '1', '河南省', '0');
INSERT INTO `regions` VALUES ('016001', '2', '郑州市', '0');
INSERT INTO `regions` VALUES ('016001001', '3', '中原区', '0');
INSERT INTO `regions` VALUES ('016001002', '3', '二七区', '0');
INSERT INTO `regions` VALUES ('016001003', '3', '管城回族区', '0');
INSERT INTO `regions` VALUES ('016001004', '3', '金水区', '0');
INSERT INTO `regions` VALUES ('016001005', '3', '上街区', '0');
INSERT INTO `regions` VALUES ('016001006', '3', '惠济区', '0');
INSERT INTO `regions` VALUES ('016001007', '3', '中牟县', '0');
INSERT INTO `regions` VALUES ('016001008', '3', '巩义市', '0');
INSERT INTO `regions` VALUES ('016001009', '3', '荥阳市', '0');
INSERT INTO `regions` VALUES ('016001010', '3', '新密市', '0');
INSERT INTO `regions` VALUES ('016001011', '3', '新郑市', '0');
INSERT INTO `regions` VALUES ('016001012', '3', '登封市', '0');
INSERT INTO `regions` VALUES ('016002', '2', '开封市', '0');
INSERT INTO `regions` VALUES ('016002001', '3', '龙亭区', '0');
INSERT INTO `regions` VALUES ('016002002', '3', '顺河回族区', '0');
INSERT INTO `regions` VALUES ('016002003', '3', '鼓楼区', '0');
INSERT INTO `regions` VALUES ('016002004', '3', '禹王台区', '0');
INSERT INTO `regions` VALUES ('016002005', '3', '祥符区', '0');
INSERT INTO `regions` VALUES ('016002006', '3', '杞县', '0');
INSERT INTO `regions` VALUES ('016002007', '3', '通许县', '0');
INSERT INTO `regions` VALUES ('016002008', '3', '尉氏县', '0');
INSERT INTO `regions` VALUES ('016002009', '3', '兰考县', '0');
INSERT INTO `regions` VALUES ('016003', '2', '洛阳市', '0');
INSERT INTO `regions` VALUES ('016003001', '3', '老城区', '0');
INSERT INTO `regions` VALUES ('016003002', '3', '西工区', '0');
INSERT INTO `regions` VALUES ('016003003', '3', '瀍河回族区', '0');
INSERT INTO `regions` VALUES ('016003004', '3', '涧西区', '0');
INSERT INTO `regions` VALUES ('016003005', '3', '吉利区', '0');
INSERT INTO `regions` VALUES ('016003006', '3', '洛龙区', '0');
INSERT INTO `regions` VALUES ('016003007', '3', '孟津县', '0');
INSERT INTO `regions` VALUES ('016003008', '3', '新安县', '0');
INSERT INTO `regions` VALUES ('016003009', '3', '栾川县', '0');
INSERT INTO `regions` VALUES ('016003010', '3', '嵩县', '0');
INSERT INTO `regions` VALUES ('016003011', '3', '汝阳县', '0');
INSERT INTO `regions` VALUES ('016003012', '3', '宜阳县', '0');
INSERT INTO `regions` VALUES ('016003013', '3', '洛宁县', '0');
INSERT INTO `regions` VALUES ('016003014', '3', '伊川县', '0');
INSERT INTO `regions` VALUES ('016003015', '3', '偃师市', '0');
INSERT INTO `regions` VALUES ('016004', '2', '平顶山市', '0');
INSERT INTO `regions` VALUES ('016004001', '3', '新华区', '0');
INSERT INTO `regions` VALUES ('016004002', '3', '卫东区', '0');
INSERT INTO `regions` VALUES ('016004003', '3', '石龙区', '0');
INSERT INTO `regions` VALUES ('016004004', '3', '湛河区', '0');
INSERT INTO `regions` VALUES ('016004005', '3', '宝丰县', '0');
INSERT INTO `regions` VALUES ('016004006', '3', '叶县', '0');
INSERT INTO `regions` VALUES ('016004007', '3', '鲁山县', '0');
INSERT INTO `regions` VALUES ('016004008', '3', '郏县', '0');
INSERT INTO `regions` VALUES ('016004009', '3', '舞钢市', '0');
INSERT INTO `regions` VALUES ('016004010', '3', '汝州市', '0');
INSERT INTO `regions` VALUES ('016005', '2', '安阳市', '0');
INSERT INTO `regions` VALUES ('016005001', '3', '文峰区', '0');
INSERT INTO `regions` VALUES ('016005002', '3', '北关区', '0');
INSERT INTO `regions` VALUES ('016005003', '3', '殷都区', '0');
INSERT INTO `regions` VALUES ('016005004', '3', '龙安区', '0');
INSERT INTO `regions` VALUES ('016005005', '3', '安阳县', '0');
INSERT INTO `regions` VALUES ('016005006', '3', '汤阴县', '0');
INSERT INTO `regions` VALUES ('016005007', '3', '滑县', '0');
INSERT INTO `regions` VALUES ('016005008', '3', '内黄县', '0');
INSERT INTO `regions` VALUES ('016005009', '3', '林州市', '0');
INSERT INTO `regions` VALUES ('016006', '2', '鹤壁市', '0');
INSERT INTO `regions` VALUES ('016006001', '3', '鹤山区', '0');
INSERT INTO `regions` VALUES ('016006002', '3', '山城区', '0');
INSERT INTO `regions` VALUES ('016006003', '3', '淇滨区', '0');
INSERT INTO `regions` VALUES ('016006004', '3', '浚县', '0');
INSERT INTO `regions` VALUES ('016006005', '3', '淇县', '0');
INSERT INTO `regions` VALUES ('016007', '2', '新乡市', '0');
INSERT INTO `regions` VALUES ('016007001', '3', '红旗区', '0');
INSERT INTO `regions` VALUES ('016007002', '3', '卫滨区', '0');
INSERT INTO `regions` VALUES ('016007003', '3', '凤泉区', '0');
INSERT INTO `regions` VALUES ('016007004', '3', '牧野区', '0');
INSERT INTO `regions` VALUES ('016007005', '3', '新乡县', '0');
INSERT INTO `regions` VALUES ('016007006', '3', '获嘉县', '0');
INSERT INTO `regions` VALUES ('016007007', '3', '原阳县', '0');
INSERT INTO `regions` VALUES ('016007008', '3', '延津县', '0');
INSERT INTO `regions` VALUES ('016007009', '3', '封丘县', '0');
INSERT INTO `regions` VALUES ('016007010', '3', '长垣县', '0');
INSERT INTO `regions` VALUES ('016007011', '3', '卫辉市', '0');
INSERT INTO `regions` VALUES ('016007012', '3', '辉县市', '0');
INSERT INTO `regions` VALUES ('016008', '2', '焦作市', '0');
INSERT INTO `regions` VALUES ('016008001', '3', '解放区', '0');
INSERT INTO `regions` VALUES ('016008002', '3', '中站区', '0');
INSERT INTO `regions` VALUES ('016008003', '3', '马村区', '0');
INSERT INTO `regions` VALUES ('016008004', '3', '山阳区', '0');
INSERT INTO `regions` VALUES ('016008005', '3', '修武县', '0');
INSERT INTO `regions` VALUES ('016008006', '3', '博爱县', '0');
INSERT INTO `regions` VALUES ('016008007', '3', '武陟县', '0');
INSERT INTO `regions` VALUES ('016008008', '3', '温县', '0');
INSERT INTO `regions` VALUES ('016008009', '3', '沁阳市', '0');
INSERT INTO `regions` VALUES ('016008010', '3', '孟州市', '0');
INSERT INTO `regions` VALUES ('016009', '2', '濮阳市', '0');
INSERT INTO `regions` VALUES ('016009001', '3', '华龙区', '0');
INSERT INTO `regions` VALUES ('016009002', '3', '清丰县', '0');
INSERT INTO `regions` VALUES ('016009003', '3', '南乐县', '0');
INSERT INTO `regions` VALUES ('016009004', '3', '范县', '0');
INSERT INTO `regions` VALUES ('016009005', '3', '台前县', '0');
INSERT INTO `regions` VALUES ('016009006', '3', '濮阳县', '0');
INSERT INTO `regions` VALUES ('016010', '2', '许昌市', '0');
INSERT INTO `regions` VALUES ('016010001', '3', '魏都区', '0');
INSERT INTO `regions` VALUES ('016010002', '3', '许昌县', '0');
INSERT INTO `regions` VALUES ('016010003', '3', '鄢陵县', '0');
INSERT INTO `regions` VALUES ('016010004', '3', '襄城县', '0');
INSERT INTO `regions` VALUES ('016010005', '3', '禹州市', '0');
INSERT INTO `regions` VALUES ('016010006', '3', '长葛市', '0');
INSERT INTO `regions` VALUES ('016011', '2', '漯河市', '0');
INSERT INTO `regions` VALUES ('016011001', '3', '源汇区', '0');
INSERT INTO `regions` VALUES ('016011002', '3', '郾城区', '0');
INSERT INTO `regions` VALUES ('016011003', '3', '召陵区', '0');
INSERT INTO `regions` VALUES ('016011004', '3', '舞阳县', '0');
INSERT INTO `regions` VALUES ('016011005', '3', '临颍县', '0');
INSERT INTO `regions` VALUES ('016012', '2', '三门峡市', '0');
INSERT INTO `regions` VALUES ('016012001', '3', '湖滨区', '0');
INSERT INTO `regions` VALUES ('016012002', '3', '渑池县', '0');
INSERT INTO `regions` VALUES ('016012003', '3', '陕县', '0');
INSERT INTO `regions` VALUES ('016012004', '3', '卢氏县', '0');
INSERT INTO `regions` VALUES ('016012005', '3', '义马市', '0');
INSERT INTO `regions` VALUES ('016012006', '3', '灵宝市', '0');
INSERT INTO `regions` VALUES ('016013', '2', '南阳市', '0');
INSERT INTO `regions` VALUES ('016013001', '3', '宛城区', '0');
INSERT INTO `regions` VALUES ('016013002', '3', '卧龙区', '0');
INSERT INTO `regions` VALUES ('016013003', '3', '南召县', '0');
INSERT INTO `regions` VALUES ('016013004', '3', '方城县', '0');
INSERT INTO `regions` VALUES ('016013005', '3', '西峡县', '0');
INSERT INTO `regions` VALUES ('016013006', '3', '镇平县', '0');
INSERT INTO `regions` VALUES ('016013007', '3', '内乡县', '0');
INSERT INTO `regions` VALUES ('016013008', '3', '淅川县', '0');
INSERT INTO `regions` VALUES ('016013009', '3', '社旗县', '0');
INSERT INTO `regions` VALUES ('016013010', '3', '唐河县', '0');
INSERT INTO `regions` VALUES ('016013011', '3', '新野县', '0');
INSERT INTO `regions` VALUES ('016013012', '3', '桐柏县', '0');
INSERT INTO `regions` VALUES ('016013013', '3', '邓州市', '0');
INSERT INTO `regions` VALUES ('016014', '2', '商丘市', '0');
INSERT INTO `regions` VALUES ('016014001', '3', '梁园区', '0');
INSERT INTO `regions` VALUES ('016014002', '3', '睢阳区', '0');
INSERT INTO `regions` VALUES ('016014003', '3', '民权县', '0');
INSERT INTO `regions` VALUES ('016014004', '3', '睢县', '0');
INSERT INTO `regions` VALUES ('016014005', '3', '宁陵县', '0');
INSERT INTO `regions` VALUES ('016014006', '3', '柘城县', '0');
INSERT INTO `regions` VALUES ('016014007', '3', '虞城县', '0');
INSERT INTO `regions` VALUES ('016014008', '3', '夏邑县', '0');
INSERT INTO `regions` VALUES ('016014009', '3', '永城市', '0');
INSERT INTO `regions` VALUES ('016015', '2', '信阳市', '0');
INSERT INTO `regions` VALUES ('016015001', '3', '浉河区', '0');
INSERT INTO `regions` VALUES ('016015002', '3', '平桥区', '0');
INSERT INTO `regions` VALUES ('016015003', '3', '罗山县', '0');
INSERT INTO `regions` VALUES ('016015004', '3', '光山县', '0');
INSERT INTO `regions` VALUES ('016015005', '3', '新县', '0');
INSERT INTO `regions` VALUES ('016015006', '3', '商城县', '0');
INSERT INTO `regions` VALUES ('016015007', '3', '固始县', '0');
INSERT INTO `regions` VALUES ('016015008', '3', '潢川县', '0');
INSERT INTO `regions` VALUES ('016015009', '3', '淮滨县', '0');
INSERT INTO `regions` VALUES ('016015010', '3', '息县', '0');
INSERT INTO `regions` VALUES ('016016', '2', '周口市', '0');
INSERT INTO `regions` VALUES ('016016001', '3', '川汇区', '0');
INSERT INTO `regions` VALUES ('016016002', '3', '扶沟县', '0');
INSERT INTO `regions` VALUES ('016016003', '3', '西华县', '0');
INSERT INTO `regions` VALUES ('016016004', '3', '商水县', '0');
INSERT INTO `regions` VALUES ('016016005', '3', '沈丘县', '0');
INSERT INTO `regions` VALUES ('016016006', '3', '郸城县', '0');
INSERT INTO `regions` VALUES ('016016007', '3', '淮阳县', '0');
INSERT INTO `regions` VALUES ('016016008', '3', '太康县', '0');
INSERT INTO `regions` VALUES ('016016009', '3', '鹿邑县', '0');
INSERT INTO `regions` VALUES ('016016010', '3', '项城市', '0');
INSERT INTO `regions` VALUES ('016017', '2', '驻马店市', '0');
INSERT INTO `regions` VALUES ('016017001', '3', '驿城区', '0');
INSERT INTO `regions` VALUES ('016017002', '3', '西平县', '0');
INSERT INTO `regions` VALUES ('016017003', '3', '上蔡县', '0');
INSERT INTO `regions` VALUES ('016017004', '3', '平舆县', '0');
INSERT INTO `regions` VALUES ('016017005', '3', '正阳县', '0');
INSERT INTO `regions` VALUES ('016017006', '3', '确山县', '0');
INSERT INTO `regions` VALUES ('016017007', '3', '泌阳县', '0');
INSERT INTO `regions` VALUES ('016017008', '3', '汝南县', '0');
INSERT INTO `regions` VALUES ('016017009', '3', '遂平县', '0');
INSERT INTO `regions` VALUES ('016017010', '3', '新蔡县', '0');
INSERT INTO `regions` VALUES ('016018', '2', '直辖县级', '0');
INSERT INTO `regions` VALUES ('016018001', '3', '济源市', '0');
INSERT INTO `regions` VALUES ('017', '1', '湖北省', '0');
INSERT INTO `regions` VALUES ('017001', '2', '武汉市', '0');
INSERT INTO `regions` VALUES ('017001001', '3', '江岸区', '0');
INSERT INTO `regions` VALUES ('017001002', '3', '江汉区', '0');
INSERT INTO `regions` VALUES ('017001003', '3', '硚口区', '0');
INSERT INTO `regions` VALUES ('017001004', '3', '汉阳区', '0');
INSERT INTO `regions` VALUES ('017001005', '3', '武昌区', '0');
INSERT INTO `regions` VALUES ('017001006', '3', '青山区', '0');
INSERT INTO `regions` VALUES ('017001007', '3', '洪山区', '0');
INSERT INTO `regions` VALUES ('017001008', '3', '东西湖区', '0');
INSERT INTO `regions` VALUES ('017001009', '3', '汉南区', '0');
INSERT INTO `regions` VALUES ('017001010', '3', '蔡甸区', '0');
INSERT INTO `regions` VALUES ('017001011', '3', '江夏区', '0');
INSERT INTO `regions` VALUES ('017001012', '3', '黄陂区', '0');
INSERT INTO `regions` VALUES ('017001013', '3', '新洲区', '0');
INSERT INTO `regions` VALUES ('017002', '2', '黄石市', '0');
INSERT INTO `regions` VALUES ('017002001', '3', '黄石港区', '0');
INSERT INTO `regions` VALUES ('017002002', '3', '西塞山区', '0');
INSERT INTO `regions` VALUES ('017002003', '3', '下陆区', '0');
INSERT INTO `regions` VALUES ('017002004', '3', '铁山区', '0');
INSERT INTO `regions` VALUES ('017002005', '3', '阳新县', '0');
INSERT INTO `regions` VALUES ('017002006', '3', '大冶市', '0');
INSERT INTO `regions` VALUES ('017003', '2', '十堰市', '0');
INSERT INTO `regions` VALUES ('017003001', '3', '茅箭区', '0');
INSERT INTO `regions` VALUES ('017003002', '3', '张湾区', '0');
INSERT INTO `regions` VALUES ('017003003', '3', '郧阳区', '0');
INSERT INTO `regions` VALUES ('017003004', '3', '郧西县', '0');
INSERT INTO `regions` VALUES ('017003005', '3', '竹山县', '0');
INSERT INTO `regions` VALUES ('017003006', '3', '竹溪县', '0');
INSERT INTO `regions` VALUES ('017003007', '3', '房县', '0');
INSERT INTO `regions` VALUES ('017003008', '3', '丹江口市', '0');
INSERT INTO `regions` VALUES ('017004', '2', '宜昌市', '0');
INSERT INTO `regions` VALUES ('017004001', '3', '西陵区', '0');
INSERT INTO `regions` VALUES ('017004002', '3', '伍家岗区', '0');
INSERT INTO `regions` VALUES ('017004003', '3', '点军区', '0');
INSERT INTO `regions` VALUES ('017004004', '3', '猇亭区', '0');
INSERT INTO `regions` VALUES ('017004005', '3', '夷陵区', '0');
INSERT INTO `regions` VALUES ('017004006', '3', '远安县', '0');
INSERT INTO `regions` VALUES ('017004007', '3', '兴山县', '0');
INSERT INTO `regions` VALUES ('017004008', '3', '秭归县', '0');
INSERT INTO `regions` VALUES ('017004009', '3', '长阳土家族自治县', '0');
INSERT INTO `regions` VALUES ('017004010', '3', '五峰土家族自治县', '0');
INSERT INTO `regions` VALUES ('017004011', '3', '宜都市', '0');
INSERT INTO `regions` VALUES ('017004012', '3', '当阳市', '0');
INSERT INTO `regions` VALUES ('017004013', '3', '枝江市', '0');
INSERT INTO `regions` VALUES ('017005', '2', '襄阳市', '0');
INSERT INTO `regions` VALUES ('017005001', '3', '襄城区', '0');
INSERT INTO `regions` VALUES ('017005002', '3', '樊城区', '0');
INSERT INTO `regions` VALUES ('017005003', '3', '襄州区', '0');
INSERT INTO `regions` VALUES ('017005004', '3', '南漳县', '0');
INSERT INTO `regions` VALUES ('017005005', '3', '谷城县', '0');
INSERT INTO `regions` VALUES ('017005006', '3', '保康县', '0');
INSERT INTO `regions` VALUES ('017005007', '3', '老河口市', '0');
INSERT INTO `regions` VALUES ('017005008', '3', '枣阳市', '0');
INSERT INTO `regions` VALUES ('017005009', '3', '宜城市', '0');
INSERT INTO `regions` VALUES ('017006', '2', '鄂州市', '0');
INSERT INTO `regions` VALUES ('017006001', '3', '梁子湖区', '0');
INSERT INTO `regions` VALUES ('017006002', '3', '华容区', '0');
INSERT INTO `regions` VALUES ('017006003', '3', '鄂城区', '0');
INSERT INTO `regions` VALUES ('017007', '2', '荆门市', '0');
INSERT INTO `regions` VALUES ('017007001', '3', '东宝区', '0');
INSERT INTO `regions` VALUES ('017007002', '3', '掇刀区', '0');
INSERT INTO `regions` VALUES ('017007003', '3', '京山县', '0');
INSERT INTO `regions` VALUES ('017007004', '3', '沙洋县', '0');
INSERT INTO `regions` VALUES ('017007005', '3', '钟祥市', '0');
INSERT INTO `regions` VALUES ('017008', '2', '孝感市', '0');
INSERT INTO `regions` VALUES ('017008001', '3', '孝南区', '0');
INSERT INTO `regions` VALUES ('017008002', '3', '孝昌县', '0');
INSERT INTO `regions` VALUES ('017008003', '3', '大悟县', '0');
INSERT INTO `regions` VALUES ('017008004', '3', '云梦县', '0');
INSERT INTO `regions` VALUES ('017008005', '3', '应城市', '0');
INSERT INTO `regions` VALUES ('017008006', '3', '安陆市', '0');
INSERT INTO `regions` VALUES ('017008007', '3', '汉川市', '0');
INSERT INTO `regions` VALUES ('017009', '2', '荆州市', '0');
INSERT INTO `regions` VALUES ('017009001', '3', '沙市区', '0');
INSERT INTO `regions` VALUES ('017009002', '3', '荆州区', '0');
INSERT INTO `regions` VALUES ('017009003', '3', '公安县', '0');
INSERT INTO `regions` VALUES ('017009004', '3', '监利县', '0');
INSERT INTO `regions` VALUES ('017009005', '3', '江陵县', '0');
INSERT INTO `regions` VALUES ('017009006', '3', '石首市', '0');
INSERT INTO `regions` VALUES ('017009007', '3', '洪湖市', '0');
INSERT INTO `regions` VALUES ('017009008', '3', '松滋市', '0');
INSERT INTO `regions` VALUES ('017010', '2', '黄冈市', '0');
INSERT INTO `regions` VALUES ('017010001', '3', '黄州区', '0');
INSERT INTO `regions` VALUES ('017010002', '3', '团风县', '0');
INSERT INTO `regions` VALUES ('017010003', '3', '红安县', '0');
INSERT INTO `regions` VALUES ('017010004', '3', '罗田县', '0');
INSERT INTO `regions` VALUES ('017010005', '3', '英山县', '0');
INSERT INTO `regions` VALUES ('017010006', '3', '浠水县', '0');
INSERT INTO `regions` VALUES ('017010007', '3', '蕲春县', '0');
INSERT INTO `regions` VALUES ('017010008', '3', '黄梅县', '0');
INSERT INTO `regions` VALUES ('017010009', '3', '麻城市', '0');
INSERT INTO `regions` VALUES ('017010010', '3', '武穴市', '0');
INSERT INTO `regions` VALUES ('017011', '2', '咸宁市', '0');
INSERT INTO `regions` VALUES ('017011001', '3', '咸安区', '0');
INSERT INTO `regions` VALUES ('017011002', '3', '嘉鱼县', '0');
INSERT INTO `regions` VALUES ('017011003', '3', '通城县', '0');
INSERT INTO `regions` VALUES ('017011004', '3', '崇阳县', '0');
INSERT INTO `regions` VALUES ('017011005', '3', '通山县', '0');
INSERT INTO `regions` VALUES ('017011006', '3', '赤壁市', '0');
INSERT INTO `regions` VALUES ('017012', '2', '随州市', '0');
INSERT INTO `regions` VALUES ('017012001', '3', '曾都区', '0');
INSERT INTO `regions` VALUES ('017012002', '3', '随县', '0');
INSERT INTO `regions` VALUES ('017012003', '3', '广水市', '0');
INSERT INTO `regions` VALUES ('017013', '2', '恩施土家族苗族自治州', '0');
INSERT INTO `regions` VALUES ('017013001', '3', '恩施市', '0');
INSERT INTO `regions` VALUES ('017013002', '3', '利川市', '0');
INSERT INTO `regions` VALUES ('017013003', '3', '建始县', '0');
INSERT INTO `regions` VALUES ('017013004', '3', '巴东县', '0');
INSERT INTO `regions` VALUES ('017013005', '3', '宣恩县', '0');
INSERT INTO `regions` VALUES ('017013006', '3', '咸丰县', '0');
INSERT INTO `regions` VALUES ('017013007', '3', '来凤县', '0');
INSERT INTO `regions` VALUES ('017013008', '3', '鹤峰县', '0');
INSERT INTO `regions` VALUES ('017014', '2', '直辖县级', '0');
INSERT INTO `regions` VALUES ('017014001', '3', '仙桃市', '0');
INSERT INTO `regions` VALUES ('017014002', '3', '潜江市', '0');
INSERT INTO `regions` VALUES ('017014003', '3', '天门市', '0');
INSERT INTO `regions` VALUES ('017014004', '3', '神农架林区', '0');
INSERT INTO `regions` VALUES ('018', '1', '湖南省', '0');
INSERT INTO `regions` VALUES ('018001', '2', '长沙市', '0');
INSERT INTO `regions` VALUES ('018001001', '3', '芙蓉区', '0');
INSERT INTO `regions` VALUES ('018001002', '3', '天心区', '0');
INSERT INTO `regions` VALUES ('018001003', '3', '岳麓区', '0');
INSERT INTO `regions` VALUES ('018001004', '3', '开福区', '0');
INSERT INTO `regions` VALUES ('018001005', '3', '雨花区', '0');
INSERT INTO `regions` VALUES ('018001006', '3', '望城区', '0');
INSERT INTO `regions` VALUES ('018001007', '3', '长沙县', '0');
INSERT INTO `regions` VALUES ('018001008', '3', '宁乡县', '0');
INSERT INTO `regions` VALUES ('018001009', '3', '浏阳市', '0');
INSERT INTO `regions` VALUES ('018002', '2', '株洲市', '0');
INSERT INTO `regions` VALUES ('018002001', '3', '荷塘区', '0');
INSERT INTO `regions` VALUES ('018002002', '3', '芦淞区', '0');
INSERT INTO `regions` VALUES ('018002003', '3', '石峰区', '0');
INSERT INTO `regions` VALUES ('018002004', '3', '天元区', '0');
INSERT INTO `regions` VALUES ('018002005', '3', '株洲县', '0');
INSERT INTO `regions` VALUES ('018002006', '3', '攸县', '0');
INSERT INTO `regions` VALUES ('018002007', '3', '茶陵县', '0');
INSERT INTO `regions` VALUES ('018002008', '3', '炎陵县', '0');
INSERT INTO `regions` VALUES ('018002009', '3', '醴陵市', '0');
INSERT INTO `regions` VALUES ('018003', '2', '湘潭市', '0');
INSERT INTO `regions` VALUES ('018003001', '3', '雨湖区', '0');
INSERT INTO `regions` VALUES ('018003002', '3', '岳塘区', '0');
INSERT INTO `regions` VALUES ('018003003', '3', '湘潭县', '0');
INSERT INTO `regions` VALUES ('018003004', '3', '湘乡市', '0');
INSERT INTO `regions` VALUES ('018003005', '3', '韶山市', '0');
INSERT INTO `regions` VALUES ('018004', '2', '衡阳市', '0');
INSERT INTO `regions` VALUES ('018004001', '3', '珠晖区', '0');
INSERT INTO `regions` VALUES ('018004002', '3', '雁峰区', '0');
INSERT INTO `regions` VALUES ('018004003', '3', '石鼓区', '0');
INSERT INTO `regions` VALUES ('018004004', '3', '蒸湘区', '0');
INSERT INTO `regions` VALUES ('018004005', '3', '南岳区', '0');
INSERT INTO `regions` VALUES ('018004006', '3', '衡阳县', '0');
INSERT INTO `regions` VALUES ('018004007', '3', '衡南县', '0');
INSERT INTO `regions` VALUES ('018004008', '3', '衡山县', '0');
INSERT INTO `regions` VALUES ('018004009', '3', '衡东县', '0');
INSERT INTO `regions` VALUES ('018004010', '3', '祁东县', '0');
INSERT INTO `regions` VALUES ('018004011', '3', '耒阳市', '0');
INSERT INTO `regions` VALUES ('018004012', '3', '常宁市', '0');
INSERT INTO `regions` VALUES ('018005', '2', '邵阳市', '0');
INSERT INTO `regions` VALUES ('018005001', '3', '双清区', '0');
INSERT INTO `regions` VALUES ('018005002', '3', '大祥区', '0');
INSERT INTO `regions` VALUES ('018005003', '3', '北塔区', '0');
INSERT INTO `regions` VALUES ('018005004', '3', '邵东县', '0');
INSERT INTO `regions` VALUES ('018005005', '3', '新邵县', '0');
INSERT INTO `regions` VALUES ('018005006', '3', '邵阳县', '0');
INSERT INTO `regions` VALUES ('018005007', '3', '隆回县', '0');
INSERT INTO `regions` VALUES ('018005008', '3', '洞口县', '0');
INSERT INTO `regions` VALUES ('018005009', '3', '绥宁县', '0');
INSERT INTO `regions` VALUES ('018005010', '3', '新宁县', '0');
INSERT INTO `regions` VALUES ('018005011', '3', '城步苗族自治县', '0');
INSERT INTO `regions` VALUES ('018005012', '3', '武冈市', '0');
INSERT INTO `regions` VALUES ('018006', '2', '岳阳市', '0');
INSERT INTO `regions` VALUES ('018006001', '3', '岳阳楼区', '0');
INSERT INTO `regions` VALUES ('018006002', '3', '云溪区', '0');
INSERT INTO `regions` VALUES ('018006003', '3', '君山区', '0');
INSERT INTO `regions` VALUES ('018006004', '3', '岳阳县', '0');
INSERT INTO `regions` VALUES ('018006005', '3', '华容县', '0');
INSERT INTO `regions` VALUES ('018006006', '3', '湘阴县', '0');
INSERT INTO `regions` VALUES ('018006007', '3', '平江县', '0');
INSERT INTO `regions` VALUES ('018006008', '3', '汨罗市', '0');
INSERT INTO `regions` VALUES ('018006009', '3', '临湘市', '0');
INSERT INTO `regions` VALUES ('018007', '2', '常德市', '0');
INSERT INTO `regions` VALUES ('018007001', '3', '武陵区', '0');
INSERT INTO `regions` VALUES ('018007002', '3', '鼎城区', '0');
INSERT INTO `regions` VALUES ('018007003', '3', '安乡县', '0');
INSERT INTO `regions` VALUES ('018007004', '3', '汉寿县', '0');
INSERT INTO `regions` VALUES ('018007005', '3', '澧县', '0');
INSERT INTO `regions` VALUES ('018007006', '3', '临澧县', '0');
INSERT INTO `regions` VALUES ('018007007', '3', '桃源县', '0');
INSERT INTO `regions` VALUES ('018007008', '3', '石门县', '0');
INSERT INTO `regions` VALUES ('018007009', '3', '津市市', '0');
INSERT INTO `regions` VALUES ('018008', '2', '张家界市', '0');
INSERT INTO `regions` VALUES ('018008001', '3', '永定区', '0');
INSERT INTO `regions` VALUES ('018008002', '3', '武陵源区', '0');
INSERT INTO `regions` VALUES ('018008003', '3', '慈利县', '0');
INSERT INTO `regions` VALUES ('018008004', '3', '桑植县', '0');
INSERT INTO `regions` VALUES ('018009', '2', '益阳市', '0');
INSERT INTO `regions` VALUES ('018009001', '3', '资阳区', '0');
INSERT INTO `regions` VALUES ('018009002', '3', '赫山区', '0');
INSERT INTO `regions` VALUES ('018009003', '3', '南县', '0');
INSERT INTO `regions` VALUES ('018009004', '3', '桃江县', '0');
INSERT INTO `regions` VALUES ('018009005', '3', '安化县', '0');
INSERT INTO `regions` VALUES ('018009006', '3', '沅江市', '0');
INSERT INTO `regions` VALUES ('018010', '2', '郴州市', '0');
INSERT INTO `regions` VALUES ('018010001', '3', '北湖区', '0');
INSERT INTO `regions` VALUES ('018010002', '3', '苏仙区', '0');
INSERT INTO `regions` VALUES ('018010003', '3', '桂阳县', '0');
INSERT INTO `regions` VALUES ('018010004', '3', '宜章县', '0');
INSERT INTO `regions` VALUES ('018010005', '3', '永兴县', '0');
INSERT INTO `regions` VALUES ('018010006', '3', '嘉禾县', '0');
INSERT INTO `regions` VALUES ('018010007', '3', '临武县', '0');
INSERT INTO `regions` VALUES ('018010008', '3', '汝城县', '0');
INSERT INTO `regions` VALUES ('018010009', '3', '桂东县', '0');
INSERT INTO `regions` VALUES ('018010010', '3', '安仁县', '0');
INSERT INTO `regions` VALUES ('018010011', '3', '资兴市', '0');
INSERT INTO `regions` VALUES ('018011', '2', '永州市', '0');
INSERT INTO `regions` VALUES ('018011001', '3', '零陵区', '0');
INSERT INTO `regions` VALUES ('018011002', '3', '冷水滩区', '0');
INSERT INTO `regions` VALUES ('018011003', '3', '祁阳县', '0');
INSERT INTO `regions` VALUES ('018011004', '3', '东安县', '0');
INSERT INTO `regions` VALUES ('018011005', '3', '双牌县', '0');
INSERT INTO `regions` VALUES ('018011006', '3', '道县', '0');
INSERT INTO `regions` VALUES ('018011007', '3', '江永县', '0');
INSERT INTO `regions` VALUES ('018011008', '3', '宁远县', '0');
INSERT INTO `regions` VALUES ('018011009', '3', '蓝山县', '0');
INSERT INTO `regions` VALUES ('018011010', '3', '新田县', '0');
INSERT INTO `regions` VALUES ('018011011', '3', '江华瑶族自治县', '0');
INSERT INTO `regions` VALUES ('018012', '2', '怀化市', '0');
INSERT INTO `regions` VALUES ('018012001', '3', '鹤城区', '0');
INSERT INTO `regions` VALUES ('018012002', '3', '中方县', '0');
INSERT INTO `regions` VALUES ('018012003', '3', '沅陵县', '0');
INSERT INTO `regions` VALUES ('018012004', '3', '辰溪县', '0');
INSERT INTO `regions` VALUES ('018012005', '3', '溆浦县', '0');
INSERT INTO `regions` VALUES ('018012006', '3', '会同县', '0');
INSERT INTO `regions` VALUES ('018012007', '3', '麻阳苗族自治县', '0');
INSERT INTO `regions` VALUES ('018012008', '3', '新晃侗族自治县', '0');
INSERT INTO `regions` VALUES ('018012009', '3', '芷江侗族自治县', '0');
INSERT INTO `regions` VALUES ('018012010', '3', '靖州苗族侗族自治县', '0');
INSERT INTO `regions` VALUES ('018012011', '3', '通道侗族自治县', '0');
INSERT INTO `regions` VALUES ('018012012', '3', '洪江市', '0');
INSERT INTO `regions` VALUES ('018013', '2', '娄底市', '0');
INSERT INTO `regions` VALUES ('018013001', '3', '娄星区', '0');
INSERT INTO `regions` VALUES ('018013002', '3', '双峰县', '0');
INSERT INTO `regions` VALUES ('018013003', '3', '新化县', '0');
INSERT INTO `regions` VALUES ('018013004', '3', '冷水江市', '0');
INSERT INTO `regions` VALUES ('018013005', '3', '涟源市', '0');
INSERT INTO `regions` VALUES ('018014', '2', '湘西土家族苗族自治州', '0');
INSERT INTO `regions` VALUES ('018014001', '3', '吉首市', '0');
INSERT INTO `regions` VALUES ('018014002', '3', '泸溪县', '0');
INSERT INTO `regions` VALUES ('018014003', '3', '凤凰县', '0');
INSERT INTO `regions` VALUES ('018014004', '3', '花垣县', '0');
INSERT INTO `regions` VALUES ('018014005', '3', '保靖县', '0');
INSERT INTO `regions` VALUES ('018014006', '3', '古丈县', '0');
INSERT INTO `regions` VALUES ('018014007', '3', '永顺县', '0');
INSERT INTO `regions` VALUES ('018014008', '3', '龙山县', '0');
INSERT INTO `regions` VALUES ('019', '1', '广东省', '0');
INSERT INTO `regions` VALUES ('019001', '2', '广州市', '0');
INSERT INTO `regions` VALUES ('019001001', '3', '荔湾区', '0');
INSERT INTO `regions` VALUES ('019001002', '3', '越秀区', '0');
INSERT INTO `regions` VALUES ('019001003', '3', '海珠区', '0');
INSERT INTO `regions` VALUES ('019001004', '3', '天河区', '0');
INSERT INTO `regions` VALUES ('019001005', '3', '白云区', '0');
INSERT INTO `regions` VALUES ('019001006', '3', '黄埔区', '0');
INSERT INTO `regions` VALUES ('019001007', '3', '番禺区', '0');
INSERT INTO `regions` VALUES ('019001008', '3', '花都区', '0');
INSERT INTO `regions` VALUES ('019001009', '3', '南沙区', '0');
INSERT INTO `regions` VALUES ('019001010', '3', '从化区', '0');
INSERT INTO `regions` VALUES ('019001011', '3', '增城区', '0');
INSERT INTO `regions` VALUES ('019002', '2', '韶关市', '0');
INSERT INTO `regions` VALUES ('019002001', '3', '武江区', '0');
INSERT INTO `regions` VALUES ('019002002', '3', '浈江区', '0');
INSERT INTO `regions` VALUES ('019002003', '3', '曲江区', '0');
INSERT INTO `regions` VALUES ('019002004', '3', '始兴县', '0');
INSERT INTO `regions` VALUES ('019002005', '3', '仁化县', '0');
INSERT INTO `regions` VALUES ('019002006', '3', '翁源县', '0');
INSERT INTO `regions` VALUES ('019002007', '3', '乳源瑶族自治县', '0');
INSERT INTO `regions` VALUES ('019002008', '3', '新丰县', '0');
INSERT INTO `regions` VALUES ('019002009', '3', '乐昌市', '0');
INSERT INTO `regions` VALUES ('019002010', '3', '南雄市', '0');
INSERT INTO `regions` VALUES ('019003', '2', '深圳市', '0');
INSERT INTO `regions` VALUES ('019003001', '3', '罗湖区', '0');
INSERT INTO `regions` VALUES ('019003002', '3', '福田区', '0');
INSERT INTO `regions` VALUES ('019003003', '3', '南山区', '0');
INSERT INTO `regions` VALUES ('019003004', '3', '宝安区', '0');
INSERT INTO `regions` VALUES ('019003005', '3', '龙岗区', '0');
INSERT INTO `regions` VALUES ('019003006', '3', '盐田区', '0');
INSERT INTO `regions` VALUES ('019003007', '3', '光明新区', '0');
INSERT INTO `regions` VALUES ('019003008', '3', '坪山新区', '0');
INSERT INTO `regions` VALUES ('019003009', '3', '大鹏新区', '0');
INSERT INTO `regions` VALUES ('019003010', '3', '龙华新区', '0');
INSERT INTO `regions` VALUES ('019004', '2', '珠海市', '0');
INSERT INTO `regions` VALUES ('019004001', '3', '香洲区', '0');
INSERT INTO `regions` VALUES ('019004002', '3', '斗门区', '0');
INSERT INTO `regions` VALUES ('019004003', '3', '金湾区', '0');
INSERT INTO `regions` VALUES ('019005', '2', '汕头市', '0');
INSERT INTO `regions` VALUES ('019005001', '3', '龙湖区', '0');
INSERT INTO `regions` VALUES ('019005002', '3', '金平区', '0');
INSERT INTO `regions` VALUES ('019005003', '3', '濠江区', '0');
INSERT INTO `regions` VALUES ('019005004', '3', '潮阳区', '0');
INSERT INTO `regions` VALUES ('019005005', '3', '潮南区', '0');
INSERT INTO `regions` VALUES ('019005006', '3', '澄海区', '0');
INSERT INTO `regions` VALUES ('019005007', '3', '南澳县', '0');
INSERT INTO `regions` VALUES ('019006', '2', '佛山市', '0');
INSERT INTO `regions` VALUES ('019006001', '3', '禅城区', '0');
INSERT INTO `regions` VALUES ('019006002', '3', '南海区', '0');
INSERT INTO `regions` VALUES ('019006003', '3', '顺德区', '0');
INSERT INTO `regions` VALUES ('019006004', '3', '三水区', '0');
INSERT INTO `regions` VALUES ('019006005', '3', '高明区', '0');
INSERT INTO `regions` VALUES ('019007', '2', '江门市', '0');
INSERT INTO `regions` VALUES ('019007001', '3', '蓬江区', '0');
INSERT INTO `regions` VALUES ('019007002', '3', '江海区', '0');
INSERT INTO `regions` VALUES ('019007003', '3', '新会区', '0');
INSERT INTO `regions` VALUES ('019007004', '3', '台山市', '0');
INSERT INTO `regions` VALUES ('019007005', '3', '开平市', '0');
INSERT INTO `regions` VALUES ('019007006', '3', '鹤山市', '0');
INSERT INTO `regions` VALUES ('019007007', '3', '恩平市', '0');
INSERT INTO `regions` VALUES ('019008', '2', '湛江市', '0');
INSERT INTO `regions` VALUES ('019008001', '3', '赤坎区', '0');
INSERT INTO `regions` VALUES ('019008002', '3', '霞山区', '0');
INSERT INTO `regions` VALUES ('019008003', '3', '坡头区', '0');
INSERT INTO `regions` VALUES ('019008004', '3', '麻章区', '0');
INSERT INTO `regions` VALUES ('019008005', '3', '遂溪县', '0');
INSERT INTO `regions` VALUES ('019008006', '3', '徐闻县', '0');
INSERT INTO `regions` VALUES ('019008007', '3', '廉江市', '0');
INSERT INTO `regions` VALUES ('019008008', '3', '雷州市', '0');
INSERT INTO `regions` VALUES ('019008009', '3', '吴川市', '0');
INSERT INTO `regions` VALUES ('019009', '2', '茂名市', '0');
INSERT INTO `regions` VALUES ('019009001', '3', '茂南区', '0');
INSERT INTO `regions` VALUES ('019009002', '3', '电白区', '0');
INSERT INTO `regions` VALUES ('019009003', '3', '高州市', '0');
INSERT INTO `regions` VALUES ('019009004', '3', '化州市', '0');
INSERT INTO `regions` VALUES ('019009005', '3', '信宜市', '0');
INSERT INTO `regions` VALUES ('019010', '2', '肇庆市', '0');
INSERT INTO `regions` VALUES ('019010001', '3', '端州区', '0');
INSERT INTO `regions` VALUES ('019010002', '3', '鼎湖区', '0');
INSERT INTO `regions` VALUES ('019010003', '3', '广宁县', '0');
INSERT INTO `regions` VALUES ('019010004', '3', '怀集县', '0');
INSERT INTO `regions` VALUES ('019010005', '3', '封开县', '0');
INSERT INTO `regions` VALUES ('019010006', '3', '德庆县', '0');
INSERT INTO `regions` VALUES ('019010007', '3', '高要市', '0');
INSERT INTO `regions` VALUES ('019010008', '3', '四会市', '0');
INSERT INTO `regions` VALUES ('019011', '2', '惠州市', '0');
INSERT INTO `regions` VALUES ('019011001', '3', '惠城区', '0');
INSERT INTO `regions` VALUES ('019011002', '3', '惠阳区', '0');
INSERT INTO `regions` VALUES ('019011003', '3', '博罗县', '0');
INSERT INTO `regions` VALUES ('019011004', '3', '惠东县', '0');
INSERT INTO `regions` VALUES ('019011005', '3', '龙门县', '0');
INSERT INTO `regions` VALUES ('019012', '2', '梅州市', '0');
INSERT INTO `regions` VALUES ('019012001', '3', '梅江区', '0');
INSERT INTO `regions` VALUES ('019012002', '3', '梅县区', '0');
INSERT INTO `regions` VALUES ('019012003', '3', '大埔县', '0');
INSERT INTO `regions` VALUES ('019012004', '3', '丰顺县', '0');
INSERT INTO `regions` VALUES ('019012005', '3', '五华县', '0');
INSERT INTO `regions` VALUES ('019012006', '3', '平远县', '0');
INSERT INTO `regions` VALUES ('019012007', '3', '蕉岭县', '0');
INSERT INTO `regions` VALUES ('019012008', '3', '兴宁市', '0');
INSERT INTO `regions` VALUES ('019013', '2', '汕尾市', '0');
INSERT INTO `regions` VALUES ('019013001', '3', '城区', '0');
INSERT INTO `regions` VALUES ('019013002', '3', '海丰县', '0');
INSERT INTO `regions` VALUES ('019013003', '3', '陆河县', '0');
INSERT INTO `regions` VALUES ('019013004', '3', '陆丰市', '0');
INSERT INTO `regions` VALUES ('019014', '2', '河源市', '0');
INSERT INTO `regions` VALUES ('019014001', '3', '源城区', '0');
INSERT INTO `regions` VALUES ('019014002', '3', '紫金县', '0');
INSERT INTO `regions` VALUES ('019014003', '3', '龙川县', '0');
INSERT INTO `regions` VALUES ('019014004', '3', '连平县', '0');
INSERT INTO `regions` VALUES ('019014005', '3', '和平县', '0');
INSERT INTO `regions` VALUES ('019014006', '3', '东源县', '0');
INSERT INTO `regions` VALUES ('019015', '2', '阳江市', '0');
INSERT INTO `regions` VALUES ('019015001', '3', '江城区', '0');
INSERT INTO `regions` VALUES ('019015002', '3', '阳东区', '0');
INSERT INTO `regions` VALUES ('019015003', '3', '阳西县', '0');
INSERT INTO `regions` VALUES ('019015004', '3', '阳春市', '0');
INSERT INTO `regions` VALUES ('019016', '2', '清远市', '0');
INSERT INTO `regions` VALUES ('019016001', '3', '清城区', '0');
INSERT INTO `regions` VALUES ('019016002', '3', '清新区', '0');
INSERT INTO `regions` VALUES ('019016003', '3', '佛冈县', '0');
INSERT INTO `regions` VALUES ('019016004', '3', '阳山县', '0');
INSERT INTO `regions` VALUES ('019016005', '3', '连山壮族瑶族自治县', '0');
INSERT INTO `regions` VALUES ('019016006', '3', '连南瑶族自治县', '0');
INSERT INTO `regions` VALUES ('019016007', '3', '英德市', '0');
INSERT INTO `regions` VALUES ('019016008', '3', '连州市', '0');
INSERT INTO `regions` VALUES ('019017', '2', '东莞市', '0');
INSERT INTO `regions` VALUES ('019017001', '3', '莞城区', '0');
INSERT INTO `regions` VALUES ('019017002', '3', '南城区', '0');
INSERT INTO `regions` VALUES ('019017003', '3', '万江区', '0');
INSERT INTO `regions` VALUES ('019017004', '3', '石碣镇', '0');
INSERT INTO `regions` VALUES ('019017005', '3', '石龙镇', '0');
INSERT INTO `regions` VALUES ('019017006', '3', '茶山镇', '0');
INSERT INTO `regions` VALUES ('019017007', '3', '石排镇', '0');
INSERT INTO `regions` VALUES ('019017008', '3', '企石镇', '0');
INSERT INTO `regions` VALUES ('019017009', '3', '横沥镇', '0');
INSERT INTO `regions` VALUES ('019017010', '3', '桥头镇', '0');
INSERT INTO `regions` VALUES ('019017011', '3', '谢岗镇', '0');
INSERT INTO `regions` VALUES ('019017012', '3', '东坑镇', '0');
INSERT INTO `regions` VALUES ('019017013', '3', '常平镇', '0');
INSERT INTO `regions` VALUES ('019017014', '3', '寮步镇', '0');
INSERT INTO `regions` VALUES ('019017015', '3', '大朗镇', '0');
INSERT INTO `regions` VALUES ('019017016', '3', '麻涌镇', '0');
INSERT INTO `regions` VALUES ('019017017', '3', '中堂镇', '0');
INSERT INTO `regions` VALUES ('019017018', '3', '高埗镇', '0');
INSERT INTO `regions` VALUES ('019017019', '3', '樟木头镇', '0');
INSERT INTO `regions` VALUES ('019017020', '3', '大岭山镇', '0');
INSERT INTO `regions` VALUES ('019017021', '3', '望牛墩镇', '0');
INSERT INTO `regions` VALUES ('019017022', '3', '黄江镇', '0');
INSERT INTO `regions` VALUES ('019017023', '3', '洪梅镇', '0');
INSERT INTO `regions` VALUES ('019017024', '3', '清溪镇', '0');
INSERT INTO `regions` VALUES ('019017025', '3', '沙田镇', '0');
INSERT INTO `regions` VALUES ('019017026', '3', '道滘镇', '0');
INSERT INTO `regions` VALUES ('019017027', '3', '塘厦镇', '0');
INSERT INTO `regions` VALUES ('019017028', '3', '虎门镇', '0');
INSERT INTO `regions` VALUES ('019017029', '3', '厚街镇', '0');
INSERT INTO `regions` VALUES ('019017030', '3', '凤岗镇', '0');
INSERT INTO `regions` VALUES ('019017031', '3', '长安镇', '0');
INSERT INTO `regions` VALUES ('019018', '2', '中山市', '0');
INSERT INTO `regions` VALUES ('019018001', '3', '石岐区', '0');
INSERT INTO `regions` VALUES ('019018002', '3', '南区', '0');
INSERT INTO `regions` VALUES ('019018003', '3', '五桂山区', '0');
INSERT INTO `regions` VALUES ('019018004', '3', '火炬开发区', '0');
INSERT INTO `regions` VALUES ('019018005', '3', '黄圃镇', '0');
INSERT INTO `regions` VALUES ('019018006', '3', '南头镇', '0');
INSERT INTO `regions` VALUES ('019018007', '3', '东凤镇', '0');
INSERT INTO `regions` VALUES ('019018008', '3', '阜沙镇', '0');
INSERT INTO `regions` VALUES ('019018009', '3', '小榄镇', '0');
INSERT INTO `regions` VALUES ('019018010', '3', '东升镇', '0');
INSERT INTO `regions` VALUES ('019018011', '3', '古镇镇', '0');
INSERT INTO `regions` VALUES ('019018012', '3', '横栏镇', '0');
INSERT INTO `regions` VALUES ('019018013', '3', '三角镇', '0');
INSERT INTO `regions` VALUES ('019018014', '3', '民众镇', '0');
INSERT INTO `regions` VALUES ('019018015', '3', '南朗镇', '0');
INSERT INTO `regions` VALUES ('019018016', '3', '港口镇', '0');
INSERT INTO `regions` VALUES ('019018017', '3', '大涌镇', '0');
INSERT INTO `regions` VALUES ('019018018', '3', '沙溪镇', '0');
INSERT INTO `regions` VALUES ('019018019', '3', '三乡镇', '0');
INSERT INTO `regions` VALUES ('019018020', '3', '板芙镇', '0');
INSERT INTO `regions` VALUES ('019018021', '3', '神湾镇', '0');
INSERT INTO `regions` VALUES ('019018022', '3', '坦洲镇', '0');
INSERT INTO `regions` VALUES ('019019', '2', '潮州市', '0');
INSERT INTO `regions` VALUES ('019019001', '3', '湘桥区', '0');
INSERT INTO `regions` VALUES ('019019002', '3', '潮安区', '0');
INSERT INTO `regions` VALUES ('019019003', '3', '饶平县', '0');
INSERT INTO `regions` VALUES ('019020', '2', '揭阳市', '0');
INSERT INTO `regions` VALUES ('019020001', '3', '榕城区', '0');
INSERT INTO `regions` VALUES ('019020002', '3', '揭东区', '0');
INSERT INTO `regions` VALUES ('019020003', '3', '揭西县', '0');
INSERT INTO `regions` VALUES ('019020004', '3', '惠来县', '0');
INSERT INTO `regions` VALUES ('019020005', '3', '普宁市', '0');
INSERT INTO `regions` VALUES ('019021', '2', '云浮市', '0');
INSERT INTO `regions` VALUES ('019021001', '3', '云城区', '0');
INSERT INTO `regions` VALUES ('019021002', '3', '云安区', '0');
INSERT INTO `regions` VALUES ('019021003', '3', '新兴县', '0');
INSERT INTO `regions` VALUES ('019021004', '3', '郁南县', '0');
INSERT INTO `regions` VALUES ('019021005', '3', '罗定市', '0');
INSERT INTO `regions` VALUES ('020', '1', '广西壮族自治区', '0');
INSERT INTO `regions` VALUES ('020001', '2', '南宁市', '0');
INSERT INTO `regions` VALUES ('020001001', '3', '兴宁区', '0');
INSERT INTO `regions` VALUES ('020001002', '3', '青秀区', '0');
INSERT INTO `regions` VALUES ('020001003', '3', '江南区', '0');
INSERT INTO `regions` VALUES ('020001004', '3', '西乡塘区', '0');
INSERT INTO `regions` VALUES ('020001005', '3', '良庆区', '0');
INSERT INTO `regions` VALUES ('020001006', '3', '邕宁区', '0');
INSERT INTO `regions` VALUES ('020001007', '3', '武鸣县', '0');
INSERT INTO `regions` VALUES ('020001008', '3', '隆安县', '0');
INSERT INTO `regions` VALUES ('020001009', '3', '马山县', '0');
INSERT INTO `regions` VALUES ('020001010', '3', '上林县', '0');
INSERT INTO `regions` VALUES ('020001011', '3', '宾阳县', '0');
INSERT INTO `regions` VALUES ('020001012', '3', '横县', '0');
INSERT INTO `regions` VALUES ('020001013', '3', '埌东新区', '0');
INSERT INTO `regions` VALUES ('020002', '2', '柳州市', '0');
INSERT INTO `regions` VALUES ('020002001', '3', '城中区', '0');
INSERT INTO `regions` VALUES ('020002002', '3', '鱼峰区', '0');
INSERT INTO `regions` VALUES ('020002003', '3', '柳南区', '0');
INSERT INTO `regions` VALUES ('020002004', '3', '柳北区', '0');
INSERT INTO `regions` VALUES ('020002005', '3', '柳江县', '0');
INSERT INTO `regions` VALUES ('020002006', '3', '柳城县', '0');
INSERT INTO `regions` VALUES ('020002007', '3', '鹿寨县', '0');
INSERT INTO `regions` VALUES ('020002008', '3', '融安县', '0');
INSERT INTO `regions` VALUES ('020002009', '3', '融水苗族自治县', '0');
INSERT INTO `regions` VALUES ('020002010', '3', '三江侗族自治县', '0');
INSERT INTO `regions` VALUES ('020002011', '3', '柳东新区', '0');
INSERT INTO `regions` VALUES ('020003', '2', '桂林市', '0');
INSERT INTO `regions` VALUES ('020003001', '3', '秀峰区', '0');
INSERT INTO `regions` VALUES ('020003002', '3', '叠彩区', '0');
INSERT INTO `regions` VALUES ('020003003', '3', '象山区', '0');
INSERT INTO `regions` VALUES ('020003004', '3', '七星区', '0');
INSERT INTO `regions` VALUES ('020003005', '3', '雁山区', '0');
INSERT INTO `regions` VALUES ('020003006', '3', '临桂区', '0');
INSERT INTO `regions` VALUES ('020003007', '3', '阳朔县', '0');
INSERT INTO `regions` VALUES ('020003008', '3', '灵川县', '0');
INSERT INTO `regions` VALUES ('020003009', '3', '全州县', '0');
INSERT INTO `regions` VALUES ('020003010', '3', '兴安县', '0');
INSERT INTO `regions` VALUES ('020003011', '3', '永福县', '0');
INSERT INTO `regions` VALUES ('020003012', '3', '灌阳县', '0');
INSERT INTO `regions` VALUES ('020003013', '3', '龙胜各族自治县', '0');
INSERT INTO `regions` VALUES ('020003014', '3', '资源县', '0');
INSERT INTO `regions` VALUES ('020003015', '3', '平乐县', '0');
INSERT INTO `regions` VALUES ('020003016', '3', '荔浦县', '0');
INSERT INTO `regions` VALUES ('020003017', '3', '恭城瑶族自治县', '0');
INSERT INTO `regions` VALUES ('020004', '2', '梧州市', '0');
INSERT INTO `regions` VALUES ('020004001', '3', '万秀区', '0');
INSERT INTO `regions` VALUES ('020004002', '3', '长洲区', '0');
INSERT INTO `regions` VALUES ('020004003', '3', '龙圩区', '0');
INSERT INTO `regions` VALUES ('020004004', '3', '苍梧县', '0');
INSERT INTO `regions` VALUES ('020004005', '3', '藤县', '0');
INSERT INTO `regions` VALUES ('020004006', '3', '蒙山县', '0');
INSERT INTO `regions` VALUES ('020004007', '3', '岑溪市', '0');
INSERT INTO `regions` VALUES ('020005', '2', '北海市', '0');
INSERT INTO `regions` VALUES ('020005001', '3', '海城区', '0');
INSERT INTO `regions` VALUES ('020005002', '3', '银海区', '0');
INSERT INTO `regions` VALUES ('020005003', '3', '铁山港区', '0');
INSERT INTO `regions` VALUES ('020005004', '3', '合浦县', '0');
INSERT INTO `regions` VALUES ('020006', '2', '防城港市', '0');
INSERT INTO `regions` VALUES ('020006001', '3', '港口区', '0');
INSERT INTO `regions` VALUES ('020006002', '3', '防城区', '0');
INSERT INTO `regions` VALUES ('020006003', '3', '上思县', '0');
INSERT INTO `regions` VALUES ('020006004', '3', '东兴市', '0');
INSERT INTO `regions` VALUES ('020007', '2', '钦州市', '0');
INSERT INTO `regions` VALUES ('020007001', '3', '钦南区', '0');
INSERT INTO `regions` VALUES ('020007002', '3', '钦北区', '0');
INSERT INTO `regions` VALUES ('020007003', '3', '灵山县', '0');
INSERT INTO `regions` VALUES ('020007004', '3', '浦北县', '0');
INSERT INTO `regions` VALUES ('020008', '2', '贵港市', '0');
INSERT INTO `regions` VALUES ('020008001', '3', '港北区', '0');
INSERT INTO `regions` VALUES ('020008002', '3', '港南区', '0');
INSERT INTO `regions` VALUES ('020008003', '3', '覃塘区', '0');
INSERT INTO `regions` VALUES ('020008004', '3', '平南县', '0');
INSERT INTO `regions` VALUES ('020008005', '3', '桂平市', '0');
INSERT INTO `regions` VALUES ('020009', '2', '玉林市', '0');
INSERT INTO `regions` VALUES ('020009001', '3', '玉州区', '0');
INSERT INTO `regions` VALUES ('020009002', '3', '福绵区', '0');
INSERT INTO `regions` VALUES ('020009003', '3', '玉东新区', '0');
INSERT INTO `regions` VALUES ('020009004', '3', '容县', '0');
INSERT INTO `regions` VALUES ('020009005', '3', '陆川县', '0');
INSERT INTO `regions` VALUES ('020009006', '3', '博白县', '0');
INSERT INTO `regions` VALUES ('020009007', '3', '兴业县', '0');
INSERT INTO `regions` VALUES ('020009008', '3', '北流市', '0');
INSERT INTO `regions` VALUES ('020010', '2', '百色市', '0');
INSERT INTO `regions` VALUES ('020010001', '3', '右江区', '0');
INSERT INTO `regions` VALUES ('020010002', '3', '田阳县', '0');
INSERT INTO `regions` VALUES ('020010003', '3', '田东县', '0');
INSERT INTO `regions` VALUES ('020010004', '3', '平果县', '0');
INSERT INTO `regions` VALUES ('020010005', '3', '德保县', '0');
INSERT INTO `regions` VALUES ('020010006', '3', '靖西县', '0');
INSERT INTO `regions` VALUES ('020010007', '3', '那坡县', '0');
INSERT INTO `regions` VALUES ('020010008', '3', '凌云县', '0');
INSERT INTO `regions` VALUES ('020010009', '3', '乐业县', '0');
INSERT INTO `regions` VALUES ('020010010', '3', '田林县', '0');
INSERT INTO `regions` VALUES ('020010011', '3', '西林县', '0');
INSERT INTO `regions` VALUES ('020010012', '3', '隆林各族自治县', '0');
INSERT INTO `regions` VALUES ('020011', '2', '贺州市', '0');
INSERT INTO `regions` VALUES ('020011001', '3', '八步区', '0');
INSERT INTO `regions` VALUES ('020011002', '3', '昭平县', '0');
INSERT INTO `regions` VALUES ('020011003', '3', '钟山县', '0');
INSERT INTO `regions` VALUES ('020011004', '3', '富川瑶族自治县', '0');
INSERT INTO `regions` VALUES ('020011005', '3', '平桂管理区', '0');
INSERT INTO `regions` VALUES ('020012', '2', '河池市', '0');
INSERT INTO `regions` VALUES ('020012001', '3', '金城江区', '0');
INSERT INTO `regions` VALUES ('020012002', '3', '南丹县', '0');
INSERT INTO `regions` VALUES ('020012003', '3', '天峨县', '0');
INSERT INTO `regions` VALUES ('020012004', '3', '凤山县', '0');
INSERT INTO `regions` VALUES ('020012005', '3', '东兰县', '0');
INSERT INTO `regions` VALUES ('020012006', '3', '罗城仫佬族自治县', '0');
INSERT INTO `regions` VALUES ('020012007', '3', '环江毛南族自治县', '0');
INSERT INTO `regions` VALUES ('020012008', '3', '巴马瑶族自治县', '0');
INSERT INTO `regions` VALUES ('020012009', '3', '都安瑶族自治县', '0');
INSERT INTO `regions` VALUES ('020012010', '3', '大化瑶族自治县', '0');
INSERT INTO `regions` VALUES ('020012011', '3', '宜州市', '0');
INSERT INTO `regions` VALUES ('020013', '2', '来宾市', '0');
INSERT INTO `regions` VALUES ('020013001', '3', '兴宾区', '0');
INSERT INTO `regions` VALUES ('020013002', '3', '忻城县', '0');
INSERT INTO `regions` VALUES ('020013003', '3', '象州县', '0');
INSERT INTO `regions` VALUES ('020013004', '3', '武宣县', '0');
INSERT INTO `regions` VALUES ('020013005', '3', '金秀瑶族自治县', '0');
INSERT INTO `regions` VALUES ('020013006', '3', '合山市', '0');
INSERT INTO `regions` VALUES ('020014', '2', '崇左市', '0');
INSERT INTO `regions` VALUES ('020014001', '3', '江州区', '0');
INSERT INTO `regions` VALUES ('020014002', '3', '扶绥县', '0');
INSERT INTO `regions` VALUES ('020014003', '3', '宁明县', '0');
INSERT INTO `regions` VALUES ('020014004', '3', '龙州县', '0');
INSERT INTO `regions` VALUES ('020014005', '3', '大新县', '0');
INSERT INTO `regions` VALUES ('020014006', '3', '天等县', '0');
INSERT INTO `regions` VALUES ('020014007', '3', '凭祥市', '0');
INSERT INTO `regions` VALUES ('021', '1', '海南省', '0');
INSERT INTO `regions` VALUES ('021001', '2', '海口市', '0');
INSERT INTO `regions` VALUES ('021001001', '3', '秀英区', '0');
INSERT INTO `regions` VALUES ('021001002', '3', '龙华区', '0');
INSERT INTO `regions` VALUES ('021001003', '3', '琼山区', '0');
INSERT INTO `regions` VALUES ('021001004', '3', '美兰区', '0');
INSERT INTO `regions` VALUES ('021002', '2', '三亚市', '0');
INSERT INTO `regions` VALUES ('021002001', '3', '海棠区', '0');
INSERT INTO `regions` VALUES ('021002002', '3', '吉阳区', '0');
INSERT INTO `regions` VALUES ('021002003', '3', '天涯区', '0');
INSERT INTO `regions` VALUES ('021002004', '3', '崖州区', '0');
INSERT INTO `regions` VALUES ('021003', '2', '三沙市', '0');
INSERT INTO `regions` VALUES ('021003001', '3', '西沙群岛', '0');
INSERT INTO `regions` VALUES ('021003002', '3', '南沙群岛', '0');
INSERT INTO `regions` VALUES ('021003003', '3', '中沙群岛', '0');
INSERT INTO `regions` VALUES ('021004', '2', '直辖县级', '0');
INSERT INTO `regions` VALUES ('021004001', '3', '五指山市', '0');
INSERT INTO `regions` VALUES ('021004002', '3', '琼海市', '0');
INSERT INTO `regions` VALUES ('021004003', '3', '儋州市', '0');
INSERT INTO `regions` VALUES ('021004004', '3', '文昌市', '0');
INSERT INTO `regions` VALUES ('021004005', '3', '万宁市', '0');
INSERT INTO `regions` VALUES ('021004006', '3', '东方市', '0');
INSERT INTO `regions` VALUES ('021004007', '3', '定安县', '0');
INSERT INTO `regions` VALUES ('021004008', '3', '屯昌县', '0');
INSERT INTO `regions` VALUES ('021004009', '3', '澄迈县', '0');
INSERT INTO `regions` VALUES ('021004010', '3', '临高县', '0');
INSERT INTO `regions` VALUES ('021004011', '3', '白沙黎族自治县', '0');
INSERT INTO `regions` VALUES ('021004012', '3', '昌江黎族自治县', '0');
INSERT INTO `regions` VALUES ('021004013', '3', '乐东黎族自治县', '0');
INSERT INTO `regions` VALUES ('021004014', '3', '陵水黎族自治县', '0');
INSERT INTO `regions` VALUES ('021004015', '3', '保亭黎族苗族自治县', '0');
INSERT INTO `regions` VALUES ('021004016', '3', '琼中黎族苗族自治县', '0');
INSERT INTO `regions` VALUES ('022', '1', '重庆', '0');
INSERT INTO `regions` VALUES ('022001', '2', '重庆市', '0');
INSERT INTO `regions` VALUES ('022001001', '3', '万州区', '0');
INSERT INTO `regions` VALUES ('022001002', '3', '涪陵区', '0');
INSERT INTO `regions` VALUES ('022001003', '3', '渝中区', '0');
INSERT INTO `regions` VALUES ('022001004', '3', '大渡口区', '0');
INSERT INTO `regions` VALUES ('022001005', '3', '江北区', '0');
INSERT INTO `regions` VALUES ('022001006', '3', '沙坪坝区', '0');
INSERT INTO `regions` VALUES ('022001007', '3', '九龙坡区', '0');
INSERT INTO `regions` VALUES ('022001008', '3', '南岸区', '0');
INSERT INTO `regions` VALUES ('022001009', '3', '北碚区', '0');
INSERT INTO `regions` VALUES ('022001010', '3', '綦江区', '0');
INSERT INTO `regions` VALUES ('022001011', '3', '大足区', '0');
INSERT INTO `regions` VALUES ('022001012', '3', '渝北区', '0');
INSERT INTO `regions` VALUES ('022001013', '3', '巴南区', '0');
INSERT INTO `regions` VALUES ('022001014', '3', '黔江区', '0');
INSERT INTO `regions` VALUES ('022001015', '3', '长寿区', '0');
INSERT INTO `regions` VALUES ('022001016', '3', '江津区', '0');
INSERT INTO `regions` VALUES ('022001017', '3', '合川区', '0');
INSERT INTO `regions` VALUES ('022001018', '3', '永川区', '0');
INSERT INTO `regions` VALUES ('022001019', '3', '南川区', '0');
INSERT INTO `regions` VALUES ('022001020', '3', '璧山区', '0');
INSERT INTO `regions` VALUES ('022001021', '3', '铜梁区', '0');
INSERT INTO `regions` VALUES ('022001022', '3', '潼南县', '0');
INSERT INTO `regions` VALUES ('022001023', '3', '荣昌县', '0');
INSERT INTO `regions` VALUES ('022001024', '3', '梁平县', '0');
INSERT INTO `regions` VALUES ('022001025', '3', '城口县', '0');
INSERT INTO `regions` VALUES ('022001026', '3', '丰都县', '0');
INSERT INTO `regions` VALUES ('022001027', '3', '垫江县', '0');
INSERT INTO `regions` VALUES ('022001028', '3', '武隆县', '0');
INSERT INTO `regions` VALUES ('022001029', '3', '忠县', '0');
INSERT INTO `regions` VALUES ('022001030', '3', '开县', '0');
INSERT INTO `regions` VALUES ('022001031', '3', '云阳县', '0');
INSERT INTO `regions` VALUES ('022001032', '3', '奉节县', '0');
INSERT INTO `regions` VALUES ('022001033', '3', '巫山县', '0');
INSERT INTO `regions` VALUES ('022001034', '3', '巫溪县', '0');
INSERT INTO `regions` VALUES ('022001035', '3', '石柱土家族自治县', '0');
INSERT INTO `regions` VALUES ('022001036', '3', '秀山土家族苗族自治县', '0');
INSERT INTO `regions` VALUES ('022001037', '3', '酉阳土家族苗族自治县', '0');
INSERT INTO `regions` VALUES ('022001038', '3', '彭水苗族土家族自治县', '0');
INSERT INTO `regions` VALUES ('022002', '2', '两江新区', '0');
INSERT INTO `regions` VALUES ('022002001', '3', '北部新区', '0');
INSERT INTO `regions` VALUES ('022002002', '3', '保税港区', '0');
INSERT INTO `regions` VALUES ('022002003', '3', '工业园区', '0');
INSERT INTO `regions` VALUES ('023', '1', '四川省', '0');
INSERT INTO `regions` VALUES ('023001', '2', '成都市', '0');
INSERT INTO `regions` VALUES ('023001001', '3', '锦江区', '0');
INSERT INTO `regions` VALUES ('023001002', '3', '青羊区', '0');
INSERT INTO `regions` VALUES ('023001003', '3', '金牛区', '0');
INSERT INTO `regions` VALUES ('023001004', '3', '武侯区', '0');
INSERT INTO `regions` VALUES ('023001005', '3', '成华区', '0');
INSERT INTO `regions` VALUES ('023001006', '3', '龙泉驿区', '0');
INSERT INTO `regions` VALUES ('023001007', '3', '青白江区', '0');
INSERT INTO `regions` VALUES ('023001008', '3', '新都区', '0');
INSERT INTO `regions` VALUES ('023001009', '3', '温江区', '0');
INSERT INTO `regions` VALUES ('023001010', '3', '金堂县', '0');
INSERT INTO `regions` VALUES ('023001011', '3', '双流县', '0');
INSERT INTO `regions` VALUES ('023001012', '3', '郫县', '0');
INSERT INTO `regions` VALUES ('023001013', '3', '大邑县', '0');
INSERT INTO `regions` VALUES ('023001014', '3', '蒲江县', '0');
INSERT INTO `regions` VALUES ('023001015', '3', '新津县', '0');
INSERT INTO `regions` VALUES ('023001016', '3', '都江堰市', '0');
INSERT INTO `regions` VALUES ('023001017', '3', '彭州市', '0');
INSERT INTO `regions` VALUES ('023001018', '3', '邛崃市', '0');
INSERT INTO `regions` VALUES ('023001019', '3', '崇州市', '0');
INSERT INTO `regions` VALUES ('023002', '2', '自贡市', '0');
INSERT INTO `regions` VALUES ('023002001', '3', '自流井区', '0');
INSERT INTO `regions` VALUES ('023002002', '3', '贡井区', '0');
INSERT INTO `regions` VALUES ('023002003', '3', '大安区', '0');
INSERT INTO `regions` VALUES ('023002004', '3', '沿滩区', '0');
INSERT INTO `regions` VALUES ('023002005', '3', '荣县', '0');
INSERT INTO `regions` VALUES ('023002006', '3', '富顺县', '0');
INSERT INTO `regions` VALUES ('023003', '2', '攀枝花市', '0');
INSERT INTO `regions` VALUES ('023003001', '3', '东区', '0');
INSERT INTO `regions` VALUES ('023003002', '3', '西区', '0');
INSERT INTO `regions` VALUES ('023003003', '3', '仁和区', '0');
INSERT INTO `regions` VALUES ('023003004', '3', '米易县', '0');
INSERT INTO `regions` VALUES ('023003005', '3', '盐边县', '0');
INSERT INTO `regions` VALUES ('023004', '2', '泸州市', '0');
INSERT INTO `regions` VALUES ('023004001', '3', '江阳区', '0');
INSERT INTO `regions` VALUES ('023004002', '3', '纳溪区', '0');
INSERT INTO `regions` VALUES ('023004003', '3', '龙马潭区', '0');
INSERT INTO `regions` VALUES ('023004004', '3', '泸县', '0');
INSERT INTO `regions` VALUES ('023004005', '3', '合江县', '0');
INSERT INTO `regions` VALUES ('023004006', '3', '叙永县', '0');
INSERT INTO `regions` VALUES ('023004007', '3', '古蔺县', '0');
INSERT INTO `regions` VALUES ('023005', '2', '德阳市', '0');
INSERT INTO `regions` VALUES ('023005001', '3', '旌阳区', '0');
INSERT INTO `regions` VALUES ('023005002', '3', '中江县', '0');
INSERT INTO `regions` VALUES ('023005003', '3', '罗江县', '0');
INSERT INTO `regions` VALUES ('023005004', '3', '广汉市', '0');
INSERT INTO `regions` VALUES ('023005005', '3', '什邡市', '0');
INSERT INTO `regions` VALUES ('023005006', '3', '绵竹市', '0');
INSERT INTO `regions` VALUES ('023006', '2', '绵阳市', '0');
INSERT INTO `regions` VALUES ('023006001', '3', '涪城区', '0');
INSERT INTO `regions` VALUES ('023006002', '3', '游仙区', '0');
INSERT INTO `regions` VALUES ('023006003', '3', '三台县', '0');
INSERT INTO `regions` VALUES ('023006004', '3', '盐亭县', '0');
INSERT INTO `regions` VALUES ('023006005', '3', '安县', '0');
INSERT INTO `regions` VALUES ('023006006', '3', '梓潼县', '0');
INSERT INTO `regions` VALUES ('023006007', '3', '北川羌族自治县', '0');
INSERT INTO `regions` VALUES ('023006008', '3', '平武县', '0');
INSERT INTO `regions` VALUES ('023006009', '3', '江油市', '0');
INSERT INTO `regions` VALUES ('023007', '2', '广元市', '0');
INSERT INTO `regions` VALUES ('023007001', '3', '利州区', '0');
INSERT INTO `regions` VALUES ('023007002', '3', '昭化区', '0');
INSERT INTO `regions` VALUES ('023007003', '3', '朝天区', '0');
INSERT INTO `regions` VALUES ('023007004', '3', '旺苍县', '0');
INSERT INTO `regions` VALUES ('023007005', '3', '青川县', '0');
INSERT INTO `regions` VALUES ('023007006', '3', '剑阁县', '0');
INSERT INTO `regions` VALUES ('023007007', '3', '苍溪县', '0');
INSERT INTO `regions` VALUES ('023008', '2', '遂宁市', '0');
INSERT INTO `regions` VALUES ('023008001', '3', '船山区', '0');
INSERT INTO `regions` VALUES ('023008002', '3', '安居区', '0');
INSERT INTO `regions` VALUES ('023008003', '3', '蓬溪县', '0');
INSERT INTO `regions` VALUES ('023008004', '3', '射洪县', '0');
INSERT INTO `regions` VALUES ('023008005', '3', '大英县', '0');
INSERT INTO `regions` VALUES ('023009', '2', '内江市', '0');
INSERT INTO `regions` VALUES ('023009001', '3', '市中区', '0');
INSERT INTO `regions` VALUES ('023009002', '3', '东兴区', '0');
INSERT INTO `regions` VALUES ('023009003', '3', '威远县', '0');
INSERT INTO `regions` VALUES ('023009004', '3', '资中县', '0');
INSERT INTO `regions` VALUES ('023009005', '3', '隆昌县', '0');
INSERT INTO `regions` VALUES ('023010', '2', '乐山市', '0');
INSERT INTO `regions` VALUES ('023010001', '3', '市中区', '0');
INSERT INTO `regions` VALUES ('023010002', '3', '沙湾区', '0');
INSERT INTO `regions` VALUES ('023010003', '3', '五通桥区', '0');
INSERT INTO `regions` VALUES ('023010004', '3', '金口河区', '0');
INSERT INTO `regions` VALUES ('023010005', '3', '犍为县', '0');
INSERT INTO `regions` VALUES ('023010006', '3', '井研县', '0');
INSERT INTO `regions` VALUES ('023010007', '3', '夹江县', '0');
INSERT INTO `regions` VALUES ('023010008', '3', '沐川县', '0');
INSERT INTO `regions` VALUES ('023010009', '3', '峨边彝族自治县', '0');
INSERT INTO `regions` VALUES ('023010010', '3', '马边彝族自治县', '0');
INSERT INTO `regions` VALUES ('023010011', '3', '峨眉山市', '0');
INSERT INTO `regions` VALUES ('023011', '2', '南充市', '0');
INSERT INTO `regions` VALUES ('023011001', '3', '顺庆区', '0');
INSERT INTO `regions` VALUES ('023011002', '3', '高坪区', '0');
INSERT INTO `regions` VALUES ('023011003', '3', '嘉陵区', '0');
INSERT INTO `regions` VALUES ('023011004', '3', '南部县', '0');
INSERT INTO `regions` VALUES ('023011005', '3', '营山县', '0');
INSERT INTO `regions` VALUES ('023011006', '3', '蓬安县', '0');
INSERT INTO `regions` VALUES ('023011007', '3', '仪陇县', '0');
INSERT INTO `regions` VALUES ('023011008', '3', '西充县', '0');
INSERT INTO `regions` VALUES ('023011009', '3', '阆中市', '0');
INSERT INTO `regions` VALUES ('023012', '2', '眉山市', '0');
INSERT INTO `regions` VALUES ('023012001', '3', '东坡区', '0');
INSERT INTO `regions` VALUES ('023012002', '3', '彭山区', '0');
INSERT INTO `regions` VALUES ('023012003', '3', '仁寿县', '0');
INSERT INTO `regions` VALUES ('023012004', '3', '洪雅县', '0');
INSERT INTO `regions` VALUES ('023012005', '3', '丹棱县', '0');
INSERT INTO `regions` VALUES ('023012006', '3', '青神县', '0');
INSERT INTO `regions` VALUES ('023013', '2', '宜宾市', '0');
INSERT INTO `regions` VALUES ('023013001', '3', '翠屏区', '0');
INSERT INTO `regions` VALUES ('023013002', '3', '南溪区', '0');
INSERT INTO `regions` VALUES ('023013003', '3', '宜宾县', '0');
INSERT INTO `regions` VALUES ('023013004', '3', '江安县', '0');
INSERT INTO `regions` VALUES ('023013005', '3', '长宁县', '0');
INSERT INTO `regions` VALUES ('023013006', '3', '高县', '0');
INSERT INTO `regions` VALUES ('023013007', '3', '珙县', '0');
INSERT INTO `regions` VALUES ('023013008', '3', '筠连县', '0');
INSERT INTO `regions` VALUES ('023013009', '3', '兴文县', '0');
INSERT INTO `regions` VALUES ('023013010', '3', '屏山县', '0');
INSERT INTO `regions` VALUES ('023014', '2', '广安市', '0');
INSERT INTO `regions` VALUES ('023014001', '3', '广安区', '0');
INSERT INTO `regions` VALUES ('023014002', '3', '前锋区', '0');
INSERT INTO `regions` VALUES ('023014003', '3', '岳池县', '0');
INSERT INTO `regions` VALUES ('023014004', '3', '武胜县', '0');
INSERT INTO `regions` VALUES ('023014005', '3', '邻水县', '0');
INSERT INTO `regions` VALUES ('023014006', '3', '华蓥市', '0');
INSERT INTO `regions` VALUES ('023015', '2', '达州市', '0');
INSERT INTO `regions` VALUES ('023015001', '3', '通川区', '0');
INSERT INTO `regions` VALUES ('023015002', '3', '达川区', '0');
INSERT INTO `regions` VALUES ('023015003', '3', '宣汉县', '0');
INSERT INTO `regions` VALUES ('023015004', '3', '开江县', '0');
INSERT INTO `regions` VALUES ('023015005', '3', '大竹县', '0');
INSERT INTO `regions` VALUES ('023015006', '3', '渠县', '0');
INSERT INTO `regions` VALUES ('023015007', '3', '万源市', '0');
INSERT INTO `regions` VALUES ('023016', '2', '雅安市', '0');
INSERT INTO `regions` VALUES ('023016001', '3', '雨城区', '0');
INSERT INTO `regions` VALUES ('023016002', '3', '名山区', '0');
INSERT INTO `regions` VALUES ('023016003', '3', '荥经县', '0');
INSERT INTO `regions` VALUES ('023016004', '3', '汉源县', '0');
INSERT INTO `regions` VALUES ('023016005', '3', '石棉县', '0');
INSERT INTO `regions` VALUES ('023016006', '3', '天全县', '0');
INSERT INTO `regions` VALUES ('023016007', '3', '芦山县', '0');
INSERT INTO `regions` VALUES ('023016008', '3', '宝兴县', '0');
INSERT INTO `regions` VALUES ('023017', '2', '巴中市', '0');
INSERT INTO `regions` VALUES ('023017001', '3', '巴州区', '0');
INSERT INTO `regions` VALUES ('023017002', '3', '恩阳区', '0');
INSERT INTO `regions` VALUES ('023017003', '3', '通江县', '0');
INSERT INTO `regions` VALUES ('023017004', '3', '南江县', '0');
INSERT INTO `regions` VALUES ('023017005', '3', '平昌县', '0');
INSERT INTO `regions` VALUES ('023018', '2', '资阳市', '0');
INSERT INTO `regions` VALUES ('023018001', '3', '雁江区', '0');
INSERT INTO `regions` VALUES ('023018002', '3', '安岳县', '0');
INSERT INTO `regions` VALUES ('023018003', '3', '乐至县', '0');
INSERT INTO `regions` VALUES ('023018004', '3', '简阳市', '0');
INSERT INTO `regions` VALUES ('023019', '2', '阿坝藏族羌族自治州', '0');
INSERT INTO `regions` VALUES ('023019001', '3', '汶川县', '0');
INSERT INTO `regions` VALUES ('023019002', '3', '理县', '0');
INSERT INTO `regions` VALUES ('023019003', '3', '茂县', '0');
INSERT INTO `regions` VALUES ('023019004', '3', '松潘县', '0');
INSERT INTO `regions` VALUES ('023019005', '3', '九寨沟县', '0');
INSERT INTO `regions` VALUES ('023019006', '3', '金川县', '0');
INSERT INTO `regions` VALUES ('023019007', '3', '小金县', '0');
INSERT INTO `regions` VALUES ('023019008', '3', '黑水县', '0');
INSERT INTO `regions` VALUES ('023019009', '3', '马尔康县', '0');
INSERT INTO `regions` VALUES ('023019010', '3', '壤塘县', '0');
INSERT INTO `regions` VALUES ('023019011', '3', '阿坝县', '0');
INSERT INTO `regions` VALUES ('023019012', '3', '若尔盖县', '0');
INSERT INTO `regions` VALUES ('023019013', '3', '红原县', '0');
INSERT INTO `regions` VALUES ('023020', '2', '甘孜藏族自治州', '0');
INSERT INTO `regions` VALUES ('023020001', '3', '康定县', '0');
INSERT INTO `regions` VALUES ('023020002', '3', '泸定县', '0');
INSERT INTO `regions` VALUES ('023020003', '3', '丹巴县', '0');
INSERT INTO `regions` VALUES ('023020004', '3', '九龙县', '0');
INSERT INTO `regions` VALUES ('023020005', '3', '雅江县', '0');
INSERT INTO `regions` VALUES ('023020006', '3', '道孚县', '0');
INSERT INTO `regions` VALUES ('023020007', '3', '炉霍县', '0');
INSERT INTO `regions` VALUES ('023020008', '3', '甘孜县', '0');
INSERT INTO `regions` VALUES ('023020009', '3', '新龙县', '0');
INSERT INTO `regions` VALUES ('023020010', '3', '德格县', '0');
INSERT INTO `regions` VALUES ('023020011', '3', '白玉县', '0');
INSERT INTO `regions` VALUES ('023020012', '3', '石渠县', '0');
INSERT INTO `regions` VALUES ('023020013', '3', '色达县', '0');
INSERT INTO `regions` VALUES ('023020014', '3', '理塘县', '0');
INSERT INTO `regions` VALUES ('023020015', '3', '巴塘县', '0');
INSERT INTO `regions` VALUES ('023020016', '3', '乡城县', '0');
INSERT INTO `regions` VALUES ('023020017', '3', '稻城县', '0');
INSERT INTO `regions` VALUES ('023020018', '3', '得荣县', '0');
INSERT INTO `regions` VALUES ('023021', '2', '凉山彝族自治州', '0');
INSERT INTO `regions` VALUES ('023021001', '3', '西昌市', '0');
INSERT INTO `regions` VALUES ('023021002', '3', '木里藏族自治县', '0');
INSERT INTO `regions` VALUES ('023021003', '3', '盐源县', '0');
INSERT INTO `regions` VALUES ('023021004', '3', '德昌县', '0');
INSERT INTO `regions` VALUES ('023021005', '3', '会理县', '0');
INSERT INTO `regions` VALUES ('023021006', '3', '会东县', '0');
INSERT INTO `regions` VALUES ('023021007', '3', '宁南县', '0');
INSERT INTO `regions` VALUES ('023021008', '3', '普格县', '0');
INSERT INTO `regions` VALUES ('023021009', '3', '布拖县', '0');
INSERT INTO `regions` VALUES ('023021010', '3', '金阳县', '0');
INSERT INTO `regions` VALUES ('023021011', '3', '昭觉县', '0');
INSERT INTO `regions` VALUES ('023021012', '3', '喜德县', '0');
INSERT INTO `regions` VALUES ('023021013', '3', '冕宁县', '0');
INSERT INTO `regions` VALUES ('023021014', '3', '越西县', '0');
INSERT INTO `regions` VALUES ('023021015', '3', '甘洛县', '0');
INSERT INTO `regions` VALUES ('023021016', '3', '美姑县', '0');
INSERT INTO `regions` VALUES ('023021017', '3', '雷波县', '0');
INSERT INTO `regions` VALUES ('024', '1', '贵州省', '0');
INSERT INTO `regions` VALUES ('024001', '2', '贵阳市', '0');
INSERT INTO `regions` VALUES ('024001001', '3', '南明区', '0');
INSERT INTO `regions` VALUES ('024001002', '3', '云岩区', '0');
INSERT INTO `regions` VALUES ('024001003', '3', '花溪区', '0');
INSERT INTO `regions` VALUES ('024001004', '3', '乌当区', '0');
INSERT INTO `regions` VALUES ('024001005', '3', '白云区', '0');
INSERT INTO `regions` VALUES ('024001006', '3', '观山湖区', '0');
INSERT INTO `regions` VALUES ('024001007', '3', '开阳县', '0');
INSERT INTO `regions` VALUES ('024001008', '3', '息烽县', '0');
INSERT INTO `regions` VALUES ('024001009', '3', '修文县', '0');
INSERT INTO `regions` VALUES ('024001010', '3', '清镇市', '0');
INSERT INTO `regions` VALUES ('024002', '2', '六盘水市', '0');
INSERT INTO `regions` VALUES ('024002001', '3', '钟山区', '0');
INSERT INTO `regions` VALUES ('024002002', '3', '六枝特区', '0');
INSERT INTO `regions` VALUES ('024002003', '3', '水城县', '0');
INSERT INTO `regions` VALUES ('024002004', '3', '盘县', '0');
INSERT INTO `regions` VALUES ('024003', '2', '遵义市', '0');
INSERT INTO `regions` VALUES ('024003001', '3', '红花岗区', '0');
INSERT INTO `regions` VALUES ('024003002', '3', '汇川区', '0');
INSERT INTO `regions` VALUES ('024003003', '3', '遵义县', '0');
INSERT INTO `regions` VALUES ('024003004', '3', '桐梓县', '0');
INSERT INTO `regions` VALUES ('024003005', '3', '绥阳县', '0');
INSERT INTO `regions` VALUES ('024003006', '3', '正安县', '0');
INSERT INTO `regions` VALUES ('024003007', '3', '道真仡佬族苗族自治县', '0');
INSERT INTO `regions` VALUES ('024003008', '3', '务川仡佬族苗族自治县', '0');
INSERT INTO `regions` VALUES ('024003009', '3', '凤冈县', '0');
INSERT INTO `regions` VALUES ('024003010', '3', '湄潭县', '0');
INSERT INTO `regions` VALUES ('024003011', '3', '余庆县', '0');
INSERT INTO `regions` VALUES ('024003012', '3', '习水县', '0');
INSERT INTO `regions` VALUES ('024003013', '3', '赤水市', '0');
INSERT INTO `regions` VALUES ('024003014', '3', '仁怀市', '0');
INSERT INTO `regions` VALUES ('024004', '2', '安顺市', '0');
INSERT INTO `regions` VALUES ('024004001', '3', '西秀区', '0');
INSERT INTO `regions` VALUES ('024004002', '3', '平坝区', '0');
INSERT INTO `regions` VALUES ('024004003', '3', '普定县', '0');
INSERT INTO `regions` VALUES ('024004004', '3', '镇宁布依族苗族自治县', '0');
INSERT INTO `regions` VALUES ('024004005', '3', '关岭布依族苗族自治县', '0');
INSERT INTO `regions` VALUES ('024004006', '3', '紫云苗族布依族自治县', '0');
INSERT INTO `regions` VALUES ('024005', '2', '毕节市', '0');
INSERT INTO `regions` VALUES ('024005001', '3', '七星关区', '0');
INSERT INTO `regions` VALUES ('024005002', '3', '大方县', '0');
INSERT INTO `regions` VALUES ('024005003', '3', '黔西县', '0');
INSERT INTO `regions` VALUES ('024005004', '3', '金沙县', '0');
INSERT INTO `regions` VALUES ('024005005', '3', '织金县', '0');
INSERT INTO `regions` VALUES ('024005006', '3', '纳雍县', '0');
INSERT INTO `regions` VALUES ('024005007', '3', '威宁彝族回族苗族自治县', '0');
INSERT INTO `regions` VALUES ('024005008', '3', '赫章县', '0');
INSERT INTO `regions` VALUES ('024006', '2', '铜仁市', '0');
INSERT INTO `regions` VALUES ('024006001', '3', '碧江区', '0');
INSERT INTO `regions` VALUES ('024006002', '3', '万山区', '0');
INSERT INTO `regions` VALUES ('024006003', '3', '江口县', '0');
INSERT INTO `regions` VALUES ('024006004', '3', '玉屏侗族自治县', '0');
INSERT INTO `regions` VALUES ('024006005', '3', '石阡县', '0');
INSERT INTO `regions` VALUES ('024006006', '3', '思南县', '0');
INSERT INTO `regions` VALUES ('024006007', '3', '印江土家族苗族自治县', '0');
INSERT INTO `regions` VALUES ('024006008', '3', '德江县', '0');
INSERT INTO `regions` VALUES ('024006009', '3', '沿河土家族自治县', '0');
INSERT INTO `regions` VALUES ('024006010', '3', '松桃苗族自治县', '0');
INSERT INTO `regions` VALUES ('024007', '2', '黔西南布依族苗族自治州', '0');
INSERT INTO `regions` VALUES ('024007001', '3', '兴义市 ', '0');
INSERT INTO `regions` VALUES ('024007002', '3', '兴仁县', '0');
INSERT INTO `regions` VALUES ('024007003', '3', '普安县', '0');
INSERT INTO `regions` VALUES ('024007004', '3', '晴隆县', '0');
INSERT INTO `regions` VALUES ('024007005', '3', '贞丰县', '0');
INSERT INTO `regions` VALUES ('024007006', '3', '望谟县', '0');
INSERT INTO `regions` VALUES ('024007007', '3', '册亨县', '0');
INSERT INTO `regions` VALUES ('024007008', '3', '安龙县', '0');
INSERT INTO `regions` VALUES ('024008', '2', '黔东南苗族侗族自治州', '0');
INSERT INTO `regions` VALUES ('024008001', '3', '凯里市', '0');
INSERT INTO `regions` VALUES ('024008002', '3', '黄平县', '0');
INSERT INTO `regions` VALUES ('024008003', '3', '施秉县', '0');
INSERT INTO `regions` VALUES ('024008004', '3', '三穗县', '0');
INSERT INTO `regions` VALUES ('024008005', '3', '镇远县', '0');
INSERT INTO `regions` VALUES ('024008006', '3', '岑巩县', '0');
INSERT INTO `regions` VALUES ('024008007', '3', '天柱县', '0');
INSERT INTO `regions` VALUES ('024008008', '3', '锦屏县', '0');
INSERT INTO `regions` VALUES ('024008009', '3', '剑河县', '0');
INSERT INTO `regions` VALUES ('024008010', '3', '台江县', '0');
INSERT INTO `regions` VALUES ('024008011', '3', '黎平县', '0');
INSERT INTO `regions` VALUES ('024008012', '3', '榕江县', '0');
INSERT INTO `regions` VALUES ('024008013', '3', '从江县', '0');
INSERT INTO `regions` VALUES ('024008014', '3', '雷山县', '0');
INSERT INTO `regions` VALUES ('024008015', '3', '麻江县', '0');
INSERT INTO `regions` VALUES ('024008016', '3', '丹寨县', '0');
INSERT INTO `regions` VALUES ('024009', '2', '黔南布依族苗族自治州', '0');
INSERT INTO `regions` VALUES ('024009001', '3', '都匀市', '0');
INSERT INTO `regions` VALUES ('024009002', '3', '福泉市', '0');
INSERT INTO `regions` VALUES ('024009003', '3', '荔波县', '0');
INSERT INTO `regions` VALUES ('024009004', '3', '贵定县', '0');
INSERT INTO `regions` VALUES ('024009005', '3', '瓮安县', '0');
INSERT INTO `regions` VALUES ('024009006', '3', '独山县', '0');
INSERT INTO `regions` VALUES ('024009007', '3', '平塘县', '0');
INSERT INTO `regions` VALUES ('024009008', '3', '罗甸县', '0');
INSERT INTO `regions` VALUES ('024009009', '3', '长顺县', '0');
INSERT INTO `regions` VALUES ('024009010', '3', '龙里县', '0');
INSERT INTO `regions` VALUES ('024009011', '3', '惠水县', '0');
INSERT INTO `regions` VALUES ('024009012', '3', '三都水族自治县', '0');
INSERT INTO `regions` VALUES ('025', '1', '云南省', '0');
INSERT INTO `regions` VALUES ('025001', '2', '昆明市', '0');
INSERT INTO `regions` VALUES ('025001001', '3', '五华区', '0');
INSERT INTO `regions` VALUES ('025001002', '3', '盘龙区', '0');
INSERT INTO `regions` VALUES ('025001003', '3', '官渡区', '0');
INSERT INTO `regions` VALUES ('025001004', '3', '西山区', '0');
INSERT INTO `regions` VALUES ('025001005', '3', '东川区', '0');
INSERT INTO `regions` VALUES ('025001006', '3', '呈贡区', '0');
INSERT INTO `regions` VALUES ('025001007', '3', '晋宁县', '0');
INSERT INTO `regions` VALUES ('025001008', '3', '富民县', '0');
INSERT INTO `regions` VALUES ('025001009', '3', '宜良县', '0');
INSERT INTO `regions` VALUES ('025001010', '3', '石林彝族自治县', '0');
INSERT INTO `regions` VALUES ('025001011', '3', '嵩明县', '0');
INSERT INTO `regions` VALUES ('025001012', '3', '禄劝彝族苗族自治县', '0');
INSERT INTO `regions` VALUES ('025001013', '3', '寻甸回族彝族自治县 ', '0');
INSERT INTO `regions` VALUES ('025001014', '3', '安宁市', '0');
INSERT INTO `regions` VALUES ('025002', '2', '曲靖市', '0');
INSERT INTO `regions` VALUES ('025002001', '3', '麒麟区', '0');
INSERT INTO `regions` VALUES ('025002002', '3', '马龙县', '0');
INSERT INTO `regions` VALUES ('025002003', '3', '陆良县', '0');
INSERT INTO `regions` VALUES ('025002004', '3', '师宗县', '0');
INSERT INTO `regions` VALUES ('025002005', '3', '罗平县', '0');
INSERT INTO `regions` VALUES ('025002006', '3', '富源县', '0');
INSERT INTO `regions` VALUES ('025002007', '3', '会泽县', '0');
INSERT INTO `regions` VALUES ('025002008', '3', '沾益县', '0');
INSERT INTO `regions` VALUES ('025002009', '3', '宣威市', '0');
INSERT INTO `regions` VALUES ('025003', '2', '玉溪市', '0');
INSERT INTO `regions` VALUES ('025003001', '3', '红塔区', '0');
INSERT INTO `regions` VALUES ('025003002', '3', '江川县', '0');
INSERT INTO `regions` VALUES ('025003003', '3', '澄江县', '0');
INSERT INTO `regions` VALUES ('025003004', '3', '通海县', '0');
INSERT INTO `regions` VALUES ('025003005', '3', '华宁县', '0');
INSERT INTO `regions` VALUES ('025003006', '3', '易门县', '0');
INSERT INTO `regions` VALUES ('025003007', '3', '峨山彝族自治县', '0');
INSERT INTO `regions` VALUES ('025003008', '3', '新平彝族傣族自治县', '0');
INSERT INTO `regions` VALUES ('025003009', '3', '元江哈尼族彝族傣族自治县', '0');
INSERT INTO `regions` VALUES ('025004', '2', '保山市', '0');
INSERT INTO `regions` VALUES ('025004001', '3', '隆阳区', '0');
INSERT INTO `regions` VALUES ('025004002', '3', '施甸县', '0');
INSERT INTO `regions` VALUES ('025004003', '3', '腾冲县', '0');
INSERT INTO `regions` VALUES ('025004004', '3', '龙陵县', '0');
INSERT INTO `regions` VALUES ('025004005', '3', '昌宁县', '0');
INSERT INTO `regions` VALUES ('025005', '2', '昭通市', '0');
INSERT INTO `regions` VALUES ('025005001', '3', '昭阳区', '0');
INSERT INTO `regions` VALUES ('025005002', '3', '鲁甸县', '0');
INSERT INTO `regions` VALUES ('025005003', '3', '巧家县', '0');
INSERT INTO `regions` VALUES ('025005004', '3', '盐津县', '0');
INSERT INTO `regions` VALUES ('025005005', '3', '大关县', '0');
INSERT INTO `regions` VALUES ('025005006', '3', '永善县', '0');
INSERT INTO `regions` VALUES ('025005007', '3', '绥江县', '0');
INSERT INTO `regions` VALUES ('025005008', '3', '镇雄县', '0');
INSERT INTO `regions` VALUES ('025005009', '3', '彝良县', '0');
INSERT INTO `regions` VALUES ('025005010', '3', '威信县', '0');
INSERT INTO `regions` VALUES ('025005011', '3', '水富县', '0');
INSERT INTO `regions` VALUES ('025006', '2', '丽江市', '0');
INSERT INTO `regions` VALUES ('025006001', '3', '古城区', '0');
INSERT INTO `regions` VALUES ('025006002', '3', '玉龙纳西族自治县', '0');
INSERT INTO `regions` VALUES ('025006003', '3', '永胜县', '0');
INSERT INTO `regions` VALUES ('025006004', '3', '华坪县', '0');
INSERT INTO `regions` VALUES ('025006005', '3', '宁蒗彝族自治县', '0');
INSERT INTO `regions` VALUES ('025007', '2', '普洱市', '0');
INSERT INTO `regions` VALUES ('025007001', '3', '思茅区', '0');
INSERT INTO `regions` VALUES ('025007002', '3', '宁洱哈尼族彝族自治县', '0');
INSERT INTO `regions` VALUES ('025007003', '3', '墨江哈尼族自治县', '0');
INSERT INTO `regions` VALUES ('025007004', '3', '景东彝族自治县', '0');
INSERT INTO `regions` VALUES ('025007005', '3', '景谷傣族彝族自治县', '0');
INSERT INTO `regions` VALUES ('025007006', '3', '镇沅彝族哈尼族拉祜族自治县', '0');
INSERT INTO `regions` VALUES ('025007007', '3', '江城哈尼族彝族自治县', '0');
INSERT INTO `regions` VALUES ('025007008', '3', '孟连傣族拉祜族佤族自治县', '0');
INSERT INTO `regions` VALUES ('025007009', '3', '澜沧拉祜族自治县', '0');
INSERT INTO `regions` VALUES ('025007010', '3', '西盟佤族自治县', '0');
INSERT INTO `regions` VALUES ('025008', '2', '临沧市', '0');
INSERT INTO `regions` VALUES ('025008001', '3', '临翔区', '0');
INSERT INTO `regions` VALUES ('025008002', '3', '凤庆县', '0');
INSERT INTO `regions` VALUES ('025008003', '3', '云县', '0');
INSERT INTO `regions` VALUES ('025008004', '3', '永德县', '0');
INSERT INTO `regions` VALUES ('025008005', '3', '镇康县', '0');
INSERT INTO `regions` VALUES ('025008006', '3', '双江拉祜族佤族布朗族傣族自治县', '0');
INSERT INTO `regions` VALUES ('025008007', '3', '耿马傣族佤族自治县', '0');
INSERT INTO `regions` VALUES ('025008008', '3', '沧源佤族自治县', '0');
INSERT INTO `regions` VALUES ('025009', '2', '楚雄彝族自治州', '0');
INSERT INTO `regions` VALUES ('025009001', '3', '楚雄市', '0');
INSERT INTO `regions` VALUES ('025009002', '3', '双柏县', '0');
INSERT INTO `regions` VALUES ('025009003', '3', '牟定县', '0');
INSERT INTO `regions` VALUES ('025009004', '3', '南华县', '0');
INSERT INTO `regions` VALUES ('025009005', '3', '姚安县', '0');
INSERT INTO `regions` VALUES ('025009006', '3', '大姚县', '0');
INSERT INTO `regions` VALUES ('025009007', '3', '永仁县', '0');
INSERT INTO `regions` VALUES ('025009008', '3', '元谋县', '0');
INSERT INTO `regions` VALUES ('025009009', '3', '武定县', '0');
INSERT INTO `regions` VALUES ('025009010', '3', '禄丰县', '0');
INSERT INTO `regions` VALUES ('025010', '2', '红河哈尼族彝族自治州', '0');
INSERT INTO `regions` VALUES ('025010001', '3', '个旧市', '0');
INSERT INTO `regions` VALUES ('025010002', '3', '开远市', '0');
INSERT INTO `regions` VALUES ('025010003', '3', '蒙自市', '0');
INSERT INTO `regions` VALUES ('025010004', '3', '弥勒市', '0');
INSERT INTO `regions` VALUES ('025010005', '3', '屏边苗族自治县', '0');
INSERT INTO `regions` VALUES ('025010006', '3', '建水县', '0');
INSERT INTO `regions` VALUES ('025010007', '3', '石屏县', '0');
INSERT INTO `regions` VALUES ('025010008', '3', '泸西县', '0');
INSERT INTO `regions` VALUES ('025010009', '3', '元阳县', '0');
INSERT INTO `regions` VALUES ('025010010', '3', '红河县', '0');
INSERT INTO `regions` VALUES ('025010011', '3', '金平苗族瑶族傣族自治县', '0');
INSERT INTO `regions` VALUES ('025010012', '3', '绿春县', '0');
INSERT INTO `regions` VALUES ('025010013', '3', '河口瑶族自治县', '0');
INSERT INTO `regions` VALUES ('025011', '2', '文山壮族苗族自治州', '0');
INSERT INTO `regions` VALUES ('025011001', '3', '文山市', '0');
INSERT INTO `regions` VALUES ('025011002', '3', '砚山县', '0');
INSERT INTO `regions` VALUES ('025011003', '3', '西畴县', '0');
INSERT INTO `regions` VALUES ('025011004', '3', '麻栗坡县', '0');
INSERT INTO `regions` VALUES ('025011005', '3', '马关县', '0');
INSERT INTO `regions` VALUES ('025011006', '3', '丘北县', '0');
INSERT INTO `regions` VALUES ('025011007', '3', '广南县', '0');
INSERT INTO `regions` VALUES ('025011008', '3', '富宁县', '0');
INSERT INTO `regions` VALUES ('025012', '2', '西双版纳傣族自治州', '0');
INSERT INTO `regions` VALUES ('025012001', '3', '景洪市', '0');
INSERT INTO `regions` VALUES ('025012002', '3', '勐海县', '0');
INSERT INTO `regions` VALUES ('025012003', '3', '勐腊县', '0');
INSERT INTO `regions` VALUES ('025013', '2', '大理白族自治州', '0');
INSERT INTO `regions` VALUES ('025013001', '3', '大理市', '0');
INSERT INTO `regions` VALUES ('025013002', '3', '漾濞彝族自治县', '0');
INSERT INTO `regions` VALUES ('025013003', '3', '祥云县', '0');
INSERT INTO `regions` VALUES ('025013004', '3', '宾川县', '0');
INSERT INTO `regions` VALUES ('025013005', '3', '弥渡县', '0');
INSERT INTO `regions` VALUES ('025013006', '3', '南涧彝族自治县', '0');
INSERT INTO `regions` VALUES ('025013007', '3', '巍山彝族回族自治县', '0');
INSERT INTO `regions` VALUES ('025013008', '3', '永平县', '0');
INSERT INTO `regions` VALUES ('025013009', '3', '云龙县', '0');
INSERT INTO `regions` VALUES ('025013010', '3', '洱源县', '0');
INSERT INTO `regions` VALUES ('025013011', '3', '剑川县', '0');
INSERT INTO `regions` VALUES ('025013012', '3', '鹤庆县', '0');
INSERT INTO `regions` VALUES ('025014', '2', '德宏傣族景颇族自治州', '0');
INSERT INTO `regions` VALUES ('025014001', '3', '瑞丽市', '0');
INSERT INTO `regions` VALUES ('025014002', '3', '芒市', '0');
INSERT INTO `regions` VALUES ('025014003', '3', '梁河县', '0');
INSERT INTO `regions` VALUES ('025014004', '3', '盈江县', '0');
INSERT INTO `regions` VALUES ('025014005', '3', '陇川县', '0');
INSERT INTO `regions` VALUES ('025015', '2', '怒江傈僳族自治州', '0');
INSERT INTO `regions` VALUES ('025015001', '3', '泸水县', '0');
INSERT INTO `regions` VALUES ('025015002', '3', '福贡县', '0');
INSERT INTO `regions` VALUES ('025015003', '3', '贡山独龙族怒族自治县', '0');
INSERT INTO `regions` VALUES ('025015004', '3', '兰坪白族普米族自治县', '0');
INSERT INTO `regions` VALUES ('025016', '2', '迪庆藏族自治州', '0');
INSERT INTO `regions` VALUES ('025016001', '3', '香格里拉市', '0');
INSERT INTO `regions` VALUES ('025016002', '3', '德钦县', '0');
INSERT INTO `regions` VALUES ('025016003', '3', '维西傈僳族自治县', '0');
INSERT INTO `regions` VALUES ('026', '1', '西藏自治区', '0');
INSERT INTO `regions` VALUES ('026001', '2', '拉萨市', '0');
INSERT INTO `regions` VALUES ('026001001', '3', '城关区', '0');
INSERT INTO `regions` VALUES ('026001002', '3', '林周县', '0');
INSERT INTO `regions` VALUES ('026001003', '3', '当雄县', '0');
INSERT INTO `regions` VALUES ('026001004', '3', '尼木县', '0');
INSERT INTO `regions` VALUES ('026001005', '3', '曲水县', '0');
INSERT INTO `regions` VALUES ('026001006', '3', '堆龙德庆县', '0');
INSERT INTO `regions` VALUES ('026001007', '3', '达孜县', '0');
INSERT INTO `regions` VALUES ('026001008', '3', '墨竹工卡县', '0');
INSERT INTO `regions` VALUES ('026002', '2', '日喀则市', '0');
INSERT INTO `regions` VALUES ('026002001', '3', '桑珠孜区', '0');
INSERT INTO `regions` VALUES ('026002002', '3', '南木林县', '0');
INSERT INTO `regions` VALUES ('026002003', '3', '江孜县', '0');
INSERT INTO `regions` VALUES ('026002004', '3', '定日县', '0');
INSERT INTO `regions` VALUES ('026002005', '3', '萨迦县', '0');
INSERT INTO `regions` VALUES ('026002006', '3', '拉孜县', '0');
INSERT INTO `regions` VALUES ('026002007', '3', '昂仁县', '0');
INSERT INTO `regions` VALUES ('026002008', '3', '谢通门县', '0');
INSERT INTO `regions` VALUES ('026002009', '3', '白朗县', '0');
INSERT INTO `regions` VALUES ('026002010', '3', '仁布县', '0');
INSERT INTO `regions` VALUES ('026002011', '3', '康马县', '0');
INSERT INTO `regions` VALUES ('026002012', '3', '定结县', '0');
INSERT INTO `regions` VALUES ('026002013', '3', '仲巴县', '0');
INSERT INTO `regions` VALUES ('026002014', '3', '亚东县', '0');
INSERT INTO `regions` VALUES ('026002015', '3', '吉隆县', '0');
INSERT INTO `regions` VALUES ('026002016', '3', '聂拉木县', '0');
INSERT INTO `regions` VALUES ('026002017', '3', '萨嘎县', '0');
INSERT INTO `regions` VALUES ('026002018', '3', '岗巴县', '0');
INSERT INTO `regions` VALUES ('026003', '2', '昌都市', '0');
INSERT INTO `regions` VALUES ('026003001', '3', '卡若区', '0');
INSERT INTO `regions` VALUES ('026003002', '3', '江达县', '0');
INSERT INTO `regions` VALUES ('026003003', '3', '贡觉县', '0');
INSERT INTO `regions` VALUES ('026003004', '3', '类乌齐县', '0');
INSERT INTO `regions` VALUES ('026003005', '3', '丁青县', '0');
INSERT INTO `regions` VALUES ('026003006', '3', '察雅县', '0');
INSERT INTO `regions` VALUES ('026003007', '3', '八宿县', '0');
INSERT INTO `regions` VALUES ('026003008', '3', '左贡县', '0');
INSERT INTO `regions` VALUES ('026003009', '3', '芒康县', '0');
INSERT INTO `regions` VALUES ('026003010', '3', '洛隆县', '0');
INSERT INTO `regions` VALUES ('026003011', '3', '边坝县', '0');
INSERT INTO `regions` VALUES ('026004', '2', '山南地区', '0');
INSERT INTO `regions` VALUES ('026004001', '3', '乃东县', '0');
INSERT INTO `regions` VALUES ('026004002', '3', '扎囊县', '0');
INSERT INTO `regions` VALUES ('026004003', '3', '贡嘎县', '0');
INSERT INTO `regions` VALUES ('026004004', '3', '桑日县', '0');
INSERT INTO `regions` VALUES ('026004005', '3', '琼结县', '0');
INSERT INTO `regions` VALUES ('026004006', '3', '曲松县', '0');
INSERT INTO `regions` VALUES ('026004007', '3', '措美县', '0');
INSERT INTO `regions` VALUES ('026004008', '3', '洛扎县', '0');
INSERT INTO `regions` VALUES ('026004009', '3', '加查县', '0');
INSERT INTO `regions` VALUES ('026004010', '3', '隆子县', '0');
INSERT INTO `regions` VALUES ('026004011', '3', '错那县', '0');
INSERT INTO `regions` VALUES ('026004012', '3', '浪卡子县', '0');
INSERT INTO `regions` VALUES ('026005', '2', '那曲地区', '0');
INSERT INTO `regions` VALUES ('026005001', '3', '那曲县', '0');
INSERT INTO `regions` VALUES ('026005002', '3', '嘉黎县', '0');
INSERT INTO `regions` VALUES ('026005003', '3', '比如县', '0');
INSERT INTO `regions` VALUES ('026005004', '3', '聂荣县', '0');
INSERT INTO `regions` VALUES ('026005005', '3', '安多县', '0');
INSERT INTO `regions` VALUES ('026005006', '3', '申扎县', '0');
INSERT INTO `regions` VALUES ('026005007', '3', '索县', '0');
INSERT INTO `regions` VALUES ('026005008', '3', '班戈县', '0');
INSERT INTO `regions` VALUES ('026005009', '3', '巴青县', '0');
INSERT INTO `regions` VALUES ('026005010', '3', '尼玛县', '0');
INSERT INTO `regions` VALUES ('026005011', '3', '双湖县', '0');
INSERT INTO `regions` VALUES ('026006', '2', '阿里地区', '0');
INSERT INTO `regions` VALUES ('026006001', '3', '普兰县', '0');
INSERT INTO `regions` VALUES ('026006002', '3', '札达县', '0');
INSERT INTO `regions` VALUES ('026006003', '3', '噶尔县', '0');
INSERT INTO `regions` VALUES ('026006004', '3', '日土县', '0');
INSERT INTO `regions` VALUES ('026006005', '3', '革吉县', '0');
INSERT INTO `regions` VALUES ('026006006', '3', '改则县', '0');
INSERT INTO `regions` VALUES ('026006007', '3', '措勤县', '0');
INSERT INTO `regions` VALUES ('026007', '2', '林芝地区', '0');
INSERT INTO `regions` VALUES ('026007001', '3', '林芝县', '0');
INSERT INTO `regions` VALUES ('026007002', '3', '工布江达县', '0');
INSERT INTO `regions` VALUES ('026007003', '3', '米林县', '0');
INSERT INTO `regions` VALUES ('026007004', '3', '墨脱县', '0');
INSERT INTO `regions` VALUES ('026007005', '3', '波密县', '0');
INSERT INTO `regions` VALUES ('026007006', '3', '察隅县', '0');
INSERT INTO `regions` VALUES ('026007007', '3', '朗县', '0');
INSERT INTO `regions` VALUES ('027', '1', '陕西省', '0');
INSERT INTO `regions` VALUES ('027001', '2', '西安市', '0');
INSERT INTO `regions` VALUES ('027001001', '3', '新城区', '0');
INSERT INTO `regions` VALUES ('027001002', '3', '碑林区', '0');
INSERT INTO `regions` VALUES ('027001003', '3', '莲湖区', '0');
INSERT INTO `regions` VALUES ('027001004', '3', '灞桥区', '0');
INSERT INTO `regions` VALUES ('027001005', '3', '未央区', '0');
INSERT INTO `regions` VALUES ('027001006', '3', '雁塔区', '0');
INSERT INTO `regions` VALUES ('027001007', '3', '阎良区', '0');
INSERT INTO `regions` VALUES ('027001008', '3', '临潼区', '0');
INSERT INTO `regions` VALUES ('027001009', '3', '长安区', '0');
INSERT INTO `regions` VALUES ('027001010', '3', '蓝田县', '0');
INSERT INTO `regions` VALUES ('027001011', '3', '周至县', '0');
INSERT INTO `regions` VALUES ('027001012', '3', '户县', '0');
INSERT INTO `regions` VALUES ('027001013', '3', '高陵区', '0');
INSERT INTO `regions` VALUES ('027002', '2', '铜川市', '0');
INSERT INTO `regions` VALUES ('027002001', '3', '王益区', '0');
INSERT INTO `regions` VALUES ('027002002', '3', '印台区', '0');
INSERT INTO `regions` VALUES ('027002003', '3', '耀州区', '0');
INSERT INTO `regions` VALUES ('027002004', '3', '宜君县', '0');
INSERT INTO `regions` VALUES ('027003', '2', '宝鸡市', '0');
INSERT INTO `regions` VALUES ('027003001', '3', '渭滨区', '0');
INSERT INTO `regions` VALUES ('027003002', '3', '金台区', '0');
INSERT INTO `regions` VALUES ('027003003', '3', '陈仓区', '0');
INSERT INTO `regions` VALUES ('027003004', '3', '凤翔县', '0');
INSERT INTO `regions` VALUES ('027003005', '3', '岐山县', '0');
INSERT INTO `regions` VALUES ('027003006', '3', '扶风县', '0');
INSERT INTO `regions` VALUES ('027003007', '3', '眉县', '0');
INSERT INTO `regions` VALUES ('027003008', '3', '陇县', '0');
INSERT INTO `regions` VALUES ('027003009', '3', '千阳县', '0');
INSERT INTO `regions` VALUES ('027003010', '3', '麟游县', '0');
INSERT INTO `regions` VALUES ('027003011', '3', '凤县', '0');
INSERT INTO `regions` VALUES ('027003012', '3', '太白县', '0');
INSERT INTO `regions` VALUES ('027004', '2', '咸阳市', '0');
INSERT INTO `regions` VALUES ('027004001', '3', '秦都区', '0');
INSERT INTO `regions` VALUES ('027004002', '3', '杨陵区', '0');
INSERT INTO `regions` VALUES ('027004003', '3', '渭城区', '0');
INSERT INTO `regions` VALUES ('027004004', '3', '三原县', '0');
INSERT INTO `regions` VALUES ('027004005', '3', '泾阳县', '0');
INSERT INTO `regions` VALUES ('027004006', '3', '乾县', '0');
INSERT INTO `regions` VALUES ('027004007', '3', '礼泉县', '0');
INSERT INTO `regions` VALUES ('027004008', '3', '永寿县', '0');
INSERT INTO `regions` VALUES ('027004009', '3', '彬县', '0');
INSERT INTO `regions` VALUES ('027004010', '3', '长武县', '0');
INSERT INTO `regions` VALUES ('027004011', '3', '旬邑县', '0');
INSERT INTO `regions` VALUES ('027004012', '3', '淳化县', '0');
INSERT INTO `regions` VALUES ('027004013', '3', '武功县', '0');
INSERT INTO `regions` VALUES ('027004014', '3', '兴平市', '0');
INSERT INTO `regions` VALUES ('027005', '2', '渭南市', '0');
INSERT INTO `regions` VALUES ('027005001', '3', '临渭区', '0');
INSERT INTO `regions` VALUES ('027005002', '3', '华县', '0');
INSERT INTO `regions` VALUES ('027005003', '3', '潼关县', '0');
INSERT INTO `regions` VALUES ('027005004', '3', '大荔县', '0');
INSERT INTO `regions` VALUES ('027005005', '3', '合阳县', '0');
INSERT INTO `regions` VALUES ('027005006', '3', '澄城县', '0');
INSERT INTO `regions` VALUES ('027005007', '3', '蒲城县', '0');
INSERT INTO `regions` VALUES ('027005008', '3', '白水县', '0');
INSERT INTO `regions` VALUES ('027005009', '3', '富平县', '0');
INSERT INTO `regions` VALUES ('027005010', '3', '韩城市', '0');
INSERT INTO `regions` VALUES ('027005011', '3', '华阴市', '0');
INSERT INTO `regions` VALUES ('027006', '2', '延安市', '0');
INSERT INTO `regions` VALUES ('027006001', '3', '宝塔区', '0');
INSERT INTO `regions` VALUES ('027006002', '3', '延长县', '0');
INSERT INTO `regions` VALUES ('027006003', '3', '延川县', '0');
INSERT INTO `regions` VALUES ('027006004', '3', '子长县', '0');
INSERT INTO `regions` VALUES ('027006005', '3', '安塞县', '0');
INSERT INTO `regions` VALUES ('027006006', '3', '志丹县', '0');
INSERT INTO `regions` VALUES ('027006007', '3', '吴起县', '0');
INSERT INTO `regions` VALUES ('027006008', '3', '甘泉县', '0');
INSERT INTO `regions` VALUES ('027006009', '3', '富县', '0');
INSERT INTO `regions` VALUES ('027006010', '3', '洛川县', '0');
INSERT INTO `regions` VALUES ('027006011', '3', '宜川县', '0');
INSERT INTO `regions` VALUES ('027006012', '3', '黄龙县', '0');
INSERT INTO `regions` VALUES ('027006013', '3', '黄陵县', '0');
INSERT INTO `regions` VALUES ('027007', '2', '汉中市', '0');
INSERT INTO `regions` VALUES ('027007001', '3', '汉台区', '0');
INSERT INTO `regions` VALUES ('027007002', '3', '南郑县', '0');
INSERT INTO `regions` VALUES ('027007003', '3', '城固县', '0');
INSERT INTO `regions` VALUES ('027007004', '3', '洋县', '0');
INSERT INTO `regions` VALUES ('027007005', '3', '西乡县', '0');
INSERT INTO `regions` VALUES ('027007006', '3', '勉县', '0');
INSERT INTO `regions` VALUES ('027007007', '3', '宁强县', '0');
INSERT INTO `regions` VALUES ('027007008', '3', '略阳县', '0');
INSERT INTO `regions` VALUES ('027007009', '3', '镇巴县', '0');
INSERT INTO `regions` VALUES ('027007010', '3', '留坝县', '0');
INSERT INTO `regions` VALUES ('027007011', '3', '佛坪县', '0');
INSERT INTO `regions` VALUES ('027008', '2', '榆林市', '0');
INSERT INTO `regions` VALUES ('027008001', '3', '榆阳区', '0');
INSERT INTO `regions` VALUES ('027008002', '3', '神木县', '0');
INSERT INTO `regions` VALUES ('027008003', '3', '府谷县', '0');
INSERT INTO `regions` VALUES ('027008004', '3', '横山县', '0');
INSERT INTO `regions` VALUES ('027008005', '3', '靖边县', '0');
INSERT INTO `regions` VALUES ('027008006', '3', '定边县', '0');
INSERT INTO `regions` VALUES ('027008007', '3', '绥德县', '0');
INSERT INTO `regions` VALUES ('027008008', '3', '米脂县', '0');
INSERT INTO `regions` VALUES ('027008009', '3', '佳县', '0');
INSERT INTO `regions` VALUES ('027008010', '3', '吴堡县', '0');
INSERT INTO `regions` VALUES ('027008011', '3', '清涧县', '0');
INSERT INTO `regions` VALUES ('027008012', '3', '子洲县', '0');
INSERT INTO `regions` VALUES ('027009', '2', '安康市', '0');
INSERT INTO `regions` VALUES ('027009001', '3', '汉滨区', '0');
INSERT INTO `regions` VALUES ('027009002', '3', '汉阴县', '0');
INSERT INTO `regions` VALUES ('027009003', '3', '石泉县', '0');
INSERT INTO `regions` VALUES ('027009004', '3', '宁陕县', '0');
INSERT INTO `regions` VALUES ('027009005', '3', '紫阳县', '0');
INSERT INTO `regions` VALUES ('027009006', '3', '岚皋县', '0');
INSERT INTO `regions` VALUES ('027009007', '3', '平利县', '0');
INSERT INTO `regions` VALUES ('027009008', '3', '镇坪县', '0');
INSERT INTO `regions` VALUES ('027009009', '3', '旬阳县', '0');
INSERT INTO `regions` VALUES ('027009010', '3', '白河县', '0');
INSERT INTO `regions` VALUES ('027010', '2', '商洛市', '0');
INSERT INTO `regions` VALUES ('027010001', '3', '商州区', '0');
INSERT INTO `regions` VALUES ('027010002', '3', '洛南县', '0');
INSERT INTO `regions` VALUES ('027010003', '3', '丹凤县', '0');
INSERT INTO `regions` VALUES ('027010004', '3', '商南县', '0');
INSERT INTO `regions` VALUES ('027010005', '3', '山阳县', '0');
INSERT INTO `regions` VALUES ('027010006', '3', '镇安县', '0');
INSERT INTO `regions` VALUES ('027010007', '3', '柞水县', '0');
INSERT INTO `regions` VALUES ('027011', '2', '西咸新区', '0');
INSERT INTO `regions` VALUES ('027011001', '3', '空港新城', '0');
INSERT INTO `regions` VALUES ('027011002', '3', '沣东新城', '0');
INSERT INTO `regions` VALUES ('027011003', '3', '秦汉新城', '0');
INSERT INTO `regions` VALUES ('027011004', '3', '沣西新城', '0');
INSERT INTO `regions` VALUES ('027011005', '3', '泾河新城', '0');
INSERT INTO `regions` VALUES ('028', '1', '甘肃省', '0');
INSERT INTO `regions` VALUES ('028001', '2', '兰州市', '0');
INSERT INTO `regions` VALUES ('028001001', '3', '城关区', '0');
INSERT INTO `regions` VALUES ('028001002', '3', '七里河区', '0');
INSERT INTO `regions` VALUES ('028001003', '3', '西固区', '0');
INSERT INTO `regions` VALUES ('028001004', '3', '安宁区', '0');
INSERT INTO `regions` VALUES ('028001005', '3', '红古区', '0');
INSERT INTO `regions` VALUES ('028001006', '3', '永登县', '0');
INSERT INTO `regions` VALUES ('028001007', '3', '皋兰县', '0');
INSERT INTO `regions` VALUES ('028001008', '3', '榆中县', '0');
INSERT INTO `regions` VALUES ('028002', '2', '嘉峪关市', '0');
INSERT INTO `regions` VALUES ('028002001', '3', '雄关区', '0');
INSERT INTO `regions` VALUES ('028002002', '3', '长城区', '0');
INSERT INTO `regions` VALUES ('028002003', '3', '镜铁区', '0');
INSERT INTO `regions` VALUES ('028003', '2', '金昌市', '0');
INSERT INTO `regions` VALUES ('028003001', '3', '金川区', '0');
INSERT INTO `regions` VALUES ('028003002', '3', '永昌县', '0');
INSERT INTO `regions` VALUES ('028004', '2', '白银市', '0');
INSERT INTO `regions` VALUES ('028004001', '3', '白银区', '0');
INSERT INTO `regions` VALUES ('028004002', '3', '平川区', '0');
INSERT INTO `regions` VALUES ('028004003', '3', '靖远县', '0');
INSERT INTO `regions` VALUES ('028004004', '3', '会宁县', '0');
INSERT INTO `regions` VALUES ('028004005', '3', '景泰县', '0');
INSERT INTO `regions` VALUES ('028005', '2', '天水市', '0');
INSERT INTO `regions` VALUES ('028005001', '3', '秦州区', '0');
INSERT INTO `regions` VALUES ('028005002', '3', '麦积区', '0');
INSERT INTO `regions` VALUES ('028005003', '3', '清水县', '0');
INSERT INTO `regions` VALUES ('028005004', '3', '秦安县', '0');
INSERT INTO `regions` VALUES ('028005005', '3', '甘谷县', '0');
INSERT INTO `regions` VALUES ('028005006', '3', '武山县', '0');
INSERT INTO `regions` VALUES ('028005007', '3', '张家川回族自治县', '0');
INSERT INTO `regions` VALUES ('028006', '2', '武威市', '0');
INSERT INTO `regions` VALUES ('028006001', '3', '凉州区', '0');
INSERT INTO `regions` VALUES ('028006002', '3', '民勤县', '0');
INSERT INTO `regions` VALUES ('028006003', '3', '古浪县', '0');
INSERT INTO `regions` VALUES ('028006004', '3', '天祝藏族自治县', '0');
INSERT INTO `regions` VALUES ('028007', '2', '张掖市', '0');
INSERT INTO `regions` VALUES ('028007001', '3', '甘州区', '0');
INSERT INTO `regions` VALUES ('028007002', '3', '肃南裕固族自治县', '0');
INSERT INTO `regions` VALUES ('028007003', '3', '民乐县', '0');
INSERT INTO `regions` VALUES ('028007004', '3', '临泽县', '0');
INSERT INTO `regions` VALUES ('028007005', '3', '高台县', '0');
INSERT INTO `regions` VALUES ('028007006', '3', '山丹县', '0');
INSERT INTO `regions` VALUES ('028008', '2', '平凉市', '0');
INSERT INTO `regions` VALUES ('028008001', '3', '崆峒区', '0');
INSERT INTO `regions` VALUES ('028008002', '3', '泾川县', '0');
INSERT INTO `regions` VALUES ('028008003', '3', '灵台县', '0');
INSERT INTO `regions` VALUES ('028008004', '3', '崇信县', '0');
INSERT INTO `regions` VALUES ('028008005', '3', '华亭县', '0');
INSERT INTO `regions` VALUES ('028008006', '3', '庄浪县', '0');
INSERT INTO `regions` VALUES ('028008007', '3', '静宁县', '0');
INSERT INTO `regions` VALUES ('028009', '2', '酒泉市', '0');
INSERT INTO `regions` VALUES ('028009001', '3', '肃州区', '0');
INSERT INTO `regions` VALUES ('028009002', '3', '金塔县', '0');
INSERT INTO `regions` VALUES ('028009003', '3', '瓜州县', '0');
INSERT INTO `regions` VALUES ('028009004', '3', '肃北蒙古族自治县', '0');
INSERT INTO `regions` VALUES ('028009005', '3', '阿克塞哈萨克族自治县', '0');
INSERT INTO `regions` VALUES ('028009006', '3', '玉门市', '0');
INSERT INTO `regions` VALUES ('028009007', '3', '敦煌市', '0');
INSERT INTO `regions` VALUES ('028010', '2', '庆阳市', '0');
INSERT INTO `regions` VALUES ('028010001', '3', '西峰区', '0');
INSERT INTO `regions` VALUES ('028010002', '3', '庆城县', '0');
INSERT INTO `regions` VALUES ('028010003', '3', '环县', '0');
INSERT INTO `regions` VALUES ('028010004', '3', '华池县', '0');
INSERT INTO `regions` VALUES ('028010005', '3', '合水县', '0');
INSERT INTO `regions` VALUES ('028010006', '3', '正宁县', '0');
INSERT INTO `regions` VALUES ('028010007', '3', '宁县', '0');
INSERT INTO `regions` VALUES ('028010008', '3', '镇原县', '0');
INSERT INTO `regions` VALUES ('028011', '2', '定西市', '0');
INSERT INTO `regions` VALUES ('028011001', '3', '安定区', '0');
INSERT INTO `regions` VALUES ('028011002', '3', '通渭县', '0');
INSERT INTO `regions` VALUES ('028011003', '3', '陇西县', '0');
INSERT INTO `regions` VALUES ('028011004', '3', '渭源县', '0');
INSERT INTO `regions` VALUES ('028011005', '3', '临洮县', '0');
INSERT INTO `regions` VALUES ('028011006', '3', '漳县', '0');
INSERT INTO `regions` VALUES ('028011007', '3', '岷县', '0');
INSERT INTO `regions` VALUES ('028012', '2', '陇南市', '0');
INSERT INTO `regions` VALUES ('028012001', '3', '武都区', '0');
INSERT INTO `regions` VALUES ('028012002', '3', '成县', '0');
INSERT INTO `regions` VALUES ('028012003', '3', '文县', '0');
INSERT INTO `regions` VALUES ('028012004', '3', '宕昌县', '0');
INSERT INTO `regions` VALUES ('028012005', '3', '康县', '0');
INSERT INTO `regions` VALUES ('028012006', '3', '西和县', '0');
INSERT INTO `regions` VALUES ('028012007', '3', '礼县', '0');
INSERT INTO `regions` VALUES ('028012008', '3', '徽县', '0');
INSERT INTO `regions` VALUES ('028012009', '3', '两当县', '0');
INSERT INTO `regions` VALUES ('028013', '2', '临夏回族自治州', '0');
INSERT INTO `regions` VALUES ('028013001', '3', '临夏市', '0');
INSERT INTO `regions` VALUES ('028013002', '3', '临夏县', '0');
INSERT INTO `regions` VALUES ('028013003', '3', '康乐县', '0');
INSERT INTO `regions` VALUES ('028013004', '3', '永靖县', '0');
INSERT INTO `regions` VALUES ('028013005', '3', '广河县', '0');
INSERT INTO `regions` VALUES ('028013006', '3', '和政县', '0');
INSERT INTO `regions` VALUES ('028013007', '3', '东乡族自治县', '0');
INSERT INTO `regions` VALUES ('028013008', '3', '积石山保安族东乡族撒拉族自治县', '0');
INSERT INTO `regions` VALUES ('028014', '2', '甘南藏族自治州', '0');
INSERT INTO `regions` VALUES ('028014001', '3', '合作市', '0');
INSERT INTO `regions` VALUES ('028014002', '3', '临潭县', '0');
INSERT INTO `regions` VALUES ('028014003', '3', '卓尼县', '0');
INSERT INTO `regions` VALUES ('028014004', '3', '舟曲县', '0');
INSERT INTO `regions` VALUES ('028014005', '3', '迭部县', '0');
INSERT INTO `regions` VALUES ('028014006', '3', '玛曲县', '0');
INSERT INTO `regions` VALUES ('028014007', '3', '碌曲县', '0');
INSERT INTO `regions` VALUES ('028014008', '3', '夏河县', '0');
INSERT INTO `regions` VALUES ('029', '1', '青海省', '0');
INSERT INTO `regions` VALUES ('029001', '2', '西宁市', '0');
INSERT INTO `regions` VALUES ('029001001', '3', '城东区', '0');
INSERT INTO `regions` VALUES ('029001002', '3', '城中区', '0');
INSERT INTO `regions` VALUES ('029001003', '3', '城西区', '0');
INSERT INTO `regions` VALUES ('029001004', '3', '城北区', '0');
INSERT INTO `regions` VALUES ('029001005', '3', '大通回族土族自治县', '0');
INSERT INTO `regions` VALUES ('029001006', '3', '湟中县', '0');
INSERT INTO `regions` VALUES ('029001007', '3', '湟源县', '0');
INSERT INTO `regions` VALUES ('029002', '2', '海东市', '0');
INSERT INTO `regions` VALUES ('029002001', '3', '乐都区', '0');
INSERT INTO `regions` VALUES ('029002002', '3', '平安县', '0');
INSERT INTO `regions` VALUES ('029002003', '3', '民和回族土族自治县', '0');
INSERT INTO `regions` VALUES ('029002004', '3', '互助土族自治县', '0');
INSERT INTO `regions` VALUES ('029002005', '3', '化隆回族自治县', '0');
INSERT INTO `regions` VALUES ('029002006', '3', '循化撒拉族自治县', '0');
INSERT INTO `regions` VALUES ('029003', '2', '海北藏族自治州', '0');
INSERT INTO `regions` VALUES ('029003001', '3', '门源回族自治县', '0');
INSERT INTO `regions` VALUES ('029003002', '3', '祁连县', '0');
INSERT INTO `regions` VALUES ('029003003', '3', '海晏县', '0');
INSERT INTO `regions` VALUES ('029003004', '3', '刚察县', '0');
INSERT INTO `regions` VALUES ('029004', '2', '黄南藏族自治州', '0');
INSERT INTO `regions` VALUES ('029004001', '3', '同仁县', '0');
INSERT INTO `regions` VALUES ('029004002', '3', '尖扎县', '0');
INSERT INTO `regions` VALUES ('029004003', '3', '泽库县', '0');
INSERT INTO `regions` VALUES ('029004004', '3', '河南蒙古族自治县', '0');
INSERT INTO `regions` VALUES ('029005', '2', '海南藏族自治州', '0');
INSERT INTO `regions` VALUES ('029005001', '3', '共和县', '0');
INSERT INTO `regions` VALUES ('029005002', '3', '同德县', '0');
INSERT INTO `regions` VALUES ('029005003', '3', '贵德县', '0');
INSERT INTO `regions` VALUES ('029005004', '3', '兴海县', '0');
INSERT INTO `regions` VALUES ('029005005', '3', '贵南县', '0');
INSERT INTO `regions` VALUES ('029006', '2', '果洛藏族自治州', '0');
INSERT INTO `regions` VALUES ('029006001', '3', '玛沁县', '0');
INSERT INTO `regions` VALUES ('029006002', '3', '班玛县', '0');
INSERT INTO `regions` VALUES ('029006003', '3', '甘德县', '0');
INSERT INTO `regions` VALUES ('029006004', '3', '达日县', '0');
INSERT INTO `regions` VALUES ('029006005', '3', '久治县', '0');
INSERT INTO `regions` VALUES ('029006006', '3', '玛多县', '0');
INSERT INTO `regions` VALUES ('029007', '2', '玉树藏族自治州', '0');
INSERT INTO `regions` VALUES ('029007001', '3', '玉树市', '0');
INSERT INTO `regions` VALUES ('029007002', '3', '杂多县', '0');
INSERT INTO `regions` VALUES ('029007003', '3', '称多县', '0');
INSERT INTO `regions` VALUES ('029007004', '3', '治多县', '0');
INSERT INTO `regions` VALUES ('029007005', '3', '囊谦县', '0');
INSERT INTO `regions` VALUES ('029007006', '3', '曲麻莱县', '0');
INSERT INTO `regions` VALUES ('029008', '2', '海西蒙古族藏族自治州', '0');
INSERT INTO `regions` VALUES ('029008001', '3', '格尔木市', '0');
INSERT INTO `regions` VALUES ('029008002', '3', '德令哈市', '0');
INSERT INTO `regions` VALUES ('029008003', '3', '乌兰县', '0');
INSERT INTO `regions` VALUES ('029008004', '3', '都兰县', '0');
INSERT INTO `regions` VALUES ('029008005', '3', '天峻县', '0');
INSERT INTO `regions` VALUES ('030', '1', '宁夏回族自治区', '0');
INSERT INTO `regions` VALUES ('030001', '2', '银川市', '0');
INSERT INTO `regions` VALUES ('030001001', '3', '兴庆区', '0');
INSERT INTO `regions` VALUES ('030001002', '3', '西夏区', '0');
INSERT INTO `regions` VALUES ('030001003', '3', '金凤区', '0');
INSERT INTO `regions` VALUES ('030001004', '3', '永宁县', '0');
INSERT INTO `regions` VALUES ('030001005', '3', '贺兰县', '0');
INSERT INTO `regions` VALUES ('030001006', '3', '灵武市', '0');
INSERT INTO `regions` VALUES ('030002', '2', '石嘴山市', '0');
INSERT INTO `regions` VALUES ('030002001', '3', '大武口区', '0');
INSERT INTO `regions` VALUES ('030002002', '3', '惠农区', '0');
INSERT INTO `regions` VALUES ('030002003', '3', '平罗县', '0');
INSERT INTO `regions` VALUES ('030003', '2', '吴忠市', '0');
INSERT INTO `regions` VALUES ('030003001', '3', '利通区', '0');
INSERT INTO `regions` VALUES ('030003002', '3', '红寺堡区', '0');
INSERT INTO `regions` VALUES ('030003003', '3', '盐池县', '0');
INSERT INTO `regions` VALUES ('030003004', '3', '同心县', '0');
INSERT INTO `regions` VALUES ('030003005', '3', '青铜峡市', '0');
INSERT INTO `regions` VALUES ('030004', '2', '固原市', '0');
INSERT INTO `regions` VALUES ('030004001', '3', '原州区', '0');
INSERT INTO `regions` VALUES ('030004002', '3', '西吉县', '0');
INSERT INTO `regions` VALUES ('030004003', '3', '隆德县', '0');
INSERT INTO `regions` VALUES ('030004004', '3', '泾源县', '0');
INSERT INTO `regions` VALUES ('030004005', '3', '彭阳县', '0');
INSERT INTO `regions` VALUES ('030005', '2', '中卫市', '0');
INSERT INTO `regions` VALUES ('030005001', '3', '沙坡头区', '0');
INSERT INTO `regions` VALUES ('030005002', '3', '中宁县', '0');
INSERT INTO `regions` VALUES ('030005003', '3', '海原县', '0');
INSERT INTO `regions` VALUES ('031', '1', '新疆维吾尔自治区', '0');
INSERT INTO `regions` VALUES ('031001', '2', '乌鲁木齐市', '0');
INSERT INTO `regions` VALUES ('031001001', '3', '天山区', '0');
INSERT INTO `regions` VALUES ('031001002', '3', '沙依巴克区', '0');
INSERT INTO `regions` VALUES ('031001003', '3', '新市区', '0');
INSERT INTO `regions` VALUES ('031001004', '3', '水磨沟区', '0');
INSERT INTO `regions` VALUES ('031001005', '3', '头屯河区', '0');
INSERT INTO `regions` VALUES ('031001006', '3', '达坂城区', '0');
INSERT INTO `regions` VALUES ('031001007', '3', '米东区', '0');
INSERT INTO `regions` VALUES ('031001008', '3', '乌鲁木齐县', '0');
INSERT INTO `regions` VALUES ('031002', '2', '克拉玛依市', '0');
INSERT INTO `regions` VALUES ('031002001', '3', '独山子区', '0');
INSERT INTO `regions` VALUES ('031002002', '3', '克拉玛依区', '0');
INSERT INTO `regions` VALUES ('031002003', '3', '白碱滩区', '0');
INSERT INTO `regions` VALUES ('031002004', '3', '乌尔禾区', '0');
INSERT INTO `regions` VALUES ('031003', '2', '吐鲁番地区', '0');
INSERT INTO `regions` VALUES ('031003001', '3', '吐鲁番市', '0');
INSERT INTO `regions` VALUES ('031003002', '3', '鄯善县', '0');
INSERT INTO `regions` VALUES ('031003003', '3', '托克逊县', '0');
INSERT INTO `regions` VALUES ('031004', '2', '哈密地区', '0');
INSERT INTO `regions` VALUES ('031004001', '3', '哈密市', '0');
INSERT INTO `regions` VALUES ('031004002', '3', '巴里坤哈萨克自治县', '0');
INSERT INTO `regions` VALUES ('031004003', '3', '伊吾县', '0');
INSERT INTO `regions` VALUES ('031005', '2', '昌吉回族自治州', '0');
INSERT INTO `regions` VALUES ('031005001', '3', '昌吉市', '0');
INSERT INTO `regions` VALUES ('031005002', '3', '阜康市', '0');
INSERT INTO `regions` VALUES ('031005003', '3', '呼图壁县', '0');
INSERT INTO `regions` VALUES ('031005004', '3', '玛纳斯县', '0');
INSERT INTO `regions` VALUES ('031005005', '3', '奇台县', '0');
INSERT INTO `regions` VALUES ('031005006', '3', '吉木萨尔县', '0');
INSERT INTO `regions` VALUES ('031005007', '3', '木垒哈萨克自治县', '0');
INSERT INTO `regions` VALUES ('031006', '2', '博尔塔拉蒙古自治州', '0');
INSERT INTO `regions` VALUES ('031006001', '3', '博乐市', '0');
INSERT INTO `regions` VALUES ('031006002', '3', '阿拉山口市', '0');
INSERT INTO `regions` VALUES ('031006003', '3', '精河县', '0');
INSERT INTO `regions` VALUES ('031006004', '3', '温泉县', '0');
INSERT INTO `regions` VALUES ('031007', '2', '巴音郭楞蒙古自治州', '0');
INSERT INTO `regions` VALUES ('031007001', '3', '库尔勒市', '0');
INSERT INTO `regions` VALUES ('031007002', '3', '轮台县', '0');
INSERT INTO `regions` VALUES ('031007003', '3', '尉犁县', '0');
INSERT INTO `regions` VALUES ('031007004', '3', '若羌县', '0');
INSERT INTO `regions` VALUES ('031007005', '3', '且末县', '0');
INSERT INTO `regions` VALUES ('031007006', '3', '焉耆回族自治县', '0');
INSERT INTO `regions` VALUES ('031007007', '3', '和静县', '0');
INSERT INTO `regions` VALUES ('031007008', '3', '和硕县', '0');
INSERT INTO `regions` VALUES ('031007009', '3', '博湖县', '0');
INSERT INTO `regions` VALUES ('031008', '2', '阿克苏地区', '0');
INSERT INTO `regions` VALUES ('031008001', '3', '阿克苏市', '0');
INSERT INTO `regions` VALUES ('031008002', '3', '温宿县', '0');
INSERT INTO `regions` VALUES ('031008003', '3', '库车县', '0');
INSERT INTO `regions` VALUES ('031008004', '3', '沙雅县', '0');
INSERT INTO `regions` VALUES ('031008005', '3', '新和县', '0');
INSERT INTO `regions` VALUES ('031008006', '3', '拜城县', '0');
INSERT INTO `regions` VALUES ('031008007', '3', '乌什县', '0');
INSERT INTO `regions` VALUES ('031008008', '3', '阿瓦提县', '0');
INSERT INTO `regions` VALUES ('031008009', '3', '柯坪县', '0');
INSERT INTO `regions` VALUES ('031009', '2', '克孜勒苏柯尔克孜自治州', '0');
INSERT INTO `regions` VALUES ('031009001', '3', '阿图什市', '0');
INSERT INTO `regions` VALUES ('031009002', '3', '阿克陶县', '0');
INSERT INTO `regions` VALUES ('031009003', '3', '阿合奇县', '0');
INSERT INTO `regions` VALUES ('031009004', '3', '乌恰县', '0');
INSERT INTO `regions` VALUES ('031010', '2', '喀什地区', '0');
INSERT INTO `regions` VALUES ('031010001', '3', '喀什市', '0');
INSERT INTO `regions` VALUES ('031010002', '3', '疏附县', '0');
INSERT INTO `regions` VALUES ('031010003', '3', '疏勒县', '0');
INSERT INTO `regions` VALUES ('031010004', '3', '英吉沙县', '0');
INSERT INTO `regions` VALUES ('031010005', '3', '泽普县', '0');
INSERT INTO `regions` VALUES ('031010006', '3', '莎车县', '0');
INSERT INTO `regions` VALUES ('031010007', '3', '叶城县', '0');
INSERT INTO `regions` VALUES ('031010008', '3', '麦盖提县', '0');
INSERT INTO `regions` VALUES ('031010009', '3', '岳普湖县', '0');
INSERT INTO `regions` VALUES ('031010010', '3', '伽师县', '0');
INSERT INTO `regions` VALUES ('031010011', '3', '巴楚县', '0');
INSERT INTO `regions` VALUES ('031010012', '3', '塔什库尔干塔吉克自治县', '0');
INSERT INTO `regions` VALUES ('031011', '2', '和田地区', '0');
INSERT INTO `regions` VALUES ('031011001', '3', '和田市', '0');
INSERT INTO `regions` VALUES ('031011002', '3', '和田县', '0');
INSERT INTO `regions` VALUES ('031011003', '3', '墨玉县', '0');
INSERT INTO `regions` VALUES ('031011004', '3', '皮山县', '0');
INSERT INTO `regions` VALUES ('031011005', '3', '洛浦县', '0');
INSERT INTO `regions` VALUES ('031011006', '3', '策勒县', '0');
INSERT INTO `regions` VALUES ('031011007', '3', '于田县', '0');
INSERT INTO `regions` VALUES ('031011008', '3', '民丰县', '0');
INSERT INTO `regions` VALUES ('031012', '2', '伊犁哈萨克自治州', '0');
INSERT INTO `regions` VALUES ('031012001', '3', '伊宁市', '0');
INSERT INTO `regions` VALUES ('031012002', '3', '奎屯市', '0');
INSERT INTO `regions` VALUES ('031012003', '3', '霍尔果斯市', '0');
INSERT INTO `regions` VALUES ('031012004', '3', '伊宁县', '0');
INSERT INTO `regions` VALUES ('031012005', '3', '察布查尔锡伯自治县', '0');
INSERT INTO `regions` VALUES ('031012006', '3', '霍城县', '0');
INSERT INTO `regions` VALUES ('031012007', '3', '巩留县', '0');
INSERT INTO `regions` VALUES ('031012008', '3', '新源县', '0');
INSERT INTO `regions` VALUES ('031012009', '3', '昭苏县', '0');
INSERT INTO `regions` VALUES ('031012010', '3', '特克斯县', '0');
INSERT INTO `regions` VALUES ('031012011', '3', '尼勒克县', '0');
INSERT INTO `regions` VALUES ('031013', '2', '塔城地区', '0');
INSERT INTO `regions` VALUES ('031013001', '3', '塔城市', '0');
INSERT INTO `regions` VALUES ('031013002', '3', '乌苏市', '0');
INSERT INTO `regions` VALUES ('031013003', '3', '额敏县', '0');
INSERT INTO `regions` VALUES ('031013004', '3', '沙湾县', '0');
INSERT INTO `regions` VALUES ('031013005', '3', '托里县', '0');
INSERT INTO `regions` VALUES ('031013006', '3', '裕民县', '0');
INSERT INTO `regions` VALUES ('031013007', '3', '和布克赛尔蒙古自治县', '0');
INSERT INTO `regions` VALUES ('031014', '2', '阿勒泰地区', '0');
INSERT INTO `regions` VALUES ('031014001', '3', '阿勒泰市', '0');
INSERT INTO `regions` VALUES ('031014002', '3', '布尔津县', '0');
INSERT INTO `regions` VALUES ('031014003', '3', '富蕴县', '0');
INSERT INTO `regions` VALUES ('031014004', '3', '福海县', '0');
INSERT INTO `regions` VALUES ('031014005', '3', '哈巴河县', '0');
INSERT INTO `regions` VALUES ('031014006', '3', '青河县', '0');
INSERT INTO `regions` VALUES ('031014007', '3', '吉木乃县', '0');
INSERT INTO `regions` VALUES ('031015', '2', '直辖县级', '0');
INSERT INTO `regions` VALUES ('031015001', '3', '石河子市', '0');
INSERT INTO `regions` VALUES ('031015002', '3', '阿拉尔市', '0');
INSERT INTO `regions` VALUES ('031015003', '3', '图木舒克市', '0');
INSERT INTO `regions` VALUES ('031015004', '3', '五家渠市', '0');
INSERT INTO `regions` VALUES ('031015005', '3', '北屯市', '0');
INSERT INTO `regions` VALUES ('031015006', '3', '铁门关市', '0');
INSERT INTO `regions` VALUES ('031015007', '3', '双河市', '0');
INSERT INTO `regions` VALUES ('032', '1', '台湾', '0');
INSERT INTO `regions` VALUES ('032001', '2', '台北市', '0');
INSERT INTO `regions` VALUES ('032001001', '3', '松山区', '0');
INSERT INTO `regions` VALUES ('032001002', '3', '信义区', '0');
INSERT INTO `regions` VALUES ('032001003', '3', '大安区', '0');
INSERT INTO `regions` VALUES ('032001004', '3', '中山区', '0');
INSERT INTO `regions` VALUES ('032001005', '3', '中正区', '0');
INSERT INTO `regions` VALUES ('032001006', '3', '大同区', '0');
INSERT INTO `regions` VALUES ('032001007', '3', '万华区', '0');
INSERT INTO `regions` VALUES ('032001008', '3', '文山区', '0');
INSERT INTO `regions` VALUES ('032001009', '3', '南港区', '0');
INSERT INTO `regions` VALUES ('032001010', '3', '内湖区', '0');
INSERT INTO `regions` VALUES ('032001011', '3', '士林区', '0');
INSERT INTO `regions` VALUES ('032001012', '3', '北投区', '0');
INSERT INTO `regions` VALUES ('032002', '2', '高雄市', '0');
INSERT INTO `regions` VALUES ('032002001', '3', '盐埕区', '0');
INSERT INTO `regions` VALUES ('032002002', '3', '鼓山区', '0');
INSERT INTO `regions` VALUES ('032002003', '3', '左营区', '0');
INSERT INTO `regions` VALUES ('032002004', '3', '楠梓区', '0');
INSERT INTO `regions` VALUES ('032002005', '3', '三民区', '0');
INSERT INTO `regions` VALUES ('032002006', '3', '新兴区', '0');
INSERT INTO `regions` VALUES ('032002007', '3', '前金区', '0');
INSERT INTO `regions` VALUES ('032002008', '3', '苓雅区', '0');
INSERT INTO `regions` VALUES ('032002009', '3', '前镇区', '0');
INSERT INTO `regions` VALUES ('032002010', '3', '旗津区', '0');
INSERT INTO `regions` VALUES ('032002011', '3', '小港区', '0');
INSERT INTO `regions` VALUES ('032002012', '3', '凤山区', '0');
INSERT INTO `regions` VALUES ('032002013', '3', '林园区', '0');
INSERT INTO `regions` VALUES ('032002014', '3', '大寮区', '0');
INSERT INTO `regions` VALUES ('032002015', '3', '大树区', '0');
INSERT INTO `regions` VALUES ('032002016', '3', '大社区', '0');
INSERT INTO `regions` VALUES ('032002017', '3', '仁武区', '0');
INSERT INTO `regions` VALUES ('032002018', '3', '鸟松区', '0');
INSERT INTO `regions` VALUES ('032002019', '3', '冈山区', '0');
INSERT INTO `regions` VALUES ('032002020', '3', '桥头区', '0');
INSERT INTO `regions` VALUES ('032002021', '3', '燕巢区', '0');
INSERT INTO `regions` VALUES ('032002022', '3', '田寮区', '0');
INSERT INTO `regions` VALUES ('032002023', '3', '阿莲区', '0');
INSERT INTO `regions` VALUES ('032002024', '3', '路竹区', '0');
INSERT INTO `regions` VALUES ('032002025', '3', '湖内区', '0');
INSERT INTO `regions` VALUES ('032002026', '3', '茄萣区', '0');
INSERT INTO `regions` VALUES ('032002027', '3', '永安区', '0');
INSERT INTO `regions` VALUES ('032002028', '3', '弥陀区', '0');
INSERT INTO `regions` VALUES ('032002029', '3', '梓官区', '0');
INSERT INTO `regions` VALUES ('032002030', '3', '旗山区', '0');
INSERT INTO `regions` VALUES ('032002031', '3', '美浓区', '0');
INSERT INTO `regions` VALUES ('032002032', '3', '六龟区', '0');
INSERT INTO `regions` VALUES ('032002033', '3', '甲仙区', '0');
INSERT INTO `regions` VALUES ('032002034', '3', '杉林区', '0');
INSERT INTO `regions` VALUES ('032002035', '3', '内门区', '0');
INSERT INTO `regions` VALUES ('032002036', '3', '茂林区', '0');
INSERT INTO `regions` VALUES ('032002037', '3', '桃源区', '0');
INSERT INTO `regions` VALUES ('032002038', '3', '那玛夏区', '0');
INSERT INTO `regions` VALUES ('032003', '2', '基隆市', '0');
INSERT INTO `regions` VALUES ('032003001', '3', '中正区', '0');
INSERT INTO `regions` VALUES ('032003002', '3', '七堵区', '0');
INSERT INTO `regions` VALUES ('032003003', '3', '暖暖区', '0');
INSERT INTO `regions` VALUES ('032003004', '3', '仁爱区', '0');
INSERT INTO `regions` VALUES ('032003005', '3', '中山区', '0');
INSERT INTO `regions` VALUES ('032003006', '3', '安乐区', '0');
INSERT INTO `regions` VALUES ('032003007', '3', '信义区', '0');
INSERT INTO `regions` VALUES ('032004', '2', '台中市', '0');
INSERT INTO `regions` VALUES ('032004001', '3', '中区', '0');
INSERT INTO `regions` VALUES ('032004002', '3', '东区', '0');
INSERT INTO `regions` VALUES ('032004003', '3', '南区', '0');
INSERT INTO `regions` VALUES ('032004004', '3', '西区', '0');
INSERT INTO `regions` VALUES ('032004005', '3', '北区', '0');
INSERT INTO `regions` VALUES ('032004006', '3', '西屯区', '0');
INSERT INTO `regions` VALUES ('032004007', '3', '南屯区', '0');
INSERT INTO `regions` VALUES ('032004008', '3', '北屯区', '0');
INSERT INTO `regions` VALUES ('032004009', '3', '丰原区', '0');
INSERT INTO `regions` VALUES ('032004010', '3', '东势区', '0');
INSERT INTO `regions` VALUES ('032004011', '3', '大甲区', '0');
INSERT INTO `regions` VALUES ('032004012', '3', '清水区', '0');
INSERT INTO `regions` VALUES ('032004013', '3', '沙鹿区', '0');
INSERT INTO `regions` VALUES ('032004014', '3', '梧栖区', '0');
INSERT INTO `regions` VALUES ('032004015', '3', '后里区', '0');
INSERT INTO `regions` VALUES ('032004016', '3', '神冈区', '0');
INSERT INTO `regions` VALUES ('032004017', '3', '潭子区', '0');
INSERT INTO `regions` VALUES ('032004018', '3', '大雅区', '0');
INSERT INTO `regions` VALUES ('032004019', '3', '新社区', '0');
INSERT INTO `regions` VALUES ('032004020', '3', '石冈区', '0');
INSERT INTO `regions` VALUES ('032004021', '3', '外埔区', '0');
INSERT INTO `regions` VALUES ('032004022', '3', '大安区', '0');
INSERT INTO `regions` VALUES ('032004023', '3', '乌日区', '0');
INSERT INTO `regions` VALUES ('032004024', '3', '大肚区', '0');
INSERT INTO `regions` VALUES ('032004025', '3', '龙井区', '0');
INSERT INTO `regions` VALUES ('032004026', '3', '雾峰区', '0');
INSERT INTO `regions` VALUES ('032004027', '3', '太平区', '0');
INSERT INTO `regions` VALUES ('032004028', '3', '大里区', '0');
INSERT INTO `regions` VALUES ('032004029', '3', '和平区', '0');
INSERT INTO `regions` VALUES ('032005', '2', '台南市', '0');
INSERT INTO `regions` VALUES ('032005001', '3', '东区', '0');
INSERT INTO `regions` VALUES ('032005002', '3', '南区', '0');
INSERT INTO `regions` VALUES ('032005003', '3', '北区', '0');
INSERT INTO `regions` VALUES ('032005004', '3', '安南区', '0');
INSERT INTO `regions` VALUES ('032005005', '3', '安平区', '0');
INSERT INTO `regions` VALUES ('032005006', '3', '中西区', '0');
INSERT INTO `regions` VALUES ('032005007', '3', '新营区', '0');
INSERT INTO `regions` VALUES ('032005008', '3', '盐水区', '0');
INSERT INTO `regions` VALUES ('032005009', '3', '白河区', '0');
INSERT INTO `regions` VALUES ('032005010', '3', '柳营区', '0');
INSERT INTO `regions` VALUES ('032005011', '3', '后壁区', '0');
INSERT INTO `regions` VALUES ('032005012', '3', '东山区', '0');
INSERT INTO `regions` VALUES ('032005013', '3', '麻豆区', '0');
INSERT INTO `regions` VALUES ('032005014', '3', '下营区', '0');
INSERT INTO `regions` VALUES ('032005015', '3', '六甲区', '0');
INSERT INTO `regions` VALUES ('032005016', '3', '官田区', '0');
INSERT INTO `regions` VALUES ('032005017', '3', '大内区', '0');
INSERT INTO `regions` VALUES ('032005018', '3', '佳里区', '0');
INSERT INTO `regions` VALUES ('032005019', '3', '学甲区', '0');
INSERT INTO `regions` VALUES ('032005020', '3', '西港区', '0');
INSERT INTO `regions` VALUES ('032005021', '3', '七股区', '0');
INSERT INTO `regions` VALUES ('032005022', '3', '将军区', '0');
INSERT INTO `regions` VALUES ('032005023', '3', '北门区', '0');
INSERT INTO `regions` VALUES ('032005024', '3', '新化区', '0');
INSERT INTO `regions` VALUES ('032005025', '3', '善化区', '0');
INSERT INTO `regions` VALUES ('032005026', '3', '新市区', '0');
INSERT INTO `regions` VALUES ('032005027', '3', '安定区', '0');
INSERT INTO `regions` VALUES ('032005028', '3', '山上区', '0');
INSERT INTO `regions` VALUES ('032005029', '3', '玉井区', '0');
INSERT INTO `regions` VALUES ('032005030', '3', '楠西区', '0');
INSERT INTO `regions` VALUES ('032005031', '3', '南化区', '0');
INSERT INTO `regions` VALUES ('032005032', '3', '左镇区', '0');
INSERT INTO `regions` VALUES ('032005033', '3', '仁德区', '0');
INSERT INTO `regions` VALUES ('032005034', '3', '归仁区', '0');
INSERT INTO `regions` VALUES ('032005035', '3', '关庙区', '0');
INSERT INTO `regions` VALUES ('032005036', '3', '龙崎区', '0');
INSERT INTO `regions` VALUES ('032005037', '3', '永康区', '0');
INSERT INTO `regions` VALUES ('032006', '2', '新竹市', '0');
INSERT INTO `regions` VALUES ('032006001', '3', '东区', '0');
INSERT INTO `regions` VALUES ('032006002', '3', '北区', '0');
INSERT INTO `regions` VALUES ('032006003', '3', '香山区', '0');
INSERT INTO `regions` VALUES ('032007', '2', '嘉义市', '0');
INSERT INTO `regions` VALUES ('032007001', '3', '东区', '0');
INSERT INTO `regions` VALUES ('032007002', '3', '西区', '0');
INSERT INTO `regions` VALUES ('032008', '2', '新北市', '0');
INSERT INTO `regions` VALUES ('032008001', '3', '板桥区', '0');
INSERT INTO `regions` VALUES ('032008002', '3', '三重区', '0');
INSERT INTO `regions` VALUES ('032008003', '3', '中和区', '0');
INSERT INTO `regions` VALUES ('032008004', '3', '永和区', '0');
INSERT INTO `regions` VALUES ('032008005', '3', '新庄区', '0');
INSERT INTO `regions` VALUES ('032008006', '3', '新店区', '0');
INSERT INTO `regions` VALUES ('032008007', '3', '树林区', '0');
INSERT INTO `regions` VALUES ('032008008', '3', '莺歌区', '0');
INSERT INTO `regions` VALUES ('032008009', '3', '三峡区', '0');
INSERT INTO `regions` VALUES ('032008010', '3', '淡水区', '0');
INSERT INTO `regions` VALUES ('032008011', '3', '汐止区', '0');
INSERT INTO `regions` VALUES ('032008012', '3', '瑞芳区', '0');
INSERT INTO `regions` VALUES ('032008013', '3', '土城区', '0');
INSERT INTO `regions` VALUES ('032008014', '3', '芦洲区', '0');
INSERT INTO `regions` VALUES ('032008015', '3', '五股区', '0');
INSERT INTO `regions` VALUES ('032008016', '3', '泰山区', '0');
INSERT INTO `regions` VALUES ('032008017', '3', '林口区', '0');
INSERT INTO `regions` VALUES ('032008018', '3', '深坑区', '0');
INSERT INTO `regions` VALUES ('032008019', '3', '石碇区', '0');
INSERT INTO `regions` VALUES ('032008020', '3', '坪林区', '0');
INSERT INTO `regions` VALUES ('032008021', '3', '三芝区', '0');
INSERT INTO `regions` VALUES ('032008022', '3', '石门区', '0');
INSERT INTO `regions` VALUES ('032008023', '3', '八里区', '0');
INSERT INTO `regions` VALUES ('032008024', '3', '平溪区', '0');
INSERT INTO `regions` VALUES ('032008025', '3', '双溪区', '0');
INSERT INTO `regions` VALUES ('032008026', '3', '贡寮区', '0');
INSERT INTO `regions` VALUES ('032008027', '3', '金山区', '0');
INSERT INTO `regions` VALUES ('032008028', '3', '万里区', '0');
INSERT INTO `regions` VALUES ('032008029', '3', '乌来区', '0');
INSERT INTO `regions` VALUES ('032009', '2', '宜兰县', '0');
INSERT INTO `regions` VALUES ('032009001', '3', '宜兰市', '0');
INSERT INTO `regions` VALUES ('032009002', '3', '罗东镇', '0');
INSERT INTO `regions` VALUES ('032009003', '3', '苏澳镇', '0');
INSERT INTO `regions` VALUES ('032009004', '3', '头城镇', '0');
INSERT INTO `regions` VALUES ('032009005', '3', '礁溪乡', '0');
INSERT INTO `regions` VALUES ('032009006', '3', '壮围乡', '0');
INSERT INTO `regions` VALUES ('032009007', '3', '员山乡', '0');
INSERT INTO `regions` VALUES ('032009008', '3', '冬山乡', '0');
INSERT INTO `regions` VALUES ('032009009', '3', '五结乡', '0');
INSERT INTO `regions` VALUES ('032009010', '3', '三星乡', '0');
INSERT INTO `regions` VALUES ('032009011', '3', '大同乡', '0');
INSERT INTO `regions` VALUES ('032009012', '3', '南澳乡', '0');
INSERT INTO `regions` VALUES ('032010', '2', '桃园县', '0');
INSERT INTO `regions` VALUES ('032010001', '3', '桃园市', '0');
INSERT INTO `regions` VALUES ('032010002', '3', '中坜市', '0');
INSERT INTO `regions` VALUES ('032010003', '3', '平镇市', '0');
INSERT INTO `regions` VALUES ('032010004', '3', '八德市', '0');
INSERT INTO `regions` VALUES ('032010005', '3', '杨梅市', '0');
INSERT INTO `regions` VALUES ('032010006', '3', '芦竹市', '0');
INSERT INTO `regions` VALUES ('032010007', '3', '大溪镇', '0');
INSERT INTO `regions` VALUES ('032010008', '3', '大园乡', '0');
INSERT INTO `regions` VALUES ('032010009', '3', '龟山乡', '0');
INSERT INTO `regions` VALUES ('032010010', '3', '龙潭乡', '0');
INSERT INTO `regions` VALUES ('032010011', '3', '新屋乡', '0');
INSERT INTO `regions` VALUES ('032010012', '3', '观音乡', '0');
INSERT INTO `regions` VALUES ('032010013', '3', '复兴乡', '0');
INSERT INTO `regions` VALUES ('032011', '2', '新竹县', '0');
INSERT INTO `regions` VALUES ('032011001', '3', '竹北市', '0');
INSERT INTO `regions` VALUES ('032011002', '3', '竹东镇', '0');
INSERT INTO `regions` VALUES ('032011003', '3', '新埔镇', '0');
INSERT INTO `regions` VALUES ('032011004', '3', '关西镇', '0');
INSERT INTO `regions` VALUES ('032011005', '3', '湖口乡', '0');
INSERT INTO `regions` VALUES ('032011006', '3', '新丰乡', '0');
INSERT INTO `regions` VALUES ('032011007', '3', '芎林乡', '0');
INSERT INTO `regions` VALUES ('032011008', '3', '横山乡', '0');
INSERT INTO `regions` VALUES ('032011009', '3', '北埔乡', '0');
INSERT INTO `regions` VALUES ('032011010', '3', '宝山乡', '0');
INSERT INTO `regions` VALUES ('032011011', '3', '峨眉乡', '0');
INSERT INTO `regions` VALUES ('032011012', '3', '尖石乡', '0');
INSERT INTO `regions` VALUES ('032011013', '3', '五峰乡', '0');
INSERT INTO `regions` VALUES ('032012', '2', '苗栗县', '0');
INSERT INTO `regions` VALUES ('032012001', '3', '苗栗市', '0');
INSERT INTO `regions` VALUES ('032012002', '3', '苑里镇', '0');
INSERT INTO `regions` VALUES ('032012003', '3', '通霄镇', '0');
INSERT INTO `regions` VALUES ('032012004', '3', '竹南镇', '0');
INSERT INTO `regions` VALUES ('032012005', '3', '头份镇', '0');
INSERT INTO `regions` VALUES ('032012006', '3', '后龙镇', '0');
INSERT INTO `regions` VALUES ('032012007', '3', '卓兰镇', '0');
INSERT INTO `regions` VALUES ('032012008', '3', '大湖乡', '0');
INSERT INTO `regions` VALUES ('032012009', '3', '公馆乡', '0');
INSERT INTO `regions` VALUES ('032012010', '3', '铜锣乡', '0');
INSERT INTO `regions` VALUES ('032012011', '3', '南庄乡', '0');
INSERT INTO `regions` VALUES ('032012012', '3', '头屋乡', '0');
INSERT INTO `regions` VALUES ('032012013', '3', '三义乡', '0');
INSERT INTO `regions` VALUES ('032012014', '3', '西湖乡', '0');
INSERT INTO `regions` VALUES ('032012015', '3', '造桥乡', '0');
INSERT INTO `regions` VALUES ('032012016', '3', '三湾乡', '0');
INSERT INTO `regions` VALUES ('032012017', '3', '狮潭乡', '0');
INSERT INTO `regions` VALUES ('032012018', '3', '泰安乡', '0');
INSERT INTO `regions` VALUES ('032013', '2', '彰化县', '0');
INSERT INTO `regions` VALUES ('032013001', '3', '彰化市', '0');
INSERT INTO `regions` VALUES ('032013002', '3', '鹿港镇', '0');
INSERT INTO `regions` VALUES ('032013003', '3', '和美镇', '0');
INSERT INTO `regions` VALUES ('032013004', '3', '线西乡', '0');
INSERT INTO `regions` VALUES ('032013005', '3', '伸港乡', '0');
INSERT INTO `regions` VALUES ('032013006', '3', '福兴乡', '0');
INSERT INTO `regions` VALUES ('032013007', '3', '秀水乡', '0');
INSERT INTO `regions` VALUES ('032013008', '3', '花坛乡', '0');
INSERT INTO `regions` VALUES ('032013009', '3', '芬园乡', '0');
INSERT INTO `regions` VALUES ('032013010', '3', '员林镇', '0');
INSERT INTO `regions` VALUES ('032013011', '3', '溪湖镇', '0');
INSERT INTO `regions` VALUES ('032013012', '3', '田中镇', '0');
INSERT INTO `regions` VALUES ('032013013', '3', '大村乡', '0');
INSERT INTO `regions` VALUES ('032013014', '3', '埔盐乡', '0');
INSERT INTO `regions` VALUES ('032013015', '3', '埔心乡', '0');
INSERT INTO `regions` VALUES ('032013016', '3', '永靖乡', '0');
INSERT INTO `regions` VALUES ('032013017', '3', '社头乡', '0');
INSERT INTO `regions` VALUES ('032013018', '3', '二水乡', '0');
INSERT INTO `regions` VALUES ('032013019', '3', '北斗镇', '0');
INSERT INTO `regions` VALUES ('032013020', '3', '二林镇', '0');
INSERT INTO `regions` VALUES ('032013021', '3', '田尾乡', '0');
INSERT INTO `regions` VALUES ('032013022', '3', '埤头乡', '0');
INSERT INTO `regions` VALUES ('032013023', '3', '芳苑乡', '0');
INSERT INTO `regions` VALUES ('032013024', '3', '大城乡', '0');
INSERT INTO `regions` VALUES ('032013025', '3', '竹塘乡', '0');
INSERT INTO `regions` VALUES ('032013026', '3', '溪州乡', '0');
INSERT INTO `regions` VALUES ('032014', '2', '南投县', '0');
INSERT INTO `regions` VALUES ('032014001', '3', '南投市', '0');
INSERT INTO `regions` VALUES ('032014002', '3', '埔里镇', '0');
INSERT INTO `regions` VALUES ('032014003', '3', '草屯镇', '0');
INSERT INTO `regions` VALUES ('032014004', '3', '竹山镇', '0');
INSERT INTO `regions` VALUES ('032014005', '3', '集集镇', '0');
INSERT INTO `regions` VALUES ('032014006', '3', '名间乡', '0');
INSERT INTO `regions` VALUES ('032014007', '3', '鹿谷乡', '0');
INSERT INTO `regions` VALUES ('032014008', '3', '中寮乡', '0');
INSERT INTO `regions` VALUES ('032014009', '3', '鱼池乡', '0');
INSERT INTO `regions` VALUES ('032014010', '3', '国姓乡', '0');
INSERT INTO `regions` VALUES ('032014011', '3', '水里乡', '0');
INSERT INTO `regions` VALUES ('032014012', '3', '信义乡', '0');
INSERT INTO `regions` VALUES ('032014013', '3', '仁爱乡', '0');
INSERT INTO `regions` VALUES ('032015', '2', '云林县', '0');
INSERT INTO `regions` VALUES ('032015001', '3', '斗六市', '0');
INSERT INTO `regions` VALUES ('032015002', '3', '斗南镇', '0');
INSERT INTO `regions` VALUES ('032015003', '3', '虎尾镇', '0');
INSERT INTO `regions` VALUES ('032015004', '3', '西螺镇', '0');
INSERT INTO `regions` VALUES ('032015005', '3', '土库镇', '0');
INSERT INTO `regions` VALUES ('032015006', '3', '北港镇', '0');
INSERT INTO `regions` VALUES ('032015007', '3', '古坑乡', '0');
INSERT INTO `regions` VALUES ('032015008', '3', '大埤乡', '0');
INSERT INTO `regions` VALUES ('032015009', '3', '莿桐乡', '0');
INSERT INTO `regions` VALUES ('032015010', '3', '林内乡', '0');
INSERT INTO `regions` VALUES ('032015011', '3', '二仑乡', '0');
INSERT INTO `regions` VALUES ('032015012', '3', '仑背乡', '0');
INSERT INTO `regions` VALUES ('032015013', '3', '麦寮乡', '0');
INSERT INTO `regions` VALUES ('032015014', '3', '东势乡', '0');
INSERT INTO `regions` VALUES ('032015015', '3', '褒忠乡', '0');
INSERT INTO `regions` VALUES ('032015016', '3', '台西乡', '0');
INSERT INTO `regions` VALUES ('032015017', '3', '元长乡', '0');
INSERT INTO `regions` VALUES ('032015018', '3', '四湖乡', '0');
INSERT INTO `regions` VALUES ('032015019', '3', '口湖乡', '0');
INSERT INTO `regions` VALUES ('032015020', '3', '水林乡', '0');
INSERT INTO `regions` VALUES ('032016', '2', '嘉义县', '0');
INSERT INTO `regions` VALUES ('032016001', '3', '太保市', '0');
INSERT INTO `regions` VALUES ('032016002', '3', '朴子市', '0');
INSERT INTO `regions` VALUES ('032016003', '3', '布袋镇', '0');
INSERT INTO `regions` VALUES ('032016004', '3', '大林镇', '0');
INSERT INTO `regions` VALUES ('032016005', '3', '民雄乡', '0');
INSERT INTO `regions` VALUES ('032016006', '3', '溪口乡', '0');
INSERT INTO `regions` VALUES ('032016007', '3', '新港乡', '0');
INSERT INTO `regions` VALUES ('032016008', '3', '六脚乡', '0');
INSERT INTO `regions` VALUES ('032016009', '3', '东石乡', '0');
INSERT INTO `regions` VALUES ('032016010', '3', '义竹乡', '0');
INSERT INTO `regions` VALUES ('032016011', '3', '鹿草乡', '0');
INSERT INTO `regions` VALUES ('032016012', '3', '水上乡', '0');
INSERT INTO `regions` VALUES ('032016013', '3', '中埔乡', '0');
INSERT INTO `regions` VALUES ('032016014', '3', '竹崎乡', '0');
INSERT INTO `regions` VALUES ('032016015', '3', '梅山乡', '0');
INSERT INTO `regions` VALUES ('032016016', '3', '番路乡', '0');
INSERT INTO `regions` VALUES ('032016017', '3', '大埔乡', '0');
INSERT INTO `regions` VALUES ('032016018', '3', '阿里山乡', '0');
INSERT INTO `regions` VALUES ('032017', '2', '屏东县', '0');
INSERT INTO `regions` VALUES ('032017001', '3', '屏东市', '0');
INSERT INTO `regions` VALUES ('032017002', '3', '潮州镇', '0');
INSERT INTO `regions` VALUES ('032017003', '3', '东港镇', '0');
INSERT INTO `regions` VALUES ('032017004', '3', '恒春镇', '0');
INSERT INTO `regions` VALUES ('032017005', '3', '万丹乡', '0');
INSERT INTO `regions` VALUES ('032017006', '3', '长治乡', '0');
INSERT INTO `regions` VALUES ('032017007', '3', '麟洛乡', '0');
INSERT INTO `regions` VALUES ('032017008', '3', '九如乡', '0');
INSERT INTO `regions` VALUES ('032017009', '3', '里港乡', '0');
INSERT INTO `regions` VALUES ('032017010', '3', '盐埔乡', '0');
INSERT INTO `regions` VALUES ('032017011', '3', '高树乡', '0');
INSERT INTO `regions` VALUES ('032017012', '3', '万峦乡', '0');
INSERT INTO `regions` VALUES ('032017013', '3', '内埔乡', '0');
INSERT INTO `regions` VALUES ('032017014', '3', '竹田乡', '0');
INSERT INTO `regions` VALUES ('032017015', '3', '新埤乡', '0');
INSERT INTO `regions` VALUES ('032017016', '3', '枋寮乡', '0');
INSERT INTO `regions` VALUES ('032017017', '3', '新园乡', '0');
INSERT INTO `regions` VALUES ('032017018', '3', '崁顶乡', '0');
INSERT INTO `regions` VALUES ('032017019', '3', '林边乡', '0');
INSERT INTO `regions` VALUES ('032017020', '3', '南州乡', '0');
INSERT INTO `regions` VALUES ('032017021', '3', '佳冬乡', '0');
INSERT INTO `regions` VALUES ('032017022', '3', '琉球乡', '0');
INSERT INTO `regions` VALUES ('032017023', '3', '车城乡', '0');
INSERT INTO `regions` VALUES ('032017024', '3', '满州乡', '0');
INSERT INTO `regions` VALUES ('032017025', '3', '枋山乡', '0');
INSERT INTO `regions` VALUES ('032017026', '3', '三地门乡', '0');
INSERT INTO `regions` VALUES ('032017027', '3', '雾台乡', '0');
INSERT INTO `regions` VALUES ('032017028', '3', '玛家乡', '0');
INSERT INTO `regions` VALUES ('032017029', '3', '泰武乡', '0');
INSERT INTO `regions` VALUES ('032017030', '3', '来义乡', '0');
INSERT INTO `regions` VALUES ('032017031', '3', '春日乡', '0');
INSERT INTO `regions` VALUES ('032017032', '3', '狮子乡', '0');
INSERT INTO `regions` VALUES ('032017033', '3', '牡丹乡', '0');
INSERT INTO `regions` VALUES ('032018', '2', '台东县', '0');
INSERT INTO `regions` VALUES ('032018001', '3', '台东市', '0');
INSERT INTO `regions` VALUES ('032018002', '3', '成功镇', '0');
INSERT INTO `regions` VALUES ('032018003', '3', '关山镇', '0');
INSERT INTO `regions` VALUES ('032018004', '3', '卑南乡', '0');
INSERT INTO `regions` VALUES ('032018005', '3', '鹿野乡', '0');
INSERT INTO `regions` VALUES ('032018006', '3', '池上乡', '0');
INSERT INTO `regions` VALUES ('032018007', '3', '东河乡', '0');
INSERT INTO `regions` VALUES ('032018008', '3', '长滨乡', '0');
INSERT INTO `regions` VALUES ('032018009', '3', '太麻里乡', '0');
INSERT INTO `regions` VALUES ('032018010', '3', '大武乡', '0');
INSERT INTO `regions` VALUES ('032018011', '3', '绿岛乡', '0');
INSERT INTO `regions` VALUES ('032018012', '3', '海端乡', '0');
INSERT INTO `regions` VALUES ('032018013', '3', '延平乡', '0');
INSERT INTO `regions` VALUES ('032018014', '3', '金峰乡', '0');
INSERT INTO `regions` VALUES ('032018015', '3', '达仁乡', '0');
INSERT INTO `regions` VALUES ('032018016', '3', '兰屿乡', '0');
INSERT INTO `regions` VALUES ('032019', '2', '花莲县', '0');
INSERT INTO `regions` VALUES ('032019001', '3', '花莲市', '0');
INSERT INTO `regions` VALUES ('032019002', '3', '凤林镇', '0');
INSERT INTO `regions` VALUES ('032019003', '3', '玉里镇', '0');
INSERT INTO `regions` VALUES ('032019004', '3', '新城乡', '0');
INSERT INTO `regions` VALUES ('032019005', '3', '吉安乡', '0');
INSERT INTO `regions` VALUES ('032019006', '3', '寿丰乡', '0');
INSERT INTO `regions` VALUES ('032019007', '3', '光复乡', '0');
INSERT INTO `regions` VALUES ('032019008', '3', '丰滨乡', '0');
INSERT INTO `regions` VALUES ('032019009', '3', '瑞穗乡', '0');
INSERT INTO `regions` VALUES ('032019010', '3', '富里乡', '0');
INSERT INTO `regions` VALUES ('032019011', '3', '秀林乡', '0');
INSERT INTO `regions` VALUES ('032019012', '3', '万荣乡', '0');
INSERT INTO `regions` VALUES ('032019013', '3', '卓溪乡', '0');
INSERT INTO `regions` VALUES ('032020', '2', '澎湖县', '0');
INSERT INTO `regions` VALUES ('032020001', '3', '马公市', '0');
INSERT INTO `regions` VALUES ('032020002', '3', '湖西乡', '0');
INSERT INTO `regions` VALUES ('032020003', '3', '白沙乡', '0');
INSERT INTO `regions` VALUES ('032020004', '3', '西屿乡', '0');
INSERT INTO `regions` VALUES ('032020005', '3', '望安乡', '0');
INSERT INTO `regions` VALUES ('032020006', '3', '七美乡', '0');
INSERT INTO `regions` VALUES ('032021', '2', '金门县', '0');
INSERT INTO `regions` VALUES ('032021001', '3', '金城镇', '0');
INSERT INTO `regions` VALUES ('032021002', '3', '金湖镇', '0');
INSERT INTO `regions` VALUES ('032021003', '3', '金沙镇', '0');
INSERT INTO `regions` VALUES ('032021004', '3', '金宁乡', '0');
INSERT INTO `regions` VALUES ('032021005', '3', '烈屿乡', '0');
INSERT INTO `regions` VALUES ('032021006', '3', '乌丘乡', '0');
INSERT INTO `regions` VALUES ('032022', '2', '连江县', '0');
INSERT INTO `regions` VALUES ('032022001', '3', '南竿乡', '0');
INSERT INTO `regions` VALUES ('032022002', '3', '北竿乡', '0');
INSERT INTO `regions` VALUES ('032022003', '3', '莒光乡', '0');
INSERT INTO `regions` VALUES ('032022004', '3', '东引乡', '0');
INSERT INTO `regions` VALUES ('033', '1', '香港特别行政区', '0');
INSERT INTO `regions` VALUES ('033001', '2', '香港岛', '0');
INSERT INTO `regions` VALUES ('033001001', '3', '中西区', '0');
INSERT INTO `regions` VALUES ('033001002', '3', '湾仔区', '0');
INSERT INTO `regions` VALUES ('033001003', '3', '东区', '0');
INSERT INTO `regions` VALUES ('033001004', '3', '南区', '0');
INSERT INTO `regions` VALUES ('033002', '2', '九龙', '0');
INSERT INTO `regions` VALUES ('033002001', '3', '油尖旺区', '0');
INSERT INTO `regions` VALUES ('033002002', '3', '深水埗区', '0');
INSERT INTO `regions` VALUES ('033002003', '3', '九龙城区', '0');
INSERT INTO `regions` VALUES ('033002004', '3', '黄大仙区', '0');
INSERT INTO `regions` VALUES ('033002005', '3', '观塘区', '0');
INSERT INTO `regions` VALUES ('033003', '2', '新界', '0');
INSERT INTO `regions` VALUES ('033003001', '3', '荃湾区', '0');
INSERT INTO `regions` VALUES ('033003002', '3', '屯门区', '0');
INSERT INTO `regions` VALUES ('033003003', '3', '元朗区', '0');
INSERT INTO `regions` VALUES ('033003004', '3', '北区', '0');
INSERT INTO `regions` VALUES ('033003005', '3', '大埔区', '0');
INSERT INTO `regions` VALUES ('033003006', '3', '西贡区', '0');
INSERT INTO `regions` VALUES ('033003007', '3', '沙田区', '0');
INSERT INTO `regions` VALUES ('033003008', '3', '葵青区', '0');
INSERT INTO `regions` VALUES ('033003009', '3', '离岛区', '0');
INSERT INTO `regions` VALUES ('034', '1', '澳门特别行政区', '0');
INSERT INTO `regions` VALUES ('034001', '2', '澳门半岛', '0');
INSERT INTO `regions` VALUES ('034001001', '3', '花地玛堂区', '0');
INSERT INTO `regions` VALUES ('034001002', '3', '圣安多尼堂区', '0');
INSERT INTO `regions` VALUES ('034001003', '3', '大堂区', '0');
INSERT INTO `regions` VALUES ('034001004', '3', '望德堂区', '0');
INSERT INTO `regions` VALUES ('034001005', '3', '风顺堂区', '0');
INSERT INTO `regions` VALUES ('034002', '2', '氹仔岛', '0');
INSERT INTO `regions` VALUES ('034002001', '3', '嘉模堂区', '0');
INSERT INTO `regions` VALUES ('034003', '2', '路环岛', '0');
INSERT INTO `regions` VALUES ('034003001', '3', '圣方济各堂区', '0');
INSERT INTO `regions` VALUES ('035', '1', '钓鱼岛', '0');

-- ----------------------------
-- Table structure for `sms_record`
-- ----------------------------
DROP TABLE IF EXISTS `sms_record`;
CREATE TABLE `sms_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `send_type` tinyint(3) unsigned NOT NULL COMMENT '发送类型 1:验证码 2:短信',
  `content_type` tinyint(3) unsigned NOT NULL COMMENT '内容类型',
  `receiver` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '接收者',
  `template_id` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '模板ID',
  `template_content` text CHARACTER SET utf8 NOT NULL COMMENT '模板内容,json格式',
  `status` tinyint(3) unsigned NOT NULL COMMENT '状态',
  `remark` text CHARACTER SET utf8 NOT NULL COMMENT '备注信息',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  KEY `receiver` (`receiver`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='短信记录表';

-- ----------------------------
-- Table structure for `store_image`
-- ----------------------------
DROP TABLE IF EXISTS `store_image`;
CREATE TABLE `store_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `url` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '链接地址',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户图库表';

-- ----------------------------
-- Table structure for `store_music`
-- ----------------------------
DROP TABLE IF EXISTS `store_music`;
CREATE TABLE `store_music` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型',
  `title` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '标题',
  `url` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '链接地址',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户音乐表';

-- ----------------------------
-- Table structure for `store_txt`
-- ----------------------------
DROP TABLE IF EXISTS `store_txt`;
CREATE TABLE `store_txt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户文本库表';

-- ----------------------------
-- Table structure for `system_day_statistics`
-- ----------------------------
DROP TABLE IF EXISTS `system_day_statistics`;
CREATE TABLE `system_day_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) unsigned NOT NULL COMMENT '统计类型',
  `num` int(11) NOT NULL COMMENT '数量',
  `created` int(11) unsigned NOT NULL COMMENT '当日0时0分0秒时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tc` (`type`,`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统日统计表';

-- ----------------------------
-- Table structure for `system_money_day_statistics`
-- ----------------------------
DROP TABLE IF EXISTS `system_money_day_statistics`;
CREATE TABLE `system_money_day_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '类型',
  `number` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '资金',
  `type` int(5) NOT NULL DEFAULT '0',
  `day` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统资金日统计';

-- ----------------------------
-- Table structure for `system_money_statistics`
-- ----------------------------
DROP TABLE IF EXISTS `system_money_statistics`;
CREATE TABLE `system_money_statistics` (
  `type` int(4) NOT NULL DEFAULT '0' COMMENT '类型',
  `number` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '资金',
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统资金统计';

-- ----------------------------
-- Table structure for `system_statistics`
-- ----------------------------
DROP TABLE IF EXISTS `system_statistics`;
CREATE TABLE `system_statistics` (
  `type` int(11) unsigned NOT NULL COMMENT '统计类型',
  `num` int(11) NOT NULL COMMENT '数量',
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统总统计表';

-- ----------------------------
-- Table structure for `template_base`
-- ----------------------------
DROP TABLE IF EXISTS `template_base`;
CREATE TABLE `template_base` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '模板标题',
  `case_title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '案例标题',
  `images` text CHARACTER SET utf8 NOT NULL COMMENT '图片',
  `activity` text CHARACTER SET utf8 NOT NULL COMMENT '适用活动描述',
  `use_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用次数',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序,数字越大越在前',
  `path` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT '模板路径',
  `keyword` text CHARACTER SET utf8 NOT NULL COMMENT '关键词',
  `suitable_holiday` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '适用节日',
  `suitable_activity` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '适合活动',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态 -1:删除 0:无效 1:有效',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL COMMENT '修改时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='模板基本信息表';

-- ----------------------------
-- Table structure for `template_classify`
-- ----------------------------
DROP TABLE IF EXISTS `template_classify`;
CREATE TABLE `template_classify` (
  `tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '标识',
  `ptag` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '父分类标识',
  `type` varchar(4) CHARACTER SET utf8 NOT NULL COMMENT '类型标识',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '层级',
  `title` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '标题',
  `image` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '图标',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`tag`),
  KEY `ptag` (`ptag`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='模板分类表';

-- ----------------------------
-- Table structure for `template_classify_relation`
-- ----------------------------
DROP TABLE IF EXISTS `template_classify_relation`;
CREATE TABLE `template_classify_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) unsigned NOT NULL COMMENT '模板ID',
  `ctag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '分类标识',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `ctag` (`ctag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='模板分类关联表';

-- ----------------------------
-- Table structure for `template_classify_types`
-- ----------------------------
DROP TABLE IF EXISTS `template_classify_types`;
CREATE TABLE `template_classify_types` (
  `tag` varchar(4) CHARACTER SET utf8 NOT NULL COMMENT '标识',
  `title` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '标题',
  `image` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '图标',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='模板分类类型表';

-- ----------------------------
-- Table structure for `timed_task`
-- ----------------------------
DROP TABLE IF EXISTS `timed_task`;
CREATE TABLE `timed_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL COMMENT '任务类型',
  `model_type` tinyint(3) unsigned NOT NULL COMMENT '请求模块类型',
  `uri` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '请求uri',
  `content` text CHARACTER SET utf8 NOT NULL COMMENT '请求参数内容,json格式',
  `handle_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始执行的时间戳',
  `handle_tag` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '处理标识',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  KEY `tstatus` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='定时任务表';

-- ----------------------------
-- Table structure for `total_stat_product`
-- ----------------------------
DROP TABLE IF EXISTS `total_stat_product`;
CREATE TABLE `total_stat_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) NOT NULL COMMENT '用户ID',
  `type` tinyint(3) unsigned NOT NULL COMMENT '统计类型',
  `ptype` tinyint(3) unsigned NOT NULL COMMENT '产品类型',
  `num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nunique` (`uid`,`type`,`ptype`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品数量统计总表';

-- ----------------------------
-- Table structure for `user_act_money_history`
-- ----------------------------
DROP TABLE IF EXISTS `user_act_money_history`;
CREATE TABLE `user_act_money_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `data_type` tinyint(5) unsigned NOT NULL COMMENT '数据类型',
  `data_id` varchar(32) NOT NULL DEFAULT '' COMMENT '数据对应ID',
  `data_name` varchar(100) NOT NULL COMMENT '数据名称',
  `data_sn` varchar(100) NOT NULL DEFAULT '' COMMENT '数据单号',
  `total_money` int(11) NOT NULL COMMENT '总金额，单位为分，入账为正数，出账为负数',
  `user_id` varchar(32) NOT NULL COMMENT '用户ID',
  `user_in_money` int(11) NOT NULL COMMENT '用户收入金额，单位为分',
  `user_now_money` bigint(15) NOT NULL DEFAULT '0' COMMENT '用户当前总金额，单位为分',
  `commission` varchar(10) NOT NULL DEFAULT '' COMMENT '提成比例',
  `check_money` int(11) NOT NULL DEFAULT '0' COMMENT '检查金额,单位为分',
  `account_info` text NOT NULL COMMENT '银行账号信息，json格式',
  `remark` text NOT NULL COMMENT '备注',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`),
  KEY `data_id` (`data_id`),
  KEY `data_sn` (`data_sn`),
  KEY `data_type` (`data_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户活动金额变动记录表';

-- ----------------------------
-- Table structure for `user_address`
-- ----------------------------
DROP TABLE IF EXISTS `user_address`;
CREATE TABLE `user_address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `link_man` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '联系人名称',
  `link_tel` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '联系电话',
  `province_tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '省份标识',
  `province_title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '省份名称',
  `city_tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '城市标识',
  `city_title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '城市名称',
  `county_tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '县标识',
  `county_title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '县名称',
  `address` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '详细地址',
  `default_status` tinyint(1) NOT NULL COMMENT '默认状态 0:非默认 1:默认',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户物流地址表';

-- ----------------------------
-- Table structure for `user_base`
-- ----------------------------
DROP TABLE IF EXISTS `user_base`;
CREATE TABLE `user_base` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `pid` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '合伙人用户ID',
  `pstatus` tinyint(3) NOT NULL DEFAULT '0' COMMENT '合伙人状态 0:不是合伙人 1:是合伙人',
  `nickname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '昵称',
  `head_image` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '头像图片',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `phone` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '手机号码',
  `pwd` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '密码',
  `pwd_salt` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '加密盐',
  `login_time` int(11) unsigned NOT NULL COMMENT '最后登录时间',
  `wx_openid` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '微信openid',
  `wx_unid` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '微信unid',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常,0异常',
  `svip_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商家vip状态 0:非vip  1:vip',
  `svip_stime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商家vip开通时间戳',
  `svip_etime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商家vip结束时间戳',
  `provider_tag` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '服务商标识',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL COMMENT '修改时间戳',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `unid` (`wx_unid`),
  KEY `pid` (`pid`),
  KEY `pstatus` (`pstatus`),
  KEY `ctime` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户基本信息表';

-- ----------------------------
-- Table structure for `user_config`
-- ----------------------------
DROP TABLE IF EXISTS `user_config`;
CREATE TABLE `user_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `shop_name` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '商家名称',
  `shop_logo` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '商家logo',
  `shop_man` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '店铺联系人',
  `shop_tel` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '商家联系电话',
  `shop_address` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '商家地址',
  `shop_lng` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '商家地址经度',
  `shop_lat` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '商家地址纬度',
  `shop_desc` text CHARACTER SET utf8 NOT NULL COMMENT '商家介绍',
  `shop_classify` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '店铺分类',
  `wx_qrcode` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '微信二维码图片',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户配置表';

-- ----------------------------
-- Table structure for `user_login_history`
-- ----------------------------
DROP TABLE IF EXISTS `user_login_history`;
CREATE TABLE `user_login_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) NOT NULL COMMENT '用户ID',
  `type` varchar(20) NOT NULL COMMENT '登录类型',
  `ip` varchar(32) NOT NULL DEFAULT '' COMMENT '登录IP',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户登录历史记录表';

-- ----------------------------
-- Table structure for `user_money`
-- ----------------------------
DROP TABLE IF EXISTS `user_money`;
CREATE TABLE `user_money` (
  `uid` varchar(32) NOT NULL COMMENT '用户ID',
  `online_money` bigint(15) unsigned NOT NULL DEFAULT '0' COMMENT '线上交易总金额,单位为分',
  `offline_money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '线下交易总金额,单位为分',
  `income_money` bigint(15) unsigned NOT NULL DEFAULT '0' COMMENT '累计收入金额,单位为分',
  `withdraw_money` bigint(15) NOT NULL DEFAULT '0' COMMENT '可提现金额,单位为分',
  `withdraw_accumulate` bigint(15) NOT NULL DEFAULT '0' COMMENT '累计提现金额,单位为分',
  `transfer_accumulate` bigint(15) NOT NULL DEFAULT '0' COMMENT '累计到账金额,单位为分',
  `freeze_money` bigint(15) NOT NULL DEFAULT '0' COMMENT '冻结金额,单位为分',
  `partner_money_add_up` int(11) NOT NULL DEFAULT '0' COMMENT '合伙人累计分成金额,单位分',
  `withdrawing` int(10) NOT NULL DEFAULT '0' COMMENT '正在提现中的金额,单位分',
  `actonline_money` bigint(15) unsigned NOT NULL DEFAULT '0' COMMENT '活动在线支付金额,单位为分',
  `actincome_money` bigint(15) unsigned NOT NULL DEFAULT '0' COMMENT '活动收入金额,单位为分',
  `actwithdrawed_money` bigint(15) NOT NULL DEFAULT '0' COMMENT '已提现的金额,单位为分',
  `actwithdrawing_money` bigint(15) NOT NULL DEFAULT '0' COMMENT '提现中的金额,单位为分',
  `actwithdraw_money` bigint(15) NOT NULL DEFAULT '0' COMMENT '活动可提现金额,单位为分',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户金额记录表';

-- ----------------------------
-- Table structure for `user_money_history`
-- ----------------------------
DROP TABLE IF EXISTS `user_money_history`;
CREATE TABLE `user_money_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `data_type` tinyint(5) unsigned NOT NULL COMMENT '数据类型',
  `data_id` varchar(32) NOT NULL DEFAULT '' COMMENT '数据对应ID',
  `data_name` varchar(100) NOT NULL COMMENT '数据名称',
  `data_sn` varchar(100) NOT NULL DEFAULT '' COMMENT '数据单号',
  `total_money` int(11) NOT NULL COMMENT '总金额，单位为分，入账为正数，出账为负数',
  `user_id` varchar(32) NOT NULL COMMENT '用户ID',
  `user_in_money` int(11) NOT NULL COMMENT '用户收入金额，单位为分',
  `user_now_money` bigint(15) NOT NULL DEFAULT '0' COMMENT '用户当前总金额，单位为分',
  `check_money` int(11) NOT NULL DEFAULT '0' COMMENT '检查金额,单位为分',
  `account_info` text NOT NULL COMMENT '银行账号信息，json格式',
  `remark` text NOT NULL COMMENT '备注',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`),
  KEY `data_id` (`data_id`),
  KEY `data_sn` (`data_sn`),
  KEY `data_type` (`data_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户金额变动记录表';

-- ----------------------------
-- Table structure for `user_stat`
-- ----------------------------
DROP TABLE IF EXISTS `user_stat`;
CREATE TABLE `user_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型',
  `stat_type` tinyint(3) unsigned NOT NULL COMMENT '统计类型',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `num` bigint(15) NOT NULL DEFAULT '0' COMMENT '统计数量',
  PRIMARY KEY (`id`),
  UNIQUE KEY `typeunique` (`type`,`stat_type`,`uid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户数据统计总表';

-- ----------------------------
-- Table structure for `user_stat_day`
-- ----------------------------
DROP TABLE IF EXISTS `user_stat_day`;
CREATE TABLE `user_stat_day` (
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型',
  `stat_type` tinyint(3) unsigned NOT NULL COMMENT '统计类型',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `num` bigint(15) NOT NULL DEFAULT '0' COMMENT '统计数量',
  `created` int(11) unsigned NOT NULL COMMENT '当日0点0分0秒的时间戳',
  UNIQUE KEY `typeunique` (`type`,`stat_type`,`uid`,`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户数据日统计表';

-- ----------------------------
-- Table structure for `vote_area`
-- ----------------------------
DROP TABLE IF EXISTS `vote_area`;
CREATE TABLE `vote_area` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '投票ID',
  `province_tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '省份地区标识',
  `province_title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '省份地区标题',
  `city_tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '市地区标识',
  `city_title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '市地区标题',
  `county_tag` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '县地区标识',
  `county_title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '县地区标题',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投票地区表';

-- ----------------------------
-- Table structure for `vote_base`
-- ----------------------------
DROP TABLE IF EXISTS `vote_base`;
CREATE TABLE `vote_base` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '产品ID',
  `eid` int(11) unsigned NOT NULL COMMENT '报名ID',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型 1:免费投票 2:礼物投票',
  `num` int(11) unsigned NOT NULL COMMENT '票数',
  `gift_tag` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '礼物标识',
  `gift_title` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '礼物名称',
  `gift_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '礼物数量',
  `gift_unitprice` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '礼物单价,单位为分',
  `gift_money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '礼物总金额,单位为分',
  `gift_commission` decimal(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '礼物抽成比例,单位为百分比',
  `pay_sn` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '支付单号',
  `pay_money` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付金额,单位为分',
  `remark` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '备注',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `eid` (`eid`),
  KEY `uid` (`uid`),
  KEY `gtag` (`gift_tag`),
  KEY `paysn` (`pay_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投票记录表';

-- ----------------------------
-- Table structure for `vote_enter`
-- ----------------------------
DROP TABLE IF EXISTS `vote_enter`;
CREATE TABLE `vote_enter` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `no` int(11) unsigned NOT NULL COMMENT '编号',
  `pid` int(11) unsigned NOT NULL COMMENT '投票ID',
  `type` tinyint(3) unsigned NOT NULL COMMENT '报名类型 1:用户报名 2:商家报名',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `belong_uid` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '所属者用户ID',
  `ename` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '报名人名称',
  `etel` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '报名人联系方式',
  `images` text CHARACTER SET utf8 NOT NULL COMMENT '图片链接',
  `description` mediumtext CHARACTER SET utf8 NOT NULL COMMENT '描述',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `vote_tnum` bigint(15) unsigned NOT NULL DEFAULT '0' COMMENT '投票总数量',
  `vote_nnum` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当前投票数量',
  `vote_gtnum` bigint(15) unsigned NOT NULL DEFAULT '0' COMMENT '礼物总投票数',
  `vote_gnnum` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '礼物当前投票数',
  `vote_itnum` bigint(15) unsigned NOT NULL DEFAULT '0' COMMENT '免费总投票数',
  `vote_innum` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '免费当前投票数',
  `gift_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '礼物数量',
  `hits_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `share_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分享量',
  `unreal_votenum` int(11) NOT NULL DEFAULT '0' COMMENT '虚假投票数量',
  `unreal_giftnum` int(11) NOT NULL DEFAULT '0' COMMENT '虚假礼物数量',
  `unreal_hitsnum` int(11) NOT NULL DEFAULT '0' COMMENT '虚假浏览量',
  `unreal_sharenum` int(11) NOT NULL DEFAULT '0' COMMENT '虚假分享量',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投票报名表';

-- ----------------------------
-- Table structure for `vote_gift`
-- ----------------------------
DROP TABLE IF EXISTS `vote_gift`;
CREATE TABLE `vote_gift` (
  `tag` varchar(8) CHARACTER SET utf8 NOT NULL COMMENT '标识',
  `title` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '名称',
  `image` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '图片链接',
  `price` int(11) unsigned NOT NULL COMMENT '价格,单位为分',
  `vote_num` int(11) unsigned NOT NULL COMMENT '票数',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序,数字越小越在前',
  `use_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用数量',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投票礼物表';

-- ----------------------------
-- Table structure for `vote_join`
-- ----------------------------
DROP TABLE IF EXISTS `vote_join`;
CREATE TABLE `vote_join` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '投票ID',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `gift_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '赠送的礼物总数量',
  `gift_money` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '赠送的礼物总金额,单位为分',
  `vote_tnum` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '总投票数',
  `vote_gnum` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '礼物投票数',
  `vote_inum` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '免费投票数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `puid` (`pid`,`uid`),
  KEY `pid` (`pid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投票参与记录表';

-- ----------------------------
-- Table structure for `vote_prize`
-- ----------------------------
DROP TABLE IF EXISTS `vote_prize`;
CREATE TABLE `vote_prize` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '投票ID',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `type` tinyint(3) unsigned NOT NULL COMMENT '奖品类型 0:鼓励奖 1:名次奖',
  `title` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '奖品标题',
  `image` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '图片链接',
  `vote_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '票数,0为不限制',
  `total_num` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '奖品总份数',
  `exchange_limit` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '兑换限制次数,0为不限制',
  `exchange_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '兑换次数',
  `description` text CHARACTER SET utf8 NOT NULL COMMENT '描述',
  `detail_content` mediumtext CHARACTER SET utf8mb4 COMMENT '详情内容',
  `detail_json` mediumtext CHARACTER SET utf8mb4 COMMENT '详情json',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投票奖品表';

-- ----------------------------
-- Table structure for `vote_prize_exchange`
-- ----------------------------
DROP TABLE IF EXISTS `vote_prize_exchange`;
CREATE TABLE `vote_prize_exchange` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `pid` int(11) NOT NULL COMMENT '产品ID',
  `enter_id` int(11) unsigned NOT NULL COMMENT '报名ID',
  `enter_name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '报名名称',
  `enter_tel` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '报名电话',
  `prize_id` int(11) unsigned NOT NULL COMMENT '奖品ID',
  `prize_title` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '奖品名称',
  `prize_image` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '奖品图片',
  `prize_num` int(11) unsigned NOT NULL COMMENT '奖品数量',
  `expire_time` int(11) unsigned NOT NULL COMMENT '过期时间戳',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `pid` (`pid`),
  KEY `prizeid` (`prize_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='投票奖品兑换记录表';

-- ----------------------------
-- Table structure for `withdraw_act_base`
-- ----------------------------
DROP TABLE IF EXISTS `withdraw_act_base`;
CREATE TABLE `withdraw_act_base` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '提现单号',
  `way` tinyint(3) unsigned NOT NULL COMMENT '提现方式 1:企业付款 2:银行卡 3:支付宝转账',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `money` int(11) unsigned NOT NULL COMMENT '提现金额,单位为分',
  `user_money` int(11) unsigned NOT NULL COMMENT '用户提现到帐金额,单位为分',
  `way_name` varchar(200) CHARACTER SET utf8 NOT NULL COMMENT '提现方式名称,如果是银行卡则填写银行名称',
  `way_account` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '提现方式账号',
  `account_name` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '提现到帐账户名称',
  `qrcode` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '二维码图片',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `remark` text CHARACTER SET utf8 NOT NULL COMMENT '备注信息',
  `audit_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '审核时间',
  `user_money_info` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户资金信息JSON',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL COMMENT '修改时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`),
  KEY `uid` (`uid`),
  KEY `ws` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='活动提现基础信息表';

-- ----------------------------
-- Table structure for `withdraw_act_history`
-- ----------------------------
DROP TABLE IF EXISTS `withdraw_act_history`;
CREATE TABLE `withdraw_act_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wsn` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '提现单号',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '操作者用户ID',
  `option_title` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '操作标题',
  `option_type` tinyint(3) unsigned NOT NULL COMMENT '操作类型',
  `option_content` text CHARACTER SET utf8 NOT NULL COMMENT '操作内容',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  KEY `wid` (`wsn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='提现操作历史记录表';

-- ----------------------------
-- Table structure for `withdraw_base`
-- ----------------------------
DROP TABLE IF EXISTS `withdraw_base`;
CREATE TABLE `withdraw_base` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '提现单号',
  `way` tinyint(3) unsigned NOT NULL COMMENT '提现方式 1:企业付款 2:银行卡 3:支付宝转账',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `partner_id` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '合伙人用户ID',
  `total_commission` decimal(5,2) unsigned NOT NULL DEFAULT '2.00' COMMENT '系统提成比例,单位为%',
  `partner_commission` decimal(5,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '合伙人提成比例,单位为%',
  `money` int(11) unsigned NOT NULL COMMENT '提现金额,单位为分',
  `user_money` int(11) unsigned NOT NULL COMMENT '用户提现到帐金额,单位为分',
  `system_money` int(11) unsigned NOT NULL COMMENT '平台到帐金额,单位为分',
  `partner_money` int(11) unsigned NOT NULL COMMENT '合伙人入账金额',
  `way_name` varchar(200) CHARACTER SET utf8 NOT NULL COMMENT '提现方式名称,如果是银行卡则填写银行名称',
  `way_account` varchar(150) CHARACTER SET utf8 NOT NULL COMMENT '提现方式账号',
  `account_name` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '提现到帐账户名称',
  `qrcode` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '二维码图片',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `remark` text CHARACTER SET utf8 NOT NULL COMMENT '备注信息',
  `withdraw_accumulate` int(11) NOT NULL COMMENT '累计提现金额,单位为分,不包含本次',
  `transfer_accumulate` int(11) NOT NULL COMMENT '累计到账金额,单位为分,不包含本次',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated` int(11) unsigned NOT NULL COMMENT '修改时间戳',
  `audit_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '审核时间',
  `user_money_info` text COLLATE utf8mb4_unicode_ci COMMENT '用户资金信息JSON',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`),
  KEY `uid` (`uid`),
  KEY `ws` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='提现基础信息表';

-- ----------------------------
-- Table structure for `withdraw_history`
-- ----------------------------
DROP TABLE IF EXISTS `withdraw_history`;
CREATE TABLE `withdraw_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wsn` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '提现单号',
  `uid` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '操作者用户ID',
  `option_title` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '操作标题',
  `option_type` tinyint(3) unsigned NOT NULL COMMENT '操作类型',
  `option_content` text CHARACTER SET utf8 NOT NULL COMMENT '操作内容',
  `created` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  KEY `wid` (`wsn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='提现操作历史记录表';
