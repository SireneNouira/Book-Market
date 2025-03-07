<?php
session_start();
require '../utils/connect_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $mail = trim($_POST['mail']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $nomEntreprise = isset($_POST['nom_entreprise']) ? trim($_POST['nom_entreprise']) : null;
    $adresseEntreprise = isset($_POST['adresse_entreprise']) ? trim($_POST['adresse_entreprise']) : null;

    try {
        // Vérification de l'existence de l'email dans la base de données
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE mail = :mail');
        $stmt->bindParam(':mail', $mail);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error_message = 'Cet email est déjà utilisé.';
        } else {
            // Validation des champs entreprise pour les vendeurs
            if ($role === 'vendeur' && (empty($nomEntreprise) || empty($adresseEntreprise))) {
                $error_message = 'Les informations d\'entreprise sont obligatoires pour les vendeurs.';
            } else {
                // Hacher le mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


                // Insertion dans la table utilisateurs
                $role = $role === 'vendeur' ? 2 : 1; // 2 pour vendeur, 1 pour acheteur
                $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, prenom, telephone, mail, password, id_role) 
        VALUES (:nom, :prenom, :telephone, :mail, :password, :role)');
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->bindParam(':telephone', $telephone);
                $stmt->bindParam(':mail', $mail);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':role', $role);
                $stmt->execute();


                // Si c'est un vendeur, ajouter les informations d'entreprise dans la table 'vendeurs'
                if ($role == 2) {
                    $lastInsertId = $pdo->lastInsertId(); // Récupérer l'ID de l'utilisateur créé

                    $stmt = $pdo->prepare('INSERT INTO vendeurs ( nom_entreprise, adresse_entreprise, id_utilisateur) 
                           VALUES ( :nom_entreprise, :adresse_entreprise, :id_utilisateur)');

                    $stmt->bindParam(':nom_entreprise', $nomEntreprise);
                    $stmt->bindParam(':adresse_entreprise', $adresseEntreprise);
                    $stmt->bindParam(':id_utilisateur', $lastInsertId);
                    $stmt->execute();
                }
                // Rediriger vers une page et session
                $_SESSION['user_id'] = $pdo->lastInsertId(); // L'ID de l'utilisateur créé
                $_SESSION['user_nom'] = $nom; // Le nom fourni lors de l'inscription
                $_SESSION['is_logged_in'] = true;

                header('Location: ../public/home.php');
                exit;
            }
        }
    } catch (Exception $e) {
        $error_message = 'Une erreur est survenue : ' . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/styles/output.css" />
    <link rel="stylesheet" href="../public/styles/style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Bacasime+Antique&display=swap" rel="stylesheet">
    <script src="../public/assets/scripts/script.js"></script>
    <title>Create</title>
</head>

<body>
    <div class="flex justify-center bg-main shadow-lg">
        <p class="text-sm text-vertfonce flex items-center">Achats et Ventes de Livres d'Occasions</p>
    </div>


    <header class="flex justify-between items-center pt-2 ">
        <div class="pl-5  justify-start">
            <a href="#" aria-label="Menu" id="menu" class="">
                <box-icon name="menu" color="#a0a0a0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(160, 160, 160, 1);">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"></path>
                    </svg>
                </box-icon>
            </a>
        </div>
        <div class=" flex absolute justify-center w-full">
            <h1 class="text-3xl text-secondary">BookMarket</h1>
        </div>

    </header>

    <section class="flex flex-col justify-center items-center min-h-screen bg-gray-100 p-6">
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-lg">
        <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Inscription</h1>

        <?php if (isset($error_message)): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="create_account.php" method="post" class="space-y-4">
            <div>
                <label for="nom" class="block text-gray-700 font-medium">Nom :</label>
                <input type="text" id="nom" name="nom" class="mt-1 w-full p-3 border rounded-md focus:ring-2 focus:main outline-none" required>
            </div>

            <div>
                <label for="prenom" class="block text-gray-700 font-medium">Prénom :</label>
                <input type="text" id="prenom" name="prenom" class="mt-1 w-full p-3 border rounded-md focus:ring-2 focus:main outline-none" required>
            </div>

            <div>
                <label for="telephone" class="block text-gray-700 font-medium">Numéro de téléphone :</label>
                <input type="text" id="telephone" name="telephone" class="mt-1 w-full p-3 border rounded-md focus:ring-2 focus:main outline-none" required>
            </div>

            <div>
                <label for="mail" class="block text-gray-700 font-medium">Email :</label>
                <input type="email" id="mail" name="mail" class="mt-1 w-full p-3 border rounded-md focus:ring-2 focus:main outline-none" required>
            </div>

            <div>
                <label for="password" class="block text-gray-700 font-medium">Mot de passe :</label>
                <input type="password" id="password" name="password" class="mt-1 w-full p-3 border rounded-md focus:ring-2 focus:main outline-none" required>
            </div>

            <div>
                <label for="role" class="block text-gray-700 font-medium">Choisir un rôle :</label>
                <select id="role" name="role" class="mt-1 w-full p-3 border rounded-md focus:ring-2 focus:main outline-none" required onchange="toggleEntrepriseFields()">
                    <option value="acheteur">Acheteur</option>
                    <option value="vendeur">Vendeur</option>
                </select>
            </div>

            <div id="extra-fields" class="hidden">
                <div>
                    <label for="nom_entreprise" class="block text-gray-700 font-medium">Nom de l'entreprise :</label>
                    <input type="text" id="nom_entreprise" name="nom_entreprise" class="mt-1 w-full p-3 border rounded-md focus:ring-2 focus:mainopacity outline-none">
                </div>

                <div>
                    <label for="adresse_entreprise" class="block text-gray-700 font-medium">Adresse de l'entreprise :</label>
                    <input type="text" id="adresse_entreprise" name="adresse_entreprise" class="mt-1 w-full p-3 border rounded-md focus:ring-2 focus:mainopacity outline-none">
                </div>
            </div>

            <button type="submit" class="w-full bg-main hover:mainmenu text-white font-medium p-3 rounded-md transition duration-300">
                S'inscrire
            </button>
        </form>

        <p class="text-center mt-6 text-gray-700">
            Vous avez déjà un compte ?
            <a href="login.php" class="text-vertfonce hover:underline">Cliquez pour vous connecter !</a>
        </p>
    </div>
</section>



</body>

</html>