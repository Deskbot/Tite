<?php // bill ?>
<!DOCTYPE html>
<html>
	<body>
		<h1>New Bill</h1>
		<?php
		$singleBill = new Template('singleBill');
		$singleBill->addVar('bill', $bill);
		echo $singleBill->html();
		?>
	</body>
</html>