<?php
session_start();
$target_dir = '../uploads/' . $_SESSION['username'] . '/';
$rep_arr = array(' ','é','à','è','ç','ê','â','ï','û','ô');
$arr_rep = array('_','e','a','e','c','e','a','i','u','o');
$target_file = $target_dir . basename(str_replace($rep_arr, $arr_rep, $_FILES["inputGroupFile01"]["name"]));
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));



// Verification de fichier existant
if (file_exists($target_file)) {
  header("Location: ../file_upload.php?error=Désolé, ce fichier existe déjà.");
  $uploadOk = 0;
}

// Verification de la taille du fichier
if ($_FILES["inputGroupFile01"]["size"] > 500000) {
  header("Location: ../file_upload.php?error=Désolé, le fichier est trop lourd.");
  $uploadOk = 0;
}

// Verification de l'extension du fichier
if($imageFileType != "pdf" && $imageFileType != "jpg") {
  header("Location: ../file_upload.php?error=Désolé, seuls les fichiers PDF et JPG sont autorisés.");
  $uploadOk = 0;
}


if ($uploadOk == 0) {
  
// si tout est bon, on essaie d'upload le fichiers
} else {
  if (move_uploaded_file($_FILES["inputGroupFile01"]["tmp_name"], $target_file)) {
    header("Location: ../file_upload.php?success=Le fichier a été importé avec succès.");
  } else {
    header("Location: ../file_upload.php?error=Désolé, il y a eu une erreur lors de l'importation de votre fichier.");
  }
}
?>