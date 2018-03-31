<?php foreach ($groups as $group): ?>
	<div class="group-square">
		
		<?php if (isset($giveTitle) && $giveTitle): ?>
		
		<h3><a href="<?php echo $group->getUrl(); ?>"><?php echo __($group->getName()); ?></a></h3>
		
		<?php endif; ?>
		
		<!--<h5>Members:</h5>-->
		
		<ul>
			<?php foreach ($group->getUsers() as $user): ?>
				
				<li><?php echo __($user->getName()); ?></li>
				
			<?php endforeach; ?>
		</ul>
		
		<form action="/request/leaveGroup.php" method="POST" class="ajax inline leave-group-form">
			<input name="groupId" type="hidden" value="<?php echo $group->getId(); ?>">
			<input name="name" type="hidden" value="<?php echo $group->getName(); ?>">
			<input type="submit" value="Leave Group">
		</form>
	</div>
<?php endforeach; ?>