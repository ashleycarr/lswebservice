DROP DATABASE IF EXISTS lifesaver;
CREATE DATABASE lifesaver;

USE lifesaver;

GRANT USAGE ON *.* TO 'lsguest'@'localhost' IDENTIFIED BY 'password';
DROP USER 'lsguest'@'localhost';

GRANT USAGE ON *.* TO 'lsadmin'@'localhost' IDENTIFIED BY 'password';
DROP USER 'lsadmin'@'localhost';

# Holds contact information of the healthcare agents
CREATE TABLE healthcareAgents (
    id INT AUTO_INCREMENT,
    name VARCHAR(128) DEFAULT NULL,
    address VARCHAR(128) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(254),
    PRIMARY KEY (id)
) ENGINE=MyISAM;

# Holds indexed latitude and longitudes for each agent
CREATE TABLE healthcareLocations (
    id INT AUTO_INCREMENT,
    agentID INT NOT NULL,
    location POINT NOT NULL,
    PRIMARY KEY (id),
    INDEX (agentID),
    SPATIAL INDEX (location),
    FOREIGN KEY (agentID) 
        REFERENCES healthcare_agents(id)
        ON DELETE CASCADE
) ENGINE=MyISAM;

# Used to store admin users and password hashes.
CREATE TABLE adminUsers (
    id INT AUTO_INCREMENT,
    userName VARCHAR(128) NOT NULL,
    userPassword VARCHAR(128) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=MyISAM;

# CREATE USER lsadmin - select delete update add from healthcare agents and locations

# Used for MySQL version 5.6x for distance calculations
CREATE FUNCTION HAVERSINE(
    userLat FLOAT(10, 6), 
    userLon FLOAT(10, 6), 
    destLat FLOAT(10, 6), 
    destLon FLOAT(10, 6))
RETURNS float(15, 6)
    NO SQL
    COMMENT 'Haversine Formula'
RETURN 6372800 * 2 * ASIN(SQRT(POWER(
    SIN((userLat - destLat) * pi()/180 / 2), 2) + 
    COS(userLat * pi()/180) * COS(destLat * pi()/ 180) * 
    POWER(SIN((userLon - destLon) * pi() /180 / 2), 2)));
    
INSERT INTO adminUsers VALUES (
    NULL,
    'admin',
    'lsAdminPassword');

# lsguest user - allowed read access on healthcare locations and agents
CREATE USER 'lsguest'@'localhost' IDENTIFIED BY 'lsguest';
GRANT SELECT ON lifesaver.healthcareLocations TO 'lsguest'@'localhost';
GRANT SELECT ON lifesaver.healthcareAgents TO 'lsguest'@'localhost';
GRANT EXECUTE ON FUNCTION HAVERSINE TO 'lsguest'@'localhost';
