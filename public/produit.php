<?php
require_once __DIR__ . '../../utils/autoloader.php';
require '../utils/connect_db.php';
session_start();

$isLoggedIn = isset($_SESSION['user_id']);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $bookId = (int)$_GET['id'];

    $bookRepository = new BookRepository();
    $book = $bookRepository->getBookById($bookId);
    if ($book) {
        try {
            $genreIds = $bookRepository->getGenreIds($bookId);
            $genreNames = $bookRepository->getGenreNames($genreIds);
            $auteur = $bookRepository->getAuteur($book->getAuteurId());
            $etatId = $book->getEtatId();
            $etat = $bookRepository->getEtat($etatId);
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "<p class='text-red-500'>Livre non trouvé.</p>";
    }
} else {
    echo "<p class='text-red-500'>Identifiant de livre non valide ou manquant.</p>";
}

try {
    $stmt = $pdo->prepare('SELECT id_utilisateur FROM vendeurs WHERE id = :id');
    $stmt->bindParam(':id', $vendeurId, PDO::PARAM_INT);
    $vendeurId = $book->getVendeurId();
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $stmtUser = $pdo->prepare('SELECT nom, prenom FROM utilisateurs WHERE id = :id');
        $stmtUser->bindParam(':id', $result['id_utilisateur'], PDO::PARAM_INT);
        $stmtUser->execute();
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<p class='text-red-500'>Aucun utilisateur trouvé pour cet id vendeur.</p>";
    }
} catch (PDOException $e) {
    echo "<p class='text-red-500'>Erreur : " . $e->getMessage() . "</p>";
}

require_once './partials/header.php';
?>

<body class="bg-gray-100">
    <div class="flex justify-center bg-main shadow-lg py-2">
        <p class="text-sm text-vertfonce flex items-center">Achats et Ventes de Livres d'Occasions</p>
    </div>

    <header class="flex justify-between items-center pt-2 px-4">
        <div class="pl-5 w-3/12 justify-start">
        <a href="#" aria-label="Menu" id="menu" class="">
                <box-icon name="menu" color="#a0a0a0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(160, 160, 160, 1);">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"></path>
                    </svg>
                </box-icon>
            </a>
        </div>
        <div class="flex-1 flex justify-center w-6/12">
            <h1 class="text-3xl text-secondary">BookMarket</h1>
        </div>

        <?php if (!$isLoggedIn): ?>
            <nav class="flex pr-5 w-3/12 justify-end text-grey">
                <a href="../back/login.php" class="text-gray-600 hover:text-gray-900">Connexion/S'inscrire</a>
            </nav>
        <?php else: ?>
            <nav class="flex pr-5 gap-4 w-3/12 justify-end">
                <ul class="flex list-none gap-4">
                    <li>
                        <a href="#" aria-label="Voir le panier" class="text-gray-600 hover:text-gray-900">
                            <box-icon name='basket' type='solid' color='#a0a0a0'></box-icon>
                        </a>
                    </li>
                    <li>
                        <a href="profil.php" aria-label="Voir le profil" class="text-gray-600 hover:text-gray-900">
                            <box-icon name='user-circle' type='solid' color='#a0a0a0'></box-icon>
                        </a>
                    </li>
                    <li>
                        <a href="#" aria-label="Voir les favoris" class="text-gray-600 hover:text-gray-900">
                            <box-icon name='heart' color='#a0a0a0'></box-icon>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </header>

    <div id="sidebar" class="hidden bg-mainMenu flex-col left-0 fixed top-0 pt-10 w-1/4 h-full text-2xl gap-5 px-8">
        <?php if (!$isLoggedIn): ?>
            <a class="border py-2 flex justify-center rounded-sm mb-2 text-gray-600 hover:text-gray-900" href="../back/login.php">Bonjour, Identifiez-vous</a>
        <?php else: ?>
            <a class="border py-2 flex justify-center rounded-sm mb-2 text-gray-600 hover:text-gray-900" href="profil.php">Bonjour, <?= $user['prenom'] ?></a>
        <?php endif; ?>
        <form class="justify-center hidden" action="search.php" method="get">
            <input class="border border-grey rounded text-center" type="text" name="query" placeholder="Rechercher..." required>
        </form>
        <ul class="flex flex-col gap-3">
            <a href="home.php"><li class="py-2 pl-4 rounded-sm bg-white text-gray-600 hover:text-gray-900">Nouveautés</li></a>
            <li class="py-2 pl-4 rounded-sm bg-white text-gray-600 hover:text-gray-900">Genres</li>
            <li class="py-2 pl-4 rounded-sm bg-white text-gray-600 hover:text-gray-900">Auteurs</li>
            <li class="py-2 pl-4 rounded-sm bg-white text-gray-600 hover:text-gray-900">Petit Prix</li>
            <a href="new-book.php"><li class="py-2 pl-4 rounded-sm bg-white text-gray-600 hover:text-gray-900">Vendre</li></a>
            <li class="py-2 pl-4 rounded-sm bg-white text-gray-600 hover:text-gray-900">Assistance</li>
            <a href="../back/logout.php"><li class="py-2 pl-4 rounded-sm bg-white text-gray-600 hover:text-gray-900">Se déconnecter</li></a>
        </ul>
    </div>

    <section class="mt-16">
        <div class="inline-flex items-center justify-between w-full">
            <span class="w-3/12 h-px bg-grey ml-4"></span>
            <form class="flex-1 flex justify-center w-6/12 mx-4" action="../back/search.php" method="get">
                <input class="border border-grey rounded text-center px-36 py-2" type="text" name="query" placeholder="Rechercher..." value="<?= htmlspecialchars($query ?? '') ?>" required>
            </form>
            <span class="w-3/12 h-px bg-grey mr-4"></span>
        </div>
    </section>

    <div class="flex items-center justify-center bg-mainMenu w-full h-12 mt-5">
        <h1 class="text-xl text-gray-600"><?= $user['nom'] . " " . $user['prenom'] ?></h1>
    </div>

    <section class="flex flex-wrap m-8">
        <div class="w-1/2 h-1/2 flex flex-col items-center">
            <div class="border border-grey w-full flex justify-center p-4">
                <img class="h-80 w-52 object-cover rounded-lg shadow-lg" src="./assets/imgs/<?= $book->getPhotoPath() ?>" alt="Photos Livres">
            </div>
            <p class="text-xl text-gray-600 mt-4"><?= $etat ?></p>
            <p class="m-2 text-2xl font-semibold text-gray-800"><?= $book->getPrix() ?>$</p>
            <div class="flex gap-2 m-2">
                <?php if ($isLoggedIn): ?>
                    <button class="px-4 py-2 text-xl font-semibold border border-grey rounded-md text-gray-600 hover:bg-gray-200">Acheter</button>
                    <form action="../back/add_to_cart.php?id=<?= $bookId ?>" method="post">
                        <button class="px-4 py-2 text-xl font-semibold border border-grey rounded-md text-gray-600 hover:bg-gray-200">Ajouter au panier</button>
                    </form>
                <?php else: ?>
                    <a href="../back/login.php"><button class="px-4 py-2 text-xl font-semibold border border-grey rounded-md text-gray-600 hover:bg-gray-200">Acheter</button></a>
                    <a href="../back/login.php"><button class="px-4 py-2 text-xl font-semibold border border-grey rounded-md text-gray-600 hover:bg-gray-200">Ajouter au panier</button></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="w-1/2 h-auto py-5 px-8 flex flex-col border border-grey bg-white rounded-lg shadow-lg">
            <div class="flex justify-center items-center">
                <h2 class="text-2xl font-semibold pb-5 text-gray-800"><?= $book->getTitre() ?></h2>
            </div>
            <p class="font-medium text-lg pl-5 py-1 underline text-gray-600">Genres : </p>
            <?php foreach ($genreNames as $genre): ?>
                <p class="text-gray-600"><?= htmlspecialchars($genre, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endforeach; ?>
            <p class="font-medium text-lg pl-5 py-1 underline text-gray-600">Auteur : </p>
            <p class="text-gray-600"><?= $auteur ?></p>
            <p class="font-medium text-lg p-5 underline text-gray-600">Description :</p>
            <p class="text-gray-600"><?= $book->getDescription() ?></p>
        </div>
        <div class="w-1/2 mt-8">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4">Découvrir</h3>
            <div class="flex flex-wrap gap-8 cursor-pointer">
                <?php
                $bookRepository = new BookRepository();
                $books = $bookRepository->getAllBooks();
                foreach ($books as $book) {
                ?>
                    <div class="flex flex-col items-center w-44 gap-2">
                        <a href="produit.php?id=<?= $book['id'] ?>">
                            <img src="<?= "./assets/imgs/" . $book['photo_path'] ?>" alt="Photo Livre" class="w-full h-40 object-cover rounded-lg shadow-lg hover:shadow-xl">
                            <h3 class="text-lg font-medium text-center text-gray-800"><?= $book['titre'] ?></h3>
                            <p class="text-md text-gray-600">Prix <br> <?= $book['prix'] ?> €</p>
                        </a>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </section>
</body>

<?php
require_once './partials/footer.php';
?>