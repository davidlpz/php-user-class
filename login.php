<form id="login" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<?php $user->display_errors(); ?>
	<label for="username">Username:</label>
	<input type="text" id="username" name="username" placeholder="user" />
	<label for="password">Password:</label>
	<input type="password" id="password" name="password" placeholder="password" />
	<input type="checkbox" id="remember" name="remember" />
	<label for="remember">Remember password?</label>
	<input id="button-login" type="submit" name="login" value="Log in">
</form>