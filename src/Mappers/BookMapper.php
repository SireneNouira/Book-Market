<?php

class BookMapper implements MapperInterface
{
    public static function mapToObject(array $data): Book
    {
        return new Book(
            $data['titre'],
            $data['prix'],
            $data['description'],
            $data['photoPath'],
            $data['auteurId'],
            $data['etatId'],
            $data['vendeurId'],
            $data['genres'],
        );
    }
}
