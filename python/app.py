from flask import Flask, jsonify, render_template, request
from datetime import datetime, timedelta

app = Flask(__name__)

class Ordonnanceur:
    def __init__(self, jours, heures_de_travail, jours_a_afficher=30):
        self.jours = [(datetime.now() + timedelta(days=i)).strftime('%Y-%m-%d') for i in range(jours_a_afficher)]
        self.heures_de_travail = heures_de_travail
        self.creneaux_disponibles = {jour: {heure: True for heure in heures_de_travail} for jour in self.jours}
        self.reservations = {}

    def afficher_creneaux_disponibles(self, jour):
        if jour in self.jours:
            print(f"Créneaux disponibles pour le {jour}:")
            #for heure, disponible in self.creneaux_disponibles[jour].items():
            #    if disponible:
            #        return heure
            return [(heure) for heure, disponible in self.creneaux_disponibles[jour].items() if disponible]
        else:
            print(f"La date {jour} n'est pas incluse dans les jours de l'ordonnanceur.")

    def reserver_creneau(self, jour, heure, personne, fichier):
        if jour in self.jours:
            if heure in self.creneaux_disponibles[jour] and self.creneaux_disponibles[jour][heure]:
                self.creneaux_disponibles[jour][heure] = False
                self.reservations[(jour, heure)] = {'personne': personne, 'fichier': fichier}
                reponse = f"Créneau réservé par {personne} le {jour} à {heure}. Fichier associé : {fichier}."
                return reponse
            else:
                reponse = f"Le créneau à {heure} le {jour} n'est pas disponible."
                return reponse
        else:
            reponse = f"La date {jour} n'est pas incluse dans les jours de l'ordonnanceur."
            return reponse



    def afficher_creneaux_reserves(self):
        creneaux_reserves = []

        for (jour, heure), details_reservation in self.reservations.items():
            personne = details_reservation['personne']
            fichier = details_reservation.get('fichier', None)  # Utilisez get() pour éviter une KeyError si 'fichier' n'est pas présent
            creneaux_reserves.append((jour, heure, personne, fichier))

        return creneaux_reserves

    def annuler_reservation(self, jour, heure):
        if jour in self.jours:
            if (jour, heure) in self.reservations:
                personne = self.reservations.pop((jour, heure))
                self.creneaux_disponibles[jour][heure] = True
                print(f"Réservation annulée pour {personne} le {jour} à {heure}.")
            else:
                print(f"Aucune réservation trouvée pour le {jour} à {heure}.")
        else:
            print(f"La date {jour} n'est pas incluse dans les jours de l'ordonnanceur.")


    def mettre_a_jour_jours(self,jours_a_afficher=14):
        self.jours = [(datetime.now() + timedelta(days=i+1)).strftime('%Y-%m-%d') for i in range(jours_a_afficher)]


# Exemple d'utilisation pour deux semaines
heures_de_travail = ["08:00", "09:00", "10:00", "11:00", "14:00", "15:00", "16:00", "17:00"]
ordonnanceur_deux_semaines = Ordonnanceur([], heures_de_travail, jours_a_afficher=30)

# Exemple d'utilisation pour un jour spécifique
douze_jours = (datetime.now() + timedelta(days=12)).strftime('%Y-%m-%d')
if douze_jours in ordonnanceur_deux_semaines.jours:
    ordonnanceur_deux_semaines.afficher_creneaux_disponibles(douze_jours)
else:
    print(f"La date {douze_jours} n'est pas incluse dans les jours de l'ordonnanceur.")

# Afficher les créneaux réservés
ordonnanceur_deux_semaines.afficher_creneaux_reserves()



@app.route('/api/reserver', methods=['POST'])
def reserver():
    jour = request.form['jour']
    heure = request.form['heure']
    personne = request.form['personne']
    fichier = request.form['nom_fichier']

    success = ordonnanceur_deux_semaines.reserver_creneau(jour, heure, personne, fichier)

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
    ordonnanceur_deux_semaines.annuler_reservation(request.form['jour'], request.form['heure'])

@app.route('/api/afficher_jours', methods=['GET'])
def api_afficher_jours():
    return jsonify(ordonnanceur_deux_semaines.jours)

@app.route('/api/afficher_heures', methods=['GET'])
def api_afficher_heures():
    return jsonify(ordonnanceur_deux_semaines.heures_de_travail)

if __name__ == '__main__':
    app.run(host='10.5.0.4', port=5000, debug=True)
