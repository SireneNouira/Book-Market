<?php

final  class AuteurRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct();
    }

       // Récupérer tous les auteurs
       public function getAllAuteurs() {
        $stmt = $this->pdo->query('SELECT * FROM auteurs');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}