<?php
/**
 * Classe PieceSquadro
 * Cette classe gère à la fois les pièces du jeu et les emplacements du plateau
 * @author ADAM Abbas Abbas
 * @author Juba Nemri
 * 
 *  */
class PieceSquadro
{
    // Constantes
    public const BLANC = 0;
    public const NOIR = 1;
    public const VIDE = -1;
    public const NEUTRE = -2;

    public const NORD = 0;
    public const EST = 1;
    public const SUD = 2;
    public const OUEST = 3;

    // Attributs
    protected int $couleur;
    protected int $direction;

    // Constructeur
    public function __construct(int $couleur, int $direction)
    {
        $this->couleur = $couleur;
        $this->direction = $direction;
    }

    // Getter pour la couleur
    public function getCouleur(): int
    {
        return $this->couleur;
    }

    // Getter pour la direction
    public function getDirection(): int
    {
        return $this->direction;
    }

    // Inverse la direction
    public function inverseDirection(): void
    {
        switch ($this->direction) {
            case self::NORD:
                $this->direction = self::SUD;
                break;
            case self::SUD:
                $this->direction = self::NORD;
                break;
            case self::EST:
                $this->direction = self::OUEST;
                break;
            case self::OUEST:
                $this->direction = self::EST;
                break;
        }
    }

    // Redéfinition de la méthode __toString
    public function __toString(): string
    {
        // Mappage des valeurs numériques aux noms des constantes pour la couleur
        $couleurs = [
            self::BLANC => 'BLANC',
            self::NOIR => 'NOIR',
            self::VIDE => 'VIDE',
            self::NEUTRE => 'NEUTRE',
        ];

        // Mappage des valeurs numériques aux noms des constantes pour la direction
        $directions = [
            self::NORD => 'NORD',
            self::EST => 'EST',
            self::SUD => 'SUD',
            self::OUEST => 'OUEST',
        ];

        // Obtenir les noms correspondants aux valeurs de $this->couleur et $this->direction
        $couleur = $couleurs[$this->couleur] ?? 'INCONNUE';
        $direction = $directions[$this->direction] ?? 'INCONNUE';

        return "PieceSquadro { couleur: {$couleur}, direction: {$direction} }";
    }


    // Méthodes d'initialisation pour chaque type de pièce
    public static function initVide(): PieceSquadro
    {
        return new PieceSquadro(self::VIDE, self::NEUTRE);
    }

    public static function initNeutre(): PieceSquadro
    {
        return new PieceSquadro(self::NEUTRE, self::NEUTRE);
    }

    public static function initNoirNord(): PieceSquadro
    {
        return new PieceSquadro(self::NOIR, self::NORD);
    }

    public static function initNoirSud(): PieceSquadro
    {
        return new PieceSquadro(self::NOIR, self::SUD);
    }

    public static function initBlancEst(): PieceSquadro
    {
        return new PieceSquadro(self::BLANC, self::EST);
    }

    public static function initBlancOuest(): PieceSquadro
    {
        return new PieceSquadro(self::BLANC, self::OUEST);
    }

    // Convertit l'objet en JSON
    public function toJson(): string
    {
        // Mappage des valeurs numériques aux noms symboliques
        $couleurs = [
            self::BLANC => 'BLANC',
            self::NOIR => 'NOIR',
            self::VIDE => 'VIDE',
            self::NEUTRE => 'NEUTRE',
        ];

        $directions = [
            self::NORD => 'NORD',
            self::EST => 'EST',
            self::SUD => 'SUD',
            self::OUEST => 'OUEST',
        ];

        // Obtenir les noms correspondants
        $couleur = $couleurs[$this->couleur] ?? 'INCONNUE';
        $direction = $directions[$this->direction] ?? 'INCONNUE';

        // Retourner un JSON utilisant les noms symboliques
        return json_encode([
            'couleur' => $couleur,
            'direction' => $direction
        ], JSON_PRETTY_PRINT);
    }

    // Crée une instance à partir d'une chaîne JSON
    public static function fromJson(string $json): PieceSquadro
    {
        $data = json_decode($json, true);
        // Mappage des noms symboliques vers les valeurs numériques
        $couleurs = [
            'BLANC' => self::BLANC,
            'NOIR' => self::NOIR,
            'VIDE' => self::VIDE,
            'NEUTRE' => self::NEUTRE,
        ];

        $directions = [
            'NORD' => self::NORD,
            'EST' => self::EST,
            'SUD' => self::SUD,
            'OUEST' => self::OUEST,
        ];

        // Récupérer les valeurs numériques correspondantes
        $couleur = $couleurs[$data['couleur']] ?? self::VIDE; // Par défaut: VIDE
        $direction = $directions[$data['direction']] ?? self::NORD; // Par défaut: NORD

        // Créer une nouvelle instance avec les valeurs numériques
        return new PieceSquadro($couleur, $direction);
    }


}