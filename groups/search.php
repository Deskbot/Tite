<?php
// GET - name

require_once '../php/all.php';

Session::start();
$errors = new Errors();

//check logged in
$loggedIn = User::checkLoggedIn();
$errors->test($loggedIn, 'Unauthorised access, please log in.');
$errors->handleRedirect('unauthorisedErrors', URL . 'loginregister.php');

//generate data
$groupsFound = array(); //needed for later to see if any found
if (Get::hasData('name')) {
	$user = new User(Cookie::get('id'));
	$groupsFound = $user->findMatchingGroupsNotJoined(Get::find('name'));
}

//generate templates
$head = new Template('head');
$head->addVar('title', 'Group Search | ' . APP_NAME);

$header = new Template('header');
$header->addVar('loggedIn', User::checkLoggedIn());

if (count($groupsFound) !== 0) {
	$searchDisplay = new Template('groupSearch');
	$searchDisplay->addVar('groups', $groupsFound);
}

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
		
		<h2>Group Search</h2>
		
		<main>
			<p id="groupResponse" class="response">
				<?php echo $groupResponse; ?>
			</p>
			
			<p id="groupError" class="response">
				<?php echo $groupErrors; ?>
			</p>
			
			<h3>Group Search</h3>
			<form action="/groups/search.php" method="GET" class="inline">
				<input name="name" placeholder="Group Name" required type="text"><br>
				<input type="submit">
			</form>
			
			<?php echo isset($searchDisplay) ? $searchDisplay->html() : '';?>
		</main>
	</body>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
</html>