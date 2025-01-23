<?php
class Book {
    private $id;
    private $titre;
    private $prix;
    private $description;
    private $photoPath;
    private $auteurId;
    private $etatId;
    private $vendeurId;
    private $genres = []; // Tableau des genres associés au livre

    public function __construct($titre, $prix, $description, $photoPath, $auteurId, $etatId, $vendeurId, $genres = []) {
        $this->titre = $titre;
        $this->prix = $prix;
        $this->description = $description;
        $this->photoPath = $photoPath;
        $this->auteurId = $auteurId;
        $this->etatId = $etatId;
        $this->vendeurId = $vendeurId;
        $this->genres = $genres;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getPrix() {
        return $this->prix;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getPhotoPath() {
        return $this->photoPath;
    }

    public function getAuteurId() {
        return $this->auteurId;
    }

    public function getEtatId() {
        return $this->etatId;
    }

    public function getVendeurId() {
        return $this->vendeurId;
    }

    public function getGenres() {
        return $this->genres;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setGenres($genres) {
        $this->genres = $genres;
    }
}