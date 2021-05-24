<?php require_once 'tools/auth.php'; ?>

<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Musei italiani</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?&family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Noto+Sans+JP&family=Work+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lexend&display=swap" rel="stylesheet">
    <link href="style/common.css" rel="stylesheet">
    <link href="style/home.css" rel="stylesheet">
    <script src="scripts/common.js" defer></script>
    <script src="scripts/home.js" defer></script>
  </head>
  <body>
    <header>
      <h1>Musei italiani</h1>
      <a href='chi_siamo.php' class="button">Chi siamo</a>
    </header>
    <a id='login' href='login.php' class='<?php if (checkAuth()) echo " hidden" ?>'>Login</a>
    <a id='logout' class='<?php if (!checkAuth()) echo " hidden" ?>'>Logout</a>
    <nav>
      <a id='firstChildNav' href='home.php'>Home</a>
      <a href='chi_siamo.php'>Chi siamo</a>
      <a href='musei.php'>Musei</a>
      <?php if (checkAuth()) echo "<a href='personal_area.php'>Area personale</a>"; ?>
      <a id='lastChildNav' href='contattaci.php'>Contattaci</a>
    </nav>
    <div id='content'>
      <div class="carousel">
        <?php
            $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
            $query = "SELECT * FROM museo m join citta c on m.citta=c.id join tipo_museo t on m.tipo=t.id
              WHERE introduzione is not null
              ORDER BY ultima_modifica desc limit 5"
            ;
            $res = mysqli_query($conn, $query);
            while($row = mysqli_fetch_object($res)){
              echo "<div class='carousel-item'>";
                echo "<section>";
                  echo "<img src='".$row->immagine_museo."'/>";
                  echo "<h1>".htmlentities($row->nome)."</h1>";
                  echo "<h2>Città: ".htmlentities($row->comune)."</h2>";
                  echo "<h2>Tipo: ".htmlentities($row->tipo)."</h2>";
                  echo "<div><p>".htmlentities($row->introduzione)."</p></div>";
                echo "</section>";
              echo "</div>";
            }
            mysqli_free_result($res);
        ?>    
      </div>
      <div id='introduction'>
        <h1>Benvenuto!</h1>
        <p>
            Gentile visitatore,<br/>
            se vuoi registrare il tuo museo, basta fare click sul pulsante "Login" e poi iscriversi.<br/>
            Il nostro sito offre agli utenti registrati la possibilità di inserire molte informazioni utii riguardanti il proprio museo, oltre che una sua immagine e
            l'elenco delle opere.<br/>
            Se sei invece un utente non registrato, nel nostro sito avrai la possibilità di visionare tutti i dati d'interesse delle centinaia (forse un po' di meno) di musei che hanno
            scelto noi!
        </p>
      </div>
    </div>  
    <footer>
      <p>Powered by <strong>Leonardo Messina</strong> O46002290 <br/>
        Viale della Libertà 3, 00118 Roma
      </p>
    </footer>
  </body>
</html>