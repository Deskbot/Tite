<?php // user
$groups = $user->getGroups();
$billTemplate = new Template('singleBill');

?>

<?php foreach ($groups as $group): ?>

<div class="group">
	<h3><a href="<?php echo $group->getUrl(); ?>"><?php echo __($group->getName()); ?></a></h3>
	
	<?php
	foreach ($group->getActiveBills() as $bill):

		$userPayment = Payment::getByBillAndUser($bill->getId(), $user->getId());
		
		if ($userPayment === null) {
			$owedStr = 'You owe ' . Payment::amountToString($bill->getAmount());
			
		} else if ($bill->getAmount() == $userPayment->getAmountPaid()) {
			$owedStr = 'You have fully paid this bill.';
			
		} else {
			$owedStr = 'You have paid ' . Payment::amountToString($userPayment->getAmountPaid()) . ' of ' . Payment::amountToString($bill->getAmount());
		}
		
		$usersPaid = $bill->getUsersPaid();
		$numTotalUsers = $group->countMembers();
		$numUsersPaid = count($usersPaid);
	?>
	
	<div class="bill">
		<h4><a href="<?php echo $bill->getUrl(); ?>"><?php echo __($bill->getName()); ?></a></h4>
		<p><?php echo $owedStr; ?></p>
		<p>Deadline: <?php echo format_sql_date($bill->getDeadline()); ?></p>
		<p><?php echo $numUsersPaid; ?> members fully paid out of <?php echo $numTotalUsers; ?>.</p>
	</div>
	
	<?php endforeach; ?>
</div>

<?php endforeach; ?>