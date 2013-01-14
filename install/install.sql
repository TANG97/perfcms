CREATE TABLE IF NOT EXISTS `forum` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `pos` int(32) NOT NULL,
  `desc` varchar(160) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `forum_c` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `f_id` int(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `pos` int(32) NOT NULL,
  `desc` varchar(160) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `forum_pt` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `topic_id` int(32) NOT NULL,
  `cat_id` int(32) NOT NULL,
  `f_id` int(32) NOT NULL,
  `time` int(32) NOT NULL,
  `user_id` int(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `file` varchar(1000) NOT NULL,
  `file_size` int(100) NOT NULL,
  `text` text NOT NULL,
  `edit_time` int(11) NOT NULL,
  `count_edit` int(11) NOT NULL,
  `edit_user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `forum_t` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `time_last_post` int(11) NOT NULL,
  `user_last_post` int(11) NOT NULL,
  `closed` int(1) NOT NULL,
  `attach` int(1) NOT NULL,
  `user_id` int(16) NOT NULL,
  `time` int(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `guests` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `browser` text NOT NULL,
  `time` text NOT NULL,
  `from` varchar(350) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_chat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `time` int(11) NOT NULL,
  `who_id` int(11) NOT NULL,
  `read` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `mail_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `who_id` int(11) NOT NULL,
  `time_last_message` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `minichat` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(20) NOT NULL,
  `time` int(20) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `category_id` int(16) NOT NULL,
  `time` int(32) NOT NULL,
  `user_id` int(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `text` text NOT NULL,
  `image` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `news_categories` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `news_comments` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `news_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `time` int(20) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `notify` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `user_id` int(16) NOT NULL,
  `request_id` int(16) NOT NULL,
  `from_id` int(16) NOT NULL,
  `type` varchar(20) NOT NULL,
  `time` int(16) NOT NULL,
  `read` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `settings` (
  `user_id` int(20) NOT NULL,
  `lang` varchar(10) NOT NULL,
  `ames` varchar(2) NOT NULL,
  `theme` varchar(50) NOT NULL,
  `web_theme` varchar(50) NOT NULL,
  `touch_theme` varchar(100) NOT NULL,
  `fast_mess` varchar(3) NOT NULL,
  `view_profile` varchar(50) NOT NULL,
  `show_email` varchar(50) NOT NULL,
  `timezone` varchar(55) NOT NULL,
  `signature` varchar(45) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `system` (
  `pts` int(2) NOT NULL,
  `open_site` int(1) NOT NULL,
  `open_reg` int(1) NOT NULL,
  `access_site` int(1) NOT NULL,
  `timezone` varchar(69) NOT NULL,
  `language` varchar(7) NOT NULL,
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `wap_theme` varchar(100) NOT NULL,
  `web_theme` varchar(100) NOT NULL,
  `touch_theme` varchar(100) NOT NULL,
  `default_type` varchar(5) NOT NULL,
  `active_switch` int(1) NOT NULL,
  `copyright` varchar(32) NOT NULL,
  `file_types` text NOT NULL,
  `system` varchar(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

INSERT INTO `system` (`pts`, `open_site`, `open_reg`, `access_site`, `timezone`, `language`, `description`, `keywords`, `wap_theme`, `web_theme`, `touch_theme`, `default_type`, `active_switch`, `copyright`, `file_types`, `system`) VALUES
(10, 0, 0, 0, 'Europe/London', 'en', 'PerfCMS - Free mobile optimized Content Management System with open source code', 'PerfCMS, CMS, mobile, wap, web, php, pdo', 'default', 'default', 'default', 'auto', 0, 'PerfCMS', 'png;gif;jpg;jpeg;bmp;sis;sisx;zip;rar;jar;jad;mp4;3gp;mp3;mid;midi;wav;amr;exe;gz', 'auto');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `nick` varchar(32) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(25) NOT NULL,
  `surname` varchar(40) NOT NULL,
  `device` varchar(40) NOT NULL,
  `icq` int(9) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `site` varchar(40) NOT NULL,
  `info` varchar(3000) NOT NULL,
  `gender` int(1) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  `active` varchar(1) NOT NULL,
  `city` varchar(55) NOT NULL,
  `country` varchar(55) NOT NULL,
  `time` varchar(55) NOT NULL,
  `reg_time` varchar(55) NOT NULL,
  `locate` varchar(100) NOT NULL,
  `level` int(2) NOT NULL,
  `ban_time` int(32) NOT NULL,
  `ban_text` varchar(1000) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
