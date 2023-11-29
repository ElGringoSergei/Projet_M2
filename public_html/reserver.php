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
$creneaux_reserves = json_decode(file_get_contents("http://10.5.0.4:5000/api/creneaux_reserves"));
$files = json_encode(shell_exec('ls -lh /var/www/html/uploads/' . $_SESSION['username']));
$arr_files = str_replace('"', '', explode('\n', $files));
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="./index.js" defer></script>
</head>


<body>
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark" id="navbar-top">
        <div class="container-fluid">
            <label class="navbar-brand">Menu</label>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navbar-list">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#" id="accueil-page">Accueil</a>
                    </li>
                    <li class="nav-item">
		    <?php if(isset($_SESSION['id'])) { echo '<a class="nav-link" href="./myaccount.php?id=' . $_SESSION['id'] . '" id="se_connecter">Mon compte</a>';}
else { echo '<a class="nav-link" href="./login.php" id="se_connecter">Se connecter</a>';}?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div>
    <ol class="list-group list-group forms-perso" id="page-accueil">
        <li class="list-group-item d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto" id="login-link"><span class="fw-bold nav-link nav-pages">Vos réservations</span>Retrouvez les créneaux que vous avez réservé.</div>
        </li>
        <?php
            $none = 0;
            for ($i = 0; $i < sizeof($creneaux_reserves); $i++) {
                if ($creneaux_reserves[$i][3] == $_SESSION['username']) {
                    echo '<li class="list-group-item d-flex flex-row justify-content-around">
                    <div class="p-2 text-center">
                      <p style="font-weight: bold">Date du créneaux</p><p>' . $creneaux_reserves[$i][0] .
                    '</p></div>
                    <div class="p-2 text-center">
                      <p style="font-weight: bold">Heure du créneaux</p><p>' . $creneaux_reserves[$i][1] .
                    '</p></div>
                    <div class="p-2 text-center">
                      <p style="font-weight: bold">Fichier associé</p><p>' . $creneaux_reserves[$i][4] .
                    '</p></div>
                    <div class="p-2 text-center">
                      <p style="font-weight: bold">Nombre de noeuds</p><p>' . $creneaux_reserves[$i][5] .
                    '</p></div>                       
                    <div class="p-2 ms-auto">
                      <form action="php/delete_reservation.php" method="post">
                          <input type="text" style="display: none" visibility="hidden" value="' . $creneaux_reserves[$i][2] . '" name="id_res">
                          <input type="hidden" name="csrf_del" value="' . $token_del . '">
                          <button type="submit" class="btn btn-outline-danger" style="margin-top: 10%" name="delete" onclick="return confirm(`Êtes-vous sûr de vouloir supprimer cette réservation ?`); return false;">Supprimer la réservation</button>
                      </form>
                    </li>';
                    $none = 1;
                }
            }
            
            if ($none == 0) {
                echo '<li class="list-group-item d-flex justify-content-between">' . "Vous n'avez aucun créneaux réservé" . "</li>";
            }

        ?>

        
        <?php
        if (isset($_GET['success']) && $_GET['success'] == "La réservation a bien été annulée.") {
            echo '<li class="list-group-item d-flex justify-content-between align-items-start delete"><div class="col">' . $_GET['success'] . "</div></li>";
        }
        if (isset($_SESSION['message'])) {
            echo '<li class="list-group-item d-flex justify-content-between align-items-start success"><div class="col">' . $_SESSION['message'] . "</div></li>";
            unset($_SESSION['message']);
        }
        ?>
    </ol>
    </div>

    <ol class="list-group list-group forms-perso2">
        <li class="list-group-item justify-content-between align-items-start">
            <div class="ms-2 me-auto" id="login-link"><span class="fw-bold nav-link nav-pages">Créneaux disponibles</span>Retrouvez les créneaux que vous pouvez réserver.</div>
        </li>
        <li class="list-group-item justify-content-between align-items-start"> 
            <div class="d-flex flex-row overflow-x-auto" style="padding: 1%; padding-right: 3%;">
            <?php 
            $jours = json_decode(file_get_contents('http://10.5.0.4:5000/api/afficher_jours'));
            $heures = json_decode(file_get_contents('http://10.5.0.4:5000/api/afficher_heures'));
            $heures_libres = [];
            

            for ($j = 0; $j < sizeof($jours); $j++) {
                echo '<div class="p-2"><p style="font-weight: bold">' . $jours[$j] . "</p>";
                $postdata = http_build_query(
                    array(
                        'jour' => $jours[$j]
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
                $heures_libres[$j] = json_decode(file_get_contents('http://10.5.0.4:5000/api/creneaux_libres', false, $context));
                

                for ($m = 0; $m < sizeof($heures); $m++) {
                    echo '<div class="row">';
                    echo '<form method="post" action="">';
                    echo '<input type="hidden" name="jour" value="' . $jours[$j] . '">';
                    echo '<input type="hidden" name="heure" value="' . $heures[$m] . '">';
                    echo '<input type="hidden" name="empl_jour" value="' . $j . '">';
                    $valid_heure = 0;
                    for ($o = 0; $o < sizeof($heures_libres[$j]); $o++) {
                        if (in_array($heures[$m], $heures_libres[$j][$o])) {
                            $valid_heure = 1;
                            echo '<input type="hidden" name="emplacement" value="' . $o . '">';
                        }
                    }
                    if (date("Y-m-d") == $jours[$j]) {
                        if ($valid_heure == 1 && (int)date("h") < (int)str_replace(":*","", $heures[$m])) {
                            echo '<button type="submit" class="btn btn-outline-secondary" id="creneauButton" style="font-weight: bold;">' . $heures[$m] . '</button></form></div>';
                        } else {
                            echo '<button type="submit" class="btn btn-outline-secondary" style="font-weight: bold;" disabled>' . $heures[$m] . '</button></form></div>';
                        }
                    } else {
                        if ($valid_heure == 1) {
                            echo '<button type="submit" class="btn btn-outline-secondary" id="creneauButton" style="font-weight: bold;">' . $heures[$m] . '</button></form></div>';
                        } else {
                            echo '<button type="submit" class="btn btn-outline-secondary" style="font-weight: bold;" disabled>' . $heures[$m] . '</button></form></div>';
                        }
                    }
                }
                echo "</div>";
            }

            ?>
            </div>
            <div class="row">

            </div>
        </li>
    </ol>

    <div id="popup" class="modal" <?php if (isset($_POST['jour'])) { echo 'style="display: block;"'; }?>>
  <div class="modal-content">
    <span class="fermer" onclick="fermerPopup()">&times;</span>
    <p>Voulez-vous vraiment réserver le créneau de <?php echo $_POST['heure'] . " le " . $_POST['jour']?>?</p>
    <form method="post" action="php/reserver_creneaux.php">
    <input type="hidden" name="jour" value="<?php echo $_POST['jour'];?>">
    <input type="hidden" name="heure" value="<?php echo $_POST['heure'];?>">
    <input type="hidden" name="csrf" value="<?php echo $token ?>">
    <div class="input-group mb-3">
        <label class="input-group-text" for="inputGroupSelect01">Options</label>
        <select class="form-select" id="inputGroupSelect01" name="file_name" required>
            <option value="">Choisissez un fichier</option>
        <?php
        for($i = 1; $i < (sizeof($arr_files) - 1); $i++) {
                $arr_files[$i] = str_replace(" ", "|", $arr_files[$i]);
                $elements = str_replace('"', '', explode("|", str_replace('||', '|', $arr_files[$i])));
                echo '<option value="' . $elements[8] . '">' . $elements[8] . '</option>';
            }; ?>
        </select>
    </div>
    <div class="input-group mb-3">
        <label class="input-group-text" for="input2">Options</label>
        <select class="form-select" id="input2" name="nombre_cartes" required>
            <option value="">Choisissez le nombre de noeuds à utiliser</option>
            <?php
            for ($nodes = 1; $nodes <= $heures_libres[$_POST['empl_jour']][$_POST['emplacement']][1]; $nodes++) {
                echo '<option value="' . $nodes . '">' . $nodes . '</option>';
            }
            ?>
        </select>
    </div>
    <button type="submit" class="btn btn-outline-secondary" onclick="validerReservation();">Réserver ce créneau</button>
    </form>
  </div>
</div>
</body>

</html>