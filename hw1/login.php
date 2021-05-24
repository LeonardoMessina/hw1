<?php
    require_once 'tools/auth.php';
    
    if (checkAuth()) {
        header('Location: home.php');
        exit;
    }

    if(isset($_POST["usernameEmail"])){
        if (!empty($_POST["usernameEmail"]) && !empty($_POST["password"]) )
        {
            $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));
            $usernameEmail = mysqli_real_escape_string($conn, $_POST['usernameEmail']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $loginType = filter_var($usernameEmail, FILTER_VALIDATE_EMAIL) ? "email" : "username";
            $query = "SELECT u.*, m.id as idMuseo, m.nome as nomeMuseo FROM utente u join museo m on m.id=u.museo WHERE u.$loginType = '$usernameEmail'";

            $res = mysqli_query($conn, $query) or die(mysqli_error($conn));;
            if (mysqli_num_rows($res) > 0) {
                $entry = mysqli_fetch_assoc($res);
                if (password_verify($_POST['password'], $entry['password'])) {
                    $_SESSION["id_utente"] = $entry['id'];
                    $_SESSION["username"] = $entry['username'];
                    $_SESSION["email"] = $entry['email'];
                    $_SESSION["id_museo"] = $entry['idMuseo'];
                    $_SESSION["nome_museo"] = $entry['nomeMuseo'];
                    mysqli_free_result($res);
                    
                    $res = mysqli_query($conn, "SELECT * FROM museo_privato WHERE id={$_SESSION["id_museo"]}");
                    $_SESSION["museoPubblicoPrivato"]=mysqli_num_rows($res) > 0 ? "privato" : "pubblico";
                    mysqli_free_result($res);

                    header("Location: home.php");
                    mysqli_close($conn);
                    exit;
                }
            }
            $error = "Username e/o password errati";
        }else{
            $error = "Inserisci username e password";
        }
    }

?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="favicon.png">
        <title>Musei italiani - Login</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?&family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Noto+Sans+JP&family=Work+Sans&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Lexend&display=swap" rel="stylesheet">
        <link href="style/common.css" rel="stylesheet">
        <link href="style/login.css" rel="stylesheet">
        <script src="scripts/common.js" defer></script>
    </head>
    <body>
        <header>
            <h1>Accesso</h1>
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
        <div>
            <div id="input">
                <form method='post'>
                    <h1>Accedi</h1>
                    <div class="usernameEmail">
                        <div><label for='usernameEmail'>Nome utente o email</label></div>
                        <div><input type='text' name='usernameEmail' <?php if(isset($_POST["usernameEmail"])){echo "value='".$_POST["usernameEmail"]."'";} ?>/></div>
                    </div>
                    <div class="password">
                        <div><label for='password'>Password</label></div>
                        <div><input type='password' name='password' <?php if(isset($_POST["password"])){echo "value='".$_POST["password"]."'";} ?>/></div>
                    </div>
                    <div class='buttons'>
                        <input type='submit' value="Accedi"/>
                    </div>
                    <div class="errors<?php if(!isset($error)) echo " hidden";?>">
                        <h1>Errori:</h1>
                        <p>
                            <?php
                                if(isset($error)){
                                    echo "$error";
                                }
                            ?>
                        </p>
                        </div>
                </form>
                <div class="registerTip">Non hai ancora un account? <a href="signup.php">Iscriviti</a></div>
            </div>
        </div>
        <footer>
            <p>Powered by <strong>Leonardo Messina</strong> O46002290 <br/>
                Viale della Libertà 3, 00118 Roma
            </p>
        </footer>
    </body>
</html>