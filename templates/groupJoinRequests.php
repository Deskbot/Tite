<?php
//get error data
$joinErrors = "";
if (Session::hasData('joinErrors')) {
	$joinErrors = Session::get('joinErrors')->toReadable();
	Session::remove('joinErrors');
}
?>
<div id="join-requests">
	<p id="joinErrors" class="errors">
		<?php echo $joinErrors; ?>
	</p>
	<ul class="join-requests">
		<?php foreach ($group->getJoinRequests() as $joinId => $user): ?>
			
			<li>
				<?php echo __($user->getName()); ?> (<?php echo __($user->getEmail()); ?>)
				<form action="/request/acceptJoin.php" method="POST" class="inline accept-join-form">
					<input name="groupId" type="hidden" value="<?php echo $group->getId();?>">
					<input name="joinId" type="hidden" value="<?php echo $joinId;?>">
					<input type="submit" value="Accept">
				</form>
				<form action="/request/rejectJoin.php" method="POST" class="inline reject-join-form">
					<input name="groupId" type="hidden" value="<?php echo $group->getId();?>">
					<input name="joinId" type="hidden" value="<?php echo $joinId;?>">
					<input type="submit" value="Reject">
				</form>
			</li>
			
		<?php endforeach; ?>
	</ul>
</div>
