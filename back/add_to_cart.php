<?php
include_once '../utils/autoloader.php';
require '../utils/connect_db.php';
session_start();

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $bookId = (int)$_GET['id'];

    // Récupérer les détails du livre à partir du repository
    $bookRepository = new BookRepository();
    $book = $bookRepository->getBookById($bookId);

    if ($book) {
        $product_id = $bookId;
        $product_path = $book->getPhotoPath();
        $product_name = $book->getTitre();
        $product_price = $book->getPrix();
        $product_quantity = 1;



        // Initialiser le panier si non défini
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // Vérifier si le produit existe déjà dans le panier
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] === $product_id) {
                $item['quantity'] += $product_quantity; // Augmenter la quantité si le produit existe déjà
                $found = true;
                break;
            }
        }

        // Si le produit n'est pas encore dans le panier, l'ajouter
        if (!$found) {
            $cart_item = array(
                'id' => $product_id,
                'path' => $product_path,
                'name' => $product_name,
                'price' => $product_price,
                'quantity' => $product_quantity
            );
            $_SESSION['cart'][] = $cart_item;
            
        }
        
  
        header('Location: ../public/panier.php');
        exit();
    } else {
        // Livre non trouvé
        echo "Le produit demandé n'existe pas.";
    }
} else {
    // ID invalide
    echo "ID de produit invalide.";
}
?>
