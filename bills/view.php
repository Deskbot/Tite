<?php
//GET - billId

require_once '../php/all.php';

Session::start();
$errors = new Errors();

//check logged in
$loggedIn = User::checkLoggedIn();
$errors->test($loggedIn, 'Unauthorised access, please log in.');
$errors->handleRedirect('unauthorisedErrors', URL . 'loginregister.php');

//validate input
$groupErrors = new Errors();
if (!Get::hasData('billId')) {
	echo 'Bill not specified.';
	die;
}

//generate data
$bill = new Bill(Get::find('billId'));
$group = $bill->getGroup();

$amount = $bill->getAmount();
$amountStr = Payment::amountToString($amount);

$userPayment = Payment::getByBillAndUser($bill->getId(), Cookie::get('id'));
$userHasPayed = false;

if ($userPayment === null) {
	$owedStr = 'You owe ' . $amountStr;
	
} else if ($amount === $userPayment->getAmountPaid()) {
	$owedStr = 'You have fully paid this bill.';
	$userHasPayed = true;
	
} else {
	$owedStr = 'You have paid ' . Payment::amountToString($userPayment->getAmountPaid()) . ' of ' . $amountStr;
}

//generate templates
$head = new Template('head');
$head->addVar('title', $bill->getName() . ' | Bill | ' . APP_NAME);

$header = new Template('header');
$header->addVar('loggedIn', User::checkLoggedIn());

$paymentFormTemplate = new Template('makePaymentForm');
$paymentFormTemplate->addVars(array(
	'maxAmount' => $bill->get('amount') / 100,
	'billId' => $bill->getId()
));

$paymentsTemplate = new Template('payments');
$paymentsTemplate->addVars(array(
	'payments' => $bill->getPayments(),
	'bill' => $bill
));

//get response data
$paymentResponse = "";
if (Session::hasData('paymentResponse')) {
	$paymentResponse = Session::get('paymentResponse')->toReadable();
	Session::remove('paymentResponse');
}

//get error data
$paymentErrors = "";
if (Session::hasData('paymentErrors')) {
	$paymentErrors = Session::get('paymentErrors')->toReadable();
	Session::remove('paymentErrors');
}
?>

<!DOCTYPE html>
<html>
	<?php echo $head->html(); ?>
	<body id="settings">
		<?php echo $header->html(); ?>
		
		<h2>Bill: <?php echo $bill->getName(); ?></h2>
		
		<main>
			<p class="payment-description"><?php echo $bill->getDescription(); ?></p>
			<p><span class="field">From Group:</span> <a href="<?php echo $bill->getGroup()->getUrl(); ?>"><?php echo $bill->getGroup()->getName(); ?></a></p>
			<p><span class="field">Payment Per Person:</span> <?php echo Payment::amountToString($bill->getAmount()); ?>.</p>
			<p><span class="field">Date Created:</span> <?php echo format_sql_date($bill->get('dateCreated')); ?>.</p>
			<p><span class="field">Payment Deadline:</span> <?php echo format_sql_date($bill->get('deadline')); ?>.</p>
			<p><span class="field">Money unaccounted for:</span> <?php echo $bill->getLeftover() === null ? '&pound;0.00' : Payment::amountToString($bill->getLeftover()); ?></p>
			<p><?php echo $owedStr; ?></p>
		
			<h3>Payments</h3>
			
			<p id="paymentResponse" class="response">
				<?php echo $paymentResponse; ?>
			</p>
			
			<p id="paymentErrors" class="response">
				<?php echo $paymentErrors; ?>
			</p>
			
			<?php echo $paymentsTemplate->html(); ?>
			
			
		</main>
		
		<aside>
			<?php echo !$userHasPayed ? $paymentFormTemplate->html() : ''; ?>
		</aside>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	</body>
</html>