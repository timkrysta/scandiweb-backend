CREATE DATABASE web_developer_test_assignment;

USE web_developer_test_assignment;

CREATE TABLE `products` 
( 
  `id`     INT NOT NULL AUTO_INCREMENT,
  `sku`    VARCHAR(255) NOT NULL ,
  `name`   VARCHAR(255) NOT NULL ,
  `price`  DECIMAL(14, 2) NOT NULL,
  `size`   smallint(6),
  `weight` smallint(6),
  `height` smallint(6),
  `width`  smallint(6),
  `length` smallint(6),
  PRIMARY KEY (`id`)
);