<?php
    require_once '../tools/dbconfig.php';
    
    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
    $error = array();

    $result=array();
    $idMuseo=mysqli_real_escape_string($conn, $_GET["idMuseo"]);
    $anno_inizio=isset($_GET["annoInizio"]) ? mysqli_real_escape_string($conn, $_GET["annoInizio"]) : "";
    $anno_fine=isset($_GET["annoFine"]) ? mysqli_real_escape_string($conn, $_GET["annoFine"]) : "";

    $hasErrors=false;
    if(!empty($anno_inizio) && !empty($anno_fine)){
        if($anno_inizio>$anno_fine || !filter_var($anno_inizio, FILTER_VALIDATE_INT) || !filter_var($anno_fine, FILTER_VALIDATE_INT))
            $hasErrors=true;
    }else if(!empty($anno_inizio)){
        $anno_fine='null';
        if(!filter_var($anno_inizio, FILTER_VALIDATE_INT))
            $hasErrors=true;
    }else if(!empty($anno_fine)){
        $anno_inizio='null';
        if(!filter_var($anno_fine, FILTER_VALIDATE_INT))
            $hasErrors=true;
    }else{
        $anno_inizio='null';
        $anno_fine='null';
    }
    if(!$hasErrors){
        $query="CALL pFiltraOpereAnno($idMuseo, $anno_inizio, $anno_fine)"; //Ho preferito usare una procedura piuttosto che una select a scopo didattico
        mysqli_multi_query($conn,$query);
        $res=mysqli_use_result($conn);
        while($row = mysqli_fetch_object($res)){
            $opera=array();
            $opera["id"]=$row->id;
            $opera["nome"]=$row->nome;
            $opera["autore"]=$row->autore;
            $opera["anno_inizio_creazione"]=$row->anno_inizio_creazione;
            $opera["anno_ultimatura"]=$row->anno_ultimatura;
            $opera["immagine_opera"]=$row->immagine_opera;

            $result[]=$opera;
        }
        mysqli_free_result($res);
    }

    mysqli_close($conn);
    echo json_encode($result);
?>