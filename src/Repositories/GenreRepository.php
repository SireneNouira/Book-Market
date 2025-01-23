<?php

final  class GenreRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct();
    }

      // Récupérer tous les genres
      public function getAllGenres() {
        $stmt = $this->pdo->query('SELECT * FROM genres');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}