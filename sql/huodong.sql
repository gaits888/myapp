-- =============================================
-- 活动表 fl_huodong
-- 用于平台线上/线下活动展示（首页/活动列表/详情）
-- 导入方式：在 phpMyAdmin 或命令行执行本文件
-- =============================================

CREATE TABLE IF NOT EXISTS `fl_huodong` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '活动ID',
  `title` varchar(120) NOT NULL DEFAULT '' COMMENT '活动标题',
  `subtitle` varchar(200) NOT NULL DEFAULT '' COMMENT '副标题/一句话简介',
  `cover` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图URL',
  `city` varchar(50) NOT NULL DEFAULT '' COMMENT '活动城市',
  `address` varchar(200) NOT NULL DEFAULT '' COMMENT '活动地点',
  `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '活动开始时间(时间戳)',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '活动结束时间(时间戳)',
  `join_count` int(11) NOT NULL DEFAULT 0 COMMENT '报名/参与人数',
  `max_count` int(11) NOT NULL DEFAULT 0 COMMENT '人数上限(0为不限)',
  `content` text COMMENT '活动详情(支持HTML)',
  `tag` varchar(60) NOT NULL DEFAULT '' COMMENT '标签,如:单身派对/桌游/徒步',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:0下架 1上架',
  `is_top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否置顶:0否 1是',
  `views` int(11) NOT NULL DEFAULT 0 COMMENT '浏览量',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序权重,越大越靠前',
  `addtime` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间(时间戳)',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_city` (`city`),
  KEY `idx_sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='平台活动表';

-- =============================================
-- 活动报名记录表 fl_huodong_join
-- =============================================
CREATE TABLE IF NOT EXISTS `fl_huodong_join` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hd_id` int(11) NOT NULL DEFAULT 0 COMMENT '活动ID',
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '报名用户ID',
  `uname` varchar(60) NOT NULL DEFAULT '' COMMENT '报名昵称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  `addtime` int(11) NOT NULL DEFAULT 0 COMMENT '报名时间(时间戳)',
  PRIMARY KEY (`id`),
  KEY `idx_hd` (`hd_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活动报名记录表';

-- =============================================
-- 示例数据（可删除）
-- =============================================
INSERT INTO `fl_huodong` (`title`,`subtitle`,`cover`,`city`,`address`,`start_time`,`end_time`,`join_count`,`max_count`,`content`,`tag`,`status`,`is_top`,`sort`,`addtime`) VALUES
('周末单身派对·遇见对的人','优质单身男女线下交流，自由配对','/images/hd_demo1.jpg','北京','朝阳区三里屯某酒吧',UNIX_TIMESTAMP()+86400*3,UNIX_TIMESTAMP()+86400*3+10800,28,50,'<p>本次活动面向城市优质单身青年，现场设有破冰游戏、自由交流、心动配对等环节。</p><p>报名即送精美小礼品，期待你的到来！</p>','单身派对',1,1,100,UNIX_TIMESTAMP()),
('city walk 城市漫步交友局','边走边聊，轻松认识新朋友','/images/hd_demo2.jpg','上海','徐汇区武康路',UNIX_TIMESTAMP()+86400*5,UNIX_TIMESTAMP()+86400*5+7200,16,30,'<p>沿着最美马路漫步，在轻松的氛围中结识志同道合的朋友。</p>','户外徒步',1,0,90,UNIX_TIMESTAMP()),
('剧本杀拼场·沉浸式破冰','一起推理，一起心动','/images/hd_demo3.jpg','深圳','南山区某剧本杀店',UNIX_TIMESTAMP()+86400*2,UNIX_TIMESTAMP()+86400*2+14400,12,18,'<p>经典情感本，男女搭配，在剧情中自然拉近距离。</p>','剧本杀',1,0,80,UNIX_TIMESTAMP());
