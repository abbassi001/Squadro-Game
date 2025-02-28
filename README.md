# 🏆 Squadro - Jeu en PHP
Squadro est une implémentation du jeu de plateau Squadro en PHP, jouable sur un navigateur en local.
# 📌 Fonctionnalités
- Affichage dynamique du plateau
- Gestion des déplacements des pièces
- Respect des règles du jeu Squadro
- Système de session pour suivre l’état de la partie
  
# 🚀 Installation & Exécution
1. Cloner le projet
```bash
git clone https://www-apps.univ-lehavre.fr/forge/aa243758/squadro2025.git
cd squadro
```

2. Lancer un serveur PHP local
```bash
php -S localhost:8000
```
3. Jouer sur le navigateur
   
   Ouvrir http://localhost:8000

# 🛠 Technologie utilisée
PHP (avec sessions pour gérer l'état du jeu)
HTML/CSS (interface simple et interactive)

## Implémentation de la classe PieceSquadro
Je souhaiterais créer une application web qui permet à deux joueurs de jouer à Squadro.
Peux-tu implémenter la classe qui initialise une pièce Squadro (insertion du schéma UML en pièce jointe) ?

### Observation :
L'IA a généré un script qui initialise les pièces vides et neutres avec des paramètres null, ce qui nous a amenés à ajouter un ? dans le constructeur.

Correction :
initVide(VIDE, NEUTRE)
initNeutre(NEUTRE, NEUTRE);
Test :
Demander à l'IA un script en PHP qui teste les méthodes de la classe.

Pour tester directement, on peut utiliser aussi ce code :
$piece = new PieceSquadro(PieceSquadro::BLANC, PieceSquadro::NORD);

// Test de la méthode inverseDirection
```php
echo "Direction avant inversion: " . $piece->getDirection() . PHP_EOL;
$piece->inverseDirection();
echo "Direction après inversion: " . $piece->getDirection() . PHP_EOL;
```
// Test de la méthode toJson
$json = $piece->toJson();
echo "Représentation JSON: " . $json . PHP_EOL;

// Test de la méthode fromJson
```php
$newPiece = PieceSquadro::fromJson($json);
echo "Nouvelle pièce - Couleur: " . $newPiece->getCouleur() . ", Direction: " . $newPiece->getDirection() . PHP_EOL;
```
--------------------------------------------------------------------------
## Implémentation de la classe ArrayPieceSquadro
Les fonctions que l'on implémente de l'interface Countable :

count()
Les fonctions que l'on implémente de l'interface ArrayAccess :

offsetExists($offset): bool
offsetGet($offset): mixed
offsetSet($offset, $value): void
offsetUnset($offset): void

ChatGPT m'a donné ce code qui renvoie un tableau.
La méthode toJson doit retourner une chaîne JSON, mais elle retourne actuellement un tableau JSON encodé.

`
// Créer un tableau depuis une chaîne JSON
// public static function fromJson(string $json): ArrayPieceSquadro {
//     $array = json_decode($json, true);
//     $instance = new self();
//     foreach ($array as $pieceData) {
//         $instance->add(new PieceSquadro($pieceData['couleur'], $pieceData['direction']));
//     }
//     return $instance;
// }
`

### Remarque :
La méthode fromJson doit correctement ajouter les pièces à la collection.

Solution obtenue :
```php
public static function fromJson(string $json): self {
    $array = json_decode($json, true);
    $instance = new self();
    foreach ($array as $pieceData) {
        $instance->add(PieceSquadro::fromJson(json_encode($pieceData)));
    }
    return $instance;
} 
```

### Remarque :
L'IA générative se trompe en utilisant ses propres variables créées dans d'autres classes.

--------------------------------------------------------------------------
## Modification des méthodes d'écritures 
J'ai ajouté quelques spécificités aux méthodes __toString(), toJson(), et fromJson() pour obtenir un résultat plus lisible, c'est-à-dire :

Exemple :
`
Initialisation d'une pièce neutre :
{
    "couleur": "NEUTRE",
    "direction": "INCONNUE"
}
Initialisation d'une pièce noire au nord :
{
    "couleur": "NOIR",
    "direction": "NORD"
}
`

## Plateau Squadro 
J'ai demandé à l'IA un script qui teste l'intégralité de la classe PlateauSquadro.

## Observation 
Après l'analyse du résultat, j'ai constaté que la méthode toJson() que l'IA avait produite était incorrecte. J'ai reformulé ma question en précisant le type de résultat attendu et en rappelant à l'IA que j'avais déjà implémenté une méthode dans la classe PieceSquadro qui effectue la conversion.

Ancienne méthode 
```php
 public function toJson(): string
    {
        return json_encode($this->plateau, JSON_PRETTY_PRINT);
    }
```
Résultat 
`
[
        {},
        {},
        {},
        {},
        {},
        {},
        {}
]
`
Nouvelle méthode
```php
 public function toJson(): string
    {
        $plateauSerializable = [];

        foreach ($this->plateau as $x => $row) {
            foreach ($row as $y => $piece) {
                $plateauSerializable[$x][$y] = json_decode($piece->toJson(), true); // Appelle toJson de PieceSquadro
            }
        }

        return json_encode($plateauSerializable, JSON_PRETTY_PRINT);
    }
```
résultat 
Plateau en JSON :
`
[
    [
        {
            "couleur": "NEUTRE",
            "direction": "INCONNUE"
        },
    ]
]    
`

# Action Squadro 
## Méthode estPieceJouable 
La première version que l'IA m'avait fournie :
```php
 public function estJouablePiece(int $x, int $y): bool {
        $piece = $this->plateau->getPiece($x, $y);
        
        // Vérifie si la case contient une pièce et si elle peut se déplacer selon les règles du jeu
        return $piece instanceof PieceSquadro && in_array($x, $this->plateau->getLignesJouables()) && in_array($y, $this->plateau->getColonnesJouables());
    }
```
Le code ne répondait pas aux règles du jeu.
J'ai dû reformuler ma question en lui rappelant cette fois-ci les règles spécifiques du jeu et en lui demandant de traiter chaque cas, c'est-à-dire le cas des pièces en horizontal et des pièces en vertical.
```php 
 public function estJouablePiece($x, $y) {
        $piece = $this->plateau->getPiece($x, $y);
        if ($piece === null) {
            return false;
        }

        $direction = $piece->getDirection();
        $couleur = $piece->getCouleur();

        if ($couleur === PieceSquadro::BLANC) { // Déplacement horizontal
            $vitesse = ($direction === PieceSquadro::EST)
                ? PlateauSquadro::BLANC_V_ALLER
                : PlateauSquadro::BLANC_V_RETOUR;
            $dx = $vitesse[$x];
            $nx = ($direction === PieceSquadro::EST) ? $x + $dx : $x - $dx;
            return $nx >= 0 && $nx < 7 && $this->plateau->getPiece($nx, $y) === PieceSquadro::VIDE;

        } elseif ($couleur === PieceSquadro::NOIR) { // Déplacement vertical
            $vitesse = ($direction === PieceSquadro::NORD)
                ? PlateauSquadro::NOIR_V_ALLER
                : PlateauSquadro::NOIR_V_RETOUR;
            $dy = $vitesse[$y];
            $ny = ($direction === PieceSquadro::NORD) ? $y + $dy : $y - $dy;
            return $ny >= 0 && $ny < 7 && $this->plateau->getPiece($x, $ny) === null;
        }

        return false;
    }
```
### Observation 
Le code me semble fonctionnel, il répond à mes demandes, sauf qu'il est redondant, car l'algorithme qui trouve le nouvel emplacement de la pièce, on l'avait déjà implémenté dans PlateauSquadro. Cela m'a amené à faire cela :
```php
  public function estJouablePiece($x, $y) {
        /**
         * Vérifie si la pièce située à ($x, $y) peut être jouée.
         * Une pièce est jouable si la case d'arrivée est libre.
         */
        $destination = $this->plateau->getCoordDestination($x, $y);
        [$nx, $ny] = $destination;

        return $nx >= 0 && $nx < 7 && $ny >= 0 && $ny < 7 && $this->plateau->getPiece($nx, $ny) === null;
    }
```

## Méthode jouePiece 
J'ai demandé à l'IA de me réaliser un code qui va permettre le déplacement d'une pièceSquadro en respectant sa vitesse, la gestion des sauts et reculs des pièces adverses, ainsi que le retournement ou la sortie si nécessaire.

### Observation 
J'ai fais une simulation de l'algorithme il me semble fonctionnel 

## Test ActionSquadro 
À l'aide d'un script qui teste l'intégralité de la classe ActionSquadro, j'ai simulé plusieurs situations possibles dans le jeu, et cela semble correct.
### Remarque 
J'ai remarqué que l'IA a commis quelques erreurs dans les algorithmes permettant de jouer une pièce (pour avancer ou reculer), j'ai donc dû les corriger manuellement.
### Résultat
```
Test de estJouablePiece() :
La pièce est-elle jouable ? Oui
Test de jouePiece() :
PieceSquadro { couleur: VIDE, direction: INCONNUE }
Vérifions si la pièce est déplacée
(3,1)PieceSquadro { couleur: NOIR, direction: NORD }
(6,1)PieceSquadro { couleur: VIDE, direction: INCONNUE }
Test de reculePiece() :
(3,1)PieceSquadro { couleur: VIDE, direction: INCONNUE }
(6,1)PieceSquadro { couleur: NOIR, direction: NORD }

 
 ------------------------------------------- 
(2,3)PieceSquadro { couleur: VIDE, direction: INCONNUE }
(2,2)PieceSquadro { couleur: NOIR, direction: NORD }
(2,0)PieceSquadro { couleur: BLANC, direction: EST }
(6,2)PieceSquadro { couleur: VIDE, direction: INCONNUE }
 joue piéce (1,0) 
 
 (2,3)PieceSquadro { couleur: BLANC, direction: EST }
(2,2)PieceSquadro { couleur: VIDE, direction: INCONNUE }
(2,0)PieceSquadro { couleur: VIDE, direction: INCONNUE }
(6,2)PieceSquadro { couleur: NOIR, direction: NORD }

 
 -----------------------------------------
 Test de la méthode victoireLe joueur blanc a-t-il gagné ? Oui
```

# Etape 2 
## Classe PieceSquadroUI 
un script php avec des fonctions qui permettent de renvoie du code html 
Par exemple: 
 - fonction qui renvoie du code pour illustrer une pieceVide 
 ```php
 public static function genererCaseVide(): string
    {
        return '<button class="case vide" disabled></button>';
    }
 ```
 ### Test 
 J'ai demandé à l'IA un script PHP qui renvoie du HTML brut pour tester l'intégralité de la classe PieceSquadroUI. Ensuite, j'ai visualisé le résultat en utilisant Apache.

## Classe SquadroUIGenerator
La première version que L'IA nous a avait ne répondait pas aux spécifités du jeu et du plateau, 
J'ai essayé d'implémenter la classe cas par cas en ajoutant une classe abstraite avec deux méthodes getDebutHtml() et getFinHtml() (tout comme dans le cours Programmation web) 
les deux méthodes sont implémentées dans la classe SquadroUIGenerato. 

### Méthodes importantes 
- getDebutHTML : Crée l'en-tête HTML pour chaque page avec un titre et une référence à la feuille de style CSS.
- getFinHTML : Clôture la structure HTML.
- genererEntete : Génère l'entête HTML d'une page (titre et ouverture du body).
- getDivPlateau : Crée le tableau 9x9 représentant le plateau de jeu avec des cases qui peuvent afficher une pièce ou être vides/neutres.
- casesBords : Renvoie un tableau contenant les valeurs des cases des bords du plateau (première ligne, première colonne, dernière ligne et dernière colonne).
- genererPageJouerPiece : Affiche la page permettant au joueur d'effectuer un coup.
- genererPageConfirmerDeplacement : Affiche une page de confirmation pour un déplacement de pièce.
- genererPageVictoire : Affiche la page de victoire d'un joueur avec un message de félicitations.
- getErreurHTML : Affiche la page avec un message d'erreur ;

#### Test 
J'ai fait un script en php qui génére du code html pour tester l'intégralité de la classe SquadroUIGenerator.
```php
<?php
require_once '../SquadroUIGenerator.php';
require_once '../PlateauSquadro.php';

$joueurActif = 'blanc'; // Changez entre 'blanc' et 'noir' pour voir l'effet

$plateau = new PlateauSquadro();
echo SquadroUIGenerator::genererPageJouerPiece($joueurActif, $plateau);

?>
```
## CSS 
Pour la partie CSS, j'ai interagi avec l'IA en lui demandant un résultat précis, tout en veillant à ce que le code reste facile à comprendre par la suite.

# Etape 3
## FormulaireSquadro 
Un script qui traite les actions des joueurs et met à jour l'état du jeu en conséquence.
Démarrage et gestion de session :

- Utilise session_start() pour stocker et gérer l'état du jeu.
Récupère l'état du plateau et le joueur actif .

Gestion des actions des joueurs :
- ChoisirPièce : Le joueur sélectionne une pièce à déplacer.
- ConfirmerChoix : Validation du déplacement de la pièce et mise à jour du plateau.
- Rejouer : Réinitialise la partie en détruisant la session.
  
- Interaction avec les classes PlateauSquadro et ActionSquadro 
  
- Mise à jour de l'état du jeu :
Si un coup est valide, mise à jour du plateau et changement de joueur.
Si une erreur survient, mise à jour de $_SESSION['etat'] = 'Erreur'.
Redirection vers index.php après traitement de l'action.

Étant donné que nous sommes déjà bien avancés dans le projet, il est parfois difficile de préciser exactement ce que nous voulons à l'IA. Cela dit, elle nous a été d'une grande aide pour organiser le contenu des $POST et gérer les concaténations.

## index.php 
Un script qui présente une vue à l'utilisateur adaptée à l'état dans lequel se trouve le jeu.
- état ChoixPièce : appeler la méthode qui renvoie une page qui va permettre de choisir une pièce. 
- état ConfirmationPièce : appeler la méthode qui renvoie une page qui va permettre de confirmer une pièce choisie 
- état Victoire : appeler la méthode qui renvoie une page qui affiche le vainqueur 

j'ai rencontré des difficultés pour afficher correctement le plateau. En effet, je n'ai pas pu créer une nouvelle instance de actionSquadro sur le même plateau,(Pour désactiver les boutons des pièces non jouables) car cette classe accède au plateau et modifie son état sans que la session soit mise à jour correctement, ce qui peut entraîner un comportement inattendu.

- Dans formulaireSquadro.php :
```php
  $actionSquadro = $_SESSION['actionSquadro'];
   if ($actionSquadro->estJouablePiece($x-1, $y-1)) {
    $actionSquadro->jouePiece($x-1, $y-1);
    }
```
- Dans SquadroUIGenerator.php :
```php
    $actionSquadroTemp = new ActionSquadro($plateau);
  $jouable = $actionSquadroTemp->estJouablePiece($x - 1, $y - 1);
```
### Test
Après avoir testé les différents cas possibles du jeu, j'ai identifié quelques erreurs que j'ai ensuite corrigées

# Etape 4
## Création de la base de donnée 
- Importer PDOSquadro.skel.php fournie
- Importer la base de données fournie (SQL/squadro.sql).
- Créer la table  joueursquadro , PartieSquadro 

## Connexion à PostgreSQL avec la classe PDOSquadro
Cette étape permet d'établir une connexion à une base de données PostgreSQL en utilisant  PDO via la classe PDOSquadro.
### 📌 Prérequis
Il faut que votre  environnement répond aux conditions suivantes :

PostgreSQL installé et fonctionnel

L'extension pdo_pgsql activée (vérifiez avec php -m | grep pgsql)

Si pdo_pgsql n'est pas installé, exécutez :

```code
sudo apt-get install php-pgsql
sudo systemctl restart apache2  # Si vous utilisez Apache
```
### 🚀 Configuration
1️⃣ Modifiez le fichier env/db.php et remplissez les informations de connexion :
```php 
<?php
$_ENV['sgbd'] = 'pgsql';
$_ENV['host'] = 'localhost';
$_ENV['database'] = 'nom_de_votre_base';
$_ENV['user'] = 'postgres';
$_ENV['password'] = 'votre_mot_de_passe';
?>
```
2️⃣ Vérifier la connexion
créez un fichier testConnection.php 
```php
try {
    PDOSquadro::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);
    echo "Connexion réussie !";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
```
## Classe JoueurSquadro 

Implémentation de la classe JoueurSquadro
Cette classe représente un joueur du jeu Squadro.

### Attributs :

- nomJoueur : Nom du joueur (string)

- id : Identifiant unique du joueur (int)

### Méthodes :

- getNomJoueur(), setNomJoueur(string): Getter et setter du nom du joueur

- getId(), setId(int): Getter et setter de l'ID du joueur

- toJson(): Sérialisation en JSON

- fromJson(string): Désérialisation d'un objet JoueurSquadro
  
## Classe PartieSquadro
Cette classe représente une partie de Squadro.

### Attributs :

- partieId : Identifiant unique de la partie (int)

- joueurs : Liste des joueurs (array)

- joueurActif : Indice du joueur actif (int)

- gameStatus : État de la partie (string)

- plateau : État du plateau (PlateauSquadro)

### Méthodes :

- addJoueur(JoueurSquadro): Ajoute un joueur à la partie

- getJoueurActif(), getNomJoueurActif(): Récupère le joueur actif

- __toString(): Sérialisation en JSON

- toJson(), fromJson(string): Sérialisation/Désérialisation de l'objet PartieSquadro

## Classe PDOSquadro

Cette classe gère les interactions avec la base de données via PDO.

### Méthodes :

- initPDO(string, string, string, string, string): Initialisation de la connexion PDO

- createPlayer(string): Crée un joueur en base de données

- selectPlayerByName(string): Récupère un joueur par son nom

- createPartieSquadro(string, string, int): Crée une partie de Squadro

- savePartieSquadro(string, string, int): Sauvegarde l'état d'une partie

- addPlayerToPartieSquadro(string, string, int): Ajoute un joueur à une partie

- getPartieSquadroById(int): Récupère une partie par ID

- getAllPartieSquadro(): Récupère toutes les parties

- getAllPartieSquadroByPlayerName(string): Récupère les parties d'un joueur

- getLastGameIdForPlayer(string): Récupère l'ID de la dernière partie d'un joueur
  
## home.php - Salle de Jeux Squadro

📌 Description

En demandant à l'IA de générer un fichier qui représente la salle de jeux (Home) du jeu Squadro. C'est le point d'accès principal après l'authentification. Il permet au joueur de :

    ✅ Créer une nouvelle partie
    ✅ Rejoindre une partie en cours
    ✅ Voir les parties en attente d’un second joueur
    ✅ Accéder aux parties terminées
    ✅ Se déconnecter

L'accès à cette page nécessite une connexion : si l'utilisateur n'est pas authentifié, il est redirigé vers login.php

J'ai ajouté l'état `$SESSION['etat'] = Home` dans login.php pour faire une redirection lors d'une connexion d'un joueur

```php 
case 'Home':
        header('HTTP/1.1 303 See Other');
        header('Location: home.php');
```

📂 Structure du Code
    🔹 1. Vérification de l’authentification
    🔹 2. Connexion à la base de données
    🔹 3. Récupération des parties depuis la base de données

Le script extrait plusieurs listes de parties :

    $partieDisponible → Toutes les parties disponibles.
    $partiesEnCours → Toutes les parties du joueur actuel.
    $partiesEnAttente → Parties en attente d'un second joueur.
    $partiesNonTerminees → Parties en cours du joueur.
    $partiesTerminees → Parties terminées.
        
🔹 4. Affichage de la page Home

La fonction getPageHome() génère la page HTML contenant :

    Un bouton pour créer une nouvelle partie.
    Une liste des partiProcessus de Création d'une Partiees en attente avec un bouton pour les rejoindre.
    Une liste des parties en cours avec un bouton pour les continuer.
    Une liste des parties terminées avec un bouton pour les consulter.
    Un bouton pour se déconnecter.

### Processus de Création d'une Partie
- Vérification de la Session
- Initialisation de la Connexion à la Base de Données
- Création d'un Objet PartieSquadro
- Sauvegarde de la Partie dans la Base de Données
```php
PDOSquadro::createPartieSquadro($joueur->getNom(), $partie->toJson())
```
### Processus pour Rejoindre une Partie
- Vérification de la Session
- Récupération de la Partie
    L'identifiant de la partie est envoyé via un formulaire `($_POST['partieId'])`.

```php 
PDOSquadro::getPartieSquadroById($partieId) 
```
est appelé pour récupérer la partie correspondante dans la base de données.

- Ajout du Joueur à la Partie

    Si la partie existe, le joueur est ajouté à la liste des participants grâce à addJoueur($joueur).

```php 
PDOSquadro::addPlayerToPartieSquadro($joueur->getNom(), $partie->toJson(), $partieId)
```
met à jour la base de données.

- Mise à Jour de l'État du Jeu
```php 
PDOSquadro::savePartieSquadro($partie->gameStatus, $partie->toJson(), $partieId) 
```
sauvegarde l'état actuel de la partie.

### Accès à une Partie dans Squadro

1- Sélection de la Partie:

Depuis la page d'accueil (générée dans la fonction getPageHome), l'utilisateur peut voir la liste des parties non terminées. Pour accéder à une partie en cours, l'utilisateur clique sur le bouton associé, ce qui envoie un formulaire contenant :

    L'action : accederPartie
    L'identifiant de la partie (partieId)

2- Rechargement et Reconstitution de la Partie:

Dans le fichier principal (index.php), si la variable `$_SESSION['partieId']` est définie, l'application :

    - Initialise la connexion à la base de données via PDOSquadro::initPDO()
    - Récupère l'objet PartieSquadro correspondant
    - Décode le plateau de jeu stocké au format JSON et reconstitue l'objet PlateauSquadro grâce à la méthode PlateauSquadro::fromJson()
    - Crée une instance d'ActionSquadro à partir du plateau pour gérer les actions ultérieures
    - Identifie le joueur actif et attribue la couleur correspondante à l'utilisateur connecté (BLANC pour le premier joueur, NOIR pour le second ou NEUTRE si ce n'est pas le joueur actif)
    - Charge l'interface de jeu appropriée en fonction de l'état actuel (par exemple, la page pour choisir une pièce)
```php
if (isset($_SESSION['partieId'])) {
    PDOSquadro::initPDO($_ENV['sgbd'], $_ENV['host'], $_ENV['database'], $_ENV['user'], $_ENV['password']);
    $partie = PDOSquadro::getPartieSquadroById($_SESSION['partieId']);
    $_SESSION['partie'] = $partie;
    $partieJson = json_decode($partie->toJson(), true);
    $plateauJson = $partieJson['plateau'];
    $_SESSION['plateau'] = PlateauSquadro::fromJson($plateauJson);
    $_SESSION['actionSquadro'] = new ActionSquadro($_SESSION['plateau']);
    // Détermination du joueur actif et de la couleur
    $indexActif = $partie->joueurActif;
    $_SESSION['couleurJoueur'] = ($indexActif == PartieSquadro::PLAYER_ONE)
        ? PieceSquadro::BLANC
        : PieceSquadro::NOIR;
}
```

3- Traitement du Formulaire d'Accès:

Le fichier de gestion des formulaires  traite l'action accederPartie :

    - Récupération de l'identifiant de la partie via `$_POST['partieId']`
    - Stockage de cet identifiant dans la session `$_SESSION['partieId']`
    - Récupération de la partie correspondante depuis la base de données à l'aide de la -  - méthode `PDOSquadro::getPartieSquadroById($partieId)`
    - Mise à jour de la variable de session `$_SESSION['partie']` avec l'objet PartieSquadro obtenu
    - Définition de l'état de la session sur `ChoixPièce` afin de préparer l'affichage de la page de jeu
    - Redirection vers index.php pour recharger l'interface de jeu

###  Traitement du Formulaire de Confirmation
Le formulaire de confirmation envoie ensuite une requête POST contenant :

    - Le paramètre action avec la valeur ConfirmationPiece
    - Le paramètre confirm qui vaut soit yes pour valider, soit no pour annuler.

Le traitement dans le fichier de gestion des actions se fait dans le switch correspondant :

si l'utilisateur confirme (yes) :

    - Le système vérifie que le déplacement de la pièce est possible via la méthode estJouablePiece().
    - Si le déplacement est valide, la méthode jouePiece() effectue le déplacement sur le plateau.
    - L'objet PartieSquadro est mis à jour en réencodant le plateau (au format JSON) dans l'objet de la partie.
    - Une vérification de victoire est effectuée : si le joueur remporte la partie, l'état passe à Victoire et la partie est marquée comme terminée dans la base de données. - - - Sinon, le joueur actif est changé et l'état revient à ChoixPièce.

Si l'utilisateur annule (no) :

    - Les coordonnées de la pièce sont retirées de la session.
    - L'état est remis à ChoixPièce pour permettre une nouvelle sélection.
  
Une fois cette étape achevée, j'ai demandé à l'IA de repérer tout code redondant et m'assister dans son optimisation.# Squadro-Game
