<?php
    ini_set("session.cookie_httponly", True);
    ini_set("session.cookie_secure", True);
    ini_set("session.cookie_samesite", "Strict");
    session_start();
    if (isset($_SESSION['id'])) {
        if ($_SESSION['expire'] < time()) {
            header("Location: ../login.php?error=Session expirée");
        } else {
            $_SESSION['expire'] = time() + $_SESSION['timeout'];
        }
    } else {
        header("Location: ../login.php?error=Session expirée");
    }
    $files = json_encode(shell_exec('ls -lh /var/www/html/uploads/' . $_SESSION['username']));
    $arr_files = str_replace('"', '', explode('\n', $files));

    $token_up = bin2hex(random_bytes(16));
    $token_del = bin2hex(random_bytes(16));
    $_SESSION['csrf'] = $token_up; 
    $_SESSION['csrf_del'] = $token_del;
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
                        <a class="nav-link active" aria-current="page" href="./index.php" id="accueil-page">Accueil</a>
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
        <ol class="list-group list-group forms-perso" id="page-accueil">
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto" id="login-link">
                    <span class="fw-bold nav-link nav-pages">Vos fichiers</span>
                    Retrouvez vos fichiers importés ou importez-en de nouveaux.
                </div>

            </li>
            <?php 
            for($i = 1; $i < (sizeof($arr_files) - 1); $i++) {
                $arr_files[$i] = str_replace(" ", "|", $arr_files[$i]);
                $elements = str_replace('"', '', explode("|", str_replace('||', '|', $arr_files[$i])));
                echo '<li class="list-group-item d-flex justify-content-between align-items-start"><div class="container text-center">
                <div class="row">
                  <div class="col">
                    <p style="font-weight: bold">Nom du fichier</p><p>' . $elements[8] .
                  '</p></div>
                  <div class="col">
                    <p style="font-weight: bold">Taille du fichier</p><p>' . $elements[4] .
                  '</p></div>
                  <div class="col">
                    <p style="font-weight: bold">Date</p><p>' . $elements[6] . " " . $elements[5] . " " . $elements[7] .
                  '</p></div>
                  <div class="col">
                    <form action="php/delete_file.php" method="post">
                        <input type="text" style="display: none" visibility="hidden" value="' . $elements[8] . '" name="file_name">
                        <input type="hidden" name="csrf_del" value="' . $token_del . '">
                        <button type="submit" class="btn btn-outline-danger" style="margin-top: 10%" name="delete" onclick="return confirm(`Êtes-vous sûr de vouloir supprimer ce fichier ?`); return false;">Supprimer le fichier</button>
                    </form>
                  </div>
                </div>
              </div></li>'; 
            }; ?>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <form action="php/upload.php" method="post" enctype="multipart/form-data" style="margin-top: 1%">
                    <input type="hidden" name="csrf" value="<?php echo $token_up;?>">
                    <div class="input-group mb-3">
                        <button type="submit" class="input-group-text btn btn-outline-primary" for="inputGroupFile01">Importer</button>
                        <input type="file" accept=".pdf, .jpg" class="form-control" name="inputGroupFile01" id="inputGroupFile01">
                    </div>
                    <label for="inputGroupFile01" class="label-file">Taille max. 500 Ko</label>
                    <div>
                        <?php if(isset($_GET['error']) && ($_GET['error'] == ('Désolé, ce fichier existe déjà.' || 'Désolé, le fichier est trop lourd.' || 'Désolé, seuls les fichiers PDF et JPG sont autorisés.' || "Désolé, le fichier n'a pas pû être importé." || "Désolé, il y a eu une erreur lors de l'importation de votre fichier."))) echo "<p class='error'>" . $_GET['error'] . "</p>"; ?>
                        <?php if(isset($_GET['success']) && ($_GET['success'] == ("Le fichier a été importé avec succès."))) echo "<p class='success'>" . $_GET['success'] . "</p>"; ?>
                        <?php if(isset($_GET['delete']) && ($_GET['delete'] == ("Le fichier a bien été supprimé." || "Le fichier n'a pas été supprimé car il est utilisé dans l'une de vos réservations."))) echo "<p class='delete'>" . $_GET['delete'] . "</p>"; ?>
                    </div>
            </form>
            </li>
        </ol>
    </div>
</body>

</html>