<?php // $user, $groupId,  (null is all/any id) ?>
<?php
$billErrors = "";
if (Session::hasData('billErrors')) {
	$billErrors = Session::get('billErrors')->toReadable();
	Session::remove('billErrors');
}
$billRepsonse = "";
if (Session::hasData('billRepsonse')) {
	$billRepsonse = Session::get('billRepsonse')->toReadable();
	Session::remove('billRepsonse');
}
?>
<div id="new-bill">
	<h3>Add New Bill</h3>
	
	<p id="billResponse" class="response">
		<?php echo __($billRepsonse); ?>
	</p>
	
	<p id="billErrors" class="errors">
		<?php echo __($billErrors); ?>
	</p>
	
	<form action="/request/addBill.php" method="POST">
		<p>Bill Name: <input name="name" type="text"></p>
		<p>Amount: <span class="money-display">&pound;</span><input name="amount" type="number" min="0" step="0.01" class="money"></p>
		<p>Description: <textarea name="description"></textarea></p>
		<p>Deadline: (leave blank for no deadline)<input name="deadline" type="date"></p>
		<p>
			Group: 
			<select name="groupId">
				<?php
				$optionStr = '<option value="[groupId]" [selected]>[groupName]</option>';
				$optionTemplate = new HoleFiller($optionStr, array('[groupId]','[selected]','[groupName]'));
				
				$checkSelect = isset($groupId);
				foreach ($user->getGroups() as $group) {
					if ($checkSelect && $group->getId() === $groupId) {
						$selectStr = 'selected="selected"';
						$checkSelect = false;
					} else {
						$selectStr = '';
					}
					
					$optionElem = $optionTemplate->insert(array(
						'[groupId]' => $group->getId(),
						'[selected]' => $selectStr,
						'[groupName]' => $group->get('name')
					));
					
					echo $optionElem;
				}
				?>
			</select>
		</p>
		<input name="returnAddress" type="hidden" value="<?php echo $_SERVER['REQUEST_URI']; ?>"><br>
		<input type="submit">
	</form>
</div>
