<?php
session_start();
require 'includes/connect_db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'];
    $password = $_POST['password'];

    // Rechercher l'utilisateur dans la base de données
    $stmt = $pdo->prepare('SELECT id, password, nom FROM utilisateurs WHERE mail = :mail');
    $stmt->bindParam(':mail', $mail);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password'])) {
           
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['is_logged_in'] = true;
          
            header('Location: ../front/index.php');
            exit;
        } else {
            $error_message = 'Mot de passe incorrect.';
        }
    } else {
        $error_message = 'Aucun utilisateur trouvé avec cet email.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
<?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <!-- Formulaire de connexion -->
    <form action="login.php" method="post">
        <label for="mail">Email :</label>
        <input type="email" id="mail" name="mail" required><br>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Se connecter</button>
    </form>


    <p>Vous n'avez pas encore de compte ? <a href="create_account.php">Cliquez pour vous inscrire !</a></p>
</body>

</html>