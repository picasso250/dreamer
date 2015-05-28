CREATE TABLE `dreamer`.`user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `email` VARCHAR(145) NULL,
  `create_time` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`));
ALTER TABLE `dreamer`.`user` 
ADD COLUMN `password` VARCHAR(145) NOT NULL AFTER `create_time`;
INSERT INTO `dreamer`.`user` (`name`, `password`)
 VALUES ('xiaochi', sha1('xiaochi'));
CREATE TABLE `dreamer`.`comment` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` TEXT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `create_time` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`));
ALTER TABLE `dreamer`.`comment` 
ADD COLUMN `t_id` INT UNSIGNED NOT NULL AFTER `create_time`;
CREATE TABLE `dreamer`.`node` (
  `id` INT NOT NULL,
  `name` VARCHAR(45) NULL,
  `pid` INT UNSIGNED NOT NULL,
  `creat_time` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`));
ALTER TABLE `dreamer`.`node` 
CHANGE COLUMN `creat_time` `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ;
ALTER TABLE `dreamer`.`node` 
CHANGE COLUMN `name` `name` VARCHAR(45) NOT NULL ;
ALTER TABLE `dreamer`.`node` 
CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `dreamer`.`node` 
ADD COLUMN `user_id` INT UNSIGNED NOT NULL AFTER `create_time`;
CREATE TABLE `thread` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text,
  `node_id` int(10) unsigned NOT NULL,
  `action_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(10) unsigned NOT NULL,
  `comment_count` smallint(5) unsigned NOT NULL,
  `visit_count` int(10) unsigned NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `dreamer`.`vote` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `t_id` INT UNSIGNED NOT NULL,
  `attitude` TINYINT NOT NULL,
  PRIMARY KEY (`id`));
ALTER TABLE `dreamer`.`node` 
ADD COLUMN `description` VARCHAR(255) NULL AFTER `user_id`;
ALTER TABLE `dreamer`.`thread` 
ADD COLUMN `root_node_id` INT UNSIGNED NOT NULL AFTER `vote_against`;
