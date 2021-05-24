<?php
    require_once '../tools/dbconfig.php';

    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
    $provincia=$_GET["sigla"];
    $res=mysqli_query($conn, "SELECT * FROM citta where provincia='$provincia' order by comune");
    echo '<option value="">-</option>';
    while($row = mysqli_fetch_object($res)){
        echo '<option value="'.$row->id.'">'.$row->comune.'</option>';
    }
    mysqli_free_result($res);
    
    mysqli_close($conn);
?>