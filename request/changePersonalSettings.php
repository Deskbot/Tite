<?php
//POST: name, email

require_once '../php/all.php';
define('SETTINGS_PAGE', URL . 'settings.php');

Session::start();

//check logged in
$loginErrors = new Errors();
$loginErrors->test(User::checkLoggedIn(), 'You must be logged in to change your settings.');
$loginErrors->handleRedirect('loginErrors', URL . 'loginregister.php');

//validate input
$personalErrors = new Errors();

$nameGiven = Post::hasData('name');
$emailGiven = Post::hasData('email');

if ($nameGiven)  $personalErrors->test(User::isNameValid(Post::get('name')), 'Username is invalid.');
if ($emailGiven) $personalErrors->test(filter_var(filter_var(Post::get('email'), FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL), 'Invalid email was given.');

//redirect if errors
$personalErrors->handleRedirect('personalErrors', SETTINGS_PAGE);

//add user to RegisterLimbo
$db = Database::get();
$query = "UPDATE Users SET name=?, email=? WHERE id=?;";
$db->preparedQuery($query, array(Post::get('name'), Post::get('email'), $_COOKIE['id']));

switch ($db->lastError()) {
	//success
	case 0:
		break;
	
	//constraint failed
	case 19:
		$errors->addOne('That email is already in use.');
		break;
		
	//all other errors
	default:
		$errors->addOne($db->lastErrorString());
		break;
}

$response = new Response();
$response->test(Post::hasData('name'), 'Personal details changed successfully.');
$response->handleRedirect('personalSettingsResponse', SETTINGS_PAGE);







