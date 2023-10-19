<?php
    include 'config.php';
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
	if(isset($_POST['upload'])) {
		header("Location: ../file_upload.php");
	}
	if(isset($_POST['delete_account'])) {
		shell_exec('rm -r /var/www/html/uploads/' . $_SESSION['username'] . '/');
		session_destroy();
		header("Location: ../login.php?error=Le compte a bien été supprimé");
		$con = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$stmt = $con->prepare("DELETE FROM accounts WHERE username=?");
        $stmt->bind_param("s", $_SESSION['username']);
  		$stmt->execute();
  		$stmt->close();
		$con->close();
	}
?>
