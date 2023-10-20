<?php
    session_start();

    if(!(isset($_SESSION['id']))) {
        header("Location: login.php");
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
            <div class="mb-2" style="font-weight: bold; text-align: center;">Identifiant :</div>
            ' . $username . '
        </li>
        <li class="list-group-item">
            <div class="mb-2" style="font-weight: bold; text-align: center;">Nom : </div>
            ' . $surname . '
        </li>
        <li class="list-group-item">
            <div class="mb-2" style="font-weight: bold; text-align: center;">Prénom : </div>
            ' . $name . '
        </li>
        <li class="list-group-item">
            <div class="mb-2" style="font-weight: bold; text-align: center;">E-mail : </div>
            ' . $email . '
	</li>
    </ul>
    <form method="post" action="./php/disconnect.php">    
	<button type="submit" style="text-align: center; margin-left: 30%; margin-top: 1%;" class="btn btn-outline-danger" name="disconnect">Se déconnecter</button>
	<button type="submit" style="margin-left: 1%; margin-top: 1%;" class="btn btn-outline-primary" name="reserver">Réserver des ressources</button>
    <button type="submit" style="margin-left: 1%; margin-top: 1%;" class="btn btn-outline-primary" name="upload">Importer des fichiers</button>
    <button type="submit" style="margin-left: 1%; margin-top: 1%;" class="btn btn-danger" name="delete_account" onclick="return confirm(`Êtes-vous sûr de vouloir supprimer votre compte ?`); return false;">Supprimer ce compte</button>

    </form>';
    }
    else if(isset($_SESSION['sname']) && $_SESSION['expire'] < time()) {
      $_SESSION = [];
      header("Location: ./login.php?error=Session expirée");
    } else {
	header("Location: ./login.php?error=Session expirée");
    }
    ?>
    </div>
</body>

</html>
