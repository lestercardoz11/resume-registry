#Resume Registry

This project was part of course taken on coursera, JavaScript by University of Michigan. 

To get started run the following SQL commands in phpmyadmin:

CREATE DATABASE misc;
GRANT ALL ON misc.* TO 'fred'@'localhost' IDENTIFIED BY 'zap';
GRANT ALL ON misc.* TO 'fred'@'127.0.0.1' IDENTIFIED BY 'zap';

USE misc; (Or select misc in phpMyAdmin)

CREATE TABLE users (
  user_id INTEGER NOT NULL AUTO_INCREMENT,
  name VARCHAR(128),
  email VARCHAR(128),
  password VARCHAR(128),
  PRIMARY KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE users ADD INDEX(email);
ALTER TABLE users ADD INDEX(password);

CREATE TABLE Profile (
  profile_id INTEGER NOT NULL AUTO_INCREMENT,
  user_id INTEGER NOT NULL,
  first_name TEXT,
  last_name TEXT,
  email TEXT,
  headline TEXT,
  summary TEXT,
  PRIMARY KEY(profile_id),
  CONSTRAINT profile_ibfk_2
  FOREIGN KEY (user_id)
  REFERENCES users (user_id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO users (name,email,password) VALUES ('UMSI','umsi@umich.edu','1a52e17fa899cf40fb04cfc42e6352f1');
  


