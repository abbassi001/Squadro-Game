<?php
require_once '../SquadroUIGenerator.php';
require_once '../PlateauSquadro.php';

$joueurActif = 'blanc'; // Changez entre 'blanc' et 'noir' pour voir l'effet

$plateau = new PlateauSquadro();
//echo SquadroUIGenerator::genererPageJouerPiece( $joueurActif, $plateau);
echo SquadroUIGenerator::genererPageConfirmerDeplacement($plateau,$joueurActif, 1,0);
//echo SquadroUIGenerator::genererPageVictoire( $joueurActif, $plateau);
?>