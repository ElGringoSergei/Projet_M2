<?php
	session_start();
	if(isset($_POST['disconnect'])) {
		setcookie(session_id(), "", time() - 3600);
		session_unset();
		session_destroy();
		session_write_close();
		header("Location: ../login.php");
	}
	if(isset($_POST['reserver'])) {
		header("Location: ../reserver.php");
	}
?>
