<?php
//POST: name, password1, password2, email

require_once '../php/all.php';
define('REGISTER_PAGE', URL . 'loginregister.php');

Session::start();

//validate input
$errors = new Errors();

$nameGiven = Post::hasData('name');
$emailGiven = Post::hasData('email');
$passGiven = Post::hasData('password1') && Post::hasData('password2');

$errors->test($nameGiven,  'Name was not given.');
$errors->test($emailGiven, 'Email was not given.');
$errors->test($passGiven,  'Password field(s) were empty.');

if ($passGiven) {
	$passMatch = Post::get('password1') === Post::get('password2');
	$errors->test($passMatch, 'Passwords don\'t match.');
	
	if ($passMatch) $errors->test(User::isPasswordValid(Post::get('password1')), 'Password is not valid.');
}

if ($nameGiven) {
	$errors->test(User::isNameValid(Post::get('name')), 'Username is invalid.');
}
 
if ($emailGiven) {
	$errors->test(filter_var(filter_var(Post::get('email'), FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL), 'Invalid email was given.');
	$emailNotInUse = !User::emailExists(Post::get('email'));
	$errors->test($emailNotInUse, 'A user with this email already has an active account.');
}

//redirect if errors
$errors->handleRedirect('registerErrors', REGISTER_PAGE);

//generate salt and hash
$salt = rand_hex(128);
$confirmationStr = rand_hex(128);
$passHash = password_hash(Post::get('password1'), $salt);

//add user to RegisterLimbo
$db = Database::get();
$query = "INSERT INTO RegisterLimbo VALUES (NULL, ?, ?, ?, ?, ?, ?)";
$db->preparedQuery($query, array(Post::get('name'), Post::get('email'), $passHash, $salt, $confirmationStr, time()));

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

$errors->handleRedirect('registerErrors', REGISTER_PAGE);

//send confirmation email
$mail = Mail::get();
$body = new EmailTemplate('register');
$url = URL . 'request/registervalidate.php?userId=' . $db->lastId() . '&confirmationStr=' . $confirmationStr;

$body->addVars(array('confirmationUrl' => $url));
$body->html();
$mail->send(Post::get('email'), 'Confirm Registration to ' . APP_NAME, $body->html());

redirect(URL . 'registerconfirm.php');










