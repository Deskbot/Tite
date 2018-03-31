<?php
//POST: name

require_once '../php/all.php';
define('GROUPS_PAGE', URL . 'groups/index.php');

Session::start();

//check logged in
$loginErrors = new Errors();
$loginErrors->test(User::checkLoggedIn(), 'You must be logged in to create a group.');
$loginErrors->handleRedirect('loginErrors', URL . 'loginregister.php');

//validate input
$groupErrors = new Errors();

$nameGiven = Post::hasData('name');
$groupErrors->test($nameGiven, 'Name not given.');
$groupErrors->handleRedirect('groupErrors', GROUPS_PAGE);

//add group to db
$db = Database::get();
$query = "INSERT INTO Groups (name) VALUES (?)";
$db->preparedQuery($query, array(Post::get('name')));

switch ($db->lastError()) {
	//success
	case 0:
		break;
	
	//constraint failed
	case 19:
		$groupErrors->addOne('That group name is already in use.');
		break;
		
	//all other errors
	default:
		$groupErrors->addOne($db->lastErrorString());
		break;
}

//add user to group
$groupId = $db->lastId();
$addUserQuery = "INSERT INTO GroupsToUsers (groupId, userId, accepted, dateJoined) VALUES (?,?,1,?)";
$db->preparedQuery($addUserQuery, array($groupId, Cookie::get('id'), date('Y-m-d')));

$response = new Response();
$response->addOne('Group added: ' . Post::get('name') . '.');
$response->handleRedirect('groupResponse', GROUPS_PAGE);










