<?php
/**
 * 
 * Classe PieceSquadroUI
 * Cette classe met à disposition des méthodes permettant de générer le code HTML de tous les boutons nécessaires à la construction de l'interface de jeu  
 * @author ADAM Abbas Abbas
 * @author Juba Nemri
 * 
 */
require_once 'PieceSquadro.php';
class PieceSquadroUI
{
    /**
     * Génère le HTML pour une case vide.
     */
    public static function genererCaseVide(): string
    {
        return '<button class="case vide" disabled></button>';
    }

    /**
     * Génère le HTML pour une case neutre.
     */
    public static function genererCaseNeutre(string $arg = ""): string
    {
        return '<button  class="case neutre" disabled>' . $arg . '</button>';
    }
    public static function genererCaseNeutrePlateau(string $arg = ""): string
    {
        return '<button  class="case neutrePlateau" disabled>' . $arg . '</button>';
    }


    // j'ai ajouté un paramètre joueurActif pour désigner le joueur qui a la main 
    /**
     * Génère le HTML pour une pièce blanche.
     *
     * @param $x Coordonnée X de la pièce.
     * @param $y Coordonnée Y de la pièce.
     * @param $couleurJoueur le joueur 
     * @param $direction direction de la pièce
     */
    public static function genererPieceBlanche( $x,  $y,  $couleurJoueur,  $direction,  $jouable):string 
    {   
        $disabled = (!$jouable || $couleurJoueur != PieceSquadro::BLANC) ? 'disabled' : '';
        $icon = ($direction == PieceSquadro::EST) ? "➡" : "⬅";
        return "<button type ='submit' class='case blanc' name='piece' value='{$x}-{$y}' {$disabled}>{$icon}</button>";
    }


    /**
     * Génère le HTML pour une pièce noire.
     *
     * @param $x Coordonnée X de la pièce.
     * @param $y Coordonnée Y de la pièce.
     * @param $couleurJoueur le joueur 
     * @param $direction direction de la pièce
     */
    public static function genererPieceNoire( $x,   $y,  $couleurJoueur,  $direction,  $jouable)   :string
    {
        $disabled = (!$jouable || $couleurJoueur   != PieceSquadro::NOIR) ? 'disabled' : '';
        $icon = ($direction == PieceSquadro::NORD) ? "⬆" : "⬇";
        return "<button type= 'submit' class='case noir' name='piece' value='{$x}-{$y}' {$disabled}>{$icon}</button>";
    }

}

