# TP INFO 834 : Redis

## Installation 

### Mise en palce de la base de données

La première étape de l'installation est l'installation de la base de données. Le script de création se trouve dans le dossier `database`.

### Mise en place du backend

Le backend est une API Flask qui se trouve dans le dossier `backend`.

Afin de mettre le backend en état de fonctionnement, il faut tout d'abord modifier le fichier `.env` présent dans le dossier `backend`. Une configuration basique est : 
```js
REDIS_IP = "127.0.0.1"
PORT = 6379
```

La prochaine étape est l'installation des dépendances python avec : 
```sh
pip install -r backend/requirements.txt
```

### Mise en place du frontend

Pour le frontend, il suffit de vérifier l'IP et le mot de passe pour acceder à la base de données. Ces informations sont présentes dans le fichier `frontend/database.php`.

## Utilisation 

Pour utiliser le programme, il faut d'abord lancer le backend : 
```
python3 backend/main.py
``` 

Puis on peut lancer le frontend avec apache.