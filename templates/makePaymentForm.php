<?php // maxAmount, billId ?>

<h3>Make Payment</h3>
<form class="no-ajax" action="/request/makePayment.php" method="POST">
	<p>Amount: <span class="money-display">&pound;</span><input name="amount" type="number" min="0" max="<?php echo $maxAmount; ?>" step="0.01" class="money"></p>
	<input name="billId" type="hidden" value="<?php echo $billId; ?>">
	<input type="submit">
</form>