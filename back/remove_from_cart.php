<?php
session_start();

// Vérifier si l'ID du produit est passé en paramètre
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = (int)$_GET['id'];

    // Vérifier si le panier existe et n'est pas vide
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Parcourir le panier pour trouver l'article à supprimer
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] === $productId) {
                // Supprimer l'article du panier
                unset($_SESSION['cart'][$key]);
                break;
            }
        }

        // Réindexer le tableau pour éviter des trous dans les indices
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Rediriger l'utilisateur vers la page du panier
header('Location: ../public/panier.php');
exit();
?>