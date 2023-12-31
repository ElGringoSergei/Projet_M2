<?php
ini_set("session.cookie_httponly", True);
ini_set("session.cookie_secure", True);
ini_set("session.cookie_samesite", "Strict");
session_start();
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="./index.js" defer></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark" id="navbar-top">
        <div class="container-fluid">
            <label class="navbar-brand">Menu</label>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navbar-list">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="./index.php" id="accueil-page">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#" id="se_connecter">Se connecter</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div id="content">
        <form class="forms-perso" action="./php/sign_in.php" method="post">
            <h2>Inscription</h2>
            <div class="mb-3">
                <div>
                    <p class="error"><?php if (isset($_GET["error"]) && ($_GET["error"] == "Vous n'avez pas rempli tous les champs" || $_GET["error"] == "Les mots de passes ne correspondent pas" || $_GET["error"] == "Vous possédez déjà un compte avec ces coordonnées" || $_GET["error"] == "Vous n'avez pas rempli tous les champs")) echo $_GET["error"]; ?></p>
                </div>
                <div id="grid-account">
                    <div>
                        <label for="exampleInputName" class="form-label bold">Prénom</label>
                        <input type="text" class="form-control" id="account-name" name="account-name" placeholder="John">
                    </div>
                    <div id="flex-item">
                        <label for="exampleInputSurname" class="form-label bold">Nom</label>
                        <input type="text" class="form-control" id="account-surname" name="account-surname" placeholder="Doe">
                    </div>
                </div>
                <label for="username" class="form-label bold">Identifiant de connexion</label>
                <input type="text" pattern="[a-zA-Z0-9]*" class="form-control" id="username" name="username" oninput="checkBlankSpace()" placeholder="jdoe">
                <label for="username" class="form-text" style="margin-left: 0.2rem">Les espaces et les caractères spéciaux ne sont pas autorisés</label><br>
                <label for="exampleInputEmail1" class="form-label bold">Adresse mail</label>
                <input type="email" class="form-control" id="Email1" aria-describedby="emailHelp" name="Email1" placeholder="johndoe@mail.com">
                <div id="emailHelp" class="form-text" style="margin-left: 0.2rem;">Nous ne divulgerons pas votre adresse mail</div>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label bold">Mot de passe</label>
                <input type="password" class="form-control" id="Password1" name="Password1" oninput="updateStrength(); checkBothPasswords();" placeholder="Utilisez des majuscules, des minuscules, des chiffres et des caractères spéciaux pour plus de sécurité">
                <div class="form-text d-flex justify-content-between"><div class="p-2">Un mot de passe moyen ou fort est exigé</div><div class="p-2"><input type="checkbox" onclick="showPassword()"><text style="margin-left: 0.5rem; margin-bottom: 0.2rem;">Afficher le mot de passe</text></div></div>
                <label for="Password1" id="strength-label" class='error' style="background-color: white; border-radius: 0.2rem; padding-left: 0.5rem; padding-right: 0.5rem; margin-left: 0.2rem; margin-top: 0.2rem;">Très faible</label>
            </div>
            <div class="mb-3">
                <label for="Password2label" class="form-label bold">Confirmer le mot de passe</label>
                <input type="password" class="form-control" id="Password2" name="Password2" oninput="checkBothPasswords()">
                <label for="Password2" id="check-label" style="display: none; margin-top: 0.2rem;"></label>
            </div>
            <button type="submit" id="btn_sign_up" class="btn btn-primary" name="submit-create" disabled>Valider</button>
            <button type="button" id="login-page" class="btn btn-outline-primary" onclick="document.location.href='./login.php';">Retourner a la page de connexion</button>
        </form>
    </div>
</body>

</html>