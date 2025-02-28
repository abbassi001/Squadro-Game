<?php

require_once '../PieceSquadro.php';
require_once '../ArrayPieceSquadro.php';

// Test de la classe ArrayPieceSquadro
function testArrayPieceSquadro() {
    // Création d'un tableau de pièces
    $array = new ArrayPieceSquadro();

    // Ajout de pièces
    $array->add(PieceSquadro::initVide());
    $array->add(PieceSquadro::initNoirNord());
    $array->add(PieceSquadro::initBlancEst());

    echo "Après ajout de trois pièces :\n";
    echo $array . "\n\n";

    // Accès via ArrayAccess
    echo "Accès à la pièce 1 (Noir Nord) :\n";
    echo $array[1] . "\n\n";

    // Modification d'une pièce via ArrayAccess
    $array[1] = PieceSquadro::initBlancOuest();
    echo "Après modification de la pièce 1 :\n";
    echo $array . "\n\n";

    // Suppression d'une pièce
    $array->remove(0); // Supprime la première pièce
    echo "Après suppression de la première pièce :\n";
    echo $array . "\n\n";

    // Comptage des pièces
    echo "Nombre de pièces restantes : " . count($array) . "\n\n";

    // Conversion en JSON
    $json = $array->toJson();
    echo "Représentation JSON :\n$json\n\n";

    // Création depuis JSON
    $arrayFromJson = ArrayPieceSquadro::fromJson($json);
    echo "Création d'un nouvel objet ArrayPieceSquadro depuis JSON :\n";
    echo $arrayFromJson . "\n\n";

    // Test des exceptions
    try {
        $array[3] = "Invalid Piece"; // Essayer d'ajouter un objet non valide
    } catch (InvalidArgumentException $e) {
        echo "Exception attrapée (comme prévu) : " . $e->getMessage() . "\n\n";
    }
}

testArrayPieceSquadro();
