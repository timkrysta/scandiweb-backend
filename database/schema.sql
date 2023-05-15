/* The `height`, `width` and `length` were implemented as a separate columns because it could be better in the future to implement filters. */

CREATE DATABASE scandiweb_test_assignment;

USE scandiweb_test_assignment;

CREATE TABLE `products` 
( 
  `id`     INT NOT NULL AUTO_INCREMENT,
  `sku`    VARCHAR(255) NOT NULL UNIQUE,
  `name`   VARCHAR(255) NOT NULL,
  `price`  DECIMAL(14, 2) NOT NULL,
  `size`   smallint(6),
  `weight` smallint(6),
  `height` smallint(6),
  `width`  smallint(6),
  `length` smallint(6),
  PRIMARY KEY (`id`)
);