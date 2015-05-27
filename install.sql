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
