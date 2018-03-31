<?php // group

$singleBillTemplate = new Template('singleBill');

?>

<div id="bills">
	
	<?php
	foreach ($bills as $bill) {
		$singleBillTemplate->addVar('bill', $bill);
		
		echo $singleBillTemplate->html();
	}
	?>
	
</div>
