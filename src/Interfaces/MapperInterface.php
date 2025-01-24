<?php


interface MapperInterface
{
    /**
     * Convertit un tableau de données en un objet spécifique.
     *
     * @param array $data Les données source.
     * @return mixed Un objet correspondant.
     */
    public static function mapToObject(array $data): mixed;
}
