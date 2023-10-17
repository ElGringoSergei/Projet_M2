<?php
	session_start();
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

<style>
    body {
        font-family: Arial, sans-serif; /* Police de caractères */
        margin: 0; /* Supprimer les marges par défaut */
        padding: 0; /* Supprimer le padding par défaut */
    }

    /* Style pour le formulaire */
    .forms-perso {
        max-width: 400px; /* Largeur maximale du formulaire */
        margin: 0 auto; /* Centrer le formulaire horizontalement */
        background-color: #fff; /* Couleur de fond du formulaire */
        padding: 20px; /* Espacement intérieur du formulaire */
        border-radius: 10px; /* Coins arrondis du formulaire */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Ombre légère */
    }

    /* Style pour le bouton Valider */
    .btn-primary {
        background-color: #007bff; /* Couleur de fond du bouton */
        color: #fff; /* Couleur du texte du bouton */
        border: none; /* Supprimer la bordure du bouton */
    }

    /* Style pour la barre de navigation */
    #navbar-top {
        background-color: #343a40; /* Couleur de fond de la barre de navigation */
        color: #fff; /* Couleur du texte de la barre de navigation */
    }

</style>

<body>
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark" id="navbar-top">
        <div class="container-fluid">
            <label class="navbar-brand">Page de connexion</label>
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
                        <a class="nav-link active" href="#" id="se_connecter">Se connecter</a>
                    </li>
                </ul>
                <form class="d-flex" role="search" id="search-bar" method="get" action="./php/searchbar.php">
                    <input class="form-control me-2" type="search" name="search" placeholder="Votre recherche" id="search"
                        aria-label="Search">
                    <button class="btn btn-outline-success" type="submit" name="submit-search">Rechercher</button>
                </form>
            </div>
        </div>
    </nav>
    <br>
    <div id="content">
         <form class="forms-perso" method="post" id="connexion" action="./php/accounts.php">
        <div>
        <p class="success"><?php if(isset($_GET["success"]) && ($_GET["success"] == "Votre compte a bien été créé")) echo $_GET["success"];?></p>
	<p class="error"><?php if(isset($_GET["error"]) && ($_GET["error"] == "Vous n'avez pas rentré d'identifiant" || $_GET["error"] == "Vous n'avez pas rentré de mot de passe" || $_GET["error"] == "Cette combinaison identifiant/mot de passe n'existe pas" || $_GET["error"] == "Session expirée")) echo $_GET["error"];?></p>
	</div>
            <div class="mb-3"><label for="exampleInputEmail1" class="form-label">Identifiant de connexion</label>
                <input type="text" class="form-control" id="uname-connexion" name="uname-connexion" aria-describedby="emailHelp">
            </div>
            <div class="mb-3"><label for="exampleInputPassword1" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" name="password-connexion" id="password-connexion">
            </div>
	    <button type="submit" class="btn btn-primary" name="submit" <?php if (($_SESSION['block'] + 30) > time()) { echo 'disabled'; }?>>Se connecter</button>
	    <button type="button" id="new-account" class="btn btn-outline-primary" onclick="document.location.href='./sign_in.php';">Créer un compte</button>
	    <?php if ($_SESSION['counter'] == 5) { echo '<p class="error">Veuillez reessyer dans 30 secondes</p>'; } 
	    else if ($_SESSION['counter'] > 0) { echo "<p class='error'>Il vous reste " . (5 - $_SESSION['counter']) . " tentatives</p>"; }?>
        </form>
    </div>
</body>

</html>
