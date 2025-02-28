<?php
require_once '../ActionSquadro.php';
require_once '../PlateauSquadro.php';

class TestActionSquadro
{
    public function run()
    {
        // Création d'un plateau et d'une actionSquadro
        $plateau = new PlateauSquadro();
        $action = new ActionSquadro($plateau);

        // Test de la méthode estJouablePiece()
        echo "Test de estJouablePiece() :\n";

        // Supposons qu'il y a une pièce en (1,0) et que sa destination est (2,0)
        $jouable = $action->estJouablePiece(1, 0);
        echo "La pièce est-elle jouable ? " . ($jouable ? "Oui" : "Non") . PHP_EOL;

        // Test de la méthode jouePiece()
        echo "Test de jouePiece() :\n";
        echo $plateau->getPiece(3, 1 ) . "\n";
        // Jouons une pièce en (1,0) et déplaçons-la
        $action->jouePiece(6, 1);
        echo "Vérifions si la pièce est déplacée" . "\n";
        echo "(3,1)" . $plateau->getPiece(3, 1) . "\n";
        echo "(6,1)" . $plateau->getPiece(6, 1) . "\n";


        // Test de la méthode reculePiece() pour une pièce adversaire
        echo "Test de reculePiece() :\n";
        // Place une pièce adverse en (3, 1) et vérifie qu'elle recule
        $action->reculePiece(3, 1);
        echo "(3,1)" . $plateau->getPiece(3, 1) . "\n";
        echo "(6,1)" . $plateau->getPiece(6, 1) . "\n";
        echo "\n \n ";
        echo "------------------------------------------- \n";
        $plateau->setPiece(PieceSquadro::initNoirNord(), 2, 2);
        $plateau->setPiece(PieceSquadro::initVide(), 6, 2);
        echo "(2,3)" . $plateau->getPiece(2, 3) . "\n";
        echo "(2,2)" . $plateau->getPiece(2, 2) . "\n";
        echo "(2,0)" . $plateau->getPiece(2, 0) . "\n";
        echo "(6,2)" . $plateau->getPiece(6, 2) . "\n";
        echo " joue piéce (1,0) \n \n ";
        $action->jouePiece(2, 0);
        echo "(2,3)" . $plateau->getPiece(2, 3) . "\n";
        echo "(2,2)" . $plateau->getPiece(2, 2) . "\n";
        echo "(2,0)" . $plateau->getPiece(2, 0) . "\n";
        echo "(6,2)" . $plateau->getPiece(6, 2) . "\n";

        echo "\n \n ";
        echo "-----------------------------------------\n";
        echo " Test de la méthode victoire"; 
        // Place des pièces blanches sorties
        $plateau->retireLigneJouable(1);
        $plateau->retireLigneJouable(2);
        $plateau->retireLigneJouable(3);
        $plateau->retireLigneJouable(4);
        $victoire = $action->remporteVictoire(PieceSquadro::BLANC);
        echo "Le joueur blanc a-t-il gagné ? " . ($victoire ? "Oui" : "Non") . PHP_EOL;
        
    }
}

// Exécution du test
$test = new TestActionSquadro();
$test->run();
?>