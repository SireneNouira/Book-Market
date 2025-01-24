<?php
require_once __DIR__ . '../../utils/autoloader.php';

require '../utils/connect_db.php';
session_start();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $bookId = (int)$_GET['id'];

    $bookRepository = new BookRepository();
    $book = $bookRepository->getBookById($bookId);
    if ($book) {
        echo "<h1>{$book->getTitre()}</h1>";
         } 
         else {
        echo "<p>Livre non trouvé.</p>";
    } 
} else {
    echo "<p>Identifiant de livre non valide ou manquant.</p>";
}




require_once './partials/header.php';
