<?php
/**
 * 
 * Classe PlateauSquadro
 * Cette classe met à disposition des méthodes permettant de construire les différentes pages de l'application 
 * @author ADAM Abbas Abbas
 * @author Juba Nemri
 * 
 */
require_once 'PieceSquadro.php';
require_once 'ArrayPieceSquadro.php';

class PlateauSquadro
{
    // Constantes
    public const BLANC_V_ALLER = [0, 1, 3, 2, 3, 1, 0];
    public const BLANC_V_RETOUR = [0, 3, 1, 2, 1, 3, 0];
    public const NOIR_V_ALLER = [0, 3, 1, 2, 1, 3, 0];
    public const NOIR_V_RETOUR = [0, 1, 3, 2, 3, 1, 0];

    private array $plateau;
    private array $lignesJouables = [1, 2, 3, 4, 5];
    private array $colonnesJouables = [1, 2, 3, 4, 5];

    // Constructeur
    public function __construct()
    {
        // Initialisation d'un plateau vide de 7x7 cases
        for ($i = 0; $i < 7; $i++) {
            $this->plateau[$i] = new ArrayPieceSquadro();
        }
        $this->initCasesVides();
        $this->initCasesNeutres();
        $this->initCasesBlanches();
        $this->initCasesNoires();
    }

    // dans cette version j'ai fait en sorte que les lignes  soient une instance de ArrayPieceSquadro
    private function initCasesNeutres(): void
    {
        $this->plateau[0][0] = PieceSquadro::initNeutre();
        $this->plateau[6][6] = PieceSquadro::initNeutre();
        $this->plateau[0][6] = PieceSquadro::initNeutre();
        $this->plateau[6][0] = PieceSquadro::initNeutre();
    }

    // Initialisation des cases vides (intérieur du plateau)
    private function initCasesVides(): void
    {
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $this->plateau[$i]->add(PieceSquadro::initVide());
            }
        }

    }

    private function initCasesNoires(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->plateau[6][$i] = PieceSquadro::initNoirNord();
        }
    }
    private function initCasesBlanches(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->plateau[$i][0] = PieceSquadro::initBlancEst();
        }
    }


    public function getPlateau(): array
    {
        return $this->plateau;
    }

    public function getPiece(int $x, int $y): PieceSquadro
    {
        if (isset($this->plateau[$x][$y])) {
            // Accède à la première pièce en utilisant la syntaxe de tableau
            return $this->plateau[$x][$y];
        }
        throw new OutOfBoundsException("Coordonnées invalides : ($x, $y)");
    }
    public function setPiece(PieceSquadro $piece, int $x, int $y): void
    {
        $this->plateau[$x][$y] = $piece;
    }

    public function getLignesJouables(): array
    {
        return $this->lignesJouables;
    }

    public function getColonnesJouables(): array
    {
        return $this->colonnesJouables;
    }

    public function retireLigneJouable(int $index): void
    {
        $this->lignesJouables = array_diff($this->lignesJouables, [$index]);
    }

    public function retireColonneJouable(int $index): void
    {
        $this->colonnesJouables = array_diff($this->colonnesJouables, [$index]);
    }

    public function getCoordDestination(int $x, int $y): array
    {
        // Récupération de la pièce
        $piece = $this->getPiece($x, $y);
        $vitesse = 0;
        //Chaque pièce a une vitesse variable selon sa position actuelle et sa couleur 
        if ($piece->getCouleur() === PieceSquadro::BLANC) {
            $vitesse = $piece->getDirection() === PieceSquadro::EST
                ? self::BLANC_V_ALLER[$x]
                : self::BLANC_V_RETOUR[$x];
        } elseif ($piece->getCouleur() === PieceSquadro::NOIR) {
            $vitesse = $piece->getDirection() === PieceSquadro::NORD
                ? self::NOIR_V_ALLER[$y]
                : self::NOIR_V_RETOUR[$y];
        }

        $nouveauX = $x;
        $nouveauY = $y;

        // Les nouvelles coordonnées sont calculées selon la direction et la vitesse obtenue :
        switch ($piece->getDirection()) {
            case PieceSquadro::NORD:
                $nouveauX -= $vitesse;
                break;
            case PieceSquadro::SUD:
                $nouveauX += $vitesse;
                break;
            case PieceSquadro::EST:
                $nouveauY += $vitesse;
                break;
            case PieceSquadro::OUEST:
                $nouveauY -= $vitesse;
                break;
        }

        return [$nouveauX, $nouveauY];
    }

    public function getDestination(int $x, int $y): PieceSquadro
    {
        [$nouveauX, $nouveauY] = $this->getCoordDestination($x, $y);
        return $this->getPiece($nouveauX, $nouveauY);
    }
    /* première méthode que l'IA avait implémenté 
    elle renvoie un format Json sous cette forme :[
        {},
        {},
        {},
        {},
        {},
        {},
        {}
    ]
    public function toJson(): string
    {
        return json_encode($this->plateau, JSON_PRETTY_PRINT);
    } */
    // correction 
    public function toJson(): string
    {
        $plateauSerializable = [];

        foreach ($this->plateau as $x => $row) {

            $plateauSerializable[$x] = json_decode($row->toJson(), true); // Appelle toJson de PieceSquadro

        }

        return json_encode($plateauSerializable, JSON_PRETTY_PRINT);
    }

    public static function fromJson(string $json): PlateauSquadro
    {
        $data = json_decode($json, true);
        $plateau = new self();
        foreach ($data as $x => $ligne) {
            foreach ($ligne as $y => $pieceJson) {
                $plateau->setPiece(PieceSquadro::fromJson(json_encode($pieceJson)), (int) $x, (int) $y);
            }
        }
        return $plateau;
    }
    public function __toString(): string
    {
        $output = "";
        foreach ($this->plateau as $ligne) {
            $output .= $ligne . PHP_EOL;
        }
        return $output;
    }
}
?>