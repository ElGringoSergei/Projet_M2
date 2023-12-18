<?php
ini_set("session.cookie_httponly", True);
ini_set("session.cookie_secure", True);
ini_set("session.cookie_samesite", "Strict");
date_default_timezone_set('Europe/Paris');
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
$token_del = bin2hex(random_bytes(16));
$_SESSION['csrf_del'] = $token_del;
$token = bin2hex(random_bytes(16));
$_SESSION['csrf'] = $token;
?>

<!-- index.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remote Shell</title>
</head>
<body>
    <textarea id="output" rows="10" cols="50" readonly></textarea>
    <form method="post" action="">
    	<input type="text" id="command" placeholder="Entrer une commande">
    	<button type="submit">Envoyer la commande</button>
    </form>
</body>
</html>

