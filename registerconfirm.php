<?php
require_once 'php/all.php';

$head = new Template('head');
$head->addVars(array(
	'title' => 'Login & Register | ' . APP_NAME
));

$header = new Template('header');
$header->addVar('loggedIn', User::checkLoggedIn());
?>

<!DOCTYPE html>
<html>
	<?php echo $head->html(); ?>
	<body>
		<?php echo $header->html(); ?>
		
		<main>
			<h2>Registration Email Sent</h2>
			<p>Please check your email for a link to activate your account.</p>
		</main>
	</body>
</html>