<?php

  include 'config.php';
  $timeout = 600;
  
  session_start();
  
  $s_name = session_name();
  $_SESSION['past_u_co'] = '';


  if(isset($_POST['submit']))
  {
    $u_co = $_POST['uname-connexion'];
    $p_co = $_POST['password-connexion'];
  }

  
  $conn = mysqli_connect($servername, $username, $password, $dbname);
  $con = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  if(empty($u_co)) {
    header("Location: ../login.php?error=Vous n'avez pas rentré d'identifiant");
  } 
  else if (empty($p_co)) {
    $_SESSION['past_u_co'] = $u_co;
    header("Location: ../login.php?error=Vous n'avez pas rentré de mot de passe");
  } else {
  
  $check_req = "SELECT * FROM attempts WHERE uname='$u_co'";
  $check_res = mysqli_query($conn, $check_req);
  if (mysqli_num_rows($check_res) != 0) {
  	$check_time = mysqli_fetch_array($check_res)['last_time'];
  	$check_count = mysqli_fetch_array($check_res)['value'];
  } else {
  	$check_time = 0;
  	$check_count = 0;
  }

  


  #$stmt = "SELECT * FROM accounts WHERE username='$u_co' AND password='$p_co'";

  #$result = mysqli_query($conn, $stmt);
  $p_co = hash('sha512', $p_co);
  
  $stmt = $con->prepare("SELECT * FROM accounts WHERE username=? AND password=?");
  $stmt->bind_param("ss", $u_co, $p_co);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->fetch();
  $stmt->close();


  if ((mysqli_num_rows($result) == 1 && ($check_count < 5)) || (mysqli_num_rows($result) == 1 && (($check_time + 30) < time()))) {
    $row = mysqli_fetch_assoc($result);
    #if ($row['username'] == $uname_co && $row['password'] == $password_co) {
      $_SESSION['username'] = $row['username'];
      $_SESSION['name'] = $row['name'];
      $_SESSION['id'] = $row['id'];
      $_SESSION['surname'] = $row['surname'];
      $_SESSION['email'] = $row['email'];
      $_SESSION['sname'] = $s_name;
      $_SESSION['timeout'] = $timeout;
      $_SESSION['expire'] = time() + $timeout;
      $id_session = $_SESSION['id'];
      #$_COOKIE['name'] = $s_name;
      #$_COOKIE['timeout'] = $timeout;
      #$_COOKIE['expire'] = time() + $timeout;
    #}
    $_SESSION['past_u_co'] = $u_co;
    if ($check_time == 0) {
    	$put = $con->prepare("INSERT INTO attempts (uname, value, last_time) VALUES (?, 0, ?)");
	$put->bind_param("ss", $u_co, time());
	$put->execute();
	$put->close();
	$con->close();
	$_SESSION['counter'] = 1; 
    } else {
    	$_SESSION['block'] = time();
	$_SESSION['counter'] = 0;
  $current_time = time();
	$sql = $con->prepare("UPDATE attempts SET value=0, last_time=? WHERE uname=?");
	$sql->bind_param("ss", $current_time, $u_co);
	$sql->execute();
	$sql->close();
	$con->close();
    }
    
    header("Location: ../myaccount.php?id=" . $id_session);
    
    

  
  } else {
  	if ($u_co == $_SESSION['past_u_co']) {
	
		$_SESSION['past_u_co'] = $u_co;
		$req = $con->prepare("SELECT * FROM attempts WHERE uname=?");
		$req->bind_param("s", $u_co);
		$req->execute();
		$res_att = $req->get_result();
		$req->fetch();
		$req->close();
		if (mysqli_num_rows($res_att) == 1) {
			$row_att = $res_att->fetch_assoc();
			if (($row_att['last_time'] + 60) < time()) {
				$_SESSION['counter'] = 0;
				$sql = $con->prepare("UPDATE attempts SET value=0, last_time=? WHERE uname=?");
				$sql->bind_param("ss", time(), $u_co);
				$sql->execute();
				$sql->close();
				$con->close();
			}
			else if ($row_att['value'] < 4) {
				$new_value = $row_att['value'] + 1;
				$_SESSION['counter'] = $row_att['value']+1;	
				$sql = $con->prepare("UPDATE attempts SET value=?, last_time=? WHERE uname=?;");
				$sql->bind_param("sss", $new_value, time(), $u_co);
				$sql->execute();
				$sql->close();
				$con->close();
			} else{
				$_SESSION['block'] = time();
				$_SESSION['counter'] = 0;
				$sql = $con->prepare("UPDATE attempts SET value=0, last_time=? WHERE uname=?");
				$sql->bind_param("ss", time(), $u_co);
				$sql->execute();
				$sql->close();
				$con->close();
				
			}
		} else {
			
			$put = $con->prepare("INSERT INTO attempts (uname, value, last_time) VALUES (?, 1, ?)");
			$put->bind_param("ss", $u_co, time());
			$put->execute();
			$put->close();
			$con->close();
			$_SESSION['counter'] = 1; 	
		}
		
		
  }
    $_SESSION['past_u_co'] = $u_co;
    header("Location: ../login.php?error=Cette combinaison identifiant/mot de passe n'existe pas");
  }

  $conn->close();

 


}


?>
