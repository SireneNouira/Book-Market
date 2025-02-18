<?php
include_once '../utils/autoloader.php';
session_start();
require '../utils/connect_db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$isLoggedIn = isset($_SESSION['user_id']);
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

require_once './partials/header.php';
?>

<body >
    <div class="flex justify-center bg-main shadow-lg">
        <p class="text-sm text-vertfonce flex items-center">Achats et Ventes de Livres d'Occasions</p>
    </div>
    <header class="flex items-center pt-2">
        <div class="pl-5 justify-self-start cursor-pointer ">
            <a href="#" aria-label="Menu" id="menu" class="">
                <box-icon name='menu' color='#a0a0a0'><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(160, 160, 160, 1);">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"></path>
                    </svg></box-icon>
             
            </a>
        </div>
        <div class="flex absolute justify-center w-full">
            <h1 class="text-2xl text-secondary">BookMarket</h1>
        </div>
        <?php if ($user_role == '1') { ?>
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
    <div id="sidebar" class="hidden bg-mainMenu  flex-col left-0 fixed top-0 pt-10 w-1/4 h-full text-2xl gap-5 px-8">
        <?php
        if (!$isLoggedIn) {
            echo '<a class="border py-2 flex justify-center rounded-sm mb-2" href="../back/login.php">Bonjour, Identifiez-vous</a>';
        } else {
            echo '<a class="border py-2 flex justify-center rounded-sm mb-2" href="profil.php">Bonjour, ' . $user['prenom'] . '</a>';
        }
        ?>

        <!-- Formulaire de recherche -->
        <form class="  justify-center hidden" action="search.php" method="get">
            <input class="border border-grey rounded text-center " type="text" name="query" placeholder="Rechercher..." required>
        </form>
        <ul class="flex flex-col gap-3">
            <a href="home.php">
                <li class="py-2  pl-4 rounded-sm bg-white">Nouveautés</li>
            </a>
            <li class="py-2  pl-4 rounded-sm bg-white">Genres</li>
            <li class="py-2  pl-4 rounded-sm bg-white">Auteurs</li>
            <li class="py-2  pl-4 rounded-sm bg-white">Petit Prix</li>
            <a href="new-book.php">
                <li class="py-2  pl-4 rounded-sm bg-white">Vendre</li>
            </a>
            <li class="py-2  pl-4 rounded-sm bg-white">Assistance</li>
            <a href="../back/logout.php">
                <li class="py-2  pl-4 rounded-sm bg-white">Se deconnecter</li>
            </a>
        </ul>
    </div>
    <main>
        <div class="inline-flex items-center justify-between w-full mt-16">
            <span class="w-3/12 h-px bg-grey ml-4"></span>
            <div class="flex-1 flex justify-center w-6/12 p-4 mx-40 bg-mainopacity rounded-sm shadow-2xl">
                <h2 class="text-xl text-vertfonce"><?= $user_nom . " " . $user_prenom; ?></h2>
            </div>
            <span class="w-3/12 h-px bg-grey mr-4"></span>
        </div>

        <nav class="mt-5">
            <ul class="w-full flex flex-row justify-center gap-16">
                <li>Informations Personnel</li>
                <?php if ($user_role == '2') { ?>
                    <li><a href="annonces_vendeur.php">Annonces</a></li>
                    <li>Demandes Clients</li>
                <?php } else { ?>
                    <li>Liste d'envie</li>
                <?php } ?>
                <li>Historique d'Achat</li>
                <li>Paramètres</li>
                <a href="../back/logout.php"><li>Deconnexion</li></a>
            </ul>
        </nav>

        <article class="p-8">

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <?php
            if ($user_role == '2') { ?>
                <a href="new-book.php" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow "><box-icon type='solid' name='plus-circle'><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" style="fill: rgba(160, 159, 159, 1)">
                            <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"></path>
                        </svg></box-icon>
                <h3 class="text-lg font-semibold text-vertfonce">Ajouter un Livre</h3>
                <p class="text-sm text-gray-600">Mettez en vente votre livre.</p></a>
            <?php } ?>


 <!-- Annonces -->
 <?php if ($user_role == '2') { ?>
                    <a href="annonces_vendeur.php" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <h3 class="text-lg font-semibold text-vertfonce">Annonces</h3>
                        <p class="text-sm text-gray-600">Gérez vos annonces de livres.</p>
                    </a>
                <?php } ?>
            
                <!-- Informations Personnel -->
                <a href="informations_personnel.php" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <h3 class="text-lg font-semibold text-vertfonce">Informations Personnel</h3>
                    <p class="text-sm text-gray-600">Modifiez vos informations personnelles.</p>
                </a>


                <!-- Demandes Clients -->
                <?php if ($user_role == '2') { ?>
                    <a href="demandes_clients.php" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <h3 class="text-lg font-semibold text-vertfonce">Demandes Clients</h3>
                        <p class="text-sm text-gray-600">Consultez les demandes de vos clients.</p>
                    </a>
                <?php } ?>

                <!-- Historique d'Achat -->
                <a href="historique_achat.php" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <h3 class="text-lg font-semibold text-vertfonce">Historique d'Achat</h3>
                    <p class="text-sm text-gray-600">Retrouvez votre historique d'achat.</p>
                </a>

                <!-- Paramètres -->
                <a href="parametres.php" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <h3 class="text-lg font-semibold text-vertfonce">Paramètres</h3>
                    <p class="text-sm text-gray-600">Modifiez vos paramètres de compte.</p>
                </a>

                <!-- Deconnexion -->
                <a href="../back/logout.php" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <h3 class="text-lg font-semibold text-vertfonce">Deconnexion</h3>
                    <p class="text-sm text-gray-600">Déconnectez-vous de votre compte.</p>
                </a>
            </div>
        </article>
    </main>
 
</body>

<?php
require_once './partials/footer.php';
?>