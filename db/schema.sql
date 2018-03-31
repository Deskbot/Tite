DROP TABLE IF EXISTS Users;
CREATE TABLE Users (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	name VARCHAR(30) NOT NULL,
	email VARCHAR(100) NOT NULL UNIQUE,
	passHash CHAR(128) NOT NULL,
	salt CHAR(128) NOT NULL,
	registerTime INTEGER NOT NULL,
	loginId CHAR(128)
);

DROP TABLE IF EXISTS UserSettings;
CREATE TABLE UserSettings (
	userId INTEGER NOT NULL,
	newBill BOOLEAN NOT NULL DEFAULT 0,
	beforeDeadline BOOLEAN NOT NULL DEFAULT 0,
	
	FOREIGN KEY (userId) REFERENCES Users(id)
);

DROP TABLE IF EXISTS RegisterLimbo;
CREATE TABLE RegisterLimbo (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	name VARCHAR(30) NOT NULL,
	email VARCHAR(100) NOT NULL UNIQUE,
	passHash CHAR(128) NOT NULL,
	salt CHAR(128) NOT NULL,
	confirmationStr CHAR(128) NOT NULL,
	registerTime INTEGER NOT NULL
);

DROP TABLE IF EXISTS Groups;
CREATE TABLE Groups (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	name VARCHAR(30) NOT NULL UNIQUE
);

DROP TABLE IF EXISTS GroupsToUsers;
CREATE TABLE GroupsToUsers (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	groupId INTEGER NOT NULL,
	userId INTEGER NOT NULL,
	accepted BOOLEAN NOT NULL DEFAULT 0,
	dateJoined DATE NOT NULL,
	
	FOREIGN KEY (groupId) REFERENCES Groups(id),
	FOREIGN KEY (userId) REFERENCES Users(id)
);

DROP TABLE IF EXISTS Bills;
CREATE TABLE Bills (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	groupId INTEGER NOT NULL,
	name VARCHAR(30) NOT NULL,
	description VARCHAR(100),
	amount INTEGER NOT NULL,
	leftover INTEGER,
	deadline DATE,
	complete BOOLEAN NOT NULL DEFAULT 0,
	dateCreated DATE,
	
	FOREIGN KEY (groupId) REFERENCES Groups(id)
);
--- removed payee field because it can go in the description

DROP TABLE IF EXISTS Payments;
CREATE TABLE Payments (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	billId INTEGER NOT NULL,
	userId INTEGER NOT NULL,
	amountPaid INTEGER NOT NULL,
	datePaid DATE NOT NULL,
	
	FOREIGN KEY (billId) REFERENCES Bills(id),
	FOREIGN KEY (userId) REFERENCES Users(id)
);