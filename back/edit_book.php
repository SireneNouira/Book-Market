<?php
session_start();
require '../utils/connect_db.php';
include_once '../utils/autoloader.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/home.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
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
    exit;
}

// Vérifier si l'utilisateur est un vendeur
$stmt = $pdo->prepare('SELECT id FROM vendeurs WHERE id_utilisateur = :id_utilisateur');
$stmt->bindParam(':id_utilisateur', $user_id, PDO::PARAM_INT);
$stmt->execute();
$vendeur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendeur) {
    echo "Vous devez être un vendeur pour modifier un livre.";
    exit;
}

$id_vendeur = $vendeur['id'];

// Vérifier si l'ID du livre est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: annonces_vendeur.php');
    exit;
}

$book_id = $_GET['id'];

// Récupérer les informations du livre à modifier
$bookRepository = new BookRepository($pdo);
$book = $bookRepository->getBookById($book_id);

if (!$book) {
    echo "Livre non trouvé.";
    exit;
}

// Vérifier que le livre appartient au vendeur
// if ($book['id_vendeur'] !== $id_vendeur) {
//     echo "Vous n'êtes pas autorisé à modifier ce livre.";
//     exit;
// }

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $prix = trim($_POST['prix']);
    $description = trim($_POST['description']);
    $photo = $_FILES['photo']['name'];
    $auteurId = (int)$_POST['auteur_id'];
    $etatId = (int)$_POST['etat_id'];
    $genres = $_POST['genres'] ?? [];

    if (empty($genres)) {
        $error_message = 'Vous devez sélectionner au moins un genre.';
    } else {
        try {
            // Mettre à jour le livre
            $bookRepository->updateBook($book_id, $titre, $prix, $description, $photo, $auteurId, $etatId, $genres);

            // Rediriger après succès
            header('Location: annonces_vendeur.php');
            exit;
        } catch (Exception $e) {
            $error_message = 'Une erreur est survenue : ' . $e->getMessage();
        }
    }
}

// Récupérer les genres et auteurs pour le formulaire
$genreRepository = new GenreRepository($pdo);
$genres = $genreRepository->getAllGenres();

$auteurRepository = new AuteurRepository($pdo);
$auteurs = $auteurRepository->getAllAuteurs();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/styles/output.css" />
    <link rel="stylesheet" href="../public/styles/style.css" />
    <script defer src="../public/assets/scripts/script.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bacasime+Antique&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Document</title>
</head>
<body class="bg-gray-100">
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

        <h1 class="text-3xl font-bold text-center my-8">Modifier une annonce de livre</h1>
        <form action="edit_book.php?id=<?= $book_id ?>" method="post" enctype="multipart/form-data" class="bg-white p-8 rounded-lg shadow-lg">
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <span class="block sm:inline"><?= $error_message ?></span>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label for="titre" class="block text-gray-700">Titre du livre :</label>
                <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($book['titre']) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            <div class="mb-4">
                <label for="prix" class="block text-gray-700">Prix (€) :</label>
                <input type="number" id="prix" name="prix" value="<?= htmlspecialchars($book['prix']) ?>" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description :</label>
                <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required><?= htmlspecialchars($book['description']) ?></textarea>
            </div>

            <div class="mb-4">
                <label for="photo" class="block text-gray-700">Photo :</label>
                <input type="file" id="photo" name="photo" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <small>Laisser vide pour conserver l'image actuelle.</small>
            </div>

            <div class="mb-4">
                <label for="auteur" class="block text-gray-700">Auteur :</label>
                <select id="auteur" name="auteur_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="" disabled>Choisissez un auteur</option>
                    <?php foreach ($auteurs as $auteur): ?>
                        <option value="<?= $auteur['id'] ?>" <?= $auteur['id'] === $book['id_auteur'] ? 'selected' : '' ?>><?= $auteur['nom'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="etat" class="block text-gray-700">État du livre :</label>
                <select id="etat" name="etat_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="" disabled>Choisissez l'état</option>
                    <option value="1" <?= $book['id_etat'] === 1 ? 'selected' : '' ?>>Neuf</option>
                    <option value="2" <?= $book['id_etat'] === 2 ? 'selected' : '' ?>>Très bon état</option>
                    <option value="3" <?= $book['id_etat'] === 3 ? 'selected' : '' ?>>Bon état</option>
                    <option value="4" <?= $book['id_etat'] === 4 ? 'selected' : '' ?>>État correct</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="genres" class="block text-gray-700">Genres :</label>
                <div id="genres-container" class="max-h-36 overflow-y-auto border border-gray-300 rounded p-2">
                    <?php foreach ($genres as $genre): ?>
                        <label class="block">
                            <input type="checkbox" name="genres[]" value="<?= $genre['id'] ?>" class="genre-checkbox mr-2" <?= in_array($genre['id'], $book['genres']) ? 'checked' : '' ?>> <?= $genre['nom'] ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const checkboxes = document.querySelectorAll('.genre-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', () => {
                            const checkedCount = document.querySelectorAll('.genre-checkbox:checked').length;
                            if (checkedCount >= 3) {
                                checkboxes.forEach(cb => {
                                    if (!cb.checked) cb.disabled = true;
                                });
                            } else {
                                checkboxes.forEach(cb => cb.disabled = false);
                            }
                        });
                    });
                });
            </script>

            <div class="flex justify-center mt-8">
                <button type="submit" class="bg-main text-vertfonce px-6 py-2 rounded-lg hover:bg-mainopacity transition duration-300">Enregistrer les modifications</button>
            </div>
        </form>
    </main>
</body>

</html>

