CREATE TABLE `append` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `t_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text,
  `user_id` int(10) unsigned NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `t_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `fav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `t_id` int(10) unsigned NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_delete` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `thread` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text,
  `node_id` int(10) unsigned NOT NULL,
  `root_node_id` int(10) unsigned NOT NULL,
  `action_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(10) unsigned NOT NULL,
  `comment_count` smallint(5) unsigned NOT NULL,
  `visit_count` int(10) unsigned NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vote_for` int(10) unsigned NOT NULL,
  `vote_against` int(10) unsigned NOT NULL,
  `hot` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `email` varchar(145) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `password` varchar(145) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `vote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `t_id` int(10) unsigned NOT NULL,
  `attitude` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `user` 
ADD COLUMN `karma` INT UNSIGNED NOT NULL AFTER `password`;
ALTER TABLE `user` 
ADD COLUMN `vote_count` INT UNSIGNED NOT NULL AFTER `karma`,
ADD COLUMN `fav_count` INT UNSIGNED NOT NULL AFTER `vote_count`;
ALTER TABLE `vote` 
CHANGE COLUMN `attitude` `attitude` TINYINT(4) NOT NULL COMMENT '1/-1 赞或不赞' ;
ALTER TABLE `vote` 
CHANGE COLUMN `t_id` `tid` INT(10) UNSIGNED NOT NULL ;
ALTER TABLE `fav` 
CHANGE COLUMN `t_id` `tid` INT(10) UNSIGNED NOT NULL ;
ALTER TABLE `comment` 
CHANGE COLUMN `t_id` `tid` INT(10) UNSIGNED NOT NULL AFTER `id`;
ALTER TABLE `append` 
CHANGE COLUMN `t_id` `tid` INT(10) UNSIGNED NOT NULL AFTER `id`;
ALTER TABLE `comment` 
ADD COLUMN `device` TINYINT UNSIGNED NOT NULL AFTER `create_time`;
