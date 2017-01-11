/*
	SQLITE Database 
	Colin Bernard
	Dec 2016
*/

-- stores information about employees who work here
CREATE TABLE Employee(
	id INTEGER,
	firstname VARCHAR(25) NOT NULL,
	lastname VARCHAR(25) NOT NULL,
	email VARCHAR(250),
	phone CHAR(10),
	username VARCHAR(15),
	password VARCHAR(15),
	employed VARCHAR(5), -- true or false (SQLITE has no boolean data type)
	PRIMARY KEY(id ASC)
);

-- employees work shifts
CREATE TABLE Shift(
	sid INTEGER,
	eid INTEGER NOT NULL,
	start_date VARCHAR(16),
	finish_date VARCHAR(16),
	PRIMARY KEY(sid),
	FOREIGN KEY (eid) REFERENCES Employee(id)
		ON UPDATE CASCADE -- update shift employee if employee is updated
		ON DELETE NO ACTION -- do not delete shift employee ids if the employee is removed
);

-- have access to admin controls
CREATE TABLE Manager(
	id INTEGER,
	PRIMARY KEY(id),
	FOREIGN KEY (id) REFERENCES Employee(id)
		ON UPDATE CASCADE -- update manager id if the employee id is changed
		ON DELETE CASCADE -- remove manager if employee is removed
);

-- store hours for each week day
CREATE TABLE Hours (
	weekday VARCHAR(9),
	open_time TIME,
	close_time TIME,
	PRIMARY KEY(weekday)
);