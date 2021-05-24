<?php require_once 'tools/auth.php'; ?>

<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Musei italiani - Musei</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
    <link href="style/common.css" rel="stylesheet">
    <link href="style/musei.css" rel="stylesheet">
    <script src="scripts/common.js" defer></script>
    <script src="scripts/weather.js" defer></script>
    <script src="scripts/map.js" defer></script>
    <script src="scripts/musei.js" defer></script>
  </head>
  <body>
    <header>
      <h1>Musei italiani</h1>
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
        <div id='loading'>
          <h1>Caricamento in corso, attendere prego...</h1>
        </div>
        <div id='favourites' class='hidden'>
            <h1>Preferiti:</h1>
            <div class="museumsContainer">  
            </div>
        </div>
        <div id='results' class='hidden'>
          <div id='museumsList'>
              <h1>Elenco musei:</h1>
              <input type="text" placeholder="Cerca il nome di un museo"></input>
              <div class='museumsContainer'>
              </div>
          </div>
        </div>
    </div>
    <footer>
      <p>Powered by <strong>Leonardo Messina</strong> O46002290 <br/>
        Viale della Libertà 3, 00118 Roma
      </p>
    </footer>
    <article id="mapModal" class="modal hidden">
      <div class="container">
          <h1>Caricamento in corso, attendere prego...</h1>
          <img src=""/>
          <p></p>
          <div class="exitButton"></div>
      </div>
		</article>
    <article id="mapModalError" class="modal error hidden">
      <div class="container">
          <h1>Errore: impossibile caricare la mappa.</h1>
          <h2></h2>
      </div>
		</article>
  </body>
</html>