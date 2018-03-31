<?php
//POST: groupId, name

require_once '../php/all.php';
define('GROUPS_PAGE', URL . 'groups/index.php');

Session::start();

//check logged in
$loginErrors = new Errors();
$loginErrors->test(User::checkLoggedIn(), 'You must be logged in to join a group.');
$loginErrors->handleRedirect('loginErrors', URL . 'loginregister.php');

//validate
$joinErrors = new Errors();
$joinErrors->test(Post::hasData('groupId'), 'Group not specified.');
$joinErrors->handleRedirect('joinErrors', GROUPS_PAGE);

//add group to db
$db = Database::get();
$query = "INSERT INTO GroupsToUsers (groupId,userId,dateJoined) VALUES (?,?,?)";
$result = $db->preparedQuery($query, array(Post::get('groupId'), Cookie::get('id'), date('Y-m-d')));

switch ($db->lastError()) {
	//success
	case 0:
		break;
	
	//constraint failed
	case 19:
		$joinErrors->addOne('That group name is already in use.');
		break;
		
	//all other errors
	default:
		$joinErrors->addOne($db->lastErrorString());
		break;
}
$joinErrors->handleRedirect('joinErrors', GROUPS_PAGE);

$response = new Response();
$response->addOne('Request to join ' . Post::get('name') . ' was sent.');
$response->handleRedirect('groupResponse', GROUPS_PAGE);











