<?php
//POST: groupId, name

require_once '../php/all.php';
define('GROUPS_PAGE', URL . 'groups/index.php');

Session::start();

//check logged in
$loginErrors = new Errors();
$loginErrors->test(User::checkLoggedIn(), 'You must be logged in to create a group.');
$loginErrors->handleRedirect('loginErrors', URL . 'loginregister.php');

//validate
$groupErrors = new Errors();

//check that they don't owe money

//add group to db
$db = Database::get();
$query = "DELETE FROM GroupsToUsers WHERE groupId=? AND userId=?";
$result = $db->preparedQuery($query, array(Post::get('groupId'), Cookie::get('id')));

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

//delete the group if it has no members
$deleteIfEmptyQuery = "DELETE FROM Groups WHERE id=:gid AND 0=(SELECT COUNT(*) FROM GroupsToUsers WHERE groupId=:gid)";
$db->preparedQuery($deleteIfEmptyQuery, array('gid', Post::get('groupId')));

$response = new Response();
$response->addOne('Group left: ' . Post::get('name') . '.');
$response->handleRedirect('groupResponse', GROUPS_PAGE);










