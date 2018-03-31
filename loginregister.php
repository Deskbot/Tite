<?php
// !!! needs doing

require_once 'php/all.php';

Session::start();

if (User::checkLoggedIn()) {
	redirect(URL . 'dashboard.php');
}

//make templates
$head = new Template('head');
$head->addVars(array('poop' => 'hello', 'title' => 'Login & Register | ' . APP_NAME));

$header = new Template('header');
$header->addVar('isLoggedIn', true);

//page data
$email = Get::hasData('email') ? Get::find('email') : '';

//error data
$signupResponse = "";
$unauthorisedErrors = "";
$loginErrors = "";
$registerErrors = "";
if (Session::hasData('signupResponse')) {
	$signupResponse = Session::get('signupResponse')->toReadable();
	Session::remove('signupResponse');
}if (Session::hasData('unauthorisedErrors')) {
	$unauthorisedErrors = Session::get('unauthorisedErrors')->toReadable();
	Session::remove('unauthorisedErrors');
}
if (Session::hasData('loginErrors')) {
	$loginErrors = Session::get('loginErrors')->toReadable();
	Session::remove('loginErrors');
}
if (Session::hasData('registerErrors')) {
	$registerErrors = Session::get('registerErrors')->toReadable();
	Session::remove('registerErrors');
}
?>
<!DOCTYPE html>
<html>
	<?php echo $head->html(); ?>
	<body>
		<?php echo $header->html(); ?>	
		
		<main>
			<p class="errors" id="unauthorisedErrors">
				<?php echo $unauthorisedErrors; ?>
			</p>
			
			<div class="half">
				<h2>Log In</h2>
				
				<p class="errors" id="loginErrors">
					<?php echo $loginErrors; ?>
				</p>
				
				<form action="/request/login.php" method="POST" class="no-ajax">
					<p><input name="email" placeholder="Email" required type="text" value="<?php echo $email; ?>"></p>
					<p><input name="password" placeholder="Password" required type="password"></p>
					<input type="submit">
				</form>
			</div>
			
			<div class="half">
				<h2>Register</h2>	
				
				<p class="errors" id="registerErrors">
					<?php echo $registerErrors; ?>
				</p>
				
				<form action="/request/register.php" method="POST" class="no-ajax">
					<p><input name="name" placeholder="Name" required type="text"></p>
					<p><input name="email" placeholder="Email" required type="text"></p>
					<p><input name="password1" placeholder="Enter Password" required type="password"></p>
					<p><input name="password2" placeholder="Re-Enter Password" required type="password"></p>
					<input type="submit">
				</form>
			</div>
		</main>
	</body>
</html>













