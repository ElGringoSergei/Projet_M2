<?php
    session_start();
    
    include 'config.php';
    
    if(isset($_POST['submit-create'])) {
	if (isset($_POST['username']) && isset($_POST['account-name']) && isset($_POST['account-surname']) && isset($_POST['Email1']) && isset($_POST['Password1']) && isset($_POST['Password2'])) {
        	$uuname = $_POST['username'];
        	$uname = $_POST['account-name'];
        	$usurname = $_POST['account-surname'];
        	$umail = $_POST['Email1'];
        	$upass1 = $_POST['Password1'];
		$upass2 = $_POST['Password2'];
		$long = strlen($uuname);
    
  
    		$conn = new mysqli($servername, $username, $password, $dbname);

    		if ($conn->connect_error) {
        		die("Connection failed: " . $conn->connect_error);
    		}

    		if(strlen($uuname) == 0 || strlen($uname) == 0 || strlen($usurname) == 0 || strlen($umail) == 0 || strlen($upass1) == 0 || strlen($upass2) == 0) {
        		header("Location: ../sign_in.php?error=Vous n'avez pas rempli tous les champs");
    		} else {

    			if($upass1 != $upass2) {
        			header("Location: ../sign_in.php?error=Les mots de passes ne correspondent pas");
    			} else {
				$upass1 = hash('sha512', $upass1);
	    			$sql = $conn->prepare("SELECT * FROM accounts WHERE username=? OR email=?;");
	    			$sql->bind_param("ss", $uuname, $umail);

    				$sql->execute();
    				$result = $sql->get_result();
    				$sql->close();
    				$conn->close();

    				if(mysqli_num_rows($result) != 0) {
        				header("Location: ../sign_in.php?error=Vous possédez déjà un compte avec ces coordonnées");
    				} else {
		
    					$conn1 = new mysqli($servername, $username, $password, $dbname);
	
    					if ($conn1->connect_error) {
        					die("Connection failed: " . $conn1>connect_error);
    					}
	
    					$stmt = $conn1->prepare("INSERT INTO accounts (username, email, name, surname, password) VALUES (?, ?, ?, ?, ?);");
    					$stmt->bind_param("sssss", $uuname, $umail, $uname, $usurname, $upass1);

    					$stmt->execute();
	
    					echo "Valeurs ajoutées à la base de données";
		
    

					$stmt->close();
					$conn1->close();
					shell_exec('mkdir /var/www/html/uploads/' . $uuname . '/');
    					header("Location: ../login.php?success=Votre compte a bien été créé");
    				}
    			}
    		}
	} else {
		header("Location: ../sign_in.php?error=Vous n'avez pas rempli tous les champs");
	}
    }
    

    
?>
