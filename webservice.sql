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
    REFERENCES healthcareAgents (id)
    ON DELETE CASCADE
) ENGINE=MyISAM;

# Used to store admin users and password hashes.
CREATE TABLE adminUsers (
    id INT AUTO_INCREMENT,
    userName VARCHAR(255) NOT NULL,
    userPassword VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=MyISAM;

# CREATE USER lsadmin - select delete update add from healthcare agents and locations
# default is admin - admin
    
INSERT INTO adminUsers VALUES (
    NULL,
    'admin',
    '$2y$10$FpMKykWxzwjetsxN1T5zNuKywJrTZOytgoKr8MO.rIRJEfGgi6Gb.');

# lsguest user - allowed read access on healthcare locations and agents
CREATE USER 'lsguest'@'localhost' IDENTIFIED BY 'lsguest';
GRANT SELECT ON lifesaver.healthcareLocations TO 'lsguest'@'localhost';
GRANT SELECT ON lifesaver.healthcareAgents TO 'lsguest'@'localhost';


# lsguest user - allowed read access on healthcare locations and agents
CREATE USER 'lsadmin'@'localhost' IDENTIFIED BY 'k8U7Bn#maTrAq@FULv6%bhVqQLR*CpGz';
GRANT SELECT, DELETE, UPDATE, INSERT ON lifesaver.healthcareLocations TO 'lsadmin'@'localhost';
GRANT SELECT, DELETE, UPDATE, INSERT ON lifesaver.healthcareAgents TO 'lsadmin'@'localhost';
GRANT SELECT, DELETE, UPDATE, INSERT ON lifesaver.adminUsers TO 'lsadmin'@'localhost';
