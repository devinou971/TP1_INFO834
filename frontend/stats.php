<?php
include("./database.php");
mysqli_report(MYSQLI_REPORT_ALL)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link rel="stylesheet" href="./ressources/style.css">
</head>
<body>
    <?php
    $url = "127.0.0.1:5000/stats";

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $_SESSION);
    $response = json_decode(curl_exec($curl));

    if(isset($response->id_last_users) && isset($response->id_top_3)){
        $arr = $response->id_last_users + $response->id_top_3;
        $s = array_fill(0, count($arr), "?");
        
        $sql_prep = $conn->prepare("SELECT name, firstname, id FROM user WHERE id in (".join(", ", $s).")");
        $sql_prep->bind_param(str_repeat("i", count($arr)), ...$arr);
        $sql_prep->execute();
        $res = $sql_prep->get_result();
        $users = $res->fetch_all();
    }
    ?>

    <h1>Statistiques</h1>
    <main>
        <h3>Les derniers utilisateurs connectés:</h3>
        <ul>
        <?php
        foreach($response->id_last_users as $id){
            foreach($users as $user){
                if($user[2] == $id){
                    echo("<li>".$user[0]." ".$user[1]."</li>");
                }
            }
        }
        ?>
        </ul>

        <h3>Le top 3 des utilisateurs</h3>
        <ul>
        <?php
            foreach($response->id_top_3 as $id){
                foreach($users as $user){
                    if($user[2] == $id){
                        echo("<li>".$user[0]." ".$user[1]."</li>");
                    }
                }
            }
        ?>
        </ul>
    </main>

</body>
</html>