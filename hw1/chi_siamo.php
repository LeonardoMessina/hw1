<?php require_once 'tools/auth.php'; ?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="favicon.png">
        <title>Musei italiani - Chi siamo</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
        <link href="style/common.css" rel="stylesheet">
        <link href="style/chi_siamo.css" rel="stylesheet">
        <script src="scripts/common.js" defer></script>
    </head>
    <body>
        <header>
            <h1>Chi siamo</h1>
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
            <h1>La nostra storia</h1>
            <p>La nostra azienda, che in realtà non esiste, è stata fondata nel 1997 con lo scopo di fornire una piattaforma unificata che rendesse
                agevole per il pubblico reperire informazioni sui musei da visitare, mettendo a disposizione dei musei una strumentazione semplice ed
                efficace per inserire ed aggiornare i dati d'interesse. 
            </p>
            <h1>Servizi</h1>
            <p>
                Il nostro sito permette ai visitatori di visualizzare molte informazioni utili riguardanti i musei, tra cui il nome, la città, il tipo, 
                le condizioni meteo attuali della località ed una mappa della zona con indicata la posizione del museo ed anche eventuali problemi del
                traffico.<br/>
                I visitatori possono anche ottenere informazioni sulle opere conservate nei musei.<br/>
                Gli utenti registrati hanno invece la possiblità di amministrare tutto ciò che riguarda il proprio museo e che è visibile ai visitatori
                del sito.
            </p>
            <div id='moreInfo'>
                <h1>Vorresti altre informazioni?</h1>
                <div id='contactPanel'>
                    <a href='contattaci.php' class='button'>Contattaci!</a>
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