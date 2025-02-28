<?php

/**
 * Classe AbstractUIGenerator
 * Cette classe abstraite permet de définir les éléments de base d'un jeu de plateau.
 * @author Juba Nemri
 */

abstract class AbstractUIGenerator {

    Abstract protected static function getDebutHTML(string $title = "content title"): string ;

    Abstract protected static function getFinHTML(): string ;

    Abstract protected static function getErreurHTML(String $message): string ;

}

?>