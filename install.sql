CREATE TABLE `thread` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NULL,
  `create_time` TIMESTAMP NULL,
  PRIMARY KEY (`id`));
ALTER TABLE `dreamer`.`thread` 
CHANGE COLUMN `create_time` `create_time` TIMESTAMP NOT NULL ;
ALTER TABLE `dreamer`.`thread` 
ADD COLUMN `action_time` TIMESTAMP NOT NULL AFTER `create_time`;
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
ALTER TABLE `dreamer`.`thread` 
ADD COLUMN `user_id` INT UNSIGNED NOT NULL AFTER `action_time`;
CREATE TABLE `dreamer`.`comment` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` TEXT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `create_time` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`));
ALTER TABLE `dreamer`.`comment` 
ADD COLUMN `t_id` INT UNSIGNED NOT NULL AFTER `create_time`;
ALTER TABLE `dreamer`.`thread` 
ADD COLUMN `comment_count` SMALLINT UNSIGNED NOT NULL AFTER `user_id`;
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
ALTER TABLE `thread` 
ADD COLUMN `node_id` INT UNSIGNED NOT NULL AFTER `create_time`;
ALTER TABLE `dreamer`.`node` 
CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ;
