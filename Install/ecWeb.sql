SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- 
--  Database: `db_ecWeb`
--	author:GT 
--	time:2017/05/17
--

-- 
--  表的结构 `tb_buyer` 买家表
-- 
CREATE TABLE IF NOT EXISTS `tb_buyer`(
	`id` INTEGER NULL COMMENT "买家用户唯一标识",
	`name` CHAR(50) Not NULL COMMENT "买家实名",
	`nickname` VARCHAR(200) NULL COMMENT "昵称",
	`tel` CHAR(11) NULL COMMENT "买家联系方式",
	`mail` CHAR(20) NOT NULL COMMENT "买家邮箱，注册登陆用？",
	`pwd` VARCHAR(255) NOT NULL COMMENT "登陆密码,md5加密",
	`address` VARCHAR(255) NOT NULL COMMENT "地址",
	`c_time` TIMESTAMP  NOT NULL COMMENT "买家创建时间"
)DEFAULT CHARSET=UTF8 COMMENT="买家表";

--
-- 表的结构`tb_seller` 卖家表
--
CREATE TABLE IF NOT EXISTS `tb_seller` (
	`id` INTEGER NULL COMMENT "卖家唯一标识",
	`buyer_id` INTEGER NOT NULL COMMENT "买家身份id,外键",
	`pwd` VARCHAR(255) NOT NULL COMMENT "登陆密码,md5加密",
	`level` SMALLINT NULL DEFAULT 1 COMMENT "卖家级别",
	`sell_count` INTEGER NULL DEFAULT 0 COMMENT "交易次数",
	`c_time` TIMESTAMP NOT NULL COMMENT "卖家创建时间"
)DEFAULT CHARSET=UTF8 COMMENT="卖家表";

-- 
-- 表的结构 `tb_store` 店铺表
--
CREATE TABLE IF NOT EXISTS `tb_store` (
	`id` INTEGER NULL COMMENT "店铺唯一标识",
	`seller_id` INTEGER NOT NULL COMMENT "店铺主id,外键",
	`name` VARCHAR(255) NOT NULL COMMENT '店名',
	`sell_count` INTEGER NULL DEFAULT 0 COMMENT '店铺总销量',
	`total` INTEGER NULL DEFAULT 0 COMMENT '店铺总交易额',
	`c_time` TIMESTAMP NOT NULL COMMENT "店铺创建时间"
)DEFAULT CHARSET=UTF8 COMMENT="店铺表";

--
-- 表的结构`tb_store_terms` 店铺内商品分类
--
CREATE TABLE IF NOT EXISTS `tb_store_terms`(
	`id` BIGINT NULL COMMENT "分类唯一标识",
	`name` VARCHAR(100) NOT NULL COMMENT "分类名称",
	`parent_id` BIGINT NULL DEFAULT 0 COMMENT "上级分类id,默认0表示一级分类",
	`store_id` INTEGER NOT NULL COMMENT "店铺id,外键",
	`icon` varchar(255) NOT NULL COMMENT "类别图标",
	`status` TINYINT NULL DEFAULT 1 COMMENT "启用状态,1:启用分类,0:停用分类",
	`c_time` TIMESTAMP NOT NULL COMMENT "分类创建时间"
)DEFAULT CHARSET=UTF8 COMMENT="店铺内商品分类表";

--
-- 表的结构`tb_goods` 商品表
--
CREATE TABLE IF NOT EXISTS `tb_goods`(
	`id` BIGINT NULL COMMENT "商品唯一标识",
	`store_id` INTEGER NOT NULL COMMENT "商店id,外键",
	`name` VARCHAR(255) NOT NULL COMMENT "商品名称",
	`description` text NULL COMMENT "商品描述",
	`price` FLOAT NOT NULL COMMENT "商品单价",
	`cover` text NOT NULL COMMENT "商品封面",
	`smeta` text NULL COMMENT "资源等,使用json格式存储",
	`sell_count` INTEGER NULL DEFAULT 0 COMMENT "总销量",
	`stock` INTEGER NOT NULL COMMENT "库存",
	`term_id` BIGINT NOT NULL COMMENT "商品分类",
	`c_time` TIMESTAMP NOT NULL COMMENT "创建商品时间"
)DEFAULT CHARSET=UTF8 COMMENT="商品表";

--
-- 表的结构 `tb_order` 订单表
--
CREATE TABLE IF NOT EXISTS `tb_order`(
	`id` BIGINT NULL COMMENT "订单唯一标识",
	`buyer_id` INTEGER NOT NULL COMMENT "买家id,外键",
	`goods_id` BIGINT NOT NULL COMMENT "商品id,外键",
	`store_id` INTEGER NOT NULL COMMENT "店铺id,外键,可有可恶!!!",
	`num` INTEGER NOT NULL COMMENT "购买数量",
	`total` FLOAT NOT NULL COMMENT "交易总额",
	`message` TEXT NULL COMMENT "买家留言",
	`status` SMALLINT NOT NULL COMMENT "订单状态,0:发货,1:寄送中,2:以签收",
	`status_message` TEXT NULL COMMENT "订单状态消息,json格式数据",
	`c_time` TIMESTAMP NOT NULL COMMENT "创建订单时间"
)DEFAULT CHARSET=UTF8 COMMENT="订单表";

--
-- 表的结构 `tb_cart` 购物车表
--
CREATE TABLE IF NOT EXISTS `tb_cart`(
	`id` BIGINT NULL COMMENT "唯一标识",
	`goods_id` BIGINT NOT NULL COMMENT "商品id,外键",
	`num` INTEGER NOT NULL COMMENT "数量",
	`buyer_id` INTEGER NOT NULL COMMENT "买家id,外键",
	`c_time` TIMESTAMP NOT NULL COMMENT "创建时间"
)DEFAULT CHARSET=UTF8 COMMENT="购物车表";

--
-- 表的结构 `tb_admin` 管理员表
--
CREATE TABLE IF NOT EXISTS `tb_admin`(
	`id` INTEGER NULL COMMENT "管理员唯一标识",
	`name` CHAR(50) NOT NULL COMMENT "管理员实名",
	`tel` CHAR(11) NULL COMMENT "管理员联系电话",
	`mail` VARCHAR(20) NOT NULL COMMENT "管理员登陆注册邮箱",
	`pwd` VARCHAR(255) NOT NULL COMMENT "管理员密码,md5加密",
	`c_time` TIMESTAMP NOT NULL COMMENT "管理员创建时间"
)DEFAULT CHARSET=UTF8 COMMENT="管理员表";

--
-- 表的结构 `tb_site_terms` 商城商品分类表
--
CREATE TABLE IF NOT EXISTS `tb_site_terms`(
	`id` INTEGER NULL COMMENT "商城商品分类id",
	`name` VARCHAR(100) NOT NULL COMMENT "分类名称",
	`parent_id` BIGINT NULL DEFAULT 0 COMMENT "上级分类id,默认0表示一级分类",
	`icon` varchar(255) NOT NULL COMMENT "类别图标",
	`status` TINYINT NULL DEFAULT 1 COMMENT "启用状态,1:启用分类,0:停用分类",
	`c_time` TIMESTAMP NOT NULL COMMENT "分类创建时间"
)DEFAULT CHARSET=UTF8 COMMENT="商城商品分类表";

--
-- 表的结构 `tb_slides` 首页轮播图表
--
CREATE TABLE IF NOT EXISTS `tb_slides`(
	`id` INTEGER NULL COMMENT "轮播图id",
	`url` text NOT NULL COMMENT "轮播图url",
	`msg` text NULL COMMENT "附加消息",
	`status` TINYINT NULL DEFAULT 1 COMMENT "启用状态,1:启用,0:停用",
	`order` int(2) NOT NULL COMMENT "轮播图显示顺序",
	`c_time` TIMESTAMP NOT NULL COMMENT "创建时间"
)DEFAULT CHARSET=UTF8 COMMENT="首页轮播图表";

--
-- 表的结构 `tb_seller_activate` 卖家激活表
--
CREATE TABLE IF NOT EXISTS `tb_seller_activate`(
	`id` INTEGER NULL COMMENT '激活id',
	`mail` CHAR(20) NOT NULL COMMENT "买家邮箱,注册登陆用？",
	`pwd` VARCHAR(255) NOT NULL COMMENT "登陆密码,md5加密",
	`status` TINYINT DEFAULT 0 COMMENT '验证状态,0:待验证,1:已经验证',
	`code` CHAR(30) UNIQUE NOT NULL COMMENT '验证code',
	`time` TIMESTAMP NOT NULL COMMENT '最后修改时间'
);

--
-- 表的结构 `tb_buyer_activate` 买家激活表
--
CREATE TABLE IF NOT EXISTS `tb_buyer_activate`(
	`id` INTEGER NULL COMMENT '激活id',
	`mail` CHAR(20) NOT NULL COMMENT "买家邮箱,注册登陆用？",
	`pwd` VARCHAR(255) NOT NULL COMMENT "登陆密码,md5加密",
	`name` CHAR(20) NOT NULL COMMENT "用户真实名字",
	`address` VARCHAR(255) NOT NULL COMMENT "地址",
	`status` TINYINT DEFAULT 0 COMMENT '验证状态,0:待验证,1:已经验证',
	`code` CHAR(30) UNIQUE NOT NULL COMMENT '验证code',
	`time` TIMESTAMP NOT NULL COMMENT '最后修改时间'
);

--
-- INDEX FOR `tb_buyer`
--
ALTER TABLE `tb_buyer` 
	ADD PRIMARY KEY (`id`),
	ADD UNIQUE KEY (`mail`);

--
-- INDEX FOR `tb_seller`
--
ALTER TABLE `tb_seller` 
	ADD PRIMARY KEY (`id`);

--
-- INDEX FOR `tb_store`
--
ALTER TABLE `tb_store` 
	ADD PRIMARY KEY (`id`);

--
-- INDEX FOR `tb_store_terms`
--
ALTER TABLE `tb_store_terms` 
	ADD PRIMARY KEY (`id`);

--
-- INDEX FOR `tb_goods`
--
ALTER TABLE `tb_goods` 
	ADD PRIMARY KEY (`id`);

--
-- INDEX FOR `tb_order`
--
ALTER TABLE `tb_order` 
	ADD PRIMARY KEY (`id`);

--
-- INDEX FOR `tb_cart`
--
ALTER TABLE `tb_cart` 
	ADD PRIMARY KEY (`id`);

--
-- INDEX FOR `tb_admin`
--
ALTER TABLE `tb_admin` 
	ADD PRIMARY KEY (`id`);

--
-- INDEX FOR `tb_site_terms`
--
ALTER TABLE `tb_site_terms` 
	ADD PRIMARY KEY (`id`);

--
-- INDEX FOR `tb_slides`
--
ALTER TABLE `tb_slides` 
	ADD PRIMARY KEY (`id`);
	
--
-- INDEX FOR `tb_seller_activate`
--
ALTER TABLE `tb_seller_activate` 
	ADD PRIMARY KEY (`id`);
	
--
-- INDEX FOR `tb_buyer_activate`
--
ALTER TABLE `tb_buyer_activate` 
	ADD PRIMARY KEY (`id`);
	
--
-- AUTO_INCREMENT for table `tb_buyer`
--
ALTER TABLE `tb_buyer` 
	MODIFY `id` INTEGER AUTO_INCREMENT,AUTO_INCREMENT=1;
	
--
-- AUTO_INCREMENT for table `tb_seller`
--
ALTER TABLE `tb_seller` 
	MODIFY `id` INTEGER AUTO_INCREMENT,AUTO_INCREMENT=1;
	
--
-- AUTO_INCREMENT for table `tb_store`
--
ALTER TABLE `tb_store` 
	MODIFY `id` INTEGER AUTO_INCREMENT,AUTO_INCREMENT=1;
	
--
-- AUTO_INCREMENT for table `tb_store_terms`
--
ALTER TABLE `tb_store_terms` 
	MODIFY `id` BIGINT AUTO_INCREMENT,AUTO_INCREMENT=1;
	
--
-- AUTO_INCREMENT for table `tb_goods`
--
ALTER TABLE `tb_goods` 
	MODIFY `id` BIGINT AUTO_INCREMENT,AUTO_INCREMENT=1;
	
--
-- AUTO_INCREMENT for table `tb_order`
--
ALTER TABLE `tb_order` 
	MODIFY `id` BIGINT AUTO_INCREMENT,AUTO_INCREMENT=1;
	
--
-- AUTO_INCREMENT for table `tb_cart`
--
ALTER TABLE `tb_cart` 
	MODIFY `id` BIGINT AUTO_INCREMENT,AUTO_INCREMENT=1;
	
--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin` 
	MODIFY `id` INTEGER AUTO_INCREMENT,AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tb_site_terms`
--
ALTER TABLE `tb_site_terms` 
	MODIFY `id` INTEGER AUTO_INCREMENT,AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tb_slides`
--
ALTER TABLE `tb_slides` 
	MODIFY `id` INTEGER AUTO_INCREMENT,AUTO_INCREMENT=1;
	
--
-- AUTO_INCREMENT for table `tb_seller_activate`
--
ALTER TABLE `tb_seller_activate` 
	MODIFY `id` INTEGER AUTO_INCREMENT,AUTO_INCREMENT=1;
	
--
-- AUTO_INCREMENT for table `tb_buyer_activate`
--
ALTER TABLE `tb_buyer_activate` 
	MODIFY `id` INTEGER AUTO_INCREMENT,AUTO_INCREMENT=1;
	
--
-- FOREIGN KEY for table `tb_seller`
--
ALTER TABLE `tb_seller` 
	ADD FOREIGN KEY (`buyer_id`) REFERENCES `tb_buyer`(`id`) 
	ON DELETE CASCADE ON UPDATE CASCADE;

--
-- FOREIGN KEY for table `tb_store`
--
ALTER TABLE `tb_store` 
	ADD FOREIGN KEY (`seller_id`) REFERENCES `tb_seller`(`id`) 
	ON DELETE CASCADE ON UPDATE CASCADE;
	
--
-- FOREIGN KEY for table `tb_store_terms`
--
ALTER TABLE `tb_store_terms` 
	ADD FOREIGN KEY (`store_id`) REFERENCES `tb_store`(`id`) 
	ON DELETE CASCADE ON UPDATE CASCADE;
	
--
-- FOREIGN KEY for table `tb_goods`
--
ALTER TABLE `tb_goods` 
	ADD FOREIGN KEY (`store_id`) REFERENCES `tb_store`(`id`) 
	ON DELETE CASCADE ON UPDATE CASCADE;
	
--
-- FOREIGN KEY for table `tb_order`
--
ALTER TABLE `tb_order` 
	ADD FOREIGN KEY (`goods_id`) REFERENCES `tb_goods`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD FOREIGN KEY (`buyer_id`) REFERENCES `tb_buyer`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD FOREIGN KEY (`store_id`) REFERENCES `tb_store`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- FOREIGN KEY for table `tb_cart`
--
ALTER TABLE `tb_cart` 
	ADD FOREIGN KEY (`goods_id`) REFERENCES `tb_goods`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD FOREIGN KEY (`buyer_id`) REFERENCES `tb_buyer`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
	
--
-- DELETE ORIGINAL DELETION TASK
--
DROP EVENT IF EXISTS `e_delete_seller_activate`;

--
-- CREATE NEW SCHEDULE TASK  DELETE DEPRECATE ACTIVATE CODES IN `tb_seller_activate`
--
CREATE 	EVENT `e_delete_seller_activate` 
	ON SCHEDULE 
	EVERY 5 SECOND 
	DO 
	DELETE FROM `tb_seller_activate` WHERE `time` < DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 30 MINUTE);
	
--
-- DELETE ORIGINAL DELETION TASK
--
DROP EVENT IF EXISTS `e_delete_buyer_activate`;

--
-- CREATE NEW SCHEDULE TASK  DELETE DEPRECATE ACTIVATE CODES IN `tb_buyer_activate`
--
CREATE 	EVENT `e_delete_buyer_activate` 
	ON SCHEDULE 
	EVERY 5 SECOND 
	DO 
	DELETE FROM `tb_buyer_activate` WHERE `time` < DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 30 MINUTE);
	
--
-- INSERT DATA FOR TEST
--
INSERT INTO `tb_admin`(`name`,`tel`,`mail`,`pwd`,`c_time`) VALUE 
	('admin','17862701356','GT_GameEmail@163.com','e10adc3949ba59abbe56e057f20f883e','2017-05-27 16:30:00');
	
INSERT INTO `tb_buyer`(`name`,`nickname`,`tel`,`mail`,`pwd`,`address`,`c_time`) VALUE 
	('GT','HTML_GT_MK','17862701356','GT_GameEmail@163.com','e10adc3949ba59abbe56e057f20f883e',
		'山东威海哈工大(威海)','2017-05-27 16:30:00');
	
INSERT INTO `tb_seller`(`buyer_id`,`pwd`,`c_time`) VALUE 
	('1','e10adc3949ba59abbe56e057f20f883e','2017-05-27 16:30:00');

INSERT INTO `tb_store`(`seller_id`,`name`,`c_time`) VALUE
	('1','好评...','2017-05-27 16:30:00');
	
INSERT INTO `tb_store_terms`(`name`,`parent_id`,`icon`,`store_id`,`c_time`) VALUE 
	('家电','0','appliances.svg','1','2017-05-27 16:30:00'),
	('数码','0','digital.svg','1','2017-05-27 16:30:00'),
	('图书','0','book.svg','1','2017-05-27 16:30:00'),
	('食品','0','food.svg','1','2017-05-27 16:30:00'),
	('服装','0','clothes.svg','1','2017-05-27 16:30:00');
	
INSERT INTO `tb_goods` (`id`,`store_id`,`name`,`description`,`price`,`cover`,`smeta`,`sell_count`,`stock`,`term_id`,`c_time`) VALUE
	('1', '1', '茶具 贴身衣物洗涤颗粒', '茶具 贴身衣物洗涤颗粒', '28', 'good1.jpg', null, '99', '22' , '1', '2017-05-31 22:48:42'),
	('2', '1', 'Nums 超薄智能键盘', 'Nums 超薄智能键盘', '129', 'good2.jpg', null, '44', '22' , '1', '2017-05-31 22:48:45'),
	('3', '1', '手写板', '手写板', '248', 'good3.jpg', null, '54', '22' , '1', '2017-05-31 22:48:47'),
	('4', '1', '磁悬浮太阳能电机马达', '磁悬浮太阳能电机马达', '178', 'good4.jpg', null, '22', '22' , '1', '2017-05-31 22:48:49'),
	('5', '1', '星战系列手机盒', '星战系列手机盒', '49', 'good5.jpg', null, '85', '22' , '2', '2017-05-31 22:48:51'),
	('6', '1', '黑爵GTX 电竞机械鼠标', '黑爵GTX 电竞机械鼠标', '99', 'good6.jpg', null, '755', '22' , '3', '2017-05-31 22:48:53'),
	('7', '1', '黑爵AK35i 机械键盘', '黑爵AK35i 机械键盘', '199', 'good7.jpg', null, '63', '22' , '3', '2017-05-31 22:48:55'),
	('8', '1', 'Rokid 智能语音机器人', 'Rokid 智能语音机器人', '1399', 'good8.jpg', null, '123', '22' , '3', '2017-05-31 22:48:57'),
	('9', '1', 'Oracleen熊本熊电动牙刷', 'Oracleen熊本熊电动牙刷', '399', 'good9.jpg', null, '86', '22' , '4', '2017-05-31 22:49:00');
	
INSERT INTO `tb_order`(`buyer_id`,`goods_id`,`store_id`,`num`,`total`,`message`,`status`,`c_time`) VALUE 
	('1','1','1','1','28','不要茶具','1','2017-05-27 12:00:00'),
	('1','2','1','1','28','要帅小哥哥配送','1','2017-05-27 12:00:00'),
	('1','3','1','1','28','','2','2017-05-27 12:00:00'),
	('1','1','1','1','28','','3','2017-05-27 12:00:00'),
	('1','1','1','1','28','','4','2017-05-27 12:00:00');
	
INSERT INTO `tb_site_terms` (`id`,`name`,`parent_id`,`icon`,`status`,`c_time`) VALUE 
	('1', '数码', '0' ,'digital.svg', '1', '2017-05-31 19:38:30'),
	('2', '家电', '0' ,'appliances.svg', '1', '2017-05-31 19:38:56'),
	('3', '图书', '0' ,'book.svg', '1', '2017-05-31 19:39:02'),
	('4', '食品', '0' ,'food.svg', '1', '2017-05-31 19:39:10'),
	('5', '服装', '0' ,'clothes.svg', '1', '2017-05-31 19:39:18'),
	('6', '医药', '0' ,'medical.svg', '1', '2017-05-31 19:39:24'),
	('7', '家具', '0' ,'furniture.svg', '1', '2017-05-31 19:39:30'),
	('8', '美妆', '0' ,'makeup.svg', '1', '2017-05-31 19:39:37');

INSERT INTO `tb_slides` VALUES ('1', '1.jpg', null, '1', '1', '2017-05-31 21:33:38');
INSERT INTO `tb_slides` VALUES ('2', '2.jpg', null, '2', '1', '2017-05-31 21:33:39');
INSERT INTO `tb_slides` VALUES ('3', '3.jpg', null, '3', '1', '2017-05-31 21:33:40');
INSERT INTO `tb_slides` VALUES ('4', '4.jpg', null, '4', '1', '2017-05-31 21:34:53');
INSERT INTO `tb_slides` VALUES ('5', '5.jpg', null, '5', '1', '2017-05-31 21:35:04');

