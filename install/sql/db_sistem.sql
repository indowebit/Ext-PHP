-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 09, 2010 at 07:53 PM
-- Server version: 5.1.37
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `db_xframejs`
--

-- --------------------------------------------------------

--
-- Table structure for table `iconcls`
--

CREATE TABLE IF NOT EXISTS `iconcls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  `clsname` varchar(128) NOT NULL,
  `icon` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=86 ;

--
-- Dumping data for table `iconcls`
--

INSERT INTO `iconcls` (`id`, `title`, `clsname`, `icon`) VALUES
(1, 'Computer', 'base', 'application_cascade.png'),
(3, 'Check Password', 'chk-pwd', 'user_edit.png'),
(4, 'User Female', 'user-female', 'user_female.png'),
(50, 'user-manager', 'user-comment', 'user_go.png'),
(6, 'Logout', 'logout', 'key_go.png'),
(7, 'Login', 'login', 'lock_open.png'),
(8, 'Lock', 'lock', 'lock.png'),
(9, 'Browse', 'browse', 'grid.png'),
(10, 'Config', 'conf', 'cog_edit.png'),
(49, 'Application', 'app', 'plugin.gif'),
(12, 'Refresh', 'drop', 'table_refresh.png'),
(62, 'Duplicate', 'duplicate', 'table_multiple.png'),
(14, 'Menu Panel', 'mymenu', 'application_side_tree.png'),
(15, 'Navigator', 'navigator', 'application_side_boxes.png'),
(16, 'Setting', 'setting', 'application_get.png'),
(17, 'Form', 'form', 'application_form.png'),
(18, 'Add data', 'add-data', 'add.png'),
(19, 'Table Delete', 'table-delete', 'table_delete.png'),
(20, 'Table Addc', 'table-add', 'table_add.png'),
(21, 'Row Delete', 'row-delete', 'cancel.png'),
(22, 'App Grid', 'app-grid', 'table.png'),
(23, 'Form Edit', 'form-edit', 'application_form_edit.png'),
(24, 'Report Mode', 'report-mode', 'report_disk.png'),
(25, 'Report Pdf', 'report-pdf', 'page_white_acrobat.png'),
(26, 'Report Xls', 'report-xls', 'page_white_excel.png'),
(27, 'Parent Form', 'parent-form', 'vcard.png'),
(28, 'Arrow Down', 'arrow-down', 'arrow_down.png'),
(29, 'App Add', 'app-add', 'plugin_add.gif'),
(30, 'Panel Collapse', 'panel-collapse', 'application_put.png'),
(31, 'Image Add', 'image-add', 'image_add.png'),
(32, 'Db Table', 'db-table', 'database_table.png'),
(33, 'Db Refresh', 'db-refresh', 'database_refresh.png'),
(34, 'Menu Add', 'menu-add', 'page.png'),
(35, 'Sub Menu Add', 'submenu-add', 'page_add.png'),
(36, 'Menu Remove', 'menu-remove', 'page_delete.png'),
(37, 'Save', 'icon-save', 'disk.png'),
(38, 'Accept', 'accept', 'accept.png'),
(39, 'Js File', 'js-file', 'page_white_code.png'),
(40, 'Php File', 'php-file', 'page_white_php.png'),
(41, 'Image', 'image', 'images.png'),
(55, 'rss', 'rss', 'rss.png'),
(45, 'Event Menu', 'event-menu', 'attach.png'),
(48, 'Css Refresh', 'css-refresh', 'css_valid.png'),
(52, 'error', 'error-cls', 'error.png'),
(61, 'autosave', 'autosave', 'server_link.png'),
(64, 'pindah-kk', 'pindah-kk', 'book_go.png'),
(65, 'kk-baru', 'kk-baru', 'book_key.png'),
(66, 'split-kk', 'split-kk', 'book_open.png'),
(67, 'csv', 'csv', 'page_white_text.png'),
(68, 'upload', 'upload', 'page_attach.png'),
(69, 'group-manager', 'group-manager', 'group.png'),
(70, 'group-delete', 'group-delete', 'group_delete.png'),
(71, 'group-add', 'group-add', 'group_add.png'),
(72, 'user-delete', 'user-delete', 'user_delete.gif'),
(73, 'user-add', 'user-add', 'user_add.gif'),
(74, 'admin-page', 'admin-page', 'cog_error.png'),
(75, 'menu-disabled', 'check-none', 'plugin_disabled.png'),
(77, 'statistik', 'stat', 'chart_bar.png'),
(78, 'stat-line', 'stat-line', 'chart_curve.png'),
(79, 'stat-pie', 'stat-pie', 'chart_pie.png'),
(80, 'stat-bar', 'stat-bar', 'chart_bar_edit.png'),
(82, 'stat-line2', 'stat-line2', 'chart_line.png'),
(83, 'report-word', 'report-word', 'page_white_word.png'),
(84, 'arr', 'arrow-up', 'arrow_up.png'),
(85, 'sort', 'sort', 'text_padding_right.png');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(32) NOT NULL,
  `iconcls` varchar(32) NOT NULL,
  `handler` varchar(128) NOT NULL,
  `ajax` varchar(128) NOT NULL,
  `report` varchar(128) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `sort_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `parent_id`, `title`, `iconcls`, `handler`, `ajax`, `report`, `published`, `sort_id`) VALUES
(8, 0, 'Sample Form', 'form', '', '', '', 1, 4),
(9, 8, 'Sample Form 1', 'form-edit', 'form_v.js', 'form_c.php', '', 1, 9),
(5, 4, 'Sample Grid 1', 'app-grid', 'sppk_v.js', 'sppk_c.php', 'sppk_r.php', 1, 5),
(4, 0, 'Sample Grid', 'app-grid', '', '', '', 1, 6),
(6, 0, 'Sample Chart', 'stat', '', '', '', 1, 8),
(7, 6, 'Sample Chart 1', 'stat-line2', 'chart_v.js', 'chart_c.php', '', 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `menu_event`
--

CREATE TABLE IF NOT EXISTS `menu_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `event_name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `menu_event`
--

INSERT INTO `menu_event` (`id`, `menu_id`, `event_name`) VALUES
(1, 1, 'PRINT_DATA'),
(2, 1, 'EDIT_DATA'),
(3, 1, 'CANCEL_DATA'),
(8, 5, 'ADD_DATA'),
(9, 5, 'EDIT_DATA'),
(10, 5, 'REMOVE_DATA'),
(11, 5, 'PRINT_DATA'),
(12, 9, 'SAVE_DATA');

-- --------------------------------------------------------

--
-- Table structure for table `role_menu_event_group`
--

CREATE TABLE IF NOT EXISTS `role_menu_event_group` (
  `role_menu_event_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_menu_event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `role_menu_event_group`
--

INSERT INTO `role_menu_event_group` (`role_menu_event_id`, `role_id`, `group_id`, `is_active`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 1),
(3, 3, 1, 1),
(8, 8, 1, 1),
(9, 9, 1, 1),
(10, 10, 1, 1),
(11, 11, 1, 1),
(12, 12, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `role_menu_group`
--

CREATE TABLE IF NOT EXISTS `role_menu_group` (
  `role_menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `role_menu_group`
--

INSERT INTO `role_menu_group` (`role_menu_id`, `menu_id`, `group_id`, `is_active`) VALUES
(8, 8, 1, 1),
(5, 5, 1, 1),
(4, 4, 1, 1),
(6, 6, 1, 1),
(7, 7, 1, 1),
(9, 9, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `real_name` varchar(128) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `count_login` int(11) DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `user_password` varchar(128) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `group_id`, `real_name`, `last_login`, `count_login`, `date_created`, `user_password`, `is_active`) VALUES
(1, 'admin', 1, 'Administrator System', '2010-04-09 17:06:45', 90, '2010-02-28 15:03:33', 'YWRtaW4=', 1),
(23, 'userku', 1, 'UserName', '1899-11-30 00:00:00', 0, '2010-04-09 18:12:54', 'MTIz', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE IF NOT EXISTS `user_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(128) DEFAULT NULL,
  `group_description` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `user_group`
--

INSERT INTO `user_group` (`group_id`, `group_name`, `group_description`) VALUES
(1, 'Administrator', 'Super Administrator');
