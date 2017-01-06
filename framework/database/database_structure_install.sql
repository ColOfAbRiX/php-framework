# MySQL-Front Dump 2.5
#
# Host: localhost   Database: enrico
# --------------------------------------------------------
# Server version 4.0.18-nt


#
# Table structure for table 'menu'
#

CREATE TABLE `menu` (
  `id_menu` tinyint(3) unsigned NOT NULL default '0',
  `item` varchar(255) NOT NULL default '',
  `link` varchar(230) NOT NULL default '',
  `position` tinyint(3) unsigned default '0',
  `indent` tinyint(3) unsigned default '0',
  PRIMARY KEY  (`id_menu`,`item`)
) TYPE=MyISAM;



#
# Table structure for table 'news'
#

CREATE TABLE `news` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `category` tinyint(3) unsigned default '0',
  `date` int(11) NOT NULL default '0',
  `content` text,
  `image` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



#
# Table structure for table 'news_category'
#

CREATE TABLE `news_category` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `category` char(50) NOT NULL default '',
  UNIQUE KEY `id_2` (`id`)
) TYPE=MyISAM;



#
# Table structure for table 'pages'
#

CREATE TABLE `pages` (
  `id` varchar(50) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `content` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



#
# Table structure for table 'users'
#

CREATE TABLE `users` (
  `name` varchar(50) NOT NULL default '',
  `pwd_hash` varchar(40) NOT NULL default '',
  `seed` int(10) unsigned NOT NULL default '0',
  `config` mediumtext,
  PRIMARY KEY  (`name`,`pwd_hash`)
) TYPE=MyISAM;

