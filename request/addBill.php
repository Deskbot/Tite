<?php
//POST: amount, name, description, groupId, deadline, hasDeadline, returnAddress

require_once '../php/all.php';
define('RETURN_ADDRESS', Post::hasData('returnAddress') ? Post::get('returnAddress') : URL . 'dashboard.php');

Session::start();

//check logged in
$loginErrors = new Errors();
$loginErrors->test(User::checkLoggedIn(), 'You must be logged in to create a group.');
$loginErrors->handleRedirect('loginErrors', URL . 'loginregister.php');

//validate input
$billErrors = new Errors();

$amountGiven = Post::hasData('amount');
$nameGiven = Post::hasData('name');
$descriptionGiven = Post::exists('description'); //is allowed to be blank
$groupIdGiven = Post::hasData('groupId');

$billErrors->test($amountGiven, 'Amount not given.');
$billErrors->test($nameGiven, 'Name not given.');
$billErrors->test($descriptionGiven, 'Description not given.');
$billErrors->test($groupIdGiven, 'Group not given.');
$billErrors->handleRedirect('billErrors', RETURN_ADDRESS);

if ($billErrors->exist()) {
	$billRepsonse = new Response();
	$billRepsonse->addData('amount', Post::get('amount'));
	$billRepsonse->addData('name', Post::get('name'));
	$billRepsonse->addData('description', Post::get('description'));
	$billRepsonse->addData('groupId', Post::get('groupId'));
	$billRepsonse->addData('deadline', Post::get('deadline'));
}

$billErrors->handleRedirect('billErrors', RETURN_ADDRESS);

//convert amount to pence
$amountIsNumeric = $billErrors->test(is_numeric(Post::get('amount')), 'Amout given was not a number.');
$billErrors->handleRedirect('billErrors', RETURN_ADDRESS);

//get number of group members
$db = Database::get();
$qtyQuery = "SELECT COUNT(*) FROM GroupsToUsers WHERE groupId=? AND accepted=1";
$qtyResult = $db->preparedQuery($qtyQuery, array(Post::get('groupId')));
$qtyResultArr = $qtyResult->fetchArray(SQLITE3_NUM);
$qty = $qtyResultArr[0];

$amount = floor(Post::get('amount') / $qty);
$leftover = Post::get('amount') % $amount;
$amountPerPerson = round((float) $amount, 2) * 100;

//add bill to db

$query = "INSERT INTO Bills (groupId, name, description, amount, leftover, deadline, dateCreated) VALUES (?,?,?,?,?,?,?)";
if (Post::hasData('deadline')) {
	$deadlineObj = DateTime::createFromFormat('Y-m-d', Post::get('deadline'));
	$deadline = $deadlineObj ? Post::get('deadline') : null;
} else {
	$deadline = null;
}

$db->preparedQuery($query, array(Post::get('groupId'), Post::get('name'), Post::get('description'), $amountPerPerson, $leftover, $deadline, date('Y-m-d')));

$addPaymentsQuery = "";

switch ($db->lastError()) {
	//success
	case 0:
		break;
		
	//all other errors
	default:
		$billErrors->addOne($db->lastErrorString());
		break;
}
$billErrors->handleRedirect('billErrors', RETURN_ADDRESS);

//email group members
$bill = new Bill($db->lastId());
$mailList = $bill->getGroup()->getUsers();
$mailer = Mail::get();

foreach ($mailList as $user) {
	$message = new EmailTemplate('newBill');
	$message->addVar('bill', $bill);
	$mailer->send($user->getEmail(), 'New bill in ' . $bill->getGroup()->getName(), $message->html());
}

$response = new Response();
$response->addOne('Bill added: ' . Post::get('name') . '.');
$response->handleRedirect('billRepsonse', RETURN_ADDRESS);










