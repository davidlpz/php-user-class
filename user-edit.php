<form class="user" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off">
	<?php $user->display_info(); ?>
	<?php $user->display_errors(); ?>
	<input type="hidden" name="old_email" <?php echo 'value="' . $user->get_email() . '"'; ?> />
	<label for="email">E-mail:</label>
	<input type="email" id="email" name="email" placeholder="email" <?php echo 'value="' . $user->get_email() . '"'; ?> />
	<label for="password">Old password:</label>
	<input type="password" id="password" name="password" placeholder="old password" />
	<label for="newpassword1">New password:</label>
	<input type="password" id="newpassword1" name="newpassword1" placeholder="new password" />
	<label for="newpassword2">New password, again:</label>
	<input type="password" id="newpassword2" name="newpassword2" placeholder="new password, again" />
	<input type="submit" id="button-update" name="update" value="Update">
</form>