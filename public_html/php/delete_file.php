<?php
ini_set("session.cookie_httponly", True);
ini_set("session.cookie_secure", True);
ini_set("session.cookie_samesite", "Strict");
session_start();

if(isset($_POST['delete'])) {
    $target_dir = "/var/www/html/uploads/" . $_SESSION['username'] . '/' . $_POST['file_name'];
    shell_exec('rm ' . $target_dir);
    header("Location: ../file_upload.php?delete=Le fichier a bien été supprimé.");
};
?>