from flask import Flask, jsonify, render_template, request
from datetime import datetime, timedelta
import logging
from pytz import timezone
from logging.handlers import TimedRotatingFileHandler
import vars

app = Flask(__name__)

# Configuration de la journalisation
log_formatter = logging.Formatter('%(asctime)s [%(levelname)s] - %(message)s')
log_handler = TimedRotatingFileHandler('/home/ubuntu/logs/app.log', when='midnight', interval=1, backupCount=30)
log_handler.setFormatter(log_formatter)
app.logger.addHandler(log_handler)
app.logger.setLevel(logging.INFO)


class Ordonnanceur:
    def __init__(self, jours, heures_de_travail, jours_a_afficher=30):
        self.jours = [(datetime.now() + timedelta(days=i)).strftime('%Y-%m-%d') for i in range(jours_a_afficher)]
        self.heures_de_travail = heures_de_travail
        self.cartes = {         # A modifier en fonction du nombre de cartes et de leur adresse IP
            'Carte 1': '1.1.1.1',
            'Carte 2': '1.1.1.2',
            'Carte 3': '1.1.1.3',
            'Carte 4': '1.1.1.4',
            'Carte 5': '1.1.1.5',
            'Carte 6': '1.1.1.6',
            'Carte 7': '1.1.1.7',
            'Carte 8': '1.1.1.8'
        }
        self.id_res = 0
        self.creneaux_disponibles = {jour: {heure: True for heure in heures_de_travail} for jour in self.jours}
        self.reservations = vars.reservations

    

    def afficher_creneaux_disponibles(self, jour):
        if jour in self.jours:
            creneaux_disponibles = []

            for heure, disponible in self.creneaux_disponibles[jour].items():
                if disponible:
                    nombre_cartes_utilisees = 0
                    if (jour, heure) in self.reservations:
                        for reservation in self.reservations[(jour, heure)]:
                            nombre_cartes_utilisees += reservation.get('nombre_cartes', 0)

                    nombre_cartes_disponibles = len(self.cartes) - nombre_cartes_utilisees
                    creneaux_disponibles.append((heure, nombre_cartes_disponibles))

            return creneaux_disponibles
        else:
            reponse = f"La date {jour} n'est pas incluse dans les jours de l'ordonnanceur."
            app.logger.warning(reponse)





    

    def reserver_creneau(self, jour, heure, personne, fichier, nombre_cartes):
        if jour in self.jours:
            if heure in self.creneaux_disponibles[jour]:
                reservation = {'id_res': self.id_res,'personne': personne, 'fichier': fichier, 'nombre_cartes': nombre_cartes}
                self.id_res += 1
                if (jour, heure) not in self.reservations:
                    self.reservations[(jour, heure)] = [reservation]
                else:
                    self.reservations[(jour, heure)].append(reservation)

                # Vérifier si toutes les cartes ont été utilisées pour cet horaire
                cartes_utilisees = sum(sum(entry['nombre_cartes'] for entry in entries) for entries in self.reservations.values())
                if cartes_utilisees < len(self.cartes):
                    self.creneaux_disponibles[jour][heure] = True
                else:
                    self.creneaux_disponibles[jour][heure] = False

                reponse = f"Créneau réservé par {personne} le {jour} à {heure}. Fichier associé : {fichier}. Nombre de cartes utilisées : {nombre_cartes}."
                app.logger.info(reponse)
                file = open("vars.py", "w")
                text = repr(self.reservations)
                file.write("reservations = " + text)
                file.close()
                return reponse
            else:
                reponse = f"Le créneau à {heure} le {jour} n'est pas disponible ou le nombre de cartes spécifié est invalide."
                app.logger.warning(reponse)
                return reponse
        else:
            reponse = f"La date {jour} n'est pas incluse dans les jours de l'ordonnanceur."
            app.logger.warning(reponse)
            return reponse




    def afficher_creneaux_reserves(self):
        creneaux_reserves = []

        for (jour, heure), reservations in self.reservations.items():
            for details_reservation in reservations:
                id_res = details_reservation['id_res']
                personne = details_reservation['personne']
                fichier = details_reservation.get('fichier', None)
                nombre_cartes_utilisees = details_reservation.get('nombre_cartes', 0)

                creneaux_reserves.append((jour, heure, id_res, personne, fichier, nombre_cartes_utilisees))

        app.logger.info(creneaux_reserves)
        return creneaux_reserves




    def annuler_reservation(self, id_res):
        reservation_found = False

        for (jour, heure), reservations in self.reservations.items():
            for index, details_reservation in enumerate(reservations):
                if details_reservation.get('id_res') == id_res:
                    personne = details_reservation.get('personne')
                    self.reservations[(jour, heure)].pop(index)
                    reservation_found = True
                    self.creneaux_disponibles[jour][heure] = True
                    reponse = f"Réservation avec l'ID {id_res} annulée pour {personne} le {jour} à {heure}."
                    app.logger.info(reponse)
                    file = open("vars.py", "w")
                    text = repr(self.reservations)
                    file.write("reservations = " + text)
                    file.close()
                    break

        if not reservation_found:
            app.logger.warning(f"Aucune réservation trouvée avec l'ID {id_res}.")

    def annuler_reservations_personne(self, nom_personne):
        reservations_annulees = 0

        for (jour, heure), reservations in list(self.reservations.items()):
            reduce_var = 0
            for index, details_reservation in list(enumerate(reservations)):
                if details_reservation.get('personne') == nom_personne:
                    self.reservations[(jour, heure)].pop(index - reduce_var)
                    self.creneaux_disponibles[jour][heure] = True
                    reservations_annulees += 1
                    reduce_var += 1

        if reservations_annulees > 0:
            reponse = f"Toutes les réservations de {nom_personne} ont été annulées."
            app.logger.info(reponse)
            return reponse
        else:
            reponse = f"Aucune réservation trouvée pour {nom_personne}."
            app.logger.warning(reponse)
            return reponse
        file = open("vars.py", "w")
        
        text = repr(self.reservations)
        file.write("reservations = " + text)
        file.close()


    def mettre_a_jour_jours(self,jours_a_afficher=14):
        self.jours = [(datetime.now() + timedelta(days=i+1)).strftime('%Y-%m-%d') for i in range(jours_a_afficher)]
        self.reservations = vars.reservations


# Initialisation
heures_de_travail = ["08:00", "09:00", "10:00", "11:00", "14:00", "15:00", "16:00", "17:00"]
ordonnanceur_deux_semaines = Ordonnanceur([], heures_de_travail, jours_a_afficher=30)


@app.route('/api/reserver', methods=['POST'])
def reserver():
    jour = request.form['jour']
    heure = request.form['heure']
    personne = request.form['personne']
    fichier = request.form['nom_fichier']
    nombre_cartes = int(request.form['nombre_cartes'])

    success = ordonnanceur_deux_semaines.reserver_creneau(jour, heure, personne, fichier, nombre_cartes)

    return success

@app.route('/api/creneaux_reserves', methods=['GET'])
def api_creneaux_reserves():
    creneaux_reserves = ordonnanceur_deux_semaines.afficher_creneaux_reserves()
    return jsonify(creneaux_reserves)

@app.route('/api/creneaux_libres', methods=['POST'])
def api_creneaux_libres():
    creneaux_libres = ordonnanceur_deux_semaines.afficher_creneaux_disponibles(request.form['jour'])
    return jsonify(creneaux_libres)

@app.route('/api/annuler_reservation', methods=['POST'])
def api_annuler_reservation():
    ordonnanceur_deux_semaines.annuler_reservation(int(request.form['id_res']))
    return "Valid"

@app.route('/api/afficher_jours', methods=['GET'])
def api_afficher_jours():
    return jsonify(ordonnanceur_deux_semaines.jours)

@app.route('/api/afficher_heures', methods=['GET'])
def api_afficher_heures():
    return jsonify(ordonnanceur_deux_semaines.heures_de_travail)

@app.route('/api/annuler_reservations_personne', methods=['POST'])
def api_annuler_reservation_personne():
    resultat = ordonnanceur_deux_semaines.annuler_reservations_personne(request.form['nom_personne'])
    return resultat
    

# Configuration de la journalisation pour l'application Flask
def timetz(*args):
    return datetime.now(tz).timetuple()

tz = timezone('Europe/Paris')

logging.Formatter.converter = timetz
log_formatter = logging.Formatter('%(asctime)s [%(levelname)s] - %(message)s')
log_handler = TimedRotatingFileHandler('/home/ubuntu/logs/flask.log', when='midnight', interval=1, backupCount=30)
log_handler.setFormatter(log_formatter)
app.logger.addHandler(log_handler)
app.logger.setLevel(logging.INFO)


if __name__ == '__main__':
    app.run()

