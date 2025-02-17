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
$isLoggedIn = isset($_SESSION['user_id']);
$user = null;

if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM utilisateurs WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
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

<body class="bg-gray-100">

    <div class="flex justify-center bg-main shadow-lg">
        <p class="text-sm text-vertfonce flex items-center">Achats et Ventes de Livres d'Occasions</p>
    </div>

    <header class="flex justify-between items-center pt-2">
        <div class="pl-5 justify-start">
            <a href="#" aria-label="Menu" id="menu" class="">
                <box-icon name="menu" color="#a0a0a0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(160, 160, 160, 1);">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"></path>
                    </svg>
                </box-icon>
            </a>
        </div>
        <div class="flex absolute justify-center w-full">
            <h1 class="text-3xl text-secondary">BookMarket</h1>
        </div>
    </header>

    <div id="sidebar" class="hidden bg-mainMenu flex-col left-0 fixed top-0 pt-10 w-1/4 h-full text-2xl gap-5 px-8">
        <?php
        if (!$isLoggedIn) {
            echo '<a class="border py-2 flex justify-center rounded-sm mb-2 bg-white hover:bg-gray-200 transition duration-300" href="../back/login.php">Bonjour, Identifiez-vous</a>';
        } else {
            echo '<a class="border py-2 flex justify-center rounded-sm mb-2 bg-white hover:bg-gray-200 transition duration-300" href="profil.php">Bonjour, ' . $user['prenom'] . '</a>';
        }
        ?>

        <!-- Formulaire de recherche -->
        <form class="justify-center hidden" action="search.php" method="get">
            <input class="border border-gray-300 rounded text-center p-2 w-full focus:outline-none focus:ring-2 focus:ring-main" type="text" name="query" placeholder="Rechercher..." required>
        </form>
        <ul class="flex flex-col gap-3">
            <a href="home.php">
                <li class="py-2 pl-4 rounded-sm bg-white hover:bg-gray-200 transition duration-300">Nouveautés</li>
            </a>
            <li class="py-2 pl-4 rounded-sm bg-white hover:bg-gray-200 transition duration-300">Genres</li>
            <li class="py-2 pl-4 rounded-sm bg-white hover:bg-gray-200 transition duration-300">Auteurs</li>
            <li class="py-2 pl-4 rounded-sm bg-white hover:bg-gray-200 transition duration-300">Petit Prix</li>
            <a href="new-book.php">
                <li class="py-2 pl-4 rounded-sm bg-white hover:bg-gray-200 transition duration-300">Vendre</li>
            </a>
            <li class="py-2 pl-4 rounded-sm bg-white hover:bg-gray-200 transition duration-300">Assistance</li>
            <a href="../back/logout.php">
                <li class="py-2 pl-4 rounded-sm bg-white hover:bg-gray-200 transition duration-300">Se deconnecter</li>
            </a>
        </ul>
    </div>

    <section class="flex flex-col justify-center items-center mx-auto max-w-2xl p-8 m-16 bg-white rounded-lg shadow-lg mt-10">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Identifiez-vous</h1>
        <?php if (isset($error_message)): ?>
            <p class="text-red-500 mb-4"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <!-- Formulaire de connexion -->
        <form action="login.php" method="post" class="w-full">
            <div class="mb-4">
                <label for="mail" class="block text-gray-700">Email :</label>
                <input type="email" id="mail" name="mail" required class="border border-gray-300 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-main">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700">Mot de passe :</label>
                <input type="password" id="password" name="password" required class="border border-gray-300 p-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-main">
            </div>
            <button type="submit" class="bg-main text-white px-4 py-2 rounded hover:bg-main-dark transition duration-300 w-full">
                Se connecter
            </button>
        </form>
        <p class="mt-4 text-gray-600">
            Vous n'avez pas encore de compte ?
            <a href="create_account.php" class="text-main hover:underline">Cliquez pour vous inscrire !</a>
        </p>
    </section>

</body>
</html>