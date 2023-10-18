<?php
$target_dir = "../uploads/";
$target_file = $target_dir . basename($_FILES["inputGroupFile01"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));



// Check if file already exists
if (file_exists($target_file)) {
  header("Location: ../file_upload.php?error=Désolé, ce fichier existe déjà.");
  $uploadOk = 0;
}

// Check file size
if ($_FILES["inputGroupFile01"]["size"] > 500000) {
  header("Location: ../file_upload.php?error=Désolé, le fichier est trop lourd.");
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "pdf" && $imageFileType != "jpg") {
  header("Location: ../file_upload.php?error=Désolé, seuls les fichiers PDF et JPG sont autorisés.");
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["inputGroupFile01"]["tmp_name"], $target_file)) {
    header("Location: ../file_upload.php?success=Le fichier a été importé avec succès.");
  } else {
    header("Location: ../file_upload.php?error=Désolé, il y a eu une erreur lors de l'importation de votre fichier.");
  }
}
?>