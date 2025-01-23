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
            header('Location: annonces.php');
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

<body>
    <div class="flex justify-center bg-main shadow-lg">
        <p class="text-sm text-vertfonce flex items-center">Achats et Ventes de Livres d'Occasions</p>
    </div>

    <header class="flex items-center  pt-2">

        <div class="pl-5  justify-self-start">
            <a href="#" aria-label="Menu">
                <box-icon name='menu' color='#a0a0a0'>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(160, 160, 160, 1);">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"></path>
                    </svg>
                </box-icon>
            </a>
        </div>

        <div class="flex absolute justify-center w-full ">
            <h1 class="text-2xl text-secondary">BookMarket</h1>
        </div>


    </header>

    <main>

        <div class="inline-flex items-center justify-between w-full mt-16">

            <!-- Trait à gauche -->
            <span class="w-3/12 h-px bg-grey ml-4"></span>

            <div class=" flex-1 flex justify-center w-6/12 p-4 mx-40 bg-mainopacity rounded-sm shadow-2xl">
                <h2 class="text-xl text-vertfonce"> <?= $user_nom . " " . $user_prenom; ?> </h2>
            </div>

            <!-- Trait à droite -->
            <span class=" w-3/12 h-px bg-grey  mr-4"></span>
        </div>

        <h1>Ajouter une annonce de livre</h1>
        <form action="new-book.php" method="post" enctype="multipart/form-data">
            <!-- Champ pour le titre -->
            <label for="titre">Titre du livre :</label>
            <input type="text" id="titre" name="titre" required><br>

            <!-- Champ pour le prix -->
            <label for="prix">Prix (€) :</label>
            <input type="number" id="prix" name="prix" step="0.01" min="0" required><br>

            <!-- Champ pour la description -->
            <label for="description">Description :</label><br>
            <textarea id="description" name="description" rows="5" cols="50" required></textarea><br>

            <!-- Champ pour la photo -->
            <label for="photo">Photo :</label>
            <input type="file" id="photo" name="photo" accept="image/*" required><br>

            <!-- Liste déroulante pour les auteurs -->
            <label for="auteur">Auteur :</label>
            <select id="auteur" name="auteur_id" required>
                <option value="" disabled selected>Choisissez un auteur</option>
                <option value="1">Victor Hugo</option>
                <option value="2">Jules Verne</option>
                <option value="3">Émile Zola</option>
                <option value="4">Honoré de Balzac</option>
                <option value="5">Gustave Flaubert</option>
            </select><br>

            <!-- Liste déroulante pour l'état -->
            <label for="etat">État du livre :</label>
            <select id="etat" name="etat_id" required>
                <option value="" disabled selected>Choisissez l'état</option>
                <option value="1">Neuf</option>
                <option value="2">Très bon état</option>
                <option value="3">Bon état</option>
                <option value="4">État correct</option>
            </select><br>

            <!-- Liste de genres avec checkboxes, conteneur défilable et limite de sélection -->
            <label for="genres">Genres :</label>
            <div id="genres-container" class="max-h-36 overflow-y-scroll border border-gray-300 rounded p-2 mb-4">
                <label class="block">
                    <input type="checkbox" name="genres[]" value="1" class="genre-checkbox mr-2"> Science-fiction
                </label>
                <label class="block">
                    <input type="checkbox" name="genres[]" value="2" class="genre-checkbox mr-2"> Fantastique
                </label>
                <label class="block">
                    <input type="checkbox" name="genres[]" value="3" class="genre-checkbox mr-2"> Policier
                </label>
                <label class="block">
                    <input type="checkbox" name="genres[]" value="4" class="genre-checkbox mr-2"> Romance
                </label>
                <label class="block">
                    <input type="checkbox" name="genres[]" value="5" class="genre-checkbox mr-2"> Horreur
                </label>
                <label class="block">
                    <input type="checkbox" name="genres[]" value="6" class="genre-checkbox mr-2"> Biographie
                </label>
                <label class="block">
                    <input type="checkbox" name="genres[]" value="7" class="genre-checkbox mr-2"> Histoire
                </label>
                <label class="block">
                    <input type="checkbox" name="genres[]" value="8" class="genre-checkbox mr-2"> Aventure
                </label>
                <label class="block">
                    <input type="checkbox" name="genres[]" value="9" class="genre-checkbox mr-2"> Philosophie
                </label>
                <label class="block">
                    <input type="checkbox" name="genres[]" value="10" class="genre-checkbox mr-2"> Poésie
                </label>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const checkboxes = document.querySelectorAll('.genre-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', () => {
                            const checkedCount = document.querySelectorAll('.genre-checkbox:checked').length;
                            if (checkedCount >= 3) {
                                checkboxes.forEach(cb => {
                                    if (!cb.checked) cb.disabled = true; // Désactiver les cases non sélectionnées
                                });
                            } else {
                                checkboxes.forEach(cb => cb.disabled = false); // Réactiver toutes les cases
                            }
                        });
                    });
                });
            </script>

            <!-- Bouton de soumission -->
            <button type="submit">Ajouter le livre</button>
        </form>

    </main>
</body>

<?php
require_once './partials/footer.php';
