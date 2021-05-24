<?php
    require_once '../tools/dbconfig.php';

    $complete=false;
    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
    $step=$_POST["step"];

    require_once '../tools/signup_check.php';

    mysqli_close($conn);
    echo json_encode($error);
?>