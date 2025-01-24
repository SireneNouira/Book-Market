<?php
include_once '../utils/autoloader.php';
require '../utils/connect_db.php';
session_start();

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

            header('Location: ../public/home.php');
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
    <link rel="stylesheet" href="../public/styles/output.css" />
    <link rel="stylesheet" href="../public/styles/style.css" />
    <script defer src="../public/assets/scripts/script.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bacasime+Antique&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Document</title>
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




    <section class=" flex flex-col justify-center items-center mx-80 ">
        <h1 class="text-2xl p-8">Identifiez-vous</h1>
        <?php if (isset($error_message)): ?>
            <p class="text-red-500"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <!-- Formulaire de connexion -->
        <form action="login.php" method="post">
            <label for="mail" class="block">Email :</label>
            <input type="email" id="mail" name="mail" required class="border p-2 rounded w-full"><br>

            <label for="password" class="block">Mot de passe :</label>
            <input type="password" id="password" name="password" required class="border p-2 rounded w-full"><br>

            <button type="submit" class="border rounded px-4 py-2 my-8 hover:text-main hover:underline flex justify-center items-center self-center mx-auto">
                Se connecter
            </button>
        </form>
        <p class="mt-4">
            Vous n'avez pas encore de compte ?
            <a href="create_account.php" class="hover:text-main hover:underline">Cliquez pour vous inscrire !</a>
        </p>
    </section>

</body>

</html>