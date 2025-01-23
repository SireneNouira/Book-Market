<?php
class Acheteur extends User {
    private $adresseLivraison; 

    public function __construct($nom, $prenom, $mail, $password, $telephone, $adresseLivraison) {
        parent::__construct($nom, $prenom, $mail, $password, $telephone);
        $this->adresseLivraison = $adresseLivraison;
    }
}