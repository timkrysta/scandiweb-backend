CREATE DATABASE web_developer_test_assignment;

USE web_developer_test_assignment;

CREATE TABLE `products` 
( 
  `id` INT NOT NULL AUTO_INCREMENT,
  `sku` VARCHAR(255) NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `price` DECIMAL(14, 2) NOT NULL,
  `size` INT NOT NULL,
  `weight` INT NOT NULL,
  `height` INT NOT NULL,
  `width` INT NOT NULL,
  `length` INT NOT NULL,
  PRIMARY KEY (`id`)
);