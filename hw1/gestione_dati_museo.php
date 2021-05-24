<?php
    require_once 'tools/auth.php';

	if(!checkAuth()){
        header("Location: home.php");
		exit;
    }

    $error = array();
    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));

    $res=mysqli_query($conn, "SELECT * FROM museo WHERE id=".$_SESSION["id_museo"]);
    if($row = mysqli_fetch_object($res)){
        $nomeMuseo=$row->nome;
        $latitudineMuseo=$row->lat;
        $longitudineMuseo=$row->lon;
        $tipoMuseo=$row->tipo;
        $costoBiglietto=$row->costo_biglietto;
        $dataApertura=$row->data_apertura;
        $immagineMuseo=$row->immagine_museo;
        $introMuseo=$row->introduzione;
    }
    mysqli_free_result($res);

    $res=mysqli_query($conn, "SELECT * FROM utente WHERE id=".$_SESSION["id_utente"]);
    if($row = mysqli_fetch_object($res)){
        $telefono1=$row->telefono1;
        $telefono2=$row->telefono2;
    }
    mysqli_free_result($res);

    if ($_SESSION["museoPubblicoPrivato"]=='privato') {
        $res = mysqli_query($conn, "SELECT * FROM museo_privato WHERE museo=".$_SESSION["id_museo"]." LIMIT 1");
        if($row = mysqli_fetch_object($res)){
            $nomeSocieta= $row->nome_societa;
        }
        mysqli_free_result($res);
    }else{
        $nomeSocieta='';
    }
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="favicon.png">
        <title>Musei italiani - Gestione dati del museo</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
        <link href="style/common.css" rel="stylesheet">
        <link href="style/gestione_dati_museo.css" rel="stylesheet">
        <script src="./scripts/common.js" defer></script>
        <script src="./scripts/gestione_dati_museo.js" defer></script>
    </head>
    <body>
        <header>
            <h1>Gestione dati del museo</h1>
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
            <form autocomplete='off' enctype="multipart/form-data" method='post'>
                <div id='input'>
                    <div>
                        <div>
                            <div><label for='nomeMuseo'>Nome del museo</label></div>
                            <div><input type='text' id='nomeMuseo' name='nomeMuseo' value='<?php echo $nomeMuseo ?>'/></div>
                        </div>
                        <div>
                            <div><label for='latitudineMuseo'>Latitudine del museo</label></div>
                            <div><input type='text' id='latitudineMuseo' name='latitudineMuseo' value='<?php echo $latitudineMuseo ?>'/></div>
                        </div>
                        <div>
                            <div><label for='longitudineMuseo'>Longitudine del museo</label></div>
                            <div><input type='text' id='longitudineMuseo' name='longitudineMuseo' value='<?php echo $longitudineMuseo ?>'/></div>
                        </div>
                        <div>
                            <div><label for='tipoMuseo'>Tipo del museo</label></div>
                            <div><select id="tipoMuseo" name="tipoMuseo">
                                <option value="">-</option>
                                <?php
                                    $res=mysqli_query($conn, "SELECT * FROM tipo_museo");
                                    while($row = mysqli_fetch_object($res)){
                                        echo '<option value="'.$row->id.'" '.($tipoMuseo==$row->id ? "selected" : "").'>'.$row->tipo.'</option>';
                                    }
                                    mysqli_free_result($res);
                                ?>
                            </select></div>
                        </div>
                        <div>
                            <div><label for='costoBiglietto'>Costo del biglietto</label></div>
                            <div><input type='text' id='costoBiglietto' name='costoBiglietto' value='<?php echo $costoBiglietto ?>'/></div>
                        </div>
                        <div>
                            <div><label for='dataApertura'>Data di apertura del museo</label></div>
                            <div><input type='date' id='dataApertura' name='dataApertura' value='<?php echo $dataApertura ?>'/></div>
                        </div>
                        <div>
                            <div><label for='telefono1'>Numero di telefono n.1</label></div>
                            <div><input type='text' id='telefono1' name='telefono1' value='<?php echo $telefono1 ?>' maxlength='15'/></div>
                        </div>
                        <div>
                            <div><label for='telefono2'>Numero di telefono n.2</label></div>
                            <div><input type='text' id='telefono2' name='telefono2' value='<?php echo $telefono2 ?>' maxlength='15'/></div>
                        </div>
                        <div class='<?php echo $_SESSION["museoPubblicoPrivato"]=="pubblico" ? "hidden" : ""?>'>
                            <div><label for='nomeSocieta'>Nome della società del museo privato</label></div>
                            <div><input type='text' id='nomeSocieta' name='nomeSocieta' value='<?php echo $nomeSocieta ?>'/></div>
                        </div>
                        <div>
                            <div><label for='introMuseo'>Introduzione al museo</label></div>
                            <div><textarea id='introMuseo' name='introMuseo' maxlength='2000'><?php echo $introMuseo ?></textarea></div>
                        </div>
                    </div>
                    <div>
                        <div>
                            <?php echo "<img id='image' src='$immagineMuseo'/>";?>
                            <div><label for='upload_original'>Cambiare l'immagine del museo</label></div>
                            <div>
                                <input type='file' id='upload_original' name='immagineMuseo' accept='.jpg, .jpeg, image/png'/>
                                <div id="upload"><div class="file_name">Seleziona un file...</div><div class="file_size"></div></div>
                            </div>    
                        </div>
                        <div id="errors">
                            <div class="<?php if(!isset($error) || count($error)==0) echo "hidden";?>">
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
                </div>
                <div class="buttons">
                    <a href='personal_area.php' class='button left'>Indietro</a>
                    <input type='button' id="save" value="Salva" class="button right" disabled/>
                </div>
            </form>
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