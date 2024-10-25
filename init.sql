CREATE DATABASE IF NOT EXISTS vuln_app;

USE vuln_app;

-- Check if the user already exists
CREATE USER IF NOT EXISTS 'admin'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'%' WITH GRANT OPTION;

CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' WITH GRANT OPTION;

FLUSH PRIVILEGES;
