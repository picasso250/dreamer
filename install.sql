CREATE TABLE `thread` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NULL,
  `create_time` TIMESTAMP NULL,
  PRIMARY KEY (`id`));
