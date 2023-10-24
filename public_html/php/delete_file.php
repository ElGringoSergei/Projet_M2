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
        $target_dir = "/var/www/html/uploads/" . $_SESSION['username'] . '/' . $_POST['file_name'];
        shell_exec('rm ' . $target_dir);
        header("Location: ../file_upload.php?delete=Le fichier a bien été supprimé.");
    }
}
?>