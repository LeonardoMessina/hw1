<?php
    require_once 'tools/auth.php';

    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));

    $idMuseo=mysqli_real_escape_string($conn, $_GET["id"]);
    if(empty($idMuseo)){
        header("Location: home.php");
        exit;
    }

    $res=mysqli_query($conn, "SELECT m.id as id_museo, m.nome as nome_museo, m.lat as latitudine_museo, m.lon as longitudine_museo, t.tipo as tipo_museo,
        m.costo_biglietto as costo_biglietto, m.data_apertura as data_apertura, m.immagine_museo as immagine_museo, m.introduzione as introduzione,
        mp.museo as id_museo_privato, c.comune as comune, c.provincia as provincia, c.regione as regione
        FROM museo m join citta c on m.citta=c.id join tipo_museo t on m.tipo=t.id left join museo_privato mp on m.id=mp.museo
        WHERE m.id=$idMuseo
    ");
    $row = mysqli_fetch_object($res);
    mysqli_free_result($res);
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="favicon.png">
        <title>Musei italiani - Informazioni museo</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
        <link href="style/common.css" rel="stylesheet">
        <link href="style/informazioni_museo.css" rel="stylesheet">
        <script src="scripts/common.js" defer></script>
        <script src="scripts/informazioni_museo.js" defer></script>
    </head>
    <body>
        <header>
            <h1>Informazioni museo</h1>
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
            <div id='museo'>
                <div id='fields'>
                    <h1>Nome: <span><?php echo $row->nome_museo?></span></h1>
                    <h1>Tipo: <span><?php echo $row->tipo_museo?></span></h1>
                    <h1 class='<?php echo ($row->costo_biglietto ? "" : "hidden")?>'>Costo del biglietto: <span><?php echo $row->costo_biglietto?> &euro;</span></h1>
                    <h1>Data di apertura: <span><?php echo $row->data_apertura?></span></h1>
                    <h1>Tipo di gestione: <span><?php echo ($row->id_museo_privato ? "Privata" : "Pubblica")?></span></h1>
                    <h1>Città: <span><?php echo $row->comune?></span></h1>
                    <h1>Provincia: <span><?php echo $row->provincia?></span></h1>
                </div>
                <div id='image'>
                    <img src='<?php echo $row->immagine_museo?>'/>
                </div>
            </div>
            <p id='introduction'><?php echo $row->introduzione?></p>
            <div id='opere'>
                <div>
                    <h1>Opere:</h1>
                    <div>
                        Ricerca per un periodo storico
                        <span>da: <input id="annoIniziale" type="text" placeholder="Numeri negativi per a.C" name="annoIniziale"/></span>
                        <span>a: <input id="annoFinale" type="text" placeholder="Numeri negativi per a.C" name="annoFinale"/></span>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Nome opera</th>
                            <th>Autore opera</th>
                            <th>Anno inizio della creazione</th>
                            <th>Anno ultimatura</th>
                            <th>Immagine</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $res=mysqli_query($conn, "SELECT * FROM opera WHERE museo=$idMuseo order by nome");
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
                                echo "</tr>";
                            }
                            mysqli_free_result($res);
                        ?>
                    </tbody>    
                </table>
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