<?php
require_once 'PDOSquadro.php';
require_once 'env/db.php';

session_start();

// Vérification de la connexion du joueur
if (!isset($_SESSION['player'])) {
  header('HTTP/1.1 303 See Other');
  header('Location: login.php');
  exit();
}

// Initialisation de la connexion à la base de données
PDOSquadro::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);

// Récupération du joueur connecté
$player = $_SESSION['player'];


// Récupération de toutes les parties
$allParties = PDOSquadro::getAllPartieSquadro();

// Parties en attente d'un second joueur
$partiesAttente = array_filter($allParties, function ($partie) use ($player) {
  return ($partie->gameStatus == 'initialized') && ($player->getNom() != $partie->getNomJoueurActif($player->getNom()));
});

// Parties non terminées auxquelles le joueur participe
$partiesNonTerminees = array_filter($allParties, function ($partie) use ($player) {
  return ($partie->gameStatus == 'waitingForPlayer') || ($player->getNom() == $partie->getNomJoueurActif($player->getNom()));
});

// Parties terminées auxquelles le joueur partici on null in /var/www/html/squadro2025/home.php:93\nStack trace:\n#0 /var/www/html/squadro2025/home.php(133): getPageHome()\n#1 {main}pe

$partiesEnCours = PDOSquadro::getAllPartieSquadroByPlayerName($player->getNom());
$partiesTerminees = array_filter($partiesEnCours, function ($partie) {
  return $partie->gameStatus == 'finished';
});

// Fonction pour générer la page Home
function getPageHome(array $partiesAttente, array $partiesNonTerminees, array $partiesTerminees): string
{
  $html = '<!DOCTYPE html>
<html class="no-js" lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="Author" content="Dominique Fournier" />
    <title>Salle de jeux Squadro</title>
  </head>
  <body>
    <div class="squadro"></div>
    <h1>Salon Squadro</h1>
    <h2>Bienvenue, ' . htmlspecialchars($_SESSION['player']->getNom()) . '</h2>

    <h3>Nouvelle partie</h3>
    <form action="FormulaireHome.php" method="post">
      <input type="hidden" name="action" value="creerPartie">
      <input type="submit" value="Créer une nouvelle partie">
    </form>

    <h3>Parties en attente d\'un second joueur</h3>';

  if (empty($partiesAttente)) {
    $html .= '<p>Aucune partie disponible.</p>';
  } else {
    $html .= '<ul>';
    foreach ($partiesAttente as $partie) {
      $html .= '<li>
                <form action="FormulaireHome.php" method="post">
                  <input type="hidden" name="action" value="rejoindrePartie">
                  <input type="hidden" name="partieId" value="' . $partie->getPartieId() . '">
                  <input type="submit" value="Rejoindre la partie ' . $partie->getPartieId() . ' de ' . $partie->getJoueurs()[PartieSquadro::PLAYER_ONE]->getNom() . '">
                </form>
              </li>';
    }
    $html .= '</ul>';
  }

  $html .= '<h3>Parties non terminées</h3>';
  if (empty($partiesNonTerminees)) {
    $html .= '<p>Aucune partie non terminée.</p>';
  } else {
    $html .= '<ul>';

    foreach ($partiesNonTerminees as $partie) {
      $html .= '<li>
                <form action="FormulaireHome.php" method="post">';
      if (($_SESSION['player']->getNom() == $partie->getJoueurs()[PartieSquadro::PLAYER_ONE]->getNom()) || ($_SESSION['player']->getNom() == $partie->getJoueurs()[PartieSquadro::PLAYER_TWO]->getNom())) {
        $html .=
        '<input type="hidden" name="action" value="accederPartie">
        <input type="hidden" name="partieId" value="' . $partie->getPartieId() . '">';
        $html .= ' <input type="submit" value="Accéder à la partie ' . $partie->getPartieId() . ' de ' . $partie->getJoueurs()[PartieSquadro::PLAYER_ONE]->getNom();
        $partie->getJoueurs()[PartieSquadro::PLAYER_TWO] ? $html .= '-' . $partie->getJoueurs()[PartieSquadro::PLAYER_TWO]->getNom() . '">' : $html .= '">';
      } else {
        $html .=
        '<input type="hidden" name="action" value="consulterPartie">
        <input type="hidden" name="partieId" value="' . $partie->getPartieId() . '">';
        $html .= ' <input type="submit" value="Consulter  la partie ' . $partie->getPartieId() . ' de ' . $partie->getJoueurs()[PartieSquadro::PLAYER_ONE]->getNom() . '-' . $partie->getJoueurs()[PartieSquadro::PLAYER_TWO]->getNom() . '">';
      }
      $html .= ' </form>
              </li>';

    }
    $html .= '</ul>';
  }

  $html .= '<h3>Parties terminées</h3>';
  if (empty($partiesTerminees)) {
    $html .= '<p>Aucune partie terminée.</p>';
  } else {
    $html .= '<ul>';

    foreach ($partiesTerminees as $partie) {
      $html .= '<li>
                <form action="FormulaireHome.php" method="post">
                  <input type="hidden" name="action" value="consulterPartie">
                  <input type="hidden" name="partieId" value="' . $partie->getPartieId() . '">
                  <input type="submit" value="Consulter la partie ' . $partie->getPartieId() . ' de ' . $partie->getJoueurs()[PartieSquadro::PLAYER_ONE]->getNom() . '-' . $partie->getJoueurs()[PartieSquadro::PLAYER_TWO]->getNom() . '">
                </form>
              </li>';
    }
    $html .= '</ul>';
  }

  $html .= '<h3>Quitter la session</h3>
    <form action="FormulaireHome.php" method="post">
      <input type="hidden" name="action" value="Exit">
      <input type="submit" value="Déconnexion">
    </form>
  </body>
</html>';

  return $html;
}

// Affichage de la page Home
echo getPageHome($partiesAttente, $partiesNonTerminees, $partiesTerminees);