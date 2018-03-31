<?php
//POST: amount, billId

require_once '../php/all.php';
define('BILL_URL', URL . (Post::hasData('billId') ? 'bills/view.php?billId=' . Post::get('billId') : 'dashboard.php'));

Session::start();

//check logged in
$loginErrors = new Errors();
$loginErrors->test(User::checkLoggedIn(), 'You must be logged in to create a group.');
$loginErrors->handleRedirect('loginErrors', URL . 'loginregister.php');

//validate input
$paymentErrors = new Errors();

$amountGiven = Post::hasData('amount');
$billIdGiven = Post::hasData('billId');

$paymentErrors->test($amountGiven, 'Amount not given.');
$paymentErrors->test($billIdGiven, 'Bill not specified.');
$paymentErrors->handleRedirect('paymentErrors', BILL_URL);

//test amount is numeric
$paymentErrors->test(is_numeric(Post::get('amount')), 'Amout given was not a number.');
$paymentErrors->handleRedirect('paymentErrors', BILL_URL);

//get current payment info
$currentPayment = Payment::getByBillAndUser(Post::get('billId'), Cookie::get('id'));
$paymentNeedsUpdate = $currentPayment !== null;
$currentAmountPaid = ($paymentNeedsUpdate ? $currentPayment->getAmountPaid() : 0);

$amountPaid = round(Post::get('amount'), 2) * 100 + $currentAmountPaid;

//check they don't go over the amount needed
$bill = new Bill(Post::get('billId'));
$paymentErrors->test($amountPaid <= $bill->getAmount(), 'That is more than the amount you are required to pay.');
$paymentErrors->handleRedirect('paymentErrors', BILL_URL);

//add payment to db
$db = Database::get();
if ($paymentNeedsUpdate) {
	$updateQuery = "UPDATE Payments SET amountPaid=?, datePaid=? WHERE billId=? AND userId=?";
	$db->preparedQuery($updateQuery, array($amountPaid, date(SQL_DATE_FORMAT), Post::get('billId'), Cookie::get('id')));
} else {
	$insertQuery = "INSERT INTO Payments (billId, userId, amountPaid, datePaid) VALUES (?,?,?,?)";
	$db->preparedQuery($insertQuery, array(Post::get('billId'), Cookie::get('id'), $amountPaid, date(SQL_DATE_FORMAT)));
}

switch ($db->lastError()) {
	//success
	case 0:
		break;
		
	//all other errors
	default:
		$paymentErrors->addOne($db->lastErrorString());
		break;
}
$paymentErrors->handleRedirect('paymentErrors', BILL_URL);

//check if bill is complete
$group = $bill->getGroup();
$memberCount = $group->countMembers();

$completeQuery = "SELECT COUNT(*) FROM Payments WHERE billId=? AND amountPaid=?";
$completeResult = $db->preparedQuery($completeQuery, array(Post::get('billId'), Post::get('amount')));
$completeResultArr = $completeResult->fetchArray(SQLITE3_NUM);

if ($completeResultArr[0] === $memberCount) {
	$setCompleteQuery = "UPDATE Bills SET complete=1 WHERE billId=?";
	$db->preparedQuery($setCompleteQuery, array(Post::get('billId')));
}

$paymentErrors->handleRedirect('paymentErrors', BILL_URL);

$response = new Response();
$response->addOne('Payment added.');
$response->handleRedirect('paymentResponse', BILL_URL);










