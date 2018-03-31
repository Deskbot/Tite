<header>
	<h1><?php echo APP_NAME; ?></h1>
	<nav>
		<ul>
			<?php if (isset($loggedIn) && $loggedIn): ?>
			
			<li><a href="/dashboard.php">Dashboard</a></li>
			<li><a href="/groups/index.php">My Groups</a></li>
			<li><a href="/groups/search.php">Group Search</a></li>
			<li><a href="/settings.php">Settings</a></li>
			<li><a href="/request/logout.php">Log Out</a></li>
			
			<?php else: ?>
			
			<li><a href="/index.php">Home</a></li>
			<li><a href="/loginregister.php">Login &amp; Register</a></li>
			
			<?php endif; ?>
		</ul>
	</nav>
</header>