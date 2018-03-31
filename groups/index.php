<?php
require_once '../php/all.php';

Session::start();
$errors = new Errors();

//check logged in
$loggedIn = User::checkLoggedIn();
$errors->test($loggedIn, 'Unauthorised access, please log in.');
$errors->handleRedirect('unauthorisedErrors', URL . 'loginregister.php');

//generate data
$user = new User(Cookie::get('id'));
$userGroups = $user->getGroups();

//generate templates
$head = new Template('head');
$head->addVar('title', 'Groups | ' . APP_NAME);

$header = new Template('header');
$header->addVar('loggedIn', User::checkLoggedIn());

$groups = new Template('groups');
$groups->addVar('groups', $userGroups);
$groups->addVar('giveTitle', true);

//get response data
$groupResponse = "";
if (Session::hasData('groupResponse')) {
	$groupResponse = Session::get('groupResponse')->toReadable();
	Session::remove('groupResponse');
}

//get error data
$groupErrors = "";
if (Session::hasData('groupErrors')) {
	$groupErrors = Session::get('groupErrors')->toReadable();
	Session::remove('groupErrors');
}
?>

<!DOCTYPE html>
<html>
	<?php echo $head->html(); ?>
	<body id="settings">
		<?php echo $header->html(); ?>
		
		<h2>Groups Overview</h2>
		
		<main>
			<p id="groupResponse" class="response">
				<?php echo $groupResponse; ?>
			</p>
			
			<p id="groupError" class="response">
				<?php echo $groupErrors; ?>
			</p>
			
			<div id="groups">
				<?php echo $groups->html(); ?>
			</div>
			
			<h3>Create a new group</h3>
			<form action="/request/addGroup.php" method="POST">
				<input name="name" placeholder="Group Name" required type="text"><br>
				<input type="submit">
			</form>
		</main>
	</body>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
</html>