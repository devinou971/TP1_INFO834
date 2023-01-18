<?php
include("./database.php")
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./ressources/style.css">
</head>
<body>
    <h1>Connexion</h1>
    <main>
    <?php
        if(isset($_POST) && isset($_POST["email"]) && isset($_POST["password"]) && !empty($_POST["email"]) && !empty($_POST["password"])){
            $sql_prep = $conn->prepare("SELECT name, firstname, id FROM user WHERE email = ? and password = ?");
            $sql_prep->bind_param("ss", $_POST["email"], $_POST["password"]);
            $sql_prep->execute();
            $sql_prep->bind_result($name, $firstname, $id);
            $sql_prep->fetch();

            if(is_null($name)){
                ?>
                <p class="error">Erreur : L'identifiant ou le mot de passe est faux </p>
                <?php
            } else {

                $_SESSION["name"] = $name;
                $_SESSION["firstname"] = $firstname;
                $_SESSION["id"] = $id;
                $url = "127.0.0.1:5000/connection";

                $curl = curl_init($url);

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                curl_setopt($curl, CURLOPT_POSTFIELDS, $_SESSION);
                $response = json_decode(curl_exec($curl));
                if($response->authorized == 1){
                    ?>
                    <a href="services.php">Acceder aux services</a>
                    <?php
                } else {
                    ?>
                    <p class="error">Vous avez atteint le nombre maximum de session (10 toutes mes 10 minutes). Veuillez ptienter avant de vous reconnecter.</p>
                    <?php
                }
            }
        }
        $conn->close();
    ?>

        <form action="login.php" method="post">
            <div>
                <label for="email">email</label>
                <input type="text" name="email" id="email">
            </div>
            <div>
                <label for="password">password</label>
                <input type="password" name="password" id="password">
            </div>
            <input type="submit" value="Se connecter">
        </form>
    </main>
</body>
</html>