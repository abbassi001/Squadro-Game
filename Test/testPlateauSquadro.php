<?php
require_once("../PlateauSquadro.php");
class TestPlateauSquadro
{
    public function run()
    {
        // Création d'un plateau
        $plateau = new PlateauSquadro();

        // Affichage du plateau initial (devrait être rempli de pièces vides, neutres, blanches et noires)
        echo "Plateau initial :\n";
        echo $plateau;

        // Test de la méthode toJson()
        echo "Plateau en JSON :\n";
        echo $plateau->toJson() . PHP_EOL;

        // Test de la méthode fromJson()
        $jsonPlateau = $plateau->toJson();
        $plateauClone = PlateauSquadro::fromJson($jsonPlateau);
        echo "Plateau restauré depuis le JSON :\n";
        echo $plateauClone;

        // Test de la méthode getPiece() et setPiece()
        echo "Test de getPiece() et setPiece() :\n";
        $piece = $plateau->getPiece(1, 0 );
        echo "Pièce à (1,0) avant modification : " . $piece . PHP_EOL;
        $nouvellePiece = PieceSquadro::initNoirNord();
        $plateau->setPiece($nouvellePiece, 1, 0);
        echo "Pièce à (1,0) après modification : " . $plateau->getPiece(1, 0) . PHP_EOL;

        // Test des coordonnées de destination avec getCoordDestination()
        echo "Test de getCoordDestination() pour une pièce noire au nord :\n";
        $coordDestination = $plateau->getCoordDestination(6, 1);  // Pièce noire au nord
        echo "Destination : (" . $coordDestination[0] . ", " . $coordDestination[1] . ")" . PHP_EOL;

        // Test de la méthode getDestination()
        echo "Test de getDestination() :\n";
        $plateau->setPiece(PieceSquadro::initBlancEst() , 3,1);
        $destinationPiece = $plateau->getDestination(6, 1);  // Pièce noire au nord
        echo "Pièce à la destination : " . $destinationPiece . PHP_EOL;

        // Test des lignes et colonnes jouables
        echo "Lignes jouables : " . implode(", ", $plateau->getLignesJouables()) . PHP_EOL;
        echo "Colonnes jouables : " . implode(", ", $plateau->getColonnesJouables()) . PHP_EOL;

        // Retirer une ligne et une colonne jouables
        $plateau->retireLigneJouable(3);
        $plateau->retireColonneJouable(2);

        echo "Lignes jouables après retrait : " . implode(", ", $plateau->getLignesJouables()) . PHP_EOL;
        echo "Colonnes jouables après retrait : " . implode(", ", $plateau->getColonnesJouables()) . PHP_EOL;
    }
}

// Exécution du test
$test = new TestPlateauSquadro();
$test->run();

?>