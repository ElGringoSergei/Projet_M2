# Importe les modules nécessaires
from flask import Flask, jsonify, render_template, request
import subprocess

app = Flask(__name__)


# Route pour exécuter une commande shell
@app.route('/run_command', methods=['POST'])
def run_command():
    print('Requete recue\n')
    command = request.form['command']
    
    # Utilise subprocess pour exécuter la commande
    result = subprocess.run(command, shell=True, capture_output=True, text=True)
    
    # Retourne la sortie de la commande
    return {"error": result.stderr, "output": result.stdout}

# Si c'est le fichier principal, lance l'application avec Gunicorn
if __name__ == '__main__':
    app.run()

