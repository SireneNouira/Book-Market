<?php
include_once '../utils/autoloader.php';
session_start();
require '../utils/connect_db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$isLoggedIn = isset($_SESSION['user_id']);
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}


require_once './partials/header.php';
?>

<body class="bg-gray-100 font-sans">
    <div class="flex justify-center bg-main shadow-lg">
        <p class="text-sm text-vertfonce flex items-center">Achats et Ventes de Livres d'Occasions</p>
    </div>


    <header class="flex justify-between items-center pt-2">
        <div class="pl-5 w-3/12 justify-start">
            <a href="#" aria-label="Menu" id="menu" class="">
                <box-icon name="menu" color="#a0a0a0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(160, 160, 160, 1);">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"></path>
                    </svg>
                </box-icon>
            </a>
        </div>
        <div class=" flex-1 flex justify-center w-6/12 ">
            <h1 class="text-3xl text-secondary">BookMarket</h1>
        </div>


        <?php if (!$isLoggedIn): ?>
            <nav class="flex pr-5 w-3/12 justify-end text-grey">
                <a href="../back/login.php">Connexion/S'inscrire</a>
            </nav>
        <?php else: ?>

            <nav class="flex pr-5 gap-4 w-3/12 justify-end  ">
                <ul class="flex list-none gap-4">
                    <li>
                        <a href="panier.php" aria-label="Voir le panier">
                            <box-icon name='basket' type='solid' color='#a0a0a0'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(160, 160, 160, 1);">
                                    <path d="M21 9h-1.42l-3.712-6.496-1.736.992L17.277 9H6.723l3.146-5.504-1.737-.992L4.42 9H3a1.001 1.001 0 0 0-.965 1.263l2.799 10.264A2.005 2.005 0 0 0 6.764 22h10.473c.898 0 1.692-.605 1.93-1.475l2.799-10.263A.998.998 0 0 0 21 9zm-3.764 11v1-1H6.764L4.31 11h15.38l-2.454 9z"></path>
                                    <path d="M9 13h2v5H9zm4 0h2v5h-2z"></path>
                                </svg>
                            </box-icon>
                        </a>
                    </li>
                    <li>
                        <a href="profil.php" aria-label="Voir le profil">
                            <box-icon name='user-circle' type='solid' color='#a0a0a0'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(160, 160, 160, 1);">
                                    <path d="M12 2a5 5 0 1 0 5 5 5 5 0 0 0-5-5zm0 8a3 3 0 1 1 3-3 3 3 0 0 1-3 3zm9 11v-1a7 7 0 0 0-7-7h-4a7 7 0 0 0-7 7v1h2v-1a5 5 0 0 1 5-5h4a5 5 0 0 1 5 5v1z"></path>
                                </svg>
                            </box-icon>
                        </a>
                    </li>
                    <li>
                        <a href="#" aria-label="Voir les favoris">
                            <box-icon name='heart' color='#a0a0a0'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(160, 160, 160, 1);">
                                    <path d="M12 4.595a5.904 5.904 0 0 0-3.996-1.558 5.942 5.942 0 0 0-4.213 1.758c-2.353 2.363-2.352 6.059.002 8.412l7.332 7.332c.17.299.498.492.875.492a.99.99 0 0 0 .792-.409l7.415-7.415c2.354-2.354 2.354-6.049-.002-8.416a5.938 5.938 0 0 0-4.209-1.754A5.906 5.906 0 0 0 12 4.595zm6.791 1.61c1.563 1.571 1.564 4.025.002 5.588L12 18.586l-6.793-6.793c-1.562-1.563-1.561-4.017-.002-5.584.76-.756 1.754-1.172 2.799-1.172s2.035.416 2.789 1.17l.5.5a.999.999 0 0 0 1.414 0l.5-.5c1.512-1.509 4.074-1.505 5.584-.002z"></path>
                                </svg>
                            </box-icon>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </header>
    <div id="sidebar" class="hidden bg-mainMenu  flex-col left-0 fixed top-0 pt-10 w-1/4 h-full text-2xl gap-5 px-8">
        <a class="border py-2 flex justify-center rounded-sm mb-2" href="../back/login.php">Bonjour, Identifiez-vous</a>
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



    <div class="container mx-auto p-6">

        <h1 class="text-2xl font-bold text-gray-800 mt-12">Panier</h1>

        <?php if (empty($_SESSION['cart'])): ?>
            <p>Votre panier est vide.</p>
            <?php else: ?>
                <div class="scroll-container">
                <?php foreach ($_SESSION['cart'] as $item): ?>

           

                <ul id="panier-list" class="mt-4 ">

                <div class="flex place-content-between bg-white p-4 rounded-lg shadow-lg items-center text-2xl">
                    <li><img class="w-20" src="./assets/imgs/<?= $item['path'] ?>" alt="picture Item"></li>
                    <li><?= htmlspecialchars($item['name']) ?></li>
                    <li><?= htmlspecialchars($item['price']) ?> €</li>
                    <li><?= htmlspecialchars($item['quantity']) ?></li> 
                    <a href="../back/remove_from_cart.php?id=<?= $item['id'] ?>" class="text-xl bg-red-500 hover:bg-red-700 font-bold py-1 px-2 rounded text-white no-underline">
    Supprimer
</a>                </div>

                </ul>
           
                <?php endforeach; ?></div>
            <?php endif; ?>
            </div>

</body>

<?php

require_once './partials/footer.php';
