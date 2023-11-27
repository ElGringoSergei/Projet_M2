<?php
    ini_set("session.cookie_httponly", True);
    ini_set("session.cookie_secure", True);
    ini_set("session.cookie_samesite", "Strict");
    session_start();

    $token = bin2hex(random_bytes(16));
    $_SESSION['csrf'] = $token;

    if (isset($_SESSION['id'])) {
        if ($_SESSION['expire'] < time()) {
            header("Location: ../login.php?error=Session expirée");
        } else {
            $_SESSION['expire'] = time() + $_SESSION['timeout'];
        }
    } else {
        header("Location: ../login.php?error=Session expirée");
    }
    
    $s_name = session_name();
    if(isset($_GET['id'])) {
        include './php/config.php';
        $id = $_SESSION["id"];

	$conn = new mysqli($servername, $username, $password, $dbname);
	if($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$stmt = $conn->prepare("SELECT * FROM accounts WHERE id=?");
	$stmt->bind_param("s", $id);
	$stmt->execute();
	$result = $stmt->get_result();

	if(mysqli_num_rows($result) == 1) {
		$row = $result->fetch_assoc();
		$username = $row['username'];
		$name = $row['name'];
		$surname = $row['surname'];
		$email = $row['email'];
	}
	$stmt->close();
	$conn->close();

    }
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="./index.js" defer></script>
</head>


<body>
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark" id="navbar-top">
        <div class="container-fluid">
            <label class="navbar-brand">Menu</label>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navbar-list">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="./index.php"
                            id="accueil-page">Accueil</a>
                    </li>
                    <li class="nav-itema">
                    <?php if($_SESSION['expire'] > time()) {
                        echo '<a class="nav-link active" href="#" id="se_connecter">Mon compte</a>';
                        }
                    else {
                      echo '<a class="nav-link" href="./login.php" id="se_connecter">Se connecter</a>';
                    }?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="content">
	<?php if(isset($_SESSION['sname']) && $_SESSION['expire'] > time()) {
	$_SESSION['expire'] = time() + $_SESSION['timeout'];
	echo '
	</div>
    <ul class="list-group forms-perso" style="text-align: center; margin-left: 30%; margin-right: 30%;">
    <li class="list-group-item">
    <h3>Mon compte</h3>
    </li>
    <li class="list-group-item">
    <div class="mb-2" style="text-align: center;"><text style="font-weight: bold;">Nom :</text> 
    ' . $surname . '
    <text style="font-weight: bold;">Prénom :</text> 
    ' . $name . '</div>
   
</li>
        <li class="list-group-item">
            <div class="mb-2" style="text-align: center;"><text style="font-weight: bold">Identifiant :</text>
            ' . $username . '
            <text style="font-weight: bold;">E-mail :</text>
            ' . $email . '</div>
            
        </li>
       
    </ul>
    
    <form method="post" action="./php/disconnect.php">
    <input type="hidden" name="csrf" value="' . $token . '">  
    <div class="d-flex flex-row justify-content-between align-items-start forms-perso3">
	<div class="p-2"><button type="submit" class="btn btn-outline-danger" name="disconnect">Se déconnecter</button></div>
	<div class="p-2"><button type="submit" class="btn btn-outline-primary" name="reserver">Réserver des ressources</button></div>
    <div class="p-2"><button type="submit" class="btn btn-outline-primary" name="upload">Importer des fichiers</button></div>
    <div class="p-2"><button type="submit" class="btn btn-danger" name="delete_account" onclick="return confirm(`Êtes-vous sûr de vouloir supprimer votre compte ?`); return false;">Supprimer ce compte</button></div>

    </div></form>';
    }
    else if(isset($_SESSION['sname']) && $_SESSION['expire'] < time()) {
      session_unset();
      session_destroy();
      header("Location: ./login.php?error=Session expirée");
    } else {
	header("Location: ./login.php?error=Session expirée");
    }
    ?>
    </div>
</body>

</html>
