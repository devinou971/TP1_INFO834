
<?php
include("./database.php")
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="./ressources/style.css">
</head>
<body>
    
    <h1>Services</h1>
    <main>
        <p>
            Bonjour <?php if(isset($_SESSION["name"])) echo($_SESSION["name"]." ".$_SESSION["firstname"]) ?>
        </p>
        <?php
        if(isset($_POST) && isset($_POST["serviceName"]) ){
            if($_POST["serviceName"] == "Service Vente"){
                $url = "127.0.0.1:5000/vente";

                $curl = curl_init($url);

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                curl_setopt($curl, CURLOPT_POSTFIELDS, $_SESSION);
                $response = json_decode(curl_exec($curl));
                ?>
                <p>
                    Vous êtes dans le service de vente
                </p>
                <?php

            } else if ($_POST["serviceName"] == "Service Achat"){
                $url = "127.0.0.1:5000/achat";

                $curl = curl_init($url);

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                curl_setopt($curl, CURLOPT_POSTFIELDS, $_SESSION);
                $response = json_decode(curl_exec($curl));
                ?>
                <p>
                    Vous êtes dans le service d'achat
                </p>
                <?php
            }
        }
        ?>
        <form action="services.php" method="post">
            <input type="submit" name="serviceName" value="Service Vente">
            <input type="submit" name="serviceName" value="Service Achat">
        </form>
    </main>
    
</body>
</html>