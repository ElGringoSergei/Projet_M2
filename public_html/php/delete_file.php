<?php
session_start();

if(isset($_POST['delete'])) {
    $target_dir = "/var/www/html/uploads/" . $_POST['file_name'];
    shell_exec('rm ' . $target_dir);
    header("Location: ../file_upload.php?delete=Le fichier a bien été supprimé.");
};
?>