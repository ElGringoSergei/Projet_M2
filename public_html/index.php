<?php
    ini_set("session.cookie_httponly", True);
    ini_set("session.cookie_secure", True);
    ini_set("session.cookie_samesite", "Strict");
	session_start();
    if (isset($_SESSION['id'])) {
        if ($_SESSION['expire'] >= time()) {
            $_SESSION['expire'] = time() + $_SESSION['timeout'];
        }        
    }
	#if(isset($_SESSION['sname']) && $_SESSION['expire'] > time()) {
	#	$_SESSION['expire'] = time() + $_SESSION['timeout'];
	#} else {
	#	$_SESSION = [];
	#}
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
                        <a class="nav-link active" aria-current="page" href="#" id="accueil-page">Accueil</a>
                    </li>
                    <li class="nav-item">
		    <?php if(isset($_SESSION['id'])) { echo '<a class="nav-link" href="./myaccount.php?id=' . $_SESSION['id'] . '" id="se_connecter">Mon compte</a>';}
else { echo '<a class="nav-link" href="./login.php" id="se_connecter">Se connecter</a>';}?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="content">
        <ol class="list-group list-group-numbered forms-perso" id="page-accueil">
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto" id="login-link">
                    <a href="<?php if(isset($_SESSION['id'])) { echo "myaccount.php?id=" . $_SESSION['id']; } else { echo "login.php"; };?>" class="fw-bold nav-link nav-pages">Page de connexion</a>
                    Cliquez sur cette page pour vous connecter
                </div>

            </li>
        </ol>
    </div>
    <div>
    	<a href="shell.php">Test shell</a>
    </div>
</body>

</html>
