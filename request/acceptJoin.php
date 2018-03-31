<?php
//POST: groupId, joinId

require_once '../php/all.php';
define('GROUPS_PAGE', URL . 'groups/index.php');

Session::start();

//check logged in
$loginErrors = new Errors();
$loginErrors->test(User::checkLoggedIn(), 'You must be logged in to join a group.');
$loginErrors->handleRedirect('loginErrors', URL . 'loginregister.php');

//validate
$joinErrors = new Errors();
$groupIsDefined = Post::hasData('groupId');
$joinErrors->test($groupIsDefined, 'Insufficient data required to accept join (1).');
$joinErrors->test(Post::hasData('joinId'), 'Insufficient data required to accept join (2).');

$location = $groupIsDefined ? URL . 'groups/view.php?groupId=' . Post::get('groupId') : GROUPS_PAGE;
$joinErrors->handleRedirect('joinErrors', $location);

define('VIEW_PAGE', URL . 'groups/view.php?groupId=' . Post::get('groupId'));

//set requesting user as accepted if the current user is a member of that group
$db = Database::get();
$query = "
	UPDATE GroupsToUsers
	SET accepted=1
	WHERE id=? AND 1=(
		SELECT COUNT(*)
		FROM GroupsToUsers
		WHERE userId=? AND groupId=? AND accepted=1
		LIMIT 1
	);
";
$result = $db->preparedQuery($query, array(Post::get('joinId'), Cookie::get('id'), Post::get('groupId')));

switch ($db->lastError()) {
	case 0:
		break;
	
	default:
		$joinErrors->addOne($db->lastErrorString());
		break;
}

$joinErrors->handleRedirect('joinErrors', VIEW_PAGE);

//get the accept target's name
$nameQuery = "
	SELECT Users.name
	FROM Users
	INNER JOIN GroupsToUsers
	ON Users.id = GroupsToUsers.userId
	WHERE GroupsToUsers.id=?
";
$nameResult = $db->preparedQuery($nameQuery, array(Post::get('joinId')));
$nameResultArr = $nameResult->fetchArray(SQLITE3_NUM);
$addedName = $nameResultArr[0];

$response = new Response();
$response->addOne($addedName . ' was accepted.');
$response->handleRedirect('groupResponse', VIEW_PAGE);










