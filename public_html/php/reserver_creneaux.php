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

if(!isset($_POST['csrf'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?error=CSRF détecté");
} else if($_POST['csrf'] != $_SESSION['csrf']) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?error=CSRF détecté");
} else {

        $postdata = http_build_query(       # Modifier pour adapter si le fichier est importé ou selectionné
    array(
        'jour' => $_POST['jour'],
        'heure' => $_POST['heure'],
        'personne' => $_SESSION['username'],
        'nom_fichier' => $_POST['file_name'],
        'nombre_cartes' => $_POST['nombre_cartes']
    )
    );
    $options = array('http' =>
        array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context = stream_context_create($options);
    $response = file_get_contents('http://10.5.0.4:5000/api/reserver', false, $context);

    $_SESSION['message'] = $response;

    header("Location: ../reserver.php");
}


?>