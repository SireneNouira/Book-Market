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
    public function getBookById($id): Book
    {
        $stmt = $this->pdo->prepare('SELECT * FROM livres WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);

        $bookMapper = new BookMapper();
        return $bookMapper->mapToBook($bookData);
    
    }

   // Récupérer tous les livres d'un vendeur spécifique par son ID
public function getBooksByVendeurId(int $idVendeur): array
{
    $stmt = $this->pdo->prepare('SELECT * FROM livres WHERE id_vendeur = :id_vendeur');
    $stmt->execute([':id_vendeur' => $idVendeur]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
