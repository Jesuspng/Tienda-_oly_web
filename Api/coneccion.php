<?php
    $host ='localhost';
    $db = 'u941347256_TiendaOlly';
    $user = 'u941347256_Equipo6';
    $pass = 'AEiz3x$9';

    $con = new mysqli($host, $user, $pass, $db);

    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }  
?>



