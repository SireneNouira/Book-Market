<?php
abstract class User{

    protected $nom;
    protected $prenom;
    protected $mail;
    protected $password;
    protected $telephone;
    
    protected function __construct($nom, $prenom, $mail, $password, $telephone) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->mail = $mail;
        $this->password = $password;
        $this->telephone = $telephone;
    }

}