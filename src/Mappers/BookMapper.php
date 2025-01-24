<?php

class BookMapper implements MapperInterface
{
    public static function mapToObject(array $data): Book
    {
        return new Book(
            $data['titre'],
            $data['prix'],
            $data['description'],
            $data['photo_path'],
            $data['auteur_id'],
            $data['etat_id'],
            $data['id_vendeur'],
            
        );
    }
}
