<?php
include_once '../utils/autoloader.php';
session_start();
require '../utils/connect_db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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

        <nav class="mt-5">
            <ul class=" w-full flex flex-row justify-center gap-16">
                <li>Informations Personnel</li>


                <?php
                if ($user_role == '2') { ?>
                    <li><a href="annonces_vendeur.php">Annonces</a></li>
                    <li>Demandes Clients</li>
                <?php } else { ?>
                    <li>Liste d'envie</li>
                <?php } ?>

                <li>Historique d'Achat</li>
                <li>Paramètres</li>
                <a href="../back/logout.php">
                    <li>Deconnexion</li>
                </a>
            </ul>
        </nav>

        <article>
        <?php
        if ($user_role == '2') { ?>
           <a href="new-book.php"><box-icon type='solid' name='plus-circle'><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" style="fill: rgba(160, 159, 159, 1)">
                        <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"></path>
                    </svg></box-icon>
            </a> 
        <?php } ?>
           

        </article>
    </main>

</body>

<?php
require_once './partials/footer.php';