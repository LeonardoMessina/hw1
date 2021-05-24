<?php
    require_once 'tools/auth.php';

    if (checkAuth()) {
        header("Location: home.php");
        exit;
    }

    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));

    $nextForceDisable=false;
    $nextForceEnabled=false;

    $step=isset($_POST["step"]) ? $_POST["step"] : "0";
    if(isset($_POST["step"])){
        if($step=="1" && $_POST["submit"]=="Indietro"){
            $step="0";
            $nextForceEnabled=true;
        }else{
            $complete=true; //Questa variabile, usata da signup_check, serve per dire se si devono controllare o meno i campi vuoti
            require_once 'tools/signup_check.php';
            if(count($error)==0){
                if($step=="0"){
                    $step="1";
                    $nextForceDisable=true;
                }else if($step=="1"){
                    if (count($error) == 0) {
                        if (isset($_FILES['immagineMuseo'])){
                            $file = $_FILES['immagineMuseo'];
                            if(!empty($file['tmp_name'])){
                                $type = exif_imagetype($file['tmp_name']);
                                $allowedExt = array(IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg');
                                if (isset($allowedExt[$type])) {
                                    if ($file['error'] === 0) {
                                        if ($file['size'] <= 10000000) {
                                            $fileName = uniqid('', true).".".$allowedExt[$type];
                                            $filePath = 'images/userImages/'.$fileName;
                                            move_uploaded_file($file['tmp_name'], $filePath);
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
                                $error[] = "Inserire un'immagine del museo";
                            }
                        }else{
                            $error[] = "Inserire un'immagine del museo";
                        }
                    }
                    if (count($error) == 0) {
                        $username = mysqli_real_escape_string($conn, $_POST['username']);
                        $email = mysqli_real_escape_string($conn, $_POST['email']);
                        $nomeMuseo = mysqli_real_escape_string($conn, $_POST['nomeMuseo']);
                        $museoPubblicoPrivato = $_POST['museoPubblicoPrivato'];
                        $cittaMuseo = mysqli_real_escape_string($conn, $_POST['cittaMuseo']);
                        $tipoMuseo = mysqli_real_escape_string($conn, $_POST['tipoMuseo']);
                        $dataApertura = mysqli_real_escape_string($conn, $_POST['dataApertura']);
                        $password = mysqli_real_escape_string($conn, $_POST['password']);
                        $password = password_hash($password, PASSWORD_BCRYPT);
                
                        $query = "INSERT INTO museo (nome, citta, tipo, data_apertura, immagine_museo) VALUES ('$nomeMuseo', $cittaMuseo, $tipoMuseo, '$dataApertura', '$filePath')";
                        if (mysqli_query($conn, $query)){
                            $id_museo=mysqli_insert_id($conn);
                            $_SESSION["nome_museo"] = $nomeMuseo;
                            $_SESSION["id_museo"] = $id_museo;
                
                            $telefono2=empty($telefono2) ? "NULL" : "'$telefono2'";
                        }else{
                            $error[] = "Errore col database (1.".mysqli_errno($conn).": ".mysqli_error($conn).")";
                        }
                
                        if (count($error) == 0){
                            $query = "INSERT INTO utente(museo, username, password, email, telefono1, telefono2) VALUES($id_museo,'$username', '$password', '$email', '$telefono1', $telefono2)";
                            if (mysqli_query($conn, $query)) {
                                $_SESSION["id_utente"] = mysqli_insert_id($conn);
                                $_SESSION["username"] = $username;
                                $_SESSION["email"] = $email;
                            } else {
                                $error[] = "Errore col database (2.".mysqli_errno($conn).": ".mysqli_error($conn).")";
                            }
                        }
                
                        if (count($error) == 0){
                            $query = "INSERT INTO museo_$museoPubblicoPrivato (museo) VALUES($id_museo)";
                            if (mysqli_query($conn, $query)) {
                                $_SESSION["museoPubblicoPrivato"] = $museoPubblicoPrivato;
                            } else {
                                $error[] = "Errore col database (3.".mysqli_errno($conn).": ".mysqli_error($conn).")";
                            }
                        }
                
                        if (count($error)==0){
                            mysqli_close($conn);
                            header("Location: home.php");
                            exit;
                        }
                    }
                }
            }
        }
    }
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="favicon.png">
        <title>Musei italiani - Registrazione</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
        <link href="style/common.css" rel="stylesheet">
        <link href="style/signup.css" rel="stylesheet">
        <script src="scripts/common.js" defer></script>
        <script src="scripts/signup.js" defer></script>
    </head>
    <body>
        <header>
            <h1>Registrazione</h1>
            <a id='login' href='login.php' class='<?php if (checkAuth()) echo " hidden" ?>'>Login</a>
            <a id='logout' class='<?php if (!checkAuth()) echo " hidden" ?>'>Logout</a>
        </header>
        <nav>
            <a id='firstChildNav' href='home.php'>Home</a>
            <a href='chi_siamo.php'>Chi siamo</a>
            <a href='musei.php'>Musei</a>
            <?php if (checkAuth()) echo "<a href='personal_area.php'>Area personale</a>"; ?>
            <a id='lastChildNav' href='contattaci.php'>Contattaci</a>
        </nav>
        <div id='content'>
            <div>
                <form autocomplete='off' method='post' enctype="multipart/form-data">
                    <h1>Registrati, è gratuito!</h1>
                    <input type="hidden" name="step" value="<?php echo $step ?>"/>
                    <div class='page<?php if($step<>"0"){echo " hidden";} ?>'>
                        <div>
                            <div><label for='username'>Nome utente</label></div>
                            <div><input type='text' id='username' name='username'<?php if(isset($_POST["username"])){echo "value='".$_POST["username"]."'";} ?>/></div>
                        </div>
                        <div>
                            <div><label for='password'>Password</label></div>
                            <div><input type='password' id='password' name='password' <?php if(isset($_POST["password"])){echo "value='".$_POST["password"]."'";} ?> maxlength="16"/></div>
                        </div>
                        <div>
                            <div><label for='confirm_password'>Conferma Password</label></div>
                            <div><input type='password' id='confirm_password' name='confirm_password' <?php if(isset($_POST["confirm_password"])){echo "value='".$_POST["confirm_password"]."'";} ?>/></div>
                        </div>
                        <div>
                            <div><label for='email'>Email</label></div>
                            <div><input type='text' id='email' name='email' <?php if(isset($_POST["email"])){echo "value='".$_POST["email"]."'";} ?>/></div>
                        </div>
                    </div>
                    <div class='page<?php if($step<>"1"){echo " hidden";} ?>'>
                        <div class="nomeMuseo">
                            <div><label for='nomeMuseo'>Nome del museo</label></div>
                            <div><input type='text' id='nomeMuseo' name='nomeMuseo' <?php if(isset($_POST["nomeMuseo"])){echo "value='".$_POST["nomeMuseo"]."'";} ?>/></div>
                        </div>
                        <div>
                            <div><label for='museoPubblicoPrivato'>Museo pubblico/privato</label></div>
                            <div><select id="museoPubblicoPrivato" name="museoPubblicoPrivato">
                                <option value="">-</option>
                                <option value="pubblico" <?php if(isset($_POST["museoPubblicoPrivato"]) && $_POST["museoPubblicoPrivato"]=="pubblico") echo "selected" ?>>Pubblico</option>
                                <option value="privato" <?php if(isset($_POST["museoPubblicoPrivato"]) && $_POST["museoPubblicoPrivato"]=="privato") echo "selected" ?>>Privato</option>
                            </select></div>
                        </div>
                        <div>
                            <div><label for='provinciaMuseo'>Provincia</label></div>
                            <div><select id="provinciaMuseo" name="provinciaMuseo">
                                <option value="">-</option>
                                <?php
                                    $res=mysqli_query($conn, "SELECT * FROM provincia order by provincia");
                                    while($row = mysqli_fetch_object($res)){
                                        echo '<option value="'.$row->sigla.'" '.(isset($_POST["provinciaMuseo"]) && $_POST["provinciaMuseo"]==$row->sigla ? "selected" : "").'>'.$row->provincia.'</option>';
                                    }
                                    mysqli_free_result($res);
                                ?>
                            </select></div>
                        </div>
                        <div>
                            <div><label for='cittaMuseo'>Comune</label></div>
                            <div><select id="cittaMuseo" name="cittaMuseo">
                                <option value="">-</option>
                                <?php
                                    if(isset($_POST["provinciaMuseo"])){
                                        $provincia=$_POST["provinciaMuseo"];
                                        $res=mysqli_query($conn, "SELECT * FROM citta where provincia='$provincia' order by comune");
                                        while($row = mysqli_fetch_object($res)){
                                            echo '<option value="'.$row->id.'" '.(isset($_POST["cittaMuseo"]) && $_POST["cittaMuseo"]==$row->id ? "selected" : "").'>'.$row->comune.'</option>';
                                        }
                                        mysqli_free_result($res);
                                    }
                                ?>
                            </select></div>
                        </div>
                        <div>
                            <div><label for='tipoMuseo'>Tipo del museo</label></div>
                            <div><select id="tipoMuseo" name="tipoMuseo">
                                <option value="">-</option>
                                <?php
                                    $res=mysqli_query($conn, "SELECT * FROM tipo_museo");
                                    while($row = mysqli_fetch_object($res)){
                                        echo '<option value="'.$row->id.'" '.(isset($_POST["tipoMuseo"]) && $_POST["tipoMuseo"]==$row->id ? "selected" : "").'>'.$row->tipo.'</option>';
                                    }
                                    mysqli_free_result($res);
                                ?>
                            </select></div>
                        </div>
                        <div>
                            <div><label for='dataApertura'>Data di apertura del museo</label></div>
                            <div><input type='date' id='dataApertura' name='dataApertura' <?php if(isset($_POST["dataApertura"])){echo "value='".$_POST["dataApertura"]."'";} ?>/></div>
                        </div>
                        <div>
                            <div><label for='telefono1'>Numero di telefono n.1</label></div>
                            <div><input type='text' id='telefono1' name='telefono1' <?php if(isset($_POST["telefono1"])){echo "value='".$_POST["telefono1"]."'";} ?> maxlength="15"/></div>
                        </div>
                        <div>
                            <div><label for='telefono2'>Numero di telefono n.2</label></div>
                            <div><input type='text' id='telefono2' name='telefono2' <?php if(isset($_POST["telefono2"])){echo "value='".$_POST["telefono2"]."'";} ?> maxlength="15"/></div>
                        </div>
                        <div>
                            <div><label for='upload_original'>Caricare un'immagine del museo</label></div>
                            <div>
                                <input type='file' id="upload_original" name='immagineMuseo' accept='.jpg, .jpeg, image/png'/>
                                <div id="upload"><div class="file_name">Seleziona un file...</div><div class="file_size"></div></div>
                                <div id="uploadError" class="error"></div>
                            </div>    
                        </div>
                    </div>
                    <div class="buttons">
                        <input type='submit' id="backButton" name="submit" value="Indietro" <?php if($step=="0"){echo " disabled";} ?>/>
                        <input type='submit' id="nextButton" name="submit" value="Avanti" <?php if(!$nextForceEnabled && ($nextForceDisable || !isset($_POST["step"]) || count($error)>0)) echo "disabled"; ?>/>
                    </div>
                </form>
                <div class='registerTip'>Hai già un account? <a href="login.php">Accedi</a></div>
            </div>
            <div id="errors">
                <div class="<?php if(!isset($error) || count($error)==0) echo " hidden";?>">
                    <h1>Errori:</h1>
                    <p>
                        <?php
                            if(isset($error) && count($error)>0){
                                foreach ($error as $e){
                                    echo "$e<br/>";
                                }
                            }
                        ?>
                    </p>
                </div>
            </div>
        </div>    
        <footer>
            <p>Powered by <strong>Leonardo Messina</strong> O46002290 <br/>
                Viale della Libertà 3, 00118 Roma
            </p>
        </footer>
    </body>
</html>

<?php
    mysqli_close($conn);
?>