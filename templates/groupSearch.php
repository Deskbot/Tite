<h3>Search Results</h3>
<?php foreach ($groups as $group): ?>
	<div class="group-square">
		<h4><a href="<?php echo $group->getUrl(); ?>"><?php echo __($group->getName()); ?></a></h4>
		
		<ul>
			<?php foreach ($group->getUsers() as $user): ?>
				
				<li><?php echo __($user->getName()); ?></li>
				
			<?php endforeach; ?>
		</ul>
		
		<form class="inline ajax" action="/request/joinGroup.php" method="POST">
			<input name="groupId" type="hidden" value="<?php echo $group->getId(); ?>">
			<input name="name" type="hidden" value="<?php echo $group->getName(); ?>">
			<input type="submit" value="Request to join group">
		</form>
	</div>
<?php endforeach; ?>