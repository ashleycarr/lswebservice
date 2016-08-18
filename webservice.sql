CREATE DATABASE 'lifesaver_db';
USE 'lifesaver_db';

CREATE TABLE `healthcare_agents` (
  `id` INT AUTO_INCREMENT,
  `name` VARCHAR(128) DEFAULT NULL,
  `address` VARCHAR(128) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `email` VARCHAR(254),
  `latitude` FLOAT(10, 6) NOT NULL ,
  `longitude` FLOAT(10, 6) NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM;
    
CREATE FUNCTION `haversine`(
    `userLat` FLOAT(10, 6), 
    `userLon` FLOAT(10, 6), 
    `destLat` FLOAT(10, 6), 
    `destLon` FLOAT(10, 6))
RETURNS float(10, 6)
    NO SQL
    COMMENT 'Haversine Formula'
RETURN 6372800 * 2 * ASIN(SQRT(POWER(
    SIN((userLat - destLat) * pi()/180 / 2), 2) + 
    COS(userLat * pi()/180) * COS(destLat * pi()/ 180) * 
    POWER(SIN((userLon - destLon) * pi() /180 / 2), 2)));
