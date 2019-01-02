<?php

use Phinx\Migration\AbstractMigration;

class DbInit extends AbstractMigration
{
    protected $tablePrefix = '';

    /**
     * Migrate Up.
     */
    public function up()
    {
        if ($this->getAdapter()->hasOption('table_prefix')) {
            $this->tablePrefix = $this->getAdapter()->getOption('table_prefix');
        }

        $this->execute(
            <<<EOT

-- ----------------------------
-- Table structure for {$this->tablePrefix}adminers
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}adminers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_account` varchar(50) NOT NULL COMMENT '用户名',
  `admin_password` varchar(255) NOT NULL COMMENT '密码',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `login_at` timestamp NULL DEFAULT NULL,
  `login_ip` varchar(255) DEFAULT NULL,
  `status` tinyint(255) DEFAULT '1',
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}adminers_roles
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}adminers_roles` (
  `role_id` int(11) NOT NULL,
  `adminer_id` int(11) NOT NULL,
  KEY `admin_role_users_role_id_user_id_index` (`role_id`,`adminer_id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}advertisings
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}advertisings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `block` varchar(255) DEFAULT NULL,
  `target` varchar(255) DEFAULT '_blank',
  `sort` tinyint(255) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(255) DEFAULT '1',
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}advertisings_blocks
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}advertisings_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `block` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}articles
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT '0',
  `category_parent_path` varchar(255) DEFAULT '0,',
  `type` tinyint(5) DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `summary` tinytext,
  `image` varchar(255) DEFAULT NULL,
  `content` text,
  `sort` int(255) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(255) DEFAULT '1',
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}articles_tags
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}articles_tags` (
  `tag_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  PRIMARY KEY (`tag_id`,`article_id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}categorys
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}categorys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `parent_path` varchar(255) DEFAULT '0,',
  `type` varchar(255) DEFAULT 'list',
  `name` varchar(50) NOT NULL,
  `content` text,
  `sort` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}configs
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `model` varchar(255) DEFAULT 'text',
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT 'site',
  `desc` varchar(255) DEFAULT NULL,
  `system` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Records of tadmin_configs
-- ----------------------------
INSERT INTO `{$this->tablePrefix}configs` VALUES ('1', '站点名称', 'text', 'title', 'Tadmin - 基于ThinkPHP5.1+和AmazeUI的快速后台开发框架', 'site', '站点名称', '1', '2019-01-01 11:58:26', '2019-01-01 14:35:10');
INSERT INTO `{$this->tablePrefix}configs` VALUES ('2', '关键词', 'text', 'keywords', '建站后台|快速建站|模板后台|响应式网站', 'site', '站点关键词，多个关键词之间以英文逗号‘,’或‘|’’隔开', '1', '2019-01-01 14:12:14', '2019-01-01 14:35:10');
INSERT INTO `{$this->tablePrefix}configs` VALUES ('3', '站点描述', 'textarea', 'description', 'Tadmin，基于ThinkPHP5.1+和AmazeUI的快速后台开发框架。', 'site', '站点描述，建议不超过80个字。', '1', '2019-01-01 14:14:28', '2019-01-01 14:35:10');
INSERT INTO `{$this->tablePrefix}configs` VALUES ('4', '备案号', 'text', 'icp', '', 'site', '', '1', '2019-01-01 14:25:28', '2019-01-01 14:35:10');
INSERT INTO `{$this->tablePrefix}configs` VALUES ('5', '版权信息', 'textarea', 'copyright', 'CopyRight©2019 Tadmin', 'site', '站点版权信息，通常显示在网站底部', '1', '2019-01-01 14:26:40', '2019-01-01 14:35:10');

-- ----------------------------
-- Table structure for {$this->tablePrefix}job_resumes
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}job_resumes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int(255) DEFAULT '1',
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}jobs
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT '0',
  `category_parent_path` varchar(255) DEFAULT '0,',
  `position_name` varchar(255) NOT NULL,
  `position_number` varchar(255) DEFAULT NULL,
  `content` text,
  `sort` tinyint(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(255) DEFAULT '1',
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}links
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `block` varchar(255) DEFAULT NULL,
  `target` varchar(255) DEFAULT '_blank',
  `sort` tinyint(255) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(255) DEFAULT '1',
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}links_blocks
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}links_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `block` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}menus
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}menus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `uri` varchar(50) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}message_board
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}message_board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}migrations
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}migrations` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}navs
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}navs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `uri` varchar(50) DEFAULT NULL,
  `target` varchar(255) DEFAULT '_self',
  `sort` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}operation_logs
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}operation_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adminer_id` int(11) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `input` text,
  `useragent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_operation_log_user_id_index` (`adminer_id`) USING BTREE
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}permissions
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `http_method` varchar(255) DEFAULT NULL,
  `http_path` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_permissions_name_unique` (`name`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}roles
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}roles_permissions
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}roles_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}single
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}single` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT '0',
  `category_parent_path` varchar(255) DEFAULT '0,',
  `type` tinyint(5) DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `summary` tinytext,
  `image` varchar(255) DEFAULT NULL,
  `content` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(255) DEFAULT '1',
  PRIMARY KEY (`id`)
);

-- ----------------------------
-- Table structure for {$this->tablePrefix}tags
-- ----------------------------
CREATE TABLE IF NOT EXISTS `{$this->tablePrefix}tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '0', '控制台', 'am-icon-home', '/dashboard', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '0', '常规管理', 'am-icon-cog', '', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '0', '安全管理', 'am-icon-user-secret', '', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '0', '内容管理', 'am-icon-file-text', '/logs', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '0', '营销工具', 'am-icon-send', '', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '0', '招聘管理', 'am-icon-user-plus', null, '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '2', '系统配置', null, '/config', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '3', '管理员', '', '/auth/adminer', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '3', '角色', '', '/auth/role', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '3', '权限', '', '/auth/permission', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '3', '操作日志', null, '/auth/log', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '4', '栏目', '', '/category', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '4', '文章', '', '/article', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '4', '单页', null, '/single', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '4', '导航', null, '/nav', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '5', '广告', null, '/advertising', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '5', '链接', null, '/link', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '5', '留言', null, '/message', '0', null, null);
INSERT INTO `{$this->tablePrefix}menus` VALUES (0, '6', '职位列表', null, '/job', '0', null, null);

INSERT INTO `{$this->tablePrefix}adminers` VALUES (1, 'admin', '\$2y\$10\$V18y/rcfTm5cMv2sOaLR..ZrKVEE3U/klNHaMi6ji/wdKE8esNz1i', '2018-09-12 17:20:27', '2018-09-21 16:26:49', '2018-09-21 16:26:49', '127.0.0.1', '1');
INSERT INTO `{$this->tablePrefix}permissions` VALUES ('1', '所有权限', 'ALL', '/*', '2018-09-14 13:55:31', '2018-09-14 13:56:05');
INSERT INTO `{$this->tablePrefix}roles` VALUES ('1', '超级管理员', '2018-09-20 16:14:01', '2018-09-20 16:14:01');
INSERT INTO `{$this->tablePrefix}roles_permissions` VALUES ('1', '1');
INSERT INTO `{$this->tablePrefix}adminers_roles` VALUES ('1', '1');
EOT
            );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
    }
}
