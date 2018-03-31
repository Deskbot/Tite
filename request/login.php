<?php
//POST: email, password

require_once '../php/all.php';
define('LOGIN_PAGE', URL . 'loginregister.php');

Session::start();

//validate input
$errors = new Errors();

$emailGiven = Post::hasData('email');
$passGiven = Post::hasData('password');

$errors->test($emailGiven, 'Email was not given.');
$errors->test($passGiven,  'Password field was empty.');

//redirect if errors
$errors->handleRedirect('loginErrors', LOGIN_PAGE . '?email=' . urlencode(__(Post::get('email'))));

//redirect if already logged in
if(User::checkLoggedIn()) {
	redirect(URL . 'dashboard.php');
}

//check log in successful
$user = User::getByLogin(Post::get('email'), Post::get('password'));

$errors->test($user !== false && $user->isLoggedIn(), 'Log in attempt unsuccessful.');

$errors->handleRedirect('loginErrors', LOGIN_PAGE . '?email=' . urlencode(__(Post::get('email'))));

//redirect to dashboard
redirect(URL . 'dashboard.php');










