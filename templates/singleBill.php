<?php // bill, (showPayments)
$billId	= urldecode(__($bill->getId()));

$deadlineStr = $bill->getDeadline() === null ? '' : ' by ' . format_sql_date($bill->getDeadline()) . '.' ;
?>

<div class="bill">
	<h3><a href="/bills/view.php?billId=<?php echo $billId; ?>"><?php echo __($bill->getName()); ?></a></h3>
	
	<p>Each member is required to pay: <?php echo Payment::amountToString($bill->getAmount()); ?><?php echo $deadlineStr; ?></p>
	<p>Money unaccounted for: <?php echo $bill->getLeftover() === null ? '&pound;0.00' : Payment::amountToString($bill->getLeftover()); ?></p>
	
	<?php
	if (isset($showPayments) && $showPayments) {
		
		$paymentsTemplate = new Template('payments');
		$paymentsTemplate->addVar('payments', $bill->getPayments());
		
		echo $paymentsTemplate->html();
	}
	?>
	
</div>