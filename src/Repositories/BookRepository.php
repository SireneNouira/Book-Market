<?php

final  class BookRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct();
    }
    public function addBook(Book $book)
    {
        try {
            // Insertion dans la table `livres`
            $stmt = $this->pdo->prepare('
                    INSERT INTO livres (id_vendeur, titre, prix, description, photo_path, auteur_id, etat_id)
                    VALUES (:id_vendeur, :titre, :prix, :description, :photo_path, :auteur_id, :etat_id)
                ');
            $stmt->execute([
                ':id_vendeur' => $book->getVendeurId(),
                ':titre' => $book->getTitre(),
                ':prix' => $book->getPrix(),
                ':description' => $book->getDescription(),
                ':photo_path' => $book->getPhotoPath(),
                ':auteur_id' => $book->getAuteurId(),
                ':etat_id' => $book->getEtatId()
            ]);

            // Récupérer l'ID du livre inséré
            $bookId = $this->pdo->lastInsertId();

            // Insérer les genres associés dans la table `livre_genre`
            foreach ($book->getGenres() as $genreId) {
                $stmt = $this->pdo->prepare('
                        INSERT INTO livre_genre (id_livre, id_genre)
                        VALUES (:id_livre, :id_genre)
                    ');
                $stmt->execute([
                    ':id_livre' => $bookId,
                    ':id_genre' => $genreId
                ]);
            }

            return true;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout du livre : " . $e->getMessage());
        }
    }

    // Récupérer tous les livres
    public function getAllBooks(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM livres');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un livre par son id 
    public function getBookById(int $id): ?Book
    {
        $stmt = $this->pdo->prepare('SELECT * FROM livres WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $bookMapper = new BookMapper();
        return $bookMapper->mapToObject($data);
    }


    // Récupérer tous les livres d'un vendeur spécifique par son ID
    public function getBooksByVendeurId(int $idVendeur): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM livres WHERE id_vendeur = :id_vendeur');
        $stmt->execute([':id_vendeur' => $idVendeur]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchBooks(string $query, int $page = 1, int $resultsPerPage = 10): array
    {
        $offset = ($page - 1) * $resultsPerPage; // Calcul de l'offset
        $stmt = $this->pdo->prepare('
        SELECT * 
        FROM livres 
        WHERE titre LIKE :query 
           OR description LIKE :query 
        LIMIT :limit OFFSET :offset
    ');

        // Préparer les paramètres
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Exécuter la requête
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countBooks(string $query): int
    {
        $stmt = $this->pdo->prepare('
        SELECT COUNT(*) as total 
        FROM livres 
        WHERE titre LIKE :query 
           OR description LIKE :query
    ');
        $stmt->execute([':query' => '%' . $query . '%']);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getGenreIds(int $id): array
    {
        try {

            $stmt = $this->pdo->prepare('SELECT id_genre FROM livre_genre WHERE id_livre = :id_livre');
            $stmt->bindParam(':id_livre', $id, PDO::PARAM_INT);

            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Extraction des id_genre dans un tableau
            return array_map(fn($row) => (int) $row['id_genre'], $results);
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }

    public function getGenreNames(array $genreIds): array
    {
        if (empty($genreIds)) {
            return [];
        }

        try {
            $placeholders = implode(',', array_fill(0, count($genreIds), '?'));
            $query = "SELECT nom FROM genres WHERE id IN ($placeholders)";
            $stmt = $this->pdo->prepare($query);


            $stmt->execute($genreIds);


            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Extraction des noms dans un tableau
            return array_map(fn($row) => $row['nom'], $results);
        } catch (PDOException $e) {
            // Gestion des erreurs (log ou message adapté)
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }


    public function getAuteur(int $auteurId): string
    {
        try {
            $stmt = $this->pdo->prepare('SELECT nom FROM auteurs WHERE id = :id');
            $stmt->bindParam(':id', $auteurId, PDO::PARAM_INT);
    
            $stmt->execute();
 
            $auteur = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $auteur ? $auteur['nom'] : 'Auteur inconnu';
        } catch (PDOException $e) {
            // Gestion des erreurs
            echo "Erreur : " . $e->getMessage();
            return 'Erreur lors de la récupération de l\'auteur';
        }
    }
    
    public function getEtat(int $etatId): string
    {
        try {
            $stmt = $this->pdo->prepare('SELECT description FROM etats WHERE id = :id');
            $stmt->bindParam(':id', $etatId, PDO::PARAM_INT);
    
            $stmt->execute();
    
            $etat = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $etat ? $etat['description'] : 'Etat inconnu';
        } catch (PDOException $e) {
            // Gestion des erreurs
            echo "Erreur : " . $e->getMessage();
            return 'Erreur lors de la récupération de l\'état';
        }

    }
}
