<?php
include_once '../utils/autoloader.php';
session_start();
require '../utils/connect_db.php';
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM utilisateurs WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);



if ($user) {

    $user_nom = $user['nom'];
    $user_prenom = $user['prenom'];
    $user_role = $user['id_role'];
} else {
    echo "Utilisateur non trouvé.";
}

// Vérifier si l'utilisateur est un vendeur
$stmt = $pdo->prepare('SELECT id FROM vendeurs WHERE id_utilisateur = :id_utilisateur');
$stmt->bindParam(':id_utilisateur', $user_id, PDO::PARAM_INT);
$stmt->execute();
$vendeur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendeur) {
    echo "Vous devez être un vendeur pour ajouter un livre.";
    exit;
}

$id_vendeur = $vendeur['id'];

$bookRepository = new BookRepository();

$books = $bookRepository->getBooksByVendeurId($id_vendeur);

foreach ($books as $book) {
    echo "Titre : " . $book['titre'] . ", Prix : " . $book['prix'] . "\n";
}


require_once './partials/header.php';
?>



<?php
require_once './partials/footer.php';