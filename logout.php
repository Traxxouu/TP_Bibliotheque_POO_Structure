<?php
session_start();
$_SESSION = array();
// Détruire la session
session_destroy();
// envoyer vers la page d'accueil
header('Location: index.php');
exit;
?>