<?php
// delete_book.php

// Inclure le fichier de connexion à la base de données
include '../utils/connect_db.php';

// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté et est un vendeur
if (!isset($_SESSION['user_id'])) {
    echo "Vous devez être connecté pour effectuer cette action.";
    exit;
}

// Vérifier si l'identifiant du livre est passé en paramètre
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Vérifier si l'utilisateur est autorisé à supprimer ce livre
    $stmt = $pdo->prepare('SELECT id FROM livres WHERE id = :book_id ');
    // AND id_vendeur = :id_vendeur'
    $stmt->execute([
        ':book_id' => $book_id,
        // ':id_vendeur' => $_SESSION['user_id']
    ]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        echo "Vous n'êtes pas autorisé à supprimer ce livre.";
        exit;
    }

    // Supprimer le livre
    $stmt = $pdo->prepare('DELETE FROM livres WHERE id = :book_id');
    $stmt->execute([':book_id' => $book_id]);

    // Rediriger avec un message de succès ou d'erreur
    if ($stmt->rowCount() > 0) {
        header("Location: ../public/annonces_vendeur.php?message=Le livre a été supprimé avec succès.");
        exit();
    } else {
        header("Location: ../public/annonces_vendeur.php?error=Le livre n'a pas pu être supprimé.");
        exit();
    }
} else {
    // Rediriger si l'identifiant du livre n'est pas fourni
    header("Location: ../public/annonces_vendeur.php?error=Identifiant du livre manquant.");
    exit();
}
