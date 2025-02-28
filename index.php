<?php
require_once 'PDOSquadro.php';
require_once 'env/db.php';
require_once 'PartieSquadro.php';
require_once 'JoueurSquadro.php';
require_once 'SquadroUIGenerator.php';
require_once 'PlateauSquadro.php';
require_once 'PieceSquadro.php';

session_start();

// Si l'état ou le joueur n'est pas défini, rediriger vers la page de login
if (!isset($_SESSION['etat']) && !$_SESSION['player']) {
    header('HTTP/1.1 303 See Other');
    header('Location: login.php');
    exit();
}

// Si une partie est active, la recharger depuis la BD
if (isset($_SESSION['partieId'])) {
    PDOSquadro::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);
    $partie = PDOSquadro::getPartieSquadroById($_SESSION['partieId']);

    var_dump($partie);
    die();
    $_SESSION['partie'] = $partie;
    // Recréer le plateau à partir du JSON stocké dans la partie
    $partieJson = json_decode($partie->toJson(), true); // Décodage en tableau associatif
    $plateauJson = $partieJson['plateau'];
    $_SESSION['plateau'] = PlateauSquadro::fromJson($plateauJson);

    // Créer l'instance ActionSquadro basée sur le plateau
    $_SESSION['actionSquadro'] = new ActionSquadro($_SESSION['plateau']);
    $joueur = $_SESSION['player'];

    // Déterminer le joueur actif et la couleur à utiliser
    $joueurs = $partie->getJoueurs();
    $indexActif = $partie->joueurActif; // 0 ou 1
    $joueurActif = $partie->getJoueurActif();
    // Par convention, le premier joueur = BLANC, le second = NOIR
    $_SESSION['couleurJoueur'] = ($indexActif == PartieSquadro::PLAYER_ONE)
        ? PieceSquadro::BLANC
        : PieceSquadro::NOIR;

    // Si le joueur connecté n'est pas actif, ses pièces seront affichées en mode neutre
    if ($joueurActif->getNom() != $joueur->getNom())
        $_SESSION['couleurJoueur'] = PieceSquadro::NEUTRE;
}

// Récupération des données de session
$etat = $_SESSION['etat'];

// Affichage de la bonne page selon l'état du jeu
switch ($etat) {
    case 'Home':
        header('HTTP/1.1 303 See Other');
        header('Location: home.php');
        break;
    case 'ChoixPièce':
        echo SquadroUIGenerator::genererPageJouerPiece($_SESSION['couleurJoueur'], $joueurActif, $_SESSION['plateau']);
        break;
    case 'ConfirmationPiece':
        echo SquadroUIGenerator::genererPageConfirmerDeplacement($_SESSION['plateau'], $_SESSION['couleurJoueur'], $_SESSION['x'], $_SESSION['y']);
        break;
    case 'Victoire':
        echo SquadroUIGenerator::genererPageVictoire($_SESSION['couleurJoueur'], $joueurActif, $_SESSION['plateau']);
        break;
    case 'ConsultePartieEnCours':
        echo SquadroUIGenerator::genererPageJouerPiece($_SESSION['couleurJoueur'], $joueurActif, $_SESSION['plateau']);
        break;
    case 'ConsultePartieVictoire':
        echo SquadroUIGenerator::genererPageVictoire($_SESSION['couleurJoueur'], $joueurActif, $_SESSION['plateau']);
        break;
    default:
        echo SquadroUIGenerator::getErreurHTML("État inconnu : $etat");
}
?>