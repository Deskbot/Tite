<?php
//POST: passwordOld, password1, password2

require_once '../php/all.php';
define('SETTINGS_PAGE', URL . 'settings.php');

Session::start();

//check logged in
$loginErrors = new Errors();
$loginErrors->test(User::checkLoggedIn(), 'You must be logged in to change your settings.');
$loginErrors->handleRedirect('loginErrors', URL . 'loginregister.php');

//validate input
$securityErrors = new Errors();

$oldPassGiven = Post::hasData('passwordOld');
$passGiven = Post::hasData('password1') && Post::hasData('password2');

$securityErrors->test($oldPassGiven, 'Password field(s) were empty.');
$securityErrors->test($passGiven, 'Password field(s) were empty.');

if ($oldPassGiven) {
	$oldPassCorrect = User::checkPasswordCorrect($_COOKIE['id'], $_POST['passwordOld']);
	$securityErrors->test($oldPassCorrect, 'Old password was not correct.');
}

if ($passGiven) {
	$passMatch = Post::get('password1') === Post::get('password2');
	$securityErrors->test($passMatch, 'Passwords don\'t match.');
	
	if ($passMatch) $securityErrors->test(User::isPasswordValid(Post::get('password1')), 'Password is not valid.');
}

//redirect if errors
$securityErrors->handleRedirect('securityErrors', SETTINGS_PAGE);

//generate salt and hash
$salt = rand_hex(128);
$passHash = password_hash(Post::get('password1'), $salt);

//add user to RegisterLimbo
$db = Database::get();
$query = "UPDATE Users SET passHash=?, salt=? WHERE id=?;";
$db->preparedQuery($query, array($passHash, $salt, $_COOKIE['id']));

$response = new Response();
$response->addOne('Password changed successfully.');
$response->handleRedirect('securitySettingsResponse', SETTINGS_PAGE);










