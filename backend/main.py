import redis
from flask import Flask, request, jsonify
from datetime import datetime, timedelta
from time import time

# Connection into redis
r = redis.Redis(host="192.168.69.1", port=6379, db=0)
serviceDb = redis.Redis(host="192.168.69.1", port=6379, db=1)

# Create Flask api : 
app = Flask(__name__)

@app.route("/connection", methods=["POST"])
def connection():
    connection_data = request.form
    id = connection_data["id"]
    results = r.lrange(f"conn:{id}", 0, 9)
    connection_times = []
    for re in results:
        connection_times.append(datetime.fromtimestamp(float(re.decode("utf-8"))))

    last_time = connection_times[-1]

    current_time = datetime.now()

    check = current_time - last_time
    print("Last time:", last_time, "now:", current_time, "delta:", check)

    if check <= timedelta(minutes=10) and len(connection_times) == 10:
        print(f"{id} not authorized to connect. Time to 10th connection :", check)
        return jsonify({"authorized": 0})

    r.lpush(f"conn:{id}", time())
    print(f"{id} authorized to connect. Time to 10th connection :", check)

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

if __name__ == "__main__":
    app.run(debug=True)
