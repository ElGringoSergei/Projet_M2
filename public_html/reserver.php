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
$token_del = bin2hex(random_bytes(16));
$_SESSION['csrf_del'] = $token_del;
$creneaux_reserves = json_decode(file_get_contents("http://10.5.0.4:5000/api/creneaux_reserves"));
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
                if ($creneaux_reserves[$i][2] == $_SESSION['username']) {
                    echo '<li class="list-group-item d-flex justify-content-between align-items-start"><div class="row">
                    <div class="col">
                      <p style="font-weight: bold">Date du créneaux</p><p>' . $creneaux_reserves[$i][0] .
                    '</p></div>
                    <div class="col">
                      <p style="font-weight: bold">Heure du créneaux</p><p>' . $creneaux_reserves[$i][1] .
                    '</p></div>
                    <div class="col">
                      <form action="php/delete_reservation.php" method="post">
                          <input type="text" style="display: none" visibility="hidden" value="' . $creneaux_reserves[$i][2] . '" name="user">
                          <input type="text" style="display: none" visibility="hidden" value="' . $creneaux_reserves[$i][0] . '" name="jour">
                          <input type="text" style="display: none" visibility="hidden" value="' . $creneaux_reserves[$i][1] . '" name="heure">
                          <input type="hidden" name="csrf_del" value="' . $token_del . '">
                          <button type="submit" class="btn btn-outline-danger" style="margin-top: 10%" name="delete" onclick="return confirm(`Êtes-vous sûr de vouloir supprimer cette réservation ?`); return false;">Supprimer la réservation</button>
                      </form>
                    </div>
                  </div>
                </div></li>';
                    $none = 1;
                }
            }
            
            if ($none == 0) {
                echo '<li class="list-group-item d-flex justify-content-between align-items-start">' . "Vous n'avez aucun créneaux réservé" . "</li>";
            }

        ?>

        
        <?php
        if (isset($_GET['success']) && $_GET['success'] == "La réservation a bien été annulée.") {
        '<li class="list-group-item d-flex justify-content-between align-items-start success">' . $_GET['success'] . "</li>";
        }
        ?>
    </ol>
    </div>

    <ol class="list-group list-group forms-perso2">
        <li class="list-group-item d-flex justify-content-between align-items-start"> 
            <div class="row" style="padding: 1%; padding-right: 3%;">
            <?php 
            $jours = json_decode(file_get_contents('http://10.5.0.4:5000/api/afficher_jours'));
            $heures = json_decode(file_get_contents('http://10.5.0.4:5000/api/afficher_heures'));
            $heures_libres = [];
            

            for ($j = 0; $j < sizeof($jours); $j++) {
                echo '<div class="col"><p style="font-weight: bold">' . $jours[$j] . "</p>";
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
                    if (in_array($heures[$m], $heures_libres[$j])) {
                        echo '<div class="row"><button class="btn btn-outline-secondary" style="font-weight: bold;">' . $heures[$m] . '</button></div>';
                    } else {
                        echo '<div class="row"><button class="btn btn-outline-secondary" style="font-weight: bold;" disabled>' . $heures[$m] . '</button></div>';
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
</body>

</html>