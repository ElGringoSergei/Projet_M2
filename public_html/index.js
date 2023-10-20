var logged = 0;
var contenu = document.getElementById('content');
var id_co = 0;

var comptes = [];
comptes[0] = { nom: 'WARTEL', prenom: 'Matheo', id: 'mwartel@gmail.com', pwd: '1234' };

function login_clicked() {
    if (logged == 0) {
        contenu.innerHTML = '<form class="forms-perso" method="post" id="connexion"><div class="mb-3" ><label for="exampleInputEmail1" class="form-label" >Adresse mail</label><input type="email" class="form-control" id="email-connexion" aria-describedby="emailHelp"><div id="emailHelp" class="form-text">Nous ne divulgerons pas votre email.</div></div><div class="mb-3"><label for="exampleInputPassword1" class="form-label">Mot de passe</label><input type="password" class="form-control" id="password-connexion"></div><!--< div class="mb-3 form-check" ><input type="checkbox" class="form-check-input" id="exampleCheck1"><label class="form-check-label" for="exampleCheck1">Check me out</label></div>--><button type="button" class="btn btn-primary" onclick="connect();">Se connecter</button><button type="button" id="new-account" class="btn btn-outline-primary" onclick="createAccount();">Créer un compte</button></form >';
    } else {

        contenu.innerHTML = '<ul class="list-group forms-perso1" style="text-align: center;"><li class="list-group-item"><div class="mb-2" style="font-weight: bold; text-align: center;">Identifiant :</div>' + comptes[id_co].id + '</li><li class="list-group-item"><div class="mb-2" style="font-weight: bold; text-align: center;">Nom : </div>' + comptes[id_co].nom + '</li><li class="list-group-item"><div class="mb-2" style="font-weight: bold; text-align: center;">Prénom : </div>' + comptes[id_co].prenom + '</ul></div>';
        
    }
    document.getElementById('accueil-page').classList.remove('active');
    document.getElementById('se_connecter').classList.add('active');
    document.getElementById('url-page').classList.remove('active');
    return false;
}



function connect() {
    var email = document.getElementById('email-connexion').value;
    var password = document.getElementById('password-connexion').value;
    var navbar = document.getElementById('navbar-list');
    var exist = 0;
    for (let i = 0; i < comptes.length; i++) {
        if (email == comptes[i].id) {
            exist = 1;
        }
        if (exist == 1 && password == comptes[i].pwd) {
            logged = 1;
            id_co = i;
            contenu.innerHTML = '<ol class="list-group list-group-numbered forms-perso" id="page-accueil"><li class="list-group-item d-flex justify-content-between align-items-start"><div class="ms-2 me-auto" id="login-link">Connecté</div></li><li class="list-group-item d-flex justify-content-between align-items-start"><div class="ms-2 me-auto"><a href="#" class="fw-bold nav-link nav-pages" onclick="url_clicked();">URL</a>Cliquez sur cette page pour entrer une URL</div></li></ol>';
            document.getElementById('se_connecter').innerHTML = 'Mon compte';
            navbar.innerHTML += '<li class="nav-item"><button type="button" class="btn btn-outline-danger" onclick="disconnect();" id="disconnect"> Se déconnecter</button></li>';
        }
    }
    if (exist == 0) {
        alert("Cette combinaison identifiant/mot de passe n'existe pas");
    }
}

function accueil() {
    if (logged == 0) {
        contenu.innerHTML = '<ol class="list-group list-group-numbered forms-perso" id="page-accueil"><li class="list-group-item d-flex justify-content-between align-items-start"><div class="ms-2 me-auto" id="login-link"><a href="#" class="fw-bold nav-link nav-pages" onclick="login_clicked();">Page de connexion</a>Cliquez sur cette page pour vous connecter</div></li><li class="list-group-item d-flex justify-content-between align-items-start"><div class="ms-2 me-auto"><a href="#" class="fw-bold nav-link nav-pages" onclick="url_clicked();">URL</a>Cliquez sur cette page pour entrer une URL</div></li></ol>';
    } else if (logged == 1) {
        contenu.innerHTML = '<ol class="list-group list-group-numbered forms-perso" id="page-accueil"><li class="list-group-item d-flex justify-content-between align-items-start"><div class="ms-2 me-auto" id="login-link">Connecté</div></li><li class="list-group-item d-flex justify-content-between align-items-start"><div class="ms-2 me-auto"><a href="#" class="fw-bold nav-link nav-pages" onclick="url_clicked();">URL</a>Cliquez sur cette page pour entrer une URL</div></li></ol>';
    }
    document.getElementById('accueil-page').classList.add('active');
    document.getElementById('se_connecter').classList.remove('active');
    document.getElementById('url-page').classList.remove('active');
}

function url_clicked() {
    if (logged == 0) {
        alert('Vous devez vous connecter pour pouvoir accéder à cette page.');
    } else {
        contenu.innerHTML = '<form class="forms-perso"><div class="mb-3"><label for="basic-url" class="form-label">Entrez votre URL</label><div class="input-group"><input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3 basic-addon4"></div></div><button type="submit" class="btn btn-primary" onclick="url_send();" onsubmit="return false;">Envoyer</button></form> <div id="image"></div>';
        document.getElementById('accueil-page').classList.remove('active');
        document.getElementById('se_connecter').classList.remove('active');
        document.getElementById('url-page').classList.add('active');
    }
}

function disconnect() {
    logged = 0;
    alert('Vous avez été déconnecté');
    document.getElementById('se_connecter').innerHTML = 'Se connecter';
    contenu.innerHTML = '<ol class="list-group list-group-numbered forms-perso" id="page-accueil"><li class="list-group-item d-flex justify-content-between align-items-start"><div class="ms-2 me-auto" id="login-link"><a href="#" class="fw-bold nav-link nav-pages" onclick="login_clicked();">Page de connexion</a>Cliquez sur cette page pour vous connecter</div></li><li class="list-group-item d-flex justify-content-between align-items-start"><div class="ms-2 me-auto"><a href="#" class="fw-bold nav-link nav-pages" onclick="url_clicked();">URL</a>Cliquez sur cette page pour entrer une URL</div></li></ol>';
    document.getElementById('disconnect').remove();
}

function clicked() {
    var search = document.getElementById('search').value;
    contenu.innerHTML = '<ol class="list-group list-group forms-perso"><li class="list-group-item d-flex justify-content-between align-items-start"><div class="ms-2 me-auto"><h2>Votre recherche :</h2>' + search + '</div></li></ol>';
    /*var req = new XMLHttpRequest();
    req.open('GET', 'https://www.google.com/search?q=' + search);
    req.addEventListener('load', function() {
        if (req.status >= 200 && req.status < 400) {
            console.log(this.responseText);
        } else {
            console.error(req.status + " " + req.statusText);
        }
    });

    req.addEventListener('error', function() {
        console.error('La requête à rencontrée un problême.');
    })

    req.send();*/
}

function url_send() {
    var url = document.getElementById('basic-url').value;
    var imagejavascript = document.createElement('img');
    var div = document.getElementById('image');
    imagejavascript.src = url;
    div.appendChild(imagejavascript);
    console.log(url);

}

function createAccount() {
    contenu.innerHTML = '<form class="forms-perso"><div class="mb-3"><div id="grid-account"><div><label for="exampleInputName" class="form-label">Prénom</label><input type="text" class="form-control" id="account-name"></div><div id="flex-item"><label for="exampleInputSurame" class="form-label">Nom</label><input type="text" class="form-control" id="account-surname"></div></div><label for="exampleInputEmail1" class="form-label">Adresse mail</label><input type="email" class="form-control" id="Email1" aria-describedby="emailHelp"><div id="emailHelp" class="form-text">Nous ne divulgerons pas votre adresse mail.</div></div><div class="mb-3"><label for="exampleInputPassword1" class="form-label">Mot de passe</label><input type="password" class="form-control" id="Password1"></div><div class="mb-3"><label for="Password2label" class="form-label">Confirmer le mot de passe</label><input type="password" class="form-control" id="Password2"></div><button type="submit" class="btn btn-primary" onclick="saveAccount();" onsubmit="return false;">Valider</button></form>';
}

function saveAccount() {
    var duplicate = 0;
    var password = document.getElementById('Password1').value;
    var nom = document.getElementById('account-surname').value;
    var prenom = document.getElementById('account-name').value;
    var mail = document.getElementById('Email1').value;
    if (password == null || nom == '' || prenom == '' || mail == '' || document.getElementById('Password2') == '') {
        contenu.innerHTML += '<div class="red">Veuillez remplir tous les champs.</div>';
    }
    else if (password != document.getElementById('Password2').value) {
        contenu.innerHTML += '<div class="red">Les mots de passe ne correspondent pas</div>';
    }
    else {
        for (let i = 0; i < comptes.length; i++) {
            if (mail == comptes[i].id) {
                duplicate = 1;
            }
        } if (duplicate == 1) {
            alert('Vous possédez déjà un compte avec cette adresse mail. Vous allez être redirigé vers la page de connexion.');
            login_clicked();
        }
        comptes[comptes.length] = {nom: nom, prenom: prenom, id: mail, pwd: password};
        alert('Votre compte a bien été créé. Vous allez être redirigé vers la page de connexion.');
        login_clicked();
    }
    
}

function search_launched(recherche) {
	var resultat_recherche = document.getElementById("recherche");
	resultat_recherche.innerHTML = recherche;
}


var file = document.getElementById("inputGroupFile01");

file.onchange = function(e) {
    var ext = this.value.match(/\.([^\.]+)$/)[1];
    switch (ext) {
        case 'pdf':
        case 'jpg':
            break;
        default:
            alert('Fichier non autorisé');
            this.value = '';
    }
};

var state_btn = "bad";

function checkPasswordStrength(password) {
    // Define the criteria for a strong password
    const lengthRegex = /.{8,}/; // At least 8 characters
    const lowercaseRegex = /[a-z]/; // At least one lowercase letter
    const uppercaseRegex = /[A-Z]/; // At least one uppercase letter
    const numberRegex = /\d/; // At least one number
    const specialCharRegex = /[\W_]/; // At least one special character

    // Check each criteria and assign a score
    let score = 0;
    if (lengthRegex.test(password)) score++;
    if (lowercaseRegex.test(password)) score++;
    if (uppercaseRegex.test(password)) score++;
    if (numberRegex.test(password)) score++;
    if (specialCharRegex.test(password)) score++;

    if (score >=3) state_btn = "ok";

    // Determine the password strength based on the score
    if (score === 5) return 'Fort';
    if (score >= 3) return 'Moyen';
    if (score >= 2) return 'Faible';
    return 'Très faible';
  }

  function updateStrength() {
    const passwordInput = document.getElementById('Password1');
    const strengthLabel = document.getElementById('strength-label');

    const password = passwordInput.value;
    const strength = checkPasswordStrength(password);
    strengthLabel.textContent = `${strength}`;
    if (strength == 'Très faible' || strength == 'Faible') {
        strengthLabel.className = 'error';
    } else if (strength == 'Moyen') {
        strengthLabel.className = 'delete';
    } else {
        strengthLabel.className = 'success';
    }
  }


  function checkBothPasswords() {
    const password1 = document.getElementById('Password1');
    const password2 = document.getElementById('Password2');
    const label = document.getElementById('check-label');
    const valid_btn = document.getElementById('btn_sign_up');
    if (password2 == '') {
        label.style.display = "none";
    } else {
        if (password1.value != password2.value) {
            label.textContent = "Les mots de passe ne correspondent pas";
            label.style.display = "inline";
            label.className = "error";
        } else {
            label.textContent = "Les mots de passe correspondent";
            label.style.display = "inline";
            label.className = "success";
            if (state_btn == "ok") {
                valid_btn.removeAttribute("disabled");
            }
        }
    }
    
  }

  function checkBlankSpace() {
    const uname_field = document.getElementById('username').value;
    if (uname_field.indexOf(' ') > -1) {
        alert("Les espaces ne sont pas autorisées dans le nom d'utilisateur");
        let texttorep = uname_field.replace(/\s/,'');
        document.getElementById('username').value = texttorep;
    }
  }
  

function showPassword() {
    const pass1 = document.getElementById('Password1');
    const pass2 = document.getElementById('Password2');

    if (pass1.type == 'password') {
        pass1.type = 'text';
        pass2.type = 'text';
    } else {
        pass1.type = 'password';
        pass2.type = 'password';
    }
}