<?php
session_start();
require '../utils/connect_db.php';
include_once '../utils/autoloader.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/home.php');
    exit;
}

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
            // Créer un objet Book
            $book = new Book($titre, $prix, $description, $photo, $auteurId, $etatId, $id_vendeur, $genres);

            // Ajouter le livre via le repository
            $bookRepository = new BookRepository($pdo);
            $bookRepository->addBook($book);

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

require_once './partials/header.php';
?>

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

        <h1 class="text-3xl font-bold text-center my-8">Ajouter une annonce de livre</h1>
        <form action="new-book.php" method="post" enctype="multipart/form-data" class="bg-white p-8 rounded-lg shadow-lg">
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <span class="block sm:inline"><?= $error_message ?></span>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label for="titre" class="block text-gray-700">Titre du livre :</label>
                <input type="text" id="titre" name="titre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            <div class="mb-4">
                <label for="prix" class="block text-gray-700">Prix (€) :</label>
                <input type="number" id="prix" name="prix" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description :</label>
                <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required></textarea>
            </div>

            <div class="mb-4">
                <label for="photo" class="block text-gray-700">Photo :</label>
                <input type="file" id="photo" name="photo" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            <div class="mb-4">
                <label for="auteur" class="block text-gray-700">Auteur :</label>
                <select id="auteur" name="auteur_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="" disabled selected>Choisissez un auteur</option>
                    <?php foreach ($auteurs as $auteur): ?>
                        <option value="<?= $auteur['id'] ?>"><?= $auteur['nom'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="etat" class="block text-gray-700">État du livre :</label>
                <select id="etat" name="etat_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="" disabled selected>Choisissez l'état</option>
                    <option value="1">Neuf</option>
                    <option value="2">Très bon état</option>
                    <option value="3">Bon état</option>
                    <option value="4">État correct</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="genres" class="block text-gray-700">Genres :</label>
                <div id="genres-container" class="max-h-36 overflow-y-auto border border-gray-300 rounded p-2">
                    <?php foreach ($genres as $genre): ?>
                        <label class="block">
                            <input type="checkbox" name="genres[]" value="<?= $genre['id'] ?>" class="genre-checkbox mr-2"> <?= $genre['nom'] ?>
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
                <button type="submit" class="bg-main text-vertfonce px-6 py-2 rounded-lg hover:bg-mainopacity transition duration-300">Ajouter le livre</button>
            </div>
        </form>
    </main>
</body>

<?php
require_once './partials/footer.php';
?>