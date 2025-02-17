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

require_once './partials/header.php';
?>
<body>
<div class="flex justify-center bg-main shadow-lg py-2">
        <p class="text-sm text-vertfonce">Achats et Ventes de Livres d'Occasions</p>
    </div>

    <header class="flex items-center pt-4 px-4">
        <div class="pl-5">
            <a href="#" aria-label="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(160, 160, 160, 1);">
                    <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"></path>
                </svg>
            </a>
        </div>
        <div class="flex justify-center w-full">
            <h1 class="text-2xl text-secondary">BookMarket</h1>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="inline-flex items-center justify-between w-full mt-8">
            <span class="w-3/12 h-px bg-gray-300"></span>
            <div class="flex-1 flex justify-center w-6/12 p-4 mx-4 bg-mainopacity rounded-sm shadow-2xl">
                <h2 class="text-xl text-vertfonce"><?= $user_nom . " " . $user_prenom; ?></h2>
            </div>
            <span class="w-3/12 h-px bg-gray-300"></span>
        </div>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Mes Annonces de Livres</h1>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($books as $book): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="<?= "./assets/imgs/" . $book['photo_path'] ?>" alt="<?php echo htmlspecialchars($book['titre']); ?>" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($book['titre']); ?></h2>
                    <p class="text-gray-600 mb-4 overflow-hidden"><?php echo htmlspecialchars($book['description']); ?></p>
                    <p class="text-lg font-bold text-blue-600"><?php echo htmlspecialchars($book['prix']); ?> €</p>
                    <div class="mt-4">
                        <a href="/edit-book/<?php echo htmlspecialchars($book['id']); ?>" class="text-blue-500 hover:text-blue-700 mr-2">Modifier</a>
                        <a href="/delete-book/<?php echo htmlspecialchars($book['id']); ?>" class="text-red-500 hover:text-red-700">Supprimer</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
<?php
require_once './partials/footer.php';
?>