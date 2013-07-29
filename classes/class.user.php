<?php

class User{

	private $db;
	private $username;
	private $email;
	private $is_logged = false;
	private $msg = array();
	private $error = array();

	// Create a new user object

	public function __construct($db) {

		session_start();

		$this->db = $db;

		if (isset($_GET['logout'])) {

			$this->logout();

		} elseif (isset($_COOKIE['username']) || (!empty($_SESSION['username']) && $_SESSION['is_logged']))  {

			$this->is_logged = true;
			$this->username = $_SESSION['username'];
			$this->email = $_SESSION['email'];
			
			if (isset($_POST['update'])) {

				$this->update($this->username);

			} elseif (isset($_POST['register'])) {

				$this->register();

			}

		} elseif (isset($_POST['login'])) {

			$this->login();
			
		}

		return $this;
	}

	// Get username

	public function get_username() { return $this->username; }

	// Get email 

	public function get_email() { return $this->email; }

	// Check if the user is logged

	public function is_logged() { return $this->is_logged; }

	// Get info messages

	public function get_info() { return $this->msg; }

	// Get errors

	public function get_error() { return $this->error; }

	// Login

	public function login() {

		if (!empty($_POST['username']) && !empty($_POST['password'])) {
			
			$this->username = $this->db->real_escape_string($_POST['username']);
			$this->password = sha1($this->db->real_escape_string($_POST['password']));
			
			if ($row = $this->verify_password()) {

				session_regenerate_id(true);
				$_SESSION['id'] = session_id();
				$_SESSION['username'] = $this->username;
				$_SESSION['email'] = $row->email;
				$_SESSION['is_logged'] = true;
				$this->is_logged = true;
				// Set a cookie that expires in one week
				if (isset($_POST['remember']))
					setcookie('username', $this->username, time() + 604800);	
				// To avoid resending the form on refreshing
				header('Location: ' . $_SERVER['REQUEST_URI']);
				exit();

			} else $this->error[] = 'Wrong user or password.';

		} elseif (empty($_POST['username'])) {

			$this->error[] = 'Username field was empty.';

		} elseif (empty($_POST['password'])) {

			$this->error[] = 'Password field was empty.';
		}

	}

	// Check if username and password match

	private function verify_password() {

		$query  = 'SELECT * FROM users '
				. 'WHERE user = "' . $this->username . '" '
				. 'AND password = "' . $this->password . '"';

		return ($this->db->query($query)->fetch_object());

	}

	// Logout function

	public function logout() {

		session_unset();
		session_destroy();
		$this->is_logged = false;
		setcookie('username', '', time()-3600);
		header('Location: index.php');
		exit();

	}

	// Register a new user

	public function register() {

		if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['confirm'])) {

			if ($_POST['password'] == $_POST['confirm']) {

				$username = $this->db->real_escape_string($_POST['username']);
				$password = sha1($this->db->real_escape_string($_POST['password']));
				$email = $this->db->real_escape_string($_POST['email']);

				$query  = 'INSERT INTO users (user, password, email) '
						. 'VALUES ("' . $username . '", "' . $password . '", "' . $email . '")';

				if ($this->db->query($query)) {

					$this->msg[] = 'User created.';
					// To avoid resending the form on refreshing
					$_SESSION['msg'] = $this->msg;
					header('Location: ' . $_SERVER['REQUEST_URI']);
					exit();

				} else $this->error[] = 'Username already exists.';

			} else $this->error[] = 'Password doesn\'t match.';

		} elseif (empty($_POST['username'])) {

			$this->error[] = 'Username field was empty.';

		} elseif (empty($_POST['password'])) {

			$this->error[] = 'Password field was empty.';
		
		} elseif (empty($_POST['confirm'])) {

			$this->error[] = 'You need to confirm the password.';
		}

	}

	// Update an existing user's password

	public function update($username) {

		if (!empty($_POST['email']) && $_POST['email'] !== $_POST['old_email']) {

			$this->email = $this->db->real_escape_string($_POST['email']);

			$query  = 'UPDATE users '
					. 'SET email = "' . $this->email . '" '
					. 'WHERE user = "' . $username . '"';

			if ($this->db->query($query)) $this->msg[] = 'Your email has been changed successfully.';
			else $this->error[] = 'Something went wrong. Please, try again later.';

		} elseif (!empty($_POST['email'])) $this->error[] = 'You must enter an email adress.';

		if (!empty($_POST['password']) && !empty($_POST['newpassword1']) && !empty($_POST['newpassword2'])) {

			if ($_POST['newpassword1'] == $_POST['newpassword2']) {

				$this->password = sha1($this->db->real_escape_string($_POST['password']));
				
				if ($this->verify_password()) {

					$this->password = sha1($this->db->real_escape_string($_POST['newpassword1']));

					$query  = 'UPDATE users '
							. 'SET password = "' . $this->password . '" '
							. 'WHERE user = "' . $username . '"';

					if ($this->db->query($query)) $this->msg[] = 'Your password has been changed successfully.';
					else $this->error[] = 'Something went wrong. Please, try again later.';

				} else $this->error[] = 'Wrong password.';

			} else $this->error[] = 'Password doesn\'t match.';

		} elseif (empty($_POST['password']) && (!empty($_POST['newpassword1']) || !empty($_POST['newpassword2']))) {

			$this->error[] = 'Old password field was empty.';
		
		} elseif (!empty($_POST['password']) && empty($_POST['newpassword1'])) {

			$this->error[] = 'New password field was empty.';
		
		} elseif (!empty($_POST['password']) && empty($_POST['newpassword2'])) {

			$this->error[] = 'You must enter the new password again.';
		}

		// To avoid resending the form on refreshing
		$_SESSION['msg'] = $this->msg;
		$_SESSION['error'] = $this->error;
		header('Location: ' . $_SERVER['REQUEST_URI']);
		exit();
	
	}

	// Delete an existing user

	public function delete($user) {
		$query = 'DELETE FROM users WHERE user = "' . $user . '"';
		return ($this->db->query($query));
	}

	// Get info about an user

	public function get_user_info($user) {
		$query = 'SELECT user, password, email FROM users WHERE user = "' . $user . '"';
		$result = $this->db->query($query);
		return $result->fetch_object();	
	}

	// Get all the existing users

	public function get_users() {

		$query = 'SELECT user, password, email FROM users';
		
		return ($this->db->query($query));
	}

	// Print info messages in screen

	public function display_info() {
		foreach ($this->msg as $msg) {
			echo '<p class="msg">' . $msg . '</p>';
		}
	}

	// Print errors in screen

	public function display_errors() {
		foreach ($this->error as $error) {
			echo '<p class="error">' . $error . '</p>';
		}
	}

	// Create a new  db to start with

	private function create_db() {

		$query 	= 'CREATE TABLE users ('
				. 'user VARCHAR(75) NOT NULL, '
				. 'password VARCHAR(75) NOT NULL, '
				. 'email VARCHAR(150) NULL, '
				. 'PRIMARY KEY (user) '
				. ') ENGINE=MyISAM COLLATE=utf8_general_ci';

		return ($this->db->query($query));

	}
	
	// Drop an existing db

	private function drop_db() {

		$query 	= 'DROP TABLE IF EXISTS users ';

	}

} 

?>