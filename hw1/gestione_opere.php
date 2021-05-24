<?php
    require_once 'tools/auth.php';

	if(!checkAuth()){
        header("Location: home.php");
		exit;
    }

    $error = array();
    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="favicon.png">
        <title>Musei italiani - Gestione opere</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
        <link href="style/common.css" rel="stylesheet">
        <link href="style/gestione_opere.css" rel="stylesheet">
        <script src="scripts/common.js" defer></script>
        <script src="./scripts/gestione_opere.js" defer></script>
    </head>
    <body>
        <header>
            <h1>Gestione opere</h1>
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
                <table>
                    <tr>
                        <th>Nome opera</th>
                        <th>Autore opera</th>
                        <th>Anno inizio della creazione</th>
                        <th>Anno ultimatura</th>
                        <th>Immagine</th>
                        <th>Elimina</th>
                    </tr>
                    <?php
                        $res=mysqli_query($conn, "SELECT * FROM opera WHERE museo={$_SESSION['id_museo']} order by nome");
                        while($row = mysqli_fetch_object($res)){
                            echo "<tr data-id='$row->id'>";
                                echo "<td>$row->nome</td>";
                                echo "<td>$row->autore</td>";
                                if($row->anno_inizio_creazione)
                                    echo "<td>".abs($row->anno_inizio_creazione)." ".($row->anno_inizio_creazione>0 ? "d.C." : "a.C.")."</td>";
                                else
                                    echo "<td>N.D.</td>";
                                    if($row->anno_ultimatura)
                                    echo "<td>".abs($row->anno_ultimatura)." ".($row->anno_ultimatura>0 ? "d.C." : "a.C.")."</td>";
                                else
                                    echo "<td>N.D.</td>";
                                echo "<td><img class='iconaOpera' src='$row->immagine_opera'/></td>";
                                echo "<td><img class='iconaCancellaOpera' src='assets/cancella.png'/></td>";
                            echo "</tr>";
                        }
                        mysqli_free_result($res);
                    ?>
                </table>
                <div class='button'>Inserisci un'opera</div>
            </div>
            <a href='personal_area.php' class="button" >Indietro</a>
        </div>    
        <footer>
            <p>Powered by <strong>Leonardo Messina</strong> O46002290 <br/>
                Viale della Libertà 3, 00118 Roma
            </p>
        </footer>
        <article id=artworkModal class="modal hidden">
            <div class="container">
                <form autocomplete='off' enctype="multipart/form-data" method='post'>
                    <h1>Inserisci dati</h1>
                    <div>
                        <div><label for='nomeOpera'>Nome dell'opera</label></div>
                        <div><input type='text' id='nomeOpera' name='nomeOpera'/></div>
                    </div>
                    <div>
                        <div><label for='autoreOpera'>Autore</label></div>
                        <div><input type='text' id='autoreOpera' name='autoreOpera'/></div>
                    </div>
                    <div>
                        <div><label for='annoInizioCreazioneOpera'>Anno di inizio della creazione dell'opera</label></div>
                        <div class='year'>
                            <input type='text' id='annoInizioCreazioneOpera' name='annoInizioCreazioneOpera'/>
                            <span>a.C.</span>
                            <input type='radio' id='annoInizioCreazioneOperaAC' name='annoInizioCreazioneOperaRadio'/>
                            <span>d.C.</span>
                            <input type='radio' id='annoInizioCreazioneOperaDC' name='annoInizioCreazioneOperaRadio' checked/>
                        </div>
                    </div>
                    <div>
                        <div><label for='annoUltimaturaOpera'>Anno di ultimatura dell'opera</label></div>
                        <div class='year'>
                            <input type='text' id='annoUltimaturaOpera' name='annoUltimaturaOpera'/>
                            <span>a.C.</span>
                            <input type='radio' id='annoUltimaturaOperaAC' name='annoUltimaturaOperaRadio'/>
                            <span>d.C.</span>
                            <input type='radio' id='annoUltimaturaOperaDC' name='annoUltimaturaOperaRadio' checked/>
                        </div>
                    </div>
                    <div>
                        <div><label for='upload_original'>Caricare un'immagine del museo</label></div>
                        <div>
                            <input type='file' id="upload_original" name='immagineMuseo' accept='.jpg, .jpeg, image/png'/>
                            <div id="upload"><div class="file_name">Seleziona un file...</div><div class="file_size"></div></div>
                            <div id="uploadError" class="error"></div>
                        </div>    
                    </div>
                </form>
                <img src=""/>
                <div class="exitButton"></div>
                <input type='button' id="saveArtwork" name="submit" value="Salva" class="button" disabled/>
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
        </article>
    </body>
</html>

<?php
    mysqli_close($conn);
?>