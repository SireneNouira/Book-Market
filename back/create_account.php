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
    <link rel="stylesheet" href="../front/styles/output.css" />
    <link rel="stylesheet" href="../front/styles/style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Bacasime+Antique&display=swap" rel="stylesheet">
    <script src="../public/script.js"></script>
    <title>Create</title>
</head>

<body>
    <h2>Inscription</h2>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <!-- Formulaire de création de compte -->
    <form action="create_account.php" method="post">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required><br>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required><br>

        <label for="telephone">Numéro de téléphone :</label>
        <input type="text" id="telephone" name="telephone" required><br>

        <label for="mail">Email :</label>
        <input type="email" id="mail" name="mail" required><br>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" id="password" name="password" required><br>

        <label for="role">Choisir un rôle :</label>
        <select id="role" name="role" required onchange="toggleEntrepriseFields()">
            <!-- toggleEntrepriseFields est le nom de la fonction a executer lorque l'evenement onchange est déclenché -->
            <option value="acheteur">Acheteur</option>
            <option value="vendeur">Vendeur</option>
        </select><br>

        <!-- Champ supplémentaire pour les vendeurs -->
        <div id="extra-fields" class="extra-fields hidden">
            <label for="nom_entreprise">Nom de l'entreprise :</label>
            <input type="text" id="nom_entreprise" name="nom_entreprise" ><br>

            <label for="adresse_entreprise">Adresse de l'entreprise :</label>
            <input type="text" id="adresse_entreprise" name="adresse_entreprise"><br>
        </div>





        <button type="submit">S'inscrire</button>
    </form>
    <p>Vous avez déja un compte ? <a href="login.php">Cliquez pour vous connecté !</a></p>

</body>

</html>