<?php
require_once 'php/all.php';

Session::start();
$errors = new Errors();

//check logged in
$loggedIn = User::checkLoggedIn();

$errors->test($loggedIn, 'Unauthorised access, please log in.');
$errors->handleRedirect('unauthorisedErrors', URL . 'loginregister.php');

//generate data
$user = new User(Cookie::get('id'));

//generate templates
$head = new Template('head');
$head->addVars(array(
	'title' => 'Dashboard | ' . APP_NAME
));

$header = new Template('header');
$header->addVar('loggedIn', $loggedIn);

$billAdder = new Template('newBillForm');
$billAdder->addVar('user', $user);

$groupBillOverview = new Template('groupBillOverview');
$groupBillOverview->addVar('user', $user);
?>

<!DOCTYPE html>
<html>
	<?php echo $head->html(); ?>
	<body>
		<?php echo $header->html(); ?>
		
		<main id="dashboard">
			<h2>Dashboard</h2>
			
			<?php echo $groupBillOverview->html(); ?>
			
			
		</main>
		
		<aside>
			<?php echo $billAdder->html(); ?>
		</aside>
		
		
		
		
		
		
		
		
		
		
		
		
		
	</body>
</html>