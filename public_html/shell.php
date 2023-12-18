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
$token = bin2hex(random_bytes(16));
$_SESSION['csrf'] = $token;
if(!isset($_SESSION['final_text'])) { $_SESSION['final_text'] = ""; }
if(isset($_POST['command'])) {
    $postdata = http_build_query(
        array(
            'command' => $_POST['command']
        )
    );

    $url = 'https://10.5.0.4/run_command';

    $ch = curl_init($url);

    $options = array(
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $postdata,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => 0, // N'utilisez pas en production, c'est pour les tests
        CURLOPT_SSL_VERIFYPEER => 0  // N'utilisez pas en production, c'est pour les tests
    );

    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);

    if ($response === false) {
        echo 'Erreur cURL : ' . curl_error($ch);
    } else {
        $text_content = json_decode($response, true);
    }

    curl_close($ch);
} else {
    $text_content['error'] = '';
    $text_content['output'] = '';
}
if($text_content['error'] != '') { $_SESSION['final_text'] = $_SESSION['final_text'] . $text_content['error']; } else { $_SESSION['final_text'] = $_SESSION['final_text'] . $text_content['output']; }

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
    <textarea id="output" rows="10" cols="50" readonly><?php echo $_SESSION['final_text'] ?></textarea>
    <form method="post" action="#">
    	<input type="text" id="command" name="command" placeholder="Entrer une commande">
    	<button type="submit">Envoyer la commande</button>
    </form>
</body>
</html>

