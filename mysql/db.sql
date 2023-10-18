DROP DATABASE IF EXISTS test_website_db;

CREATE DATABASE test_website_db;

CREATE TABLE test_website_db.accounts (
	id int NOT NULL AUTO_INCREMENT,
	username varchar(50) NOT NULL,
	email varchar(255) NOT NULL,
	name varchar(50) NOT NULL,
	surname varchar(50) NOT NULL,
	password varchar(128) NOT NULL,
	PRIMARY KEY (id)
);

CREATE TABLE test_website_db.attempts (
	id int NOT NULL AUTO_INCREMENT,
	uname varchar(50) NOT NULL,
	value int(11),
	last_time int(128),
	PRIMARY KEY(id)
);
