<?php
//GET - groupId

require_once '../php/all.php';

Session::start();
$errors = new Errors();

//check logged in
$loggedIn = User::checkLoggedIn();
$errors->test($loggedIn, 'Unauthorised access, please log in.');
$errors->handleRedirect('unauthorisedErrors', URL . 'loginregister.php');

//validate input
$groupErrors = new Errors();
$groupErrors->test(Get::hasData('groupId'), 'Group not specified in URL for ./view.php');
$groupErrors->handleRedirect('groupErrors', URL . 'groups/index.php');

//generate data
$db = Database::get();
$user = new User(Cookie::get('id'));
$group = new Group(Get::find('groupId'));

//check for group membership
$authorised = $user->isInGroup($group);
$groupErrors->test($authorised, 'You are not a member of that group.');
$groupErrors->handleRedirect('groupErrors', URL . 'groups/index.php');

//generate templates
$head = new Template('head');
$head->addVar('title', $group->getName() . ' | Group | ' . APP_NAME);

$header = new Template('header');
$header->addVar('loggedIn', User::checkLoggedIn());

$groupTemplate = new Template('groups');
$groupTemplate->addVar('groups', array($group)); //so that it only shows the one group

$joinTemplate = new Template('groupJoinRequests');
$joinTemplate->addVar('group', $group);

$billsTemplate = new Template('bills');
$billsTemplate->addVar('bills', $group->getBills());

$newBillTemplate = new Template('newBillForm');
$newBillTemplate->addVars(array(
	'user' => new User(Cookie::get('id')),
	'groupId' => Get::find('groupId')
));

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
		
		<h2>Group: <?php echo __($group->getName()); ?></h2>
		
		<main>
			<h3>Bills</h3>
			
			<?php echo $billsTemplate->html(); ?>
			<?php echo $newBillTemplate->html(); ?>
		</main>
		
		<aside>
			<h3>Group Members:</h3>
			
			<p id="groupResponse" class="response">
				<?php echo $groupResponse; ?>
			</p>
			
			<p id="groupError" class="response">
				<?php echo $groupErrors; ?>
			</p>
			
			<div id="groups">
				<?php echo $groupTemplate->html(); ?>
			</div>
			
			<h3>Join Requests</h3>
			
			<?php echo $joinTemplate->html(); ?>
		</aside>
	</body>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
</html>