<?php
    session_start();
    
    unset($_SESSION['idUser']);
    unset($_SESSION['userName']);
    header('Location: index.php');
