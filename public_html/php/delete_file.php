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

if(!isset($_POST['csrf_del'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?error=CSRF détecté");
  } else if($_POST['csrf_del'] != $_SESSION['csrf_del']) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?error=CSRF détecté");
  } else {

    if(isset($_POST['delete'])) {
        $validation = 0;
        $creneaux_reserves = json_decode(file_get_contents("http://10.5.0.4:5000/api/creneaux_reserves"));
        for ($i = 0; $i < sizeof($creneaux_reserves); $i++) {
            if ($_POST['file_name'] == $creneaux_reserves[$i][3]) {
                $validation = 1;
            }
        }
        if ($validation == 0) {
            $target_dir = "/var/www/html/uploads/" . $_SESSION['username'] . '/' . $_POST['file_name'];
            shell_exec('rm ' . $target_dir);
            header("Location: ../file_upload.php?delete=Le fichier a bien été supprimé.");
        } else {
            header("Location: ../file_upload.php?delete=Le fichier n'a pas été supprimé car il est utilisé dans l'une de vos réservations.");
        }
    }
}
?>