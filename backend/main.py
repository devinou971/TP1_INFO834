import redis
from flask import Flask, request, jsonify
from datetime import datetime, timedelta
from time import time
from os import environ
from dotenv import load_dotenv
load_dotenv(".env")

REDIS_IP = environ.get("REDIS_IP")
PORT = environ.get("PORT")

# Connection into redis
r = redis.Redis(host=REDIS_IP, port=PORT, db=0)
serviceDb = redis.Redis(host=REDIS_IP, port=PORT, db=1)

# Create Flask api : 
app = Flask(__name__)

@app.route("/connection", methods=["POST"])
def connection():
    connection_data = request.form
    id = connection_data["id"]
    authorized = True
    if r.exists(f"conn:{id}"):
        results = r.lrange(f"conn:{id}", 0, 9)
        connection_times = []
        for re in results:
            connection_times.append(datetime.fromtimestamp(float(re.decode("utf-8"))))

        last_time = connection_times[-1]

        current_time = datetime.now()

        check = current_time - last_time
        print("Last time:", last_time, "now:", current_time, "delta:", check)
        authorized = not(check <= timedelta(minutes=10) and len(connection_times) == 10)

    if not authorized:
        print(f"{id} not authorized to connect.")
        return jsonify({"authorized": 0})

    r.lpush(f"conn:{id}", time())
    print(f"{id} authorized to connect.")

    return jsonify({"authorized": 1})

@app.route("/vente", methods= ["POST"])
def acces_vente():
    connection_data = request.form
    id = connection_data["id"]
    print(f"{id} accede au service de vente")
    serviceDb.lpush(f"vente:{id}", time())
    return jsonify({"ok": True})

@app.route("/achat", methods= ["POST"])
def acces_achat():
    connection_data = request.form
    id = connection_data["id"]
    print(f"{id} accede au service d'achat'")
    serviceDb.lpush(f"achat:{id}", time())
    return jsonify({"ok": True})

@app.route("/stats", methods= ["POST"])
def get_stats():
    # On récup tous les users et leurs connections
    
    all_users_keys = r.keys("conn:*")
    all_user_connections = []
    for k in all_users_keys:
        id = int(k.decode("utf-8").split(":")[-1])
        connection_timestamps = r.lrange(k, 0, -1)
        connection_times = []
        for c in connection_timestamps:
            connection_times.append(datetime.fromtimestamp(float(c.decode("utf-8"))))
        all_user_connections.append((id, connection_times))
    
    # ===== Les 10 derniers utilisateurs connectées =====
    
    ids_last_users = sorted(all_user_connections, key=lambda x: x[1][0], reverse=True)
    ids_last_users = [ids_last_users[i][0] for i in range(0, 10 if(len(ids_last_users) > 10) else len(all_user_connections))]


    # ===== Le top 3 des utilisateurs qui se sont connectés les 3 derniers jours =====

    top_3_users = sorted(
        all_user_connections, 
        key=lambda x: len([i for i in x[1] if datetime.now() - i < timedelta(days=3)]), reverse=True
    )
    ids_top_3 = [top_3_users[x][0] for x in range(0, 3 if len(top_3_users) > 3 else len(top_3_users))]


    return jsonify({"id_last_users": ids_last_users, "id_top_3": ids_top_3})

if __name__ == "__main__":
    app.run(debug=True)
