<?php
	ini_set("session.cookie_httponly", True);
	ini_set("session.cookie_secure", True);
	ini_set("session.cookie_samesite", "Strict");
    include 'config.php';
	session_start();

	if(!isset($_POST['csrf'])) {
		session_unset();
		session_destroy();
		header("Location: ../login.php?error=CSRF détecté");
	  } else if($_POST['csrf'] != $_SESSION['csrf']) {
		session_unset();
		session_destroy();
		header("Location: ../login.php?error=CSRF détecté");
	  } else {
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
			shell_exec('curl -d "nom_personne=' . $_SESSION['username'] . '" -X POST http://10.5.0.4:5000/api/annuler_reservations_personne');
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
	}
?>
