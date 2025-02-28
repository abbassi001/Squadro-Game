<?php
/**
 * Gestion des formulaires
 * @author ADAM Abbas Abbas
 * @author Juba Nemri
 */
require_once 'PDOSquadro.php';
require_once 'PartieSquadro.php';
require_once 'env/db.php';

session_start();

// Vérifier si une action est envoyée
if (!isset($_POST['action']) or !isset($_SESSION['player'])) {
    $_SESSION['etat'] = 'Erreur';
    header('Location: index.php');
    exit;
}
$action = $_POST['action'];
$joueur = $_SESSION['player'];
PDOSquadro::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);

switch ($action) {
    case 'creerPartie':
        $partie = new PartieSquadro($joueur);
        PDOSquadro::createPartieSquadro($joueur->getNom(), $partie->toJson());
        $_SESSION['etat'] = 'Home';
        break;
    case 'rejoindrePartie':
        $partieId = $_POST['partieId'];
        $partie = PDOSquadro::getPartieSquadroById($partieId);
        if ($partie) {
            $partie->addJoueur($joueur);
            $partie->gameStatus = 'waitingForPlayer';
            PDOSquadro::addPlayerToPartieSquadro($joueur->getNom(), $partie->toJson(), $partieId);
            PDOSquadro::savePartieSquadro($partie->gameStatus, $partie->toJson(), $partieId);
            $_SESSION['etat'] = 'Home';
        } else {
            $_SESSION['etat'] = 'Erreur';
        }
        break;

    case 'accederPartie':
        $partieId = $_POST['partieId'];
        $_SESSION['partieId'] = $partieId;
        $partie = PDOSquadro::getPartieSquadroById($partieId);
        if ($partie) {
            $_SESSION['partie'] = $partie;
            $_SESSION['etat'] = 'ChoixPièce';
            header('HTTP/1.1 303 See Other');
            header('Location: index.php');
        } else {
            $_SESSION['etat'] = 'Erreur';
        }
        break;

    case 'consulterPartie':
        $partieId = $_POST['partieId'];
        $_SESSION['partieId'] = $partieId;
        $partie = PDOSquadro::getPartieSquadroById($partieId);
        $_SESSION['partie'] = $partie;
        if ($partie->gameStatus == 'finished') {
            $_SESSION['etat'] = 'ConsultePartieVictoire';
        } elseif (($partie->gameStatus == 'waitingForPlayer')) {
            $_SESSION['etat'] = 'ConsultePartieEnCours';
        } else {
            $_SESSION['etat'] = 'Erreur';
        }
        break;
    case 'Exit': 
        session_unset();
        session_destroy() ;
    default:
        $_SESSION['etat'] = 'Erreur';
}

// Redirection vers la page principale
header('Location: index.php');
exit;
?>