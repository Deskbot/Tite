<?php
require_once 'php/all.php';

Session::start();
$errors = new Errors();

//check logged in
$loggedIn = User::checkLoggedIn();
$errors->test($loggedIn, 'Unauthorised access, please log in.');
$errors->handleRedirect('unauthorisedErrors', URL . 'loginregister.php');

//generate templates
$head = new Template('head');
$head->addVars(array(
	'title' => 'Settings | ' . APP_NAME
));

$header = new Template('header');
$header->addVar('loggedIn', User::checkLoggedIn());

//get settings data
$settingsQuery = "SELECT name, email FROM Users WHERE id=?";
$db = Database::get();
$settingsResult = $db->preparedQuery($settingsQuery, array($_COOKIE['id']));
$settingsArr = $settingsResult->fetchArray(SQLITE3_ASSOC);

//get error data
$personalErrors = "";
$securityErrors = "";
$personalSuccess = "";
$securitySuccess = "";
if (Session::hasData('personalErrors')) {
	$personalErrors = Session::get('personalErrors')->toReadable();
	Session::remove('personalErrors');
}
if (Session::hasData('securityErrors')) {
	$securityErrors = Session::get('securityErrors')->toReadable();
	Session::remove('securityErrors');
}
if (Session::hasData('personalSettingsResponse')) {
	$personalSuccess = Session::get('personalSettingsResponse')->toReadable();
	Session::remove('personalSettingsResponse');
}
if (Session::hasData('securitySettingsResponse')) {
	$securitySuccess = Session::get('securitySettingsResponse')->toReadable();
	Session::remove('securitySettingsResponse');
}
?>

<!DOCTYPE html>
<html>
	<?php echo $head->html(); ?>
	<body id="settings">
		<?php echo $header->html(); ?>
		
		<h2>Settings</h2>
		
		<main>
			<h3>Change Personal Details</h3>
			<form id="change-personal" class="ajax" action="request/changePersonalSettings.php" method="POST">
				<p class="success" id="personalSettingsResponse">
					<?php echo $personalSuccess; ?>
				</p>
				<p class="errors" id="personalErrors">
					<?php echo $personalErrors; ?>
				</p>
				<input name="name" title="name" placeholder="Name" required type="text" value="<?php echo $settingsArr['name']; ?>">
				<input name="email" title="email" placeholder="Email" required type="text" value="<?php echo $settingsArr['email']; ?>">
				<input type="submit">
			</form>
			
			<h3>Change Security</h3>
			<form id="change-security" class="ajax" action="request/changeSecuritySettings.php" method="POST">
				<p class="success" id="securitySettingsResponse">
					<?php echo $securitySuccess; ?>
				</p>
				<p class="errors" id="securityErrors">
					<?php echo $securityErrors; ?>
				</p>
				<input name="passwordOld" placeholder="Old Password" required type="password">
				<input name="password1" placeholder="New Password" required type="password">
				<input name="password2" placeholder="Repeat New Password" required type="password">
				<input type="submit">
			</form>
		</main>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	</body>
</html>