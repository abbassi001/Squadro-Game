<?php
/**
 * 
 * Gestion des formulaires
 * @author ADAM Abbas Abbas
 * @author Juba Nemri
 * 
 */
require_once 'PartieSquadro.php';
require_once 'PlateauSquadro.php';
require_once 'ActionSquadro.php';
require_once 'PDOSquadro.php';
require_once 'env/db.php';
session_start();

// Vérifier si une action est envoyée
if (!isset($_POST['action']) or !isset($_SESSION['plateau'])) {
    $_SESSION['etat'] = 'Erreur';
    header('Location: index.php');
    exit;
}

PDOSquadro::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);
$action = $_POST['action'];
$partie = $_SESSION['partie'];
$plateau = $_SESSION['plateau'];
$couleurJoueur = $_SESSION['couleurJoueur'];
$actionSquadro = $_SESSION['actionSquadro'];

switch ($action) {
    case 'ChoisirPièce':
        if (!isset($_POST['piece'])) {
            $_SESSION['etat'] = 'Erreur';
            break;
        }
        $_SESSION['piece'] = $_POST['piece'];
        list($_SESSION['x'], $_SESSION['y']) = explode('-', $_SESSION['piece']);
        $_SESSION['etat'] = 'ConfirmationPiece';
        break;

    case 'ConfirmationPiece':
        // Vérification que la réponse est bien envoyée
        if (!isset($_POST['confirm'])) {
            $_SESSION['etat'] = 'Erreur';
            header('Location: index.php');
            exit;
        }
        $confirm = $_POST['confirm'];
        if ($confirm == 'yes') {
            $x = $_SESSION['x'];
            $y = $_SESSION['y'];
           
            // Déplacement de la pièce sur le plateau
            if ($actionSquadro->estJouablePiece($x - 1, $y - 1)) {
                $actionSquadro->jouePiece($x - 1, $y - 1);
                
                $partieJson = json_decode($partie->toJson(), true);
                // Remplacer la partie plateau par sa représentation JSON sous forme de tableau
                $partieJson['plateau'] = $plateau->toJson();
                // Recréer l'objet PartieSquadro à partir du JSON mis à jour
                $partie = PartieSquadro::fromJson(json_encode($partieJson, JSON_PRETTY_PRINT));
                $_SESSION['partie'] = $partie;

                // Vérification de la victoire
                if ($actionSquadro->remporteVictoire($couleurJoueur)) {
                    $partieJson = json_decode($partie->toJson(), true);
                    // Remplacer la partie plateau par sa représentation JSON sous forme de tableau
                    $partieJson['plateau'] = $plateau->toJson();
                    // Recréer l'objet PartieSquadro à partir du JSON mis à jour
                    $partie = PartieSquadro::fromJson(json_encode($partieJson, JSON_PRETTY_PRINT));
                    $partie->gameStatus = 'finished';
                    $_SESSION['partie'] = $partie;
                    PDOSquadro::savePartieSquadro('finished', $partie->toJson(), $partie->getPartieId());
                    $_SESSION['etat'] = 'Victoire';
                } else {
                    error_log(print_r($actionSquadro->remporteVictoire($couleurJoueur), true));
                    // Changer le joueur actif dans l'objet PartieSquadro
                    $partie->joueurActif = ($partie->joueurActif == PartieSquadro::PLAYER_ONE)
                        ? PartieSquadro::PLAYER_TWO
                        : PartieSquadro::PLAYER_ONE;
                    PDOSquadro::savePartieSquadro('initialized', $partie->toJson(), $partie->getPartieId());
                    $_SESSION['etat'] = 'ChoixPièce';
                }
            } else {
                $_SESSION['etat'] = 'Erreur';
            }
        } elseif ($confirm == 'no') {
            // Si l'utilisateur annule, on revient simplement au choix de pièce
            unset($_SESSION['x'], $_SESSION['y']);
            $_SESSION['etat'] = 'ChoixPièce';
        } else {
            $_SESSION['etat'] = 'Erreur';
        }
        break;
    case 'rejouer':
        $_SESSION['etat'] = 'Home';
        break;

    default:
        $_SESSION['etat'] = 'Erreur';
}

// Redirection vers la page principale
header('Location: index.php');
exit;
?>