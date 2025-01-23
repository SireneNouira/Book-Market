<?php
include_once '../utils/autoloader.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


require_once './partials/header.php';
?>





<?php
require_once './partials/footer.php';