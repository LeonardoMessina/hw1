<?php
    require_once '../tools/auth.php';

	if(!checkAuth())
		exit;
    
    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
    $error = array();

    $operationType=$_GET["type"];

    if($operationType=="delete"){
        $idOpera=mysqli_real_escape_string($conn, $_GET["idToDelete"]);

        $query = "SELECT immagine_opera FROM opera WHERE id=$idOpera";
        $res=mysqli_query($conn, $query);
        if (!$res){
            $error[] = "Errore col database (".mysqli_errno($conn).": ".mysqli_error($conn).")";
        }else{
            $row = mysqli_fetch_object($res);
            try{
                unlink("../$row->immagine_opera");
            }catch(Exception $var){
            }
            mysqli_free_result($res);
        }

        $query = "DELETE FROM opera WHERE id=$idOpera";
        if (!mysqli_query($conn, $query)){
            $error[] = "Errore col database (".mysqli_errno($conn).": ".mysqli_error($conn).")";
        }
        exit;
    }

    $complete=$operationType=="save";

    if(isset($_POST["nomeOpera"])){
        if(($complete || !empty($_POST["nomeOpera"])) && !preg_match("/^[a-zA-Z0-9 ']{1,30}$/", $_POST['nomeOpera'])) {
            $error[] = "Nome dell'opera non valido";
        }
        
        if(($complete || !empty($_POST["autoreOpera"])) && !preg_match("/^[a-zA-Z ']{2,30}$/", $_POST['autoreOpera'])) {
            $error[] = "Nome dell'autore non valido ";
        }else if(!empty($_POST["autoreOpera"])){
            $_POST['nomeOpera'] = mysqli_real_escape_string($conn,$_POST['nomeOpera']);
            $autoreOpera = mysqli_real_escape_string($conn, $_POST['autoreOpera']);
            $query = "SELECT * FROM opera WHERE nome='{$_POST['nomeOpera']}' and autore='$autoreOpera'";
            $res = mysqli_query($conn, $query);
            if (mysqli_num_rows($res) > 0) {
                $error[] = "Opera già inserita";
            }
            mysqli_free_result($res);
        }

        if (!empty($_POST["annoInizioCreazioneOpera"]) && !filter_var($_POST['annoInizioCreazioneOpera'], FILTER_VALIDATE_INT)) {
            $error[] = "L'anno di inizio della creazione dell'opera non è valido";
        }
    
        if (!empty($_POST["annoUltimaturaOpera"]) && !filter_var($_POST['annoUltimaturaOpera'], FILTER_VALIDATE_INT)) {
            $error[] = "L'anno di ultimatura dell'opera non è valido";
        }

        if (!empty($_POST["annoInizioCreazioneOpera"]) && !empty($_POST["annoUltimaturaOpera"]) && $_POST["annoInizioCreazioneOpera"]>$_POST["annoUltimaturaOpera"]) {
            $error[] = "L'anno di inizio della creazione dell'opera deve essere antecedente a quello di ultimatura";
        }
    }

    if(count($error)==0 && $operationType=="save"){
        if (isset($_FILES['immagineOpera'])){
            $file = $_FILES['immagineOpera'];
            if(!empty($file['tmp_name'])){
                $type = exif_imagetype($file['tmp_name']);
                $allowedExt = array(IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg');
                if (isset($allowedExt[$type])) {
                    if ($file['error'] === 0) {
                        if ($file['size'] <= 10000000) {
                            $fileName = uniqid('', true).".".$allowedExt[$type];
                            $filePath = 'images/userImages/'.$fileName;
                            move_uploaded_file($file['tmp_name'], "../$filePath");
                        } else {
                            $error[] = "L'immagine non deve avere dimensioni maggiori di 10MB";
                        }
                    } else {
                        $error[] = "Errore nel caricamento del file";
                    }
                } else {
                    $error[] = "I formati consentiti sono .png, .jpeg e .jpg";
                }
            }else{
                $error[] = "Inserire un'immagine dell'opera";
            }
        }else{
            $error[] = "Inserire un'immagine dell'opera";
        }
    
        if (count($error) == 0) {
            $nomeOpera = mysqli_real_escape_string($conn, $_POST['nomeOpera']);
            $autoreOpera = mysqli_real_escape_string($conn,$autoreOpera);
            $annoInizioCreazioneOpera = mysqli_real_escape_string($conn, $_POST["annoInizioCreazioneOpera"]);
            $annoUltimaturaOpera = mysqli_real_escape_string($conn, $_POST["annoUltimaturaOpera"]);
            $filePath = mysqli_real_escape_string($conn, $filePath);
    
            $query = "INSERT INTO opera (nome, autore, anno_inizio_creazione, anno_ultimatura, museo, immagine_opera) VALUES ('$nomeOpera', '$autoreOpera', '$annoInizioCreazioneOpera', '$annoUltimaturaOpera', '{$_SESSION["id_museo"]}', '$filePath')";
            if (!mysqli_query($conn, $query)){
                $error[] = "Errore col database (".mysqli_errno($conn).": ".mysqli_error($conn).")";
            }
            $id_opera=mysqli_insert_id($conn);
        }  
    }

    mysqli_close($conn);
    if($operationType=="save" && count($error)==0){
	    echo json_encode(array('idOpera' => $id_opera, 'filePath' => $filePath));
    }else
        echo json_encode($error);
?>