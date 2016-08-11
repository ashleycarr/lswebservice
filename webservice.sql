CREATE DATABASE 'lifesaver_db';
USE 'lifesaver_db';

CREATE TABLE `healthcare_agents` (
  `id` INT AUTO_INCREMENT,
  `name` VARCHAR(128) DEFAULT NULL,
  `address` VARCHAR(128) NOT NULL,
  `phone` INT(11) NOT NULL,
  `email` VARCHAR(254),
  `latitude` FLOAT(10, 6) NOT NULL ,
  `longitude` FLOAT(10, 6) NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM;

