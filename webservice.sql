CREATE DATABASE 'lifesaver_db';
USE 'lifesaver_db';

CREATE TABLE `healthcare_agents` (
  `id` INT AUTO_INCREMENT,
  `name` VARCHAR(128) DEFAULT NULL,
  `addr1` VARCHAR(128) NOT NULL,
  `addr2` VARCHAR(128) NOT NULL,
  `city` VARCHAR(128) NOT NULL,
  `state` VARCHAR(3) NOT NULL,
  `postcode` INT(4) NOT NULL,
  `latitude` FLOAT(10, 6) NOT NULL ,
  `longitude` FLOAT(10, 6) NOT NULL ,
  `phone` INT(11) NOT NULL,
  `email` VARCHAR(254),
  PRIMARY KEY (`id`)
) ENGINE=MYISAM;

