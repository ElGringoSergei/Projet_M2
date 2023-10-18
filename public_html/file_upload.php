<?php
    session_start();
    $files = shell_exec ('ls /usr/local/apache2/htdocs/uploads');

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
            <label class="navbar-brand">Importation de fichiers</label>
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
                <form class="d-flex" role="search" id="search-bar" method="get" action="./php/searchbar.php">
                    <input class="form-control me-2" type="search" name="search" placeholder="Votre recherche" id="search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit" name="submit-search">Rechercher</button>
                </form>
            </div>
        </div>
    </nav>

    <div id="content">
        <ol class="list-group list-group forms-perso" id="page-accueil">
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto" id="login-link">
                    <a href="./login.php" class="fw-bold nav-link nav-pages">Vos fichiers</a>
                    Retrouvez vos fichiers importés ou importez-en de nouveaux.
                </div>

            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <p><?php echo $files; ?></p>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <form action="php/upload.php" method="post" enctype="multipart/form-data" style="margin-top: 1%">
                    <div class="input-group mb-3">
                        <button type="submit" class="input-group-text btn btn-outline-primary" for="inputGroupFile01">Importer</button>
                        <input type="file" accept=".pdf, .jpg" class="form-control" name="inputGroupFile01" id="inputGroupFile01"> 
                    </div>
                    <div>
                        <?php if(isset($_GET['error']) && ($_GET['error'] == ('Désolé, ce fichier existe déjà.' || 'Désolé, le fichier est trop lourd.' || 'Désolé, seuls les fichiers PDF et JPG sont autorisés.' || "Désolé, le fichier n'a pas pû être importé." || "Désolé, il y a eu une erreur lors de l'importation de votre fichier."))) echo "<p class='error'>" . $_GET['error'] . "</p>"; ?>
                        <?php if(isset($_GET['success']) && ($_GET['success'] == ("Le fichier a été importé avec succès."))) echo "<p class='success'>" . $_GET['success'] . "</p>"; ?>
                    </div>
            </form>
            </li>
        </ol>
    </div>
</body>

</html>