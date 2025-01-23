<?php
class Vendeur extends User {
    
    private $nomEntreprise; 
    private $adresseEntreprise; 


    public function __construct($nom, $prenom, $mail, $password, $telephone, $nomEntreprise, $adresseEntreprise) {
        parent::__construct($nom, $prenom, $mail, $password, $telephone);
        $this->nomEntreprise = $nomEntreprise;
        $this->adresseEntreprise = $adresseEntreprise;

    }
}