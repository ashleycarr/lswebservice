CREATE DATABASE 'lifesaver_db';
USE 'lifesaver_db';

# Holds contact information of the healthcare agents
CREATE TABLE `healthcare_agents` (
  `id` INT AUTO_INCREMENT,
  `name` VARCHAR(128) DEFAULT NULL,
  `address` VARCHAR(128) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `email` VARCHAR(254),
  `latitude` FLOAT(10, 6) NOT NULL ,
  `longitude` FLOAT(10, 6) NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

# Holds indexed latitude and longitudes for each agent
CREATE TABLE `healthcare_locations` (
    parentid INT,
    location POINT NOT NULL,
    INDEX (parent_id),
    SPATIAL INDEX (location),
    FOREIGN KEY (parent_id) 
        REFERENCES parent(id)
        ON DELETE CASCADE
) ENGINE=MyISAM;

# Used for MySQL version 5.6x for distance calculations
CREATE FUNCTION `HAVERSINE`(
    `userLat` FLOAT(10, 6), 
    `userLon` FLOAT(10, 6), 
    `destLat` FLOAT(10, 6), 
    `destLon` FLOAT(10, 6))
RETURNS float(15, 6)
    NO SQL
    COMMENT 'Haversine Formula'
RETURN 6372800 * 2 * ASIN(SQRT(POWER(
    SIN((userLat - destLat) * pi()/180 / 2), 2) + 
    COS(userLat * pi()/180) * COS(destLat * pi()/ 180) * 
    POWER(SIN((userLon - destLon) * pi() /180 / 2), 2)));
