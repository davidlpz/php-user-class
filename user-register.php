<form method="POST" class="user" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off">
	<?php $user->display_info(); ?>
	<?php $user->display_errors(); ?>
	<label for="email">E-mail:</label>
	<input type="email" id="email" name="email" placeholder="email" autocomplete="off" />
	<label for="username">Username:</label>
	<input type="text" id="username" name="username" placeholder="username" autocomplete="off" />
	<label for="password">Password:</label>
	<input type="password" id="password" name="password" placeholder="password" autocomplete="off" />
	<label for="confirm">Password:</label>
	<input type="password" id="confirm" name="confirm" placeholder="repeat password" autocomplete="off" />
	<input id="button-register" type="submit" name="register" value="Register">
</form>