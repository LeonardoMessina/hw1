<?php
    $error = array();

    $_POST['username']=!isset($_POST['username']) ? null : mysqli_real_escape_string($conn, $_POST['username']);
    $_POST['password']=!isset($_POST['password']) ? null : mysqli_real_escape_string($conn, $_POST['password']);
    $_POST['confirm_password']=!isset($_POST['confirm_password']) ? null : mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $_POST['email']=!isset($_POST['email']) ? null : mysqli_real_escape_string($conn, $_POST['email']);
    $_POST['nomeMuseo']=!isset($_POST['nomeMuseo']) ? null : mysqli_real_escape_string($conn, $_POST['nomeMuseo']);
    $_POST['cittaMuseo']=!isset($_POST['cittaMuseo']) ? null : mysqli_real_escape_string($conn, $_POST['cittaMuseo']);
    $_POST['tipoMuseo']=!isset($_POST['tipoMuseo']) ? null : mysqli_real_escape_string($conn, $_POST['tipoMuseo']);
    $_POST['dataApertura']=!isset($_POST['dataApertura']) ? null : mysqli_real_escape_string($conn, $_POST['dataApertura']);
    $_POST['telefono1']=!isset($_POST['telefono1']) ? null : mysqli_real_escape_string($conn, $_POST['telefono1']);
    $_POST['telefono2']=!isset($_POST['telefono2']) ? null : mysqli_real_escape_string($conn, $_POST['telefono2']);
    
    if($step=="0"){
        if($complete || !empty($_POST['username'])){
            if(!preg_match('/^[a-zA-Z0-9_$.]{4,16}$/', $_POST['username'])) {
                $error[] = "Nome utente non valido";
            } else {
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $query = "SELECT username FROM utente WHERE username = '$username'";
                $res = mysqli_query($conn, $query);
                if (mysqli_num_rows($res) > 0) {
                    $error[] = "Nome utente già utilizzato";
                }
                mysqli_free_result($res);
            }
        }

        if(($complete || !empty($_POST['password'])) && !preg_match('/^[a-zA-Z0-9_$!.%]{8,16}$/', $_POST['password'])){
            $error[] = "Password non valida";
        }

        if (($complete || !empty($_POST['password'])) && strcmp($_POST["password"], $_POST["confirm_password"]) != 0) {
            $error[] = "Le password non coincidono";
        }

        if ($complete || !empty($_POST['email'])){
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $error[] = "Email non valida";
            } else {
                $email = mysqli_real_escape_string($conn, strtolower($_POST['email']));
                $res = mysqli_query($conn, "SELECT email FROM utente WHERE email = '$email'");
                if (mysqli_num_rows($res) > 0) {
                    $error[] = "Email già utilizzata";
                }
                mysqli_free_result($res);
            }
        }
    }else if($step=="1"){
        if(isset($_POST["submit"]) && $_POST["submit"]=="Indietro"){
            $step="0";
        }else{
            if(($complete || !empty($_POST['nomeMuseo'])) && (strlen($_POST['nomeMuseo'])<4 || strlen($_POST['nomeMuseo'])>30)) {
                $error[] = "Nome del museo non valido";
            }

            if($complete && empty($_POST["museoPubblicoPrivato"])){
                $error[] = "Specificare se il museo è pubblico o privato";
            }

            if($complete && empty($_POST["cittaMuseo"])){
                $error[] = "Inserire la città del museo";
            }
            if($complete && empty($_POST["tipoMuseo"])){
                $error[] = "Inserire il tipo del museo";
            }            
            if($complete && empty($_POST["dataApertura"]))
                $error[] = "Inserire una data";
            $query = "SELECT * FROM museo WHERE nome = '{$_POST["nomeMuseo"]}' and citta = '{$_POST["cittaMuseo"]}'";
            $res = mysqli_query($conn, $query);
            if (mysqli_num_rows($res) > 0) {
                $error[] = "Museo già registrato";
            }
            mysqli_free_result($res);

            function must_be_numbers($number) { return preg_replace('/[^0-9]/', '', $number); };

            if($complete || !empty($_POST["telefono1"])){
                $_POST["telefono1"]=must_be_numbers($_POST["telefono1"]);
                if (strlen($_POST["telefono1"]) > 15 || strlen($_POST["telefono1"]) < 8) {
                    $error[] = "Il primo numero di telefono non è valido";
                } else {
                    $telefono1 = mysqli_real_escape_string($conn, strtolower($_POST["telefono1"]));
                    $res = mysqli_query($conn, "SELECT telefono1 FROM utente WHERE telefono1 = '$telefono1'");
                    if (mysqli_num_rows($res) > 0) {
                        $error[] = "Il primo numero di telefono è già registrato";
                    }
                    mysqli_free_result($res);
                }
            }

            if(!empty($_POST["telefono2"])){
                $_POST["telefono2"]=must_be_numbers($_POST["telefono2"]);
                if (strlen($_POST["telefono2"]) > 15 ||  strlen($_POST["telefono2"]) < 8) {
                    $error[] = "Il secondo numero di telefono non è valido";
                }else {
                    $telefono2 = mysqli_real_escape_string($conn, strtolower($_POST["telefono2"]));
                    $res = mysqli_query($conn, "SELECT telefono2 FROM utente WHERE telefono2 = '$telefono2'");
                    if (mysqli_num_rows($res) > 0) {
                        $error[] = "Il secondo numero di telefono è già registrato";
                    }
                    mysqli_free_result($res);
                }
            }
        }
    }
?>