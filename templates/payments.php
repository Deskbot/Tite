<?php // payments, $bill ?>

<ul class="payments">
	
	<?php foreach ($payments as $payment): ?>
	
	<li class="payment">
		
		<span><?php echo __($payment->getUser()->getName()); ?></span>
		<span><?php echo $payment->getAmountPaidAsString(); ?></span>
		<div class="bar" data-paid="<?php echo $payment->getAmountPaid(); ?>" data-total="<?php echo $bill->getAmount(); ?>"></div>
	</li>
	
	<?php endforeach; ?>
	
</ul>