<?php
/**
 * Classe ActionSquadro
 * Cette classe définit les méthodes gérant les règles du jeu 
 * @author ADAM Abbas Abbas
 * @author Juba Nemri
 * 
 */
require_once 'PlateauSquadro.php';
class ActionSquadro
{
    private $plateau;

    public function __construct($plateau)
    {
        $this->plateau = $plateau;
    }
    // première version non fonctionnelle
    /* public function estJouablePiece(int $x, int $y): bool {
        $piece = $this->plateau->getPiece($x, $y);
        
        // Vérifie si la case contient une pièce et si elle peut se déplacer selon les règles du jeu
        return $piece instanceof PieceSquadro && in_array($x, $this->plateau->getLignesJouables()) && in_array($y, $this->plateau->getColonnesJouables());
    } */
    public function estJouablePiece($x, $y): bool
    {
        /**
         * Vérifie si la pièce située à ($x, $y) peut être jouée.
         * Une pièce est jouable si la case d'arrivée est libre.
         */
        $destination = $this->plateau->getCoordDestination($x, $y);
        [$nx, $ny] = $destination;
        return $nx >= 0 && $nx < 7 && $ny >= 0 && $ny < 7 && $this->plateau->getPiece($nx, $ny)->getCouleur() == pieceSquadro::VIDE;
    }
    // la première version que l'IA m'avait fournie était incorrect;
    // elle a crée des nouvelles méthodes pour gérer les sauts des pièces

    // Deuxième version 
    public function jouePiece($x, $y)
    {
        /**
         * Joue une pièce en suivant les règles :
         * - Déplacement de la pièce
         * - Gestion des sauts et reculs des pièces adverses
         * - Retournement ou sortie si nécessaire
         */
        $piece = $this->plateau->getPiece($x, $y);
        if ($piece == PieceSquadro::initVide() || !$this->estJouablePiece($x, $y)) {
            return;
        }

        $destination = $this->plateau->getCoordDestination($x, $y);
        [$nx, $ny] = $destination;
        $direction = $piece->getDirection();

        // Vérifie les sauts et reculs des pièces adverses
        if ($piece->getCouleur() == PieceSquadro::BLANC) {

            for ($i = min($y, $ny) + 1; $i < max($y, $ny); $i++) {
                $adversaire = $this->plateau->getPiece($x, $i);
                if ($adversaire !== PieceSquadro::initVide() && $adversaire->getCouleur() == PieceSquadro::NOIR) {
                    $this->reculePiece($x, $i);
                }
            }
        } elseif ($piece->getCouleur() == PieceSquadro::NOIR) {
            for ($i = min($x, $nx) + 1; $i < max($x, $nx); $i++) {
                $adversaire = $this->plateau->getPiece($i, $y);
                if ($adversaire != null && $adversaire->getCouleur() == PieceSquadro::BLANC) {
                    $this->reculePiece($i, $y);
                }
            }
        }

        // Déplacement de la pièce
        $this->plateau->setPiece(PieceSquadro::initVide(), $x, $y); // Retire la pièce de la position actuelle
        $this->plateau->setPiece($piece, $nx, $ny); // Place la pièce à la nouvelle position

        // Gestion des retournements ou sorties
        if ($piece->getCouleur() == PieceSquadro::BLANC) {
            if ($ny == 6 && $direction == PieceSquadro::EST) { // Arrive à l'Est
                $piece->inverseDirection();
            } elseif ($ny == 0 && $direction == PieceSquadro::OUEST) { // Retourne à l'Ouest
                $this->sortPiece(PieceSquadro::BLANC, $x); // Pièce sortie
            }
        } elseif ($piece->getCouleur() == PieceSquadro::NOIR) {
            if ($nx == 0 && $direction == PieceSquadro::NORD) { // Arrive au Nord
                $piece->inverseDirection();
            } elseif ($nx == 6 && $direction == PieceSquadro::SUD) { // Retourne au Sud
                $this->sortPiece(PieceSquadro::NOIR, $y); // Pièce sortie
            }
        }
    }

    public function reculePiece($x, $y)
    {
        /**
         * Reculer une pièce adversaire sautée à sa position initiale ou de retournement.
         */
        $piece = $this->plateau->getPiece($x, $y);
        if ($piece == PieceSquadro::initVide()) {
            return;
        }

        $direction = $piece->getDirection();
        $couleur = $piece->getCouleur();

        if ($couleur == PieceSquadro::BLANC) {
            $newY = ($direction == PieceSquadro::EST) ? 0 : 6;
            $this->plateau->setPiece($piece, $x, $newY);
        } elseif ($couleur == PieceSquadro::NOIR) {
            $newX = ($direction == PieceSquadro::NORD) ? 6 : 0;
            $this->plateau->setPiece($piece, $newX, $y);
        }
        $this->plateau->setPiece(PieceSquadro::initVide(), $x, $y);
    }

    // J'ai ajouté manuellement retireLigneJouable et retireColonneJouable 
    public function sortPiece($couleur, $rang)
    {
        /**
         * Retire une pièce du plateau de jeu lorsqu'elle a fini son aller-retour.
         */
        if ($couleur == PieceSquadro::BLANC) {
            $this->plateau->setPiece(PieceSquadro::initVide(), $rang, 0); // Retire la pièce blanche à l'Ouest
            $this->plateau->retireLigneJouable($rang);
        } elseif ($couleur == PieceSquadro::NOIR) {
            $this->plateau->setPiece(PieceSquadro::initVide(), 6, $rang); // Retire la pièce noire au Sud
            $this->plateau->retireColonneJouable($rang);
        }
    }


    // j'ai modifié la méthode remporteVictoire, dans cette version elle verifie uniquement les tailles des tableaux
    // lignes et colonnes jouables 
    public function remporteVictoire($couleur)
    {
        /**
         * Vérifie si une couleur a gagné en fonction de la taille des tableaux
         * `colonneJouable` ou `ligneJouable`.
         */
        if ($couleur == PieceSquadro::BLANC) {
            // Vérifie si la taille des lignes jouables pour les pièces blanches est inférieure ou égale à 4
            $lignesJouables = $this->plateau->getLignesJouables();
            return count($lignesJouables) <= 1;
        } elseif ($couleur == PieceSquadro::NOIR) {
            // Vérifie si la taille des colonnes jouables pour les pièces noires est inférieure ou égale à 4
            $colonnesJouables = $this->plateau->getColonnesJouables();
            return count($colonnesJouables) <= 1;
        }

        // Si aucune condition n'est remplie, retourne false par défaut
        return false;
    }


}

?>