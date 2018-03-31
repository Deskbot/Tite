INSERT INTO Users VALUES (
	NULL,
	"TestUser1",
	"user1@gmail.com",
	"3d3b304713cca8ea5d54dc4ddc7ed5f83844062c8591d2004b54c45add206544859cd5f5e5f8cd64145563149570ea48e9663bb3efedfcaa9e37a4054753a702",
	"8b638cf0d9126e04de6a7140e85de29c5c8c5668652f4f389605a686acf5fc94297e0cde5f90f21ea4cafa04e6646c487bc6668cea2b1b269f5ee3fabebc1a4b",
	2016-04-01,
	"38336156b09542dadbea374ce64301c0dd3c2cb845438a62a808e716dd215e1ead2b16ac38d86e0ce210ff38ff72b81af9cfe6c264da88722be082aa950e4748"
);
INSERT INTO Users VALUES (
	NULL,
	"TestUser2",
	"user2@gmail.com",
	"3d3b304713cca8ea5d54dc4ddc7ed5f83844062c8591d2004b54c45add206544859cd5f5e5f8cd64145563149570ea48e9663bb3efedfcaa9e37a4054753a702",
	"8b638cf0d9126e04de6a7140e85de29c5c8c5668652f4f389605a686acf5fc94297e0cde5f90f21ea4cafa04e6646c487bc6668cea2b1b269f5ee3fabebc1a4b",
	2016-03-01,
	"38336156b09542dadbea374ce64301c0dd3c2cb845438a62a808e716dd215e1ead2b16ac38d86e0ce210ff38ff72b81af9cfe6c264da88722be082aa950e4748"
);
INSERT INTO Users VALUES (
	NULL,
	"TestUser3",
	"user3@gmail.com",
	"3d3b304713cca8ea5d54dc4ddc7ed5f83844062c8591d2004b54c45add206544859cd5f5e5f8cd64145563149570ea48e9663bb3efedfcaa9e37a4054753a702",
	"8b638cf0d9126e04de6a7140e85de29c5c8c5668652f4f389605a686acf5fc94297e0cde5f90f21ea4cafa04e6646c487bc6668cea2b1b269f5ee3fabebc1a4b",
	2016-02-01,
	"38336156b09542dadbea374ce64301c0dd3c2cb845438a62a808e716dd215e1ead2b16ac38d86e0ce210ff38ff72b81af9cfe6c264da88722be082aa950e4748"
);
INSERT INTO Users VALUES (
	NULL,
	"Thomas R",
	"user4@gmail.com",
	"3d3b304713cca8ea5d54dc4ddc7ed5f83844062c8591d2004b54c45add206544859cd5f5e5f8cd64145563149570ea48e9663bb3efedfcaa9e37a4054753a702",
	"8b638cf0d9126e04de6a7140e85de29c5c8c5668652f4f389605a686acf5fc94297e0cde5f90f21ea4cafa04e6646c487bc6668cea2b1b269f5ee3fabebc1a4b",
	2016-01-01,
	"38336156b09542dadbea374ce64301c0dd3c2cb845438a62a808e716dd215e1ead2b16ac38d86e0ce210ff38ff72b81af9cfe6c264da88722be082aa950e4748"
);

INSERT INTO UserSettings VALUES (1,0,0);
INSERT INTO UserSettings VALUES (2,0,1);
INSERT INTO UserSettings VALUES (3,1,0);
INSERT INTO UserSettings VALUES (4,1,1);

INSERT INTO Groups (name) VALUES ("Squad");
INSERT INTO Groups (name) VALUES ("123 Lane Street");
INSERT INTO Groups (name) VALUES ("Friends");

INSERT INTO GroupsToUsers (groupId, userId, accepted, dateJoined) VALUES (1,1,1,'2016-01-01');
INSERT INTO GroupsToUsers (groupId, userId, accepted, dateJoined) VALUES (1,2,1,'2016-01-02');
INSERT INTO GroupsToUsers (groupId, userId, accepted, dateJoined) VALUES (1,3,1,'2016-01-03');
INSERT INTO GroupsToUsers (groupId, userId, accepted, dateJoined) VALUES (1,4,1,'2016-01-03');
INSERT INTO GroupsToUsers (groupId, userId, accepted, dateJoined) VALUES (2,3,0,'2016-01-03'); ---request
INSERT INTO GroupsToUsers (groupId, userId, accepted, dateJoined) VALUES (2,4,1,'2016-01-04'); ---this is Thomas R to Squad
INSERT INTO GroupsToUsers (groupId, userId, accepted, dateJoined) VALUES (3,3,1,'2016-01-05');

INSERT INTO Bills (groupId, name, description, amount, leftover, deadline, dateCreated)
VALUES (1, 'TestBill1', 'Our Bill', 1000, 0, null, '2016-01-01');
INSERT INTO Bills (groupId, name, description, amount, leftover, deadline, dateCreated)
VALUES (1, 'TestBill2', 'Your Bill', 90099, 12, '2017-01-01', '2016-02-01');

INSERT INTO Payments (billId, userId, amountPaid, datePaid)
VALUES (1, 1, 500, '2016-01-01');
INSERT INTO Payments (billId, userId, amountPaid, datePaid)
VALUES (1, 2, 0, '2016-02-01');
INSERT INTO Payments (billId, userId, amountPaid, datePaid)
VALUES (1, 3, 1000, '2016-02-01');
















