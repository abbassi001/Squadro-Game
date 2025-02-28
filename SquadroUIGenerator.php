<?php
/**
 * 
 * Classe SquadroUIGenerator
 * Cette classe met à disposition des méthodes permettant de construire les différentes pages de l'application 
 * @author ADAM Abbas Abbas
 * @author Juba Nemri
 * 
 */
require_once 'PieceSquadroUI.php';
require_once 'PlateauSquadro.php';
require_once 'ActionSquadro.php';
require_once 'JoueurSquadro.php';
require_once 'AbstractUIGenerator.php';

class SquadroUIGenerator extends AbstractUIGenerator
{
    /**
     * Génère le footer commun des pages.
     */

    public static function getDebutHTML(string $title = "Squadro"): string
    {
        return '<!doctype html>
        <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>' . $title . '</title>
                <link rel="stylesheet" href="style.css">
            </head>
            <body>
                <h1 class="squadro">' . $title . '</h1>';
    }
    public static function getFinHTML(): string
    {
        return "</body>\n</html>\n";
    }

    /**
     * Génère l'en-tête HTML de la page.
     */
    private static function genererEntete(string $titre): string
    {
        return "<html><h4 class ='titre'>{$titre}</h4><body>";
    }



    /**
     * Génère une page permettant au joueur actif de jouer une pièce.
     * Les données soumises par ce formulaire incluent les coordonnées de la pièce choisie.
     * L'IA m'a généré un code sans tableau au tout debut ce qui a entrainé une mal formation du plateau 
     * Une version en imposant cette fois-ci l'utilisation des tableau html 
     * Une version avec une nouvelle fonction pour ajouter les vitesse sur les bords
     * 
     */
    public static function genererPageJouerPiece(int $couleurJoueur, JoueurSquadro $joueur,PlateauSquadro $plateau): string
    {

        $html = self::getDebutHTML();
        $html .= self::genererEntete($joueur->getNom()." doit déplacer une de ses pièces");
        $html .= "<main><form method='post' action='FormulaireSquadro.php'>";
        $html .= "<input type='hidden' name='action' value='ChoisirPièce'>";
        $html .= self::getDivPlateau($plateau, $couleurJoueur);
        $html .= "</form></main>";
        $html .= self::getFinHTML();

        return $html;
    }
    /*
     * Génére le plateau 
     */
    public static function getDivPlateau(PlateauSquadro $plateau, int $couleurJoueur): string
    {
        $html = "<table class='plateau-squadro'>";
        // Appeler la fonction pour obtenir les valeurs des cases des bords
        $casesBords = self::casesBords();


        // Crée un tableau de 9x9
        for ($x = 0; $x < 9; $x++) { // Boucle sur les colonnes
            $html .= "<tr>";
            for ($y = 0; $y < 9; $y++) { // Boucle sur les lignes

                // Cas spécial pour la première ligne (ligne 1) et la première colonne (colonne 1)
                if ($x == 0 || $y == 0 || $x == 8 || $y == 8) {
                    // Si c'est la première ligne (ligne 1)
                    if ($y == 0) { // Première colonne
                        $html .= "<td>" . PieceSquadroUI::genererCaseNeutre($casesBords["colonne1"][$x]) . "</td>"; // Utiliser les valeurs de la colonne 1
                    } elseif ($x == 0) { // Première ligne
                        $html .= "<td>" . PieceSquadroUI::genererCaseNeutre($casesBords["ligne1"][$y]) . "</td>"; // Utiliser les valeurs de la ligne 1
                    } elseif ($y == 8) { // Dernière colonne
                        $html .= "<td>" . PieceSquadroUI::genererCaseNeutre($casesBords["colonne9"][$x]) . "</td>"; // Utiliser les valeurs de la colonne 9
                    } elseif ($x == 8) { // Dernière ligne
                        $html .= "<td   >" . PieceSquadroUI::genererCaseNeutre($casesBords["ligne9"][$y]) . "</td>"; // Utiliser les valeurs de la ligne 9
                    }
                } else {
                    // Pour les autres cases internes (hors des bords), récupérer la pièce du plateau
                    $case = $plateau->getPiece($x - 1, $y - 1); // Décale de 1 pour accéder aux cases internes du plateau
                    if (!$case instanceof PieceSquadro) {
                        throw new InvalidArgumentException("Le plateau doit contenir que des pieces squadro");
                    }
                    // Affiche la pièce selon sa couleur
                    $actionSquadroTemp = new ActionSquadro($plateau);
                    $jouable = $actionSquadroTemp->estJouablePiece($x - 1, $y - 1);
                    $direction = $case->getDirection();

                    if ($case->getCouleur() == PieceSquadro::VIDE) {
                        $html .= "<td>" . PieceSquadroUI::genererCaseVide() . "</td>";
                    } elseif ($case->getCouleur() == PieceSquadro::NEUTRE) {
                        $html .= "<td>" . PieceSquadroUI::genererCaseNeutrePlateau() . "</td>";
                    } elseif ($case->getCouleur() == PieceSquadro::BLANC) {
                        $html .= "<td>" . PieceSquadroUI::genererPieceBlanche($x, $y, $couleurJoueur, $direction, $jouable) . "</td>";
                    } elseif ($case->getCouleur() == PieceSquadro::NOIR) {
                        $html .= "<td>" . PieceSquadroUI::genererPieceNoire($x, $y, $couleurJoueur, $direction, $jouable) . "</td>";
                    }
                }
            }
            $html .= "</tr>";
        }
        // Fermeture du tableau et du formulaire
        $html .= "</table>";
        return $html;
    }


    /**
     * Tableau représentant les valeurs des cases bords (première ligne et première colonne)
     * @return array<int|string>[]
     */
    private static function casesBords(): array
    {
        // Tableau représentant les valeurs spécifiques pour la première ligne, première colonne, dernière ligne et dernière colonne
        $cases = [
            // Première ligne
            "ligne1" => ["", "", "1", "3", "2", "3", "1", "", ""],

            // Première colonne
            "colonne9" => ["", "", "3", "1", "2", "1", "3", "", ""],

            // Dernière ligne
            "ligne9" => ["", "", "3", "1", "2", "1", "3", "", ""],

            // Dernière colonne
            "colonne1" => ["", "", "1", "3", "2", "3", "1", "", ""]
        ];

        return $cases;
    }


    /**
     * Génère une page demandant de confirmer le déplacement d'une pièce.
     * Les données reçues incluent les coordonnées de la pièce.
     */
    public static function genererPageConfirmerDeplacement(PlateauSquadro $plateau,  $couleurJoueur, int $x, int $y): string
    {
        $html = self::getDebutHTML();
        $html .= "<div class='plateau-flou'>";
        $html .= self::getDivPlateau($plateau, $couleurJoueur); // Le plateau est flou
        $html .= "</div>";

        $html .= "<div class='modale'>";
        $html .= "<main>";
        $html .= "<p>Confirmez-vous le déplacement de la pièce en position ($x, $y) ?</p>";
        $html .= "<form method='post' action='FormulaireSquadro.php'>";
        $html .= "<input type='hidden' name='action' value='ConfirmationPiece'>";
        $html .= "<input type='hidden' name='x' value='$x'>";
        $html .= "<input type='hidden' name='y' value='$y'>";
        $html .= "<button type='submit' name='confirm' value='yes'>Oui</button>";
        $html .= "<button type='submit' name='confirm' value='no'>Non</button>";
        $html .= "</form>";
        $html .= "</main>";
        $html .= "</div>";

        $html .= self::getFinHTML();
        return $html;
    }

    /**
     * Génère une page affichant le plateau final et un message de victoire.
     */
    public static function genererPageVictoire(string $gagnant, PlateauSquadro $plateau): string
    {
        $html = self::getDebutHTML();
        $text = $gagnant == PieceSquadro::BLANC ? 'Pièces blanches' : 'Pièces Noires';
        $html .= "<main><h4> Bravo les " . htmlspecialchars($text) . " ont remporté la partie !</h4>";
        $html .= self::getDivPlateau($plateau, $gagnant);
        $html .= "<form method='post' action='FormulaireSquadro.php'>";
        $html .= "<input type='hidden' name='action' value='rejouer'>";
        $html .= "<button  type='submit' name='rejouer' >Rejouer</button>";
        $html .= self::getFinHTML();
        return $html;
    }

    /*
     * Génère une page annonçant la détection d'une erreur lors de l'éxécution de l'application
     */
    public static function getErreurHTML(string $message): string
    {
        $debut = self::getDebutHTML($message);
        $fin = self::getFinHTML();

        $html = '
            <h1>Erreur 400: Bad Request</h1>
            <p>$message</p>
            <a href="$urlLien">Retourner à la page d\'accueil</a>  ';

        return $debut . $html . $fin;
    }
}