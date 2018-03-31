<?php
// !!! needs doing

require_once 'php/all.php';

//redirect if logged in
if (User::checkLoggedIn()) {
	redirect(URL . 'dashboard.php');
}

//generate templates
$head = new Template('head');
$head->addVars(array(
	'title' => 'Home | ' . APP_NAME
));

$header = new Template('header');
?>

<!DOCTYPE html>
<?php echo $head->html(); ?>
<body>
	<?php echo $header->html(); ?>
	
	<h2>Welcome</h2>
	
	<main>
		<p>The only place to share bills. For people who are a little bit tight.</p>
	</main>
</body>