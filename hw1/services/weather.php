<?php
    require_once '../tools/dbconfig.php';

    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));

    $id=$_GET["id"];

    $res=mysqli_query($conn, "SELECT c.comune FROM museo m join citta c on m.citta=c.id WHERE m.id=$id");
    if(!$res){
        echo mysqli_error($conn);
        exit;
    }


    $row = mysqli_fetch_object($res);
    mysqli_free_result($res);

    $curl = curl_init();
    $url="https://api.weatherbit.io/v2.0/current?key=70d16d188ac74cbfa74101278771a2b3&lang=it&city=".urlencode ($row->comune);
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    echo curl_exec($curl);

    mysqli_close($conn);
?>