<?php
    require_once '../tools/auth.php';

	if(!checkAuth())
		exit;
    
    $postType=$_GET["type"];
    $complete=$postType=="save";
    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));

    $filePath=''; //Settiamo così il valore di default della variabile perché il cambio dell'immagine non è obbligatorio

    $error = array();

    if(isset($_POST["nomeMuseo"])){
        if(($complete || !empty($_POST["nomeMuseo"])) && (strlen($_POST['nomeMuseo'])<4 || strlen($_POST['nomeMuseo'])>30)) {
            $error[] = "Nome del museo non valido";
        }else{
            $_POST["nomeMuseo"] = mysqli_real_escape_string($conn, $_POST['nomeMuseo']);
            $query = "SELECT * FROM museo m
                    WHERE m.Id<>{$_SESSION["id_museo"]} and m.nome = '{$_POST["nomeMuseo"]}' and m.citta in (select c.id from citta c join museo m where c.id=m.citta)";
            $res = mysqli_query($conn, $query);
            if (mysqli_num_rows($res) > 0) {
                $error[] = "Esiste già un museo con questo nome nella città specificata";
            }
            mysqli_free_result($res);
        }

        if (!empty($_POST['latitudineMuseo']) && !is_numeric($_POST['latitudineMuseo'])) {
            $error[] = "La latitudine deve essere un numero";
        }

        if (!empty($_POST['longitudineMuseo']) && !is_numeric($_POST['longitudineMuseo'])) {
            $error[] = "La longitudine deve essere un numero";
        }

        if ($complete && empty($_POST['tipoMuseo'])) {
            $error[] = "Si deve inserire il tipo del museo";
        }

        if (!empty($_POST['costoBiglietto']) && !is_numeric($_POST['costoBiglietto'])) {
            $error[] = "Il costo del biglietto deve essere un numero";
        }

        if ($_SESSION["museoPubblicoPrivato"]=='privato') {
            if (!empty($_POST["nomeSocieta"]) && (strlen($_POST["nomeSocieta"]) > 30 || strlen($_POST["nomeSocieta"]) < 3)) {
                $error[] = "Inserire un nome della società compreso tra 3 e 30 caratteri";
            }
        }

        function must_be_numbers($number) { return preg_replace('/[^0-9]/', '', $number); };
        if($complete || !empty($_POST["telefono1"])){
            $_POST["telefono1"]=must_be_numbers($_POST["telefono1"]);
            if (strlen($_POST["telefono1"]) > 15 || strlen($_POST["telefono1"]) < 8) {
                $error[] = "Il primo numero di telefono non è valido";
            }else{
                $telefono1 = mysqli_real_escape_string($conn, strtolower($_POST["telefono1"]));
                $res = mysqli_query($conn, "SELECT telefono1 FROM utente WHERE id<>{$_SESSION["id_utente"]} and (telefono1 = '$telefono1' || telefono2 = '$telefono1')");
                if (mysqli_num_rows($res) > 0) {
                    $error[] = "Il primo numero di telefono è già registrato";
                }
                mysqli_free_result($res);
            }
        }

        if(!empty($_POST["telefono2"])){
            $_POST["telefono2"]=must_be_numbers($_POST["telefono2"]);
            if (strlen($_POST["telefono2"]) > 15 || strlen($_POST["telefono2"]) < 8){
                $error[] = "Il secondo numero di telefono non è valido";
            }else{
                $telefono2 = mysqli_real_escape_string($conn, strtolower($_POST["telefono2"]));
                $res = mysqli_query($conn, "SELECT telefono2 FROM utente WHERE id<>{$_SESSION["id_utente"]} and (telefono1 = '$telefono2' || telefono2 = '$telefono2')");
                if (mysqli_num_rows($res) > 0) {
                    $error[] = "Il secondo numero di telefono è già registrato";
                }
                mysqli_free_result($res);
            }
        } 

        if (!empty($_POST["introMuseo"]) && (strlen($_POST["introMuseo"]) > 2000 || strlen($_POST["introMuseo"]) < 100)) {
            $error[] = "L'introduzione non è della lunghezza adeguata";
        }
    }

    if(count($error)==0 && $postType=="save"){
        $nomeMuseo=mysqli_real_escape_string($conn,$_POST["nomeMuseo"]);
        $latitudineMuseo=mysqli_real_escape_string($conn,$_POST["latitudineMuseo"]);
        $longitudineMuseo=mysqli_real_escape_string($conn,$_POST["longitudineMuseo"]);
        $tipoMuseo=mysqli_real_escape_string($conn,$_POST["tipoMuseo"]);
        $costoBiglietto=mysqli_real_escape_string($conn,$_POST["costoBiglietto"]);
        $dataApertura=mysqli_real_escape_string($conn,$_POST["dataApertura"]);
        $nomeSocieta=mysqli_real_escape_string($conn,$_POST["nomeSocieta"]);
        $introMuseo=mysqli_real_escape_string($conn,$_POST["introMuseo"]);

        if (isset($_FILES['immagineMuseo'])){
            $file = $_FILES['immagineMuseo'];
            if(!empty($file['tmp_name'])){
                $type = exif_imagetype($file['tmp_name']);
                $allowedExt = array(IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg');
                if (isset($allowedExt[$type])) {
                    if ($file['error'] === 0) {
                        if ($file['size'] <= 10000000) {
                            $res=mysqli_query($conn, "SELECT immagine_museo FROM museo WHERE id=".$_SESSION["id_museo"]);
                            $row = mysqli_fetch_object($res);
                            if(!empty($row->immagine_museo)){
                                try{
                                    unlink("../$row->immagine_museo");
                                }catch(Exception $var){
                                }
                            }
                            mysqli_free_result($res);
            
                            $fileName = uniqid('', true).".".$allowedExt[$type];
                            $filePath = 'images/userImages/'.$fileName;
                            move_uploaded_file($file['tmp_name'], "../$filePath");
                        }else{
                            $error[] = "L'immagine non deve avere dimensioni maggiori di 10MB";
                        }
                    }else{
                        $error[] = "Errore nel caricamento del file";
                    }
                }else{    
                    $error[] = "I formati consentiti sono .png, .jpeg e .jpg";
                }
            }
        }

        if (count($error)==0) {
            $nomeMuseo = mysqli_real_escape_string($conn, $nomeMuseo);
            $tipoMuseo = mysqli_real_escape_string($conn, $tipoMuseo);
            $dataApertura = mysqli_real_escape_string($conn, $dataApertura);
            $latitudineMuseo = mysqli_real_escape_string($conn, $latitudineMuseo);
            $longitudineMuseo = mysqli_real_escape_string($conn, $longitudineMuseo);
            $costoBiglietto = mysqli_real_escape_string($conn, $costoBiglietto);
            $introMuseo = mysqli_real_escape_string($conn, $introMuseo);
            $filePath = mysqli_real_escape_string($conn, $filePath);

            $query = "UPDATE museo SET nome='$nomeMuseo', tipo=$tipoMuseo, data_apertura='$dataApertura'"
                .", lat=".(empty($latitudineMuseo) ? 'null' : $latitudineMuseo)
                .", lon=".(empty($longitudineMuseo) ? 'null' : $longitudineMuseo)
                .", costo_biglietto=".(empty($costoBiglietto) ? 'null' : $costoBiglietto)
                .", introduzione=".(empty($introMuseo) ? 'null' : "'$introMuseo'")
                .(!empty($filePath) ? ",immagine_museo='$filePath'" : "")
                ." WHERE id={$_SESSION["id_museo"]}"
            ;

            if (!mysqli_query($conn, $query)){
                $error[] = "Errore durante il salvataggio dei dati del museo";
            }
        }

        if (count($error)==0) {
            $telefono1=mysqli_real_escape_string($conn,$_POST["telefono1"]);
            $telefono2=mysqli_real_escape_string($conn,$_POST["telefono2"]);

            $query = "UPDATE utente SET telefono1='$telefono1'"
                .", telefono2=".(empty($telefono1) ? 'null' : "'$telefono2'")
                ." WHERE id={$_SESSION["id_utente"]}"
            ;
            if (!mysqli_query($conn, $query)){
                $error[] = "Errore durante il salvataggio dei dati utente";
            }
        }

        if (count($error)==0 && $_SESSION["museoPubblicoPrivato"]=='privato'){
            $nomeSocieta = mysqli_real_escape_string($conn, $nomeSocieta);
            $query = "UPDATE museo_privato SET nome_societa='$nomeSocieta' WHERE museo={$_SESSION["id_museo"]}";
            if (!mysqli_query($conn, $query)){
                $error[] = "Errore durante il salvataggio del nome della società";
            }
        }
    }

    mysqli_close($conn);

    if($postType=="save" && count($error)==0)
	    echo json_encode(array('filePath' => $filePath));
    else
        echo json_encode($error);
?>