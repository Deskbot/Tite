<?php
//GET: userId, confirmationStr

require_once '../php/all.php';

Session::start();

//validate input
$errors = new Errors();

$userIdGiven = Get::hasData('userId');
$confirmationStr = Get::hasData('confirmationStr');

$errors->test($userIdGiven || $confirmationStr, 'Invalid URL');

$errors->handleStop(function() {
	global $errors;
	echo $errors->toReadable();
});

//update the database
$db = Database::get();
$infoQuery = "SELECT name, email, passHash, salt, registerTime FROM RegisterLimbo WHERE id=:userId AND confirmationStr=:str;";
$insertQuery = "
	INSERT INTO Users (name, email, passHash, salt, registerTime) VALUES (?,?,?,?,?);
	DELETE FROM RegisterLimbo WHERE id=? AND confirmationStr=?
";

//-check if they're already signed up
$infoResult = $db->preparedQuery($infoQuery, array(':userId' => $_GET['userId'], ':str' => $_GET['confirmationStr']));
$infoArr = $infoResult->fetchArray(SQLITE3_ASSOC);

$notAlreadyRegistered = !User::emailExists($infoArr['email']);
$errors->test($notAlreadyRegistered, 'You are already signed up.');

$errors->handleStop(function() {
	global $errors;
	echo $errors->toReadable();
});

//-move user from limbo to the database
$db->preparedQuery($insertQuery, array(
	$infoArr['name'],
	$infoArr['email'],
	$infoArr['passHash'],
	$infoArr['salt'],
	$infoArr['registerTime'],
	$_GET['userId'],
	$_GET['confirmationStr']
));

switch ($db->lastError()) {
	case 0:
		break;
	default:
		$errors->addOne($db->lastErrorString());
		break;
}

$errors->handleStop(function() {
	global $errors;
	echo $errors->toReadable();
});

$response = new Response();
$response->addOne('Account Activated');
Session::set('signupResponse', $response);

redirect(URL . 'loginregister.php?email=' . urlencode(__($infoArr['email'])));







