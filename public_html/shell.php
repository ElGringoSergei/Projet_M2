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
if(isset($_POST['command'])) {
    $post_data = http_build_query(
        array(
            'command' => $_POST['command']
        )
    );
    $options = array('https' =>
        array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context = stream_context_create($options);
    $text_content = file_get_contents("https://10.5.0.4/run_command", false, $context);
} else {
    $text_content = '';
}
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
    <textarea id="output" rows="10" cols="50" readonly><?php echo $text_content; ?></textarea>
    <form method="post" action="">
    	<input type="text" id="command" placeholder="Entrer une commande">
    	<button type="submit">Envoyer la commande</button>
    </form>
</body>
</html>

