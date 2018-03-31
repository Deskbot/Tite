<?php
//POST: email, password

require_once '../php/all.php';
define('LOGIN_PAGE', URL . 'loginregister.php');

Session::start();

Cookie::remove('id');
Cookie::remove('loginId');

//redirect to dashboard
redirect(LOGIN_PAGE);










