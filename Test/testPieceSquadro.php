<?php

// Inclusion de la classe PieceSquadro
require_once '../PieceSquadro.php';

// Fonction pour afficher les résultats des tests
function afficherResultat(string $message, $resultat) {
    echo $message . ": " . (is_string($resultat) ? $resultat : $resultat->toJson()) . PHP_EOL;
}

// Test de création d'objets
$pieceVide = PieceSquadro::initVide();
afficherResultat("Initialisation d'une pièce vide", $pieceVide);

$pieceNeutre = PieceSquadro::initNeutre();
afficherResultat("Initialisation d'une pièce neutre", $pieceNeutre);

$pieceNoirNord = PieceSquadro::initNoirNord();
afficherResultat("Initialisation d'une pièce noire au nord", $pieceNoirNord);

$pieceBlancEst = PieceSquadro::initBlancEst();
afficherResultat("Initialisation d'une pièce blanche à l'est", $pieceBlancEst);

// Test des getters
echo($pieceNoirNord->getCouleur());
echo($pieceBlancEst->getDirection());

// Test de l'inversion de direction
$pieceNoirNord->inverseDirection();
afficherResultat("Inversion de la direction de la pièce noire", $pieceNoirNord);

$pieceBlancEst->inverseDirection();
afficherResultat("Inversion de la direction de la pièce blanche", $pieceBlancEst);

// Test des conversions JSON
$jsonPiece = $pieceBlancEst->toJson();
afficherResultat("Conversion de la pièce blanche à JSON", $jsonPiece);

$newPiece = PieceSquadro::fromJson($jsonPiece);
afficherResultat("Création d'une nouvelle pièce depuis JSON", $newPiece);

// Test du __toString
afficherResultat("Affichage de la pièce blanche avec __toString", $pieceBlancEst->__toString());

