CREATE DATABASE IF NOT EXISTS vuln_app;

USE vuln_app;

-- Check if the user already exists
CREATE USER IF NOT EXISTS 'admin'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'%' WITH GRANT OPTION;

CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' WITH GRANT OPTION;

FLUSH PRIVILEGES;

-- Check if the table already exists
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL
);

-- Insert sample data only if it's not already present
INSERT INTO users (firstname, lastname)
SELECT 'John', 'Doe' FROM DUAL
WHERE NOT EXISTS (SELECT * FROM users WHERE firstname = 'John' AND lastname = 'Doe');
