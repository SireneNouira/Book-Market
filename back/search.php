<?php
include_once '../utils/autoloader.php';
require '../utils/connect_db.php';

$query = $_GET['query'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Page actuelle
$resultsPerPage = 10; // Nombre de résultats par page

$bookRepository = new BookRepository();
$results = [];
$totalResults = 0;

if (!empty($query)) {
    try {
        // Obtenir les résultats de la recherche
        $results = $bookRepository->searchBooks($query, $page, $resultsPerPage);

        // Obtenir le nombre total de résultats
        $totalResults = $bookRepository->countBooks($query);
    } catch (Exception $e) {
        die("Erreur lors de la recherche : " . $e->getMessage());
    }
}

// Calcul du nombre total de pages
$totalPages = ceil($totalResults / $resultsPerPage);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche</title>
</head>
<body>
    <h1>Résultats de recherche</h1>
    <p>Résultats pour "<?= htmlspecialchars($query) ?>": <?= $totalResults ?> trouvé(s).</p>

    <?php if (!empty($results)): ?>
        <ul>
            <?php foreach ($results as $book): ?>
                <li>
                    <strong><?= htmlspecialchars($book['titre']) ?></strong> - 
                    <?= htmlspecialchars($book['description']) ?>
                    <br>
                    Prix : <?= htmlspecialchars($book['prix']) ?> €
                </li>
            <?php endforeach; ?>
        </ul>

        <div>
            <?php if ($page > 1): ?>
                <a href="?query=<?= urlencode($query) ?>&page=<?= $page - 1 ?>">Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?query=<?= urlencode($query) ?>&page=<?= $i ?>" <?= $i === $page ? 'style="font-weight: bold;"' : '' ?>>
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?query=<?= urlencode($query) ?>&page=<?= $page + 1 ?>">Suivant</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Aucun résultat trouvé.</p>
    <?php endif; ?>
</body>
</html>
