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
        <title>Musei italiani - Backup opere</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
        <link href="style/common.css" rel="stylesheet">
        <link href="style/backup_opere.css" rel="stylesheet">
        <script src="scripts/common.js" defer></script>
        <script src="scripts/backup_opere.js" defer></script>
    </head>
    <body>
        <header>
            <h1>Backup delle opere</h1>
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
					<thead>
						<tr>
							<th>Data</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$res=mysqli_query($conn, "SELECT * FROM backup WHERE museo={$_SESSION['id_museo']} order by data desc");
							while($row = mysqli_fetch_object($res)){
								echo "<tr data-id='$row->id'>";
									echo "<td>$row->data</td>";
								echo "</tr>";
							}
							mysqli_free_result($res);
						?>
					</tbody>
				</table>
				<div id='sidePanel'>
					<div id='backupButton' class='button'>Esegui un backup delle opere!</div>
					<div id='message' class='hidden'></div>
				</div>
			</div>
            <a href='personal_area.php' class="button" >Indietro</a>
        </div>
        <footer>
            <p>Powered by <strong>Leonardo Messina</strong> O46002290 <br/>
                Viale della Libertà 3, 00118 Roma
            </p>
        </footer>
    </body>
</html>