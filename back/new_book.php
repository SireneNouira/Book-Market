<?php
include_once '../utils/autoloader.php';
session_start();
require '../utils/connect_db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/home.php');
    exit;
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT * FROM utilisateurs WHERE id = :user_id";  // Utilisation d'un paramètre nommé avec PDO
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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $titre = trim($_POST['titre']);
    $prix = trim($_POST['prix']);
    $description = trim($_POST['description']);
    $photo = $_FILES['photo']['name'];
    $auteur_id = (int)$_POST['auteur_id'];
    $etat_id = (int)$_POST['etat_id'];
    $genre = $_POST['genres'] ?? [];

    if (empty($genres)) {
        $error_message = 'Vous devez sélectionner au moins un genre.';
    } else {
    try {
        
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

                // Insertion dans la table livres
                
                $stmt = $pdo->prepare('INSERT INTO livres (id_vendeur, titre, prix, description, photo_path, auteur_id, etat_id) 
        VALUES (:id_vendeur, :titre, :prix, :description, :photo_path, :auteur_id, :etat_id)');
       $stmt->bindParam(':id_vendeur', $id_vendeur, PDO::PARAM_INT);
                $stmt->bindParam(':titre', $titre);
                $stmt->bindParam(':prix', $prix);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':photo_path', $photo);
                $stmt->bindParam(':auteur_id', $auteur_id, PDO::PARAM_INT);
        $stmt->bindParam(':etat_id', $etat_id, PDO::PARAM_INT);
                $stmt->execute();


                $id_livre = $pdo->lastInsertId(); // ID du livre inséré


                // Insertion dans la table `livre_genre` pour chaque genre sélectionné
        $stmt = $pdo->prepare('INSERT INTO livre_genre (id_livre, id_genre) VALUES (:id_livre, :id_genre)');

        foreach ($genres as $id_genre) {
            // Vérifier que l'id_genre existe dans la table genres (sécuriser l'insertion)
            $checkGenreStmt = $pdo->prepare('SELECT id FROM genres WHERE id = :id_genre');
            $checkGenreStmt->bindParam(':id_genre', $id_genre, PDO::PARAM_INT);
            $checkGenreStmt->execute();
            
            if ($checkGenreStmt->rowCount() > 0) {
                // Lier l'ID du livre et l'ID du genre
                $stmt->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
                $stmt->bindParam(':id_genre', $id_genre, PDO::PARAM_INT);
                $stmt->execute(); // Insérer chaque genre
                
            }}

         // Rediriger après succès
         header('Location: ../front/annonces.php');
         exit;
     } catch (Exception $e) {
         $error_message = 'Une erreur est survenue : ' . $e->getMessage();
     }
 }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../front/styles/output.css" />
    <link rel="stylesheet" href="../front/styles/style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Bacasime+Antique&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Document</title>
</head>

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

        <?php
        if ($user_role == '1') { ?>
            <div class="pr-5 flex justify-self-end justify-end ml-auto">
                <a href="#" aria-label="Voir le panier">
                    <box-icon name='basket' type='solid' color='#a0a0a0'>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(160, 160, 160, 1);">
                            <path d="M21 9h-1.42l-3.712-6.496-1.736.992L17.277 9H6.723l3.146-5.504-1.737-.992L4.42 9H3a1.001 1.001 0 0 0-.965 1.263l2.799 10.264A2.005 2.005 0 0 0 6.764 22h10.473c.898 0 1.692-.605 1.93-1.475l2.799-10.263A.998.998 0 0 0 21 9zm-3.764 11v1-1H6.764L4.31 11h15.38l-2.454 9z"></path>
                            <path d="M9 13h2v5H9zm4 0h2v5h-2z"></path>
                        </svg>
                    </box-icon>
                </a>
            </div>
        <?php } ?>
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
    <form action="new_book.php" method="post" enctype="multipart/form-data">
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

</html>