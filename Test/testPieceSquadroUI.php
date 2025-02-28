<?php
require_once '../PieceSquadroUI.php';

$joueurActif = 'blanc'; // Changez entre 'blanc' et 'noir' pour voir l'effet
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test PieceSquadroUI</title>
    <style>
        .plateau {
            display: grid;
            grid-template-columns: repeat(5, 50px);
            gap: 5px;
            margin: 20px;
        }
        .case {
            width: 50px;
            height: 50px;
            font-size: 24px;
            text-align: center;
            cursor: pointer;
            border: 1px solid #000;
        }
        .vide { background-color: #ddd; }
        .neutre { background-color: #bbb; }
        .blanc { background-color: white; }
        .noir { background-color: black; color: white; }
    </style>
</head>
<body>

<h2>Test du rendu des pièces</h2>

<div class="plateau">
    <?php
    echo PieceSquadroUI::genererCaseVide();
    echo PieceSquadroUI::genererCaseNeutre();
    echo PieceSquadroUI::genererPieceBlanche(0, 1, $joueurActif, PieceSquadro::EST);
    echo PieceSquadroUI::genererPieceNoire(1, 2, $joueurActif, PieceSquadro::NORD);
    echo PieceSquadroUI::genererCaseVide();
    ?>  
</div>

</body>
</html>
