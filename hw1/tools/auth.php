<?php
    require_once 'dbconfig.php';
    session_start();

    function checkAuth() {
        if(isset($_SESSION['id_utente'])) {
            return $_SESSION['id_utente'];
        } else 
            return 0;
    }
?>