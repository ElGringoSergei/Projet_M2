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
        shell_exec('curl -d "jour=' . $_POST['jour'] . '&heure=' . $_POST['heure'] . '" -X POST http://10.5.0.4:5000/api/annuler_reservation ');
        header("Location: ../reserver.php?success=La réservation a bien été annulée.");
    }
}
?>