<?php
require_once 'JoueurSquadro.php';
require_once 'PartieSquadro.php';


class PDOSquadro
{
    private static PDO $pdo;

    public static function initPDO(string $sgbd, string $host, string $db, string $user, string $password): void
    {
        switch ($sgbd) {
            /*            case 'mysql':
                            TODO si nécessaire
                            break;
                            */
            case 'pgsql':
                $dsn = "$sgbd:host=$host;dbname=$db";
                self::$pdo = new PDO($dsn, $user, $password);
                break;
            default:
                exit("Type de SGBD non correct : $sgbd fourni, 'pgsql' attendu");
        }

        // pour récupérer aussi les exceptions provenant de PDOStatement
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /* requêtes Préparées pour l'entitePlayerSquadro */
    private static PDOStatement $createPlayerSquadro;
    private static PDOStatement $selectPlayerByName;

    /******** Gestion des requêtes relatives à JoueurSquadro *************/
    public static function createPlayer(string $name): JoueurSquadro
    {
        if (!isset(self::$pdo)) {
            throw new \Exception("La connexion à la base de données n'a pas été initialisée.");
        }
        self::$createPlayerSquadro = self::$pdo->prepare("INSERT INTO JoueurSquadro (joueurnom) VALUES (:name) RETURNING id");
        self::$createPlayerSquadro->execute(['name' => $name]);
        $id = self::$createPlayerSquadro->fetchColumn();
        return new JoueurSquadro($name, $id);
    }

    public static function selectPlayerByName(string $name): ?JoueurSquadro
    {
        if (!isset(self::$pdo)) {
            throw new \Exception("La connexion à la base de données n'a pas été initialisée.");
        }
        self::$selectPlayerByName = self::$pdo->prepare("SELECT * FROM JoueurSquadro WHERE joueurnom = :name");
        self::$selectPlayerByName->execute(['name' => $name]);
        $row = self::$selectPlayerByName->fetch(PDO::FETCH_ASSOC);
        return $row ? new JoueurSquadro($row['joueurnom'], $row['id']) : null;
    }

    /* requêtes préparées pour l'entite PartieSquadro */
    private static PDOStatement $createPartieSquadro;
    private static PDOStatement $savePartieSquadro;
    private static PDOStatement $addPlayerToPartieSquadro;
    private static PDOStatement $selectPartieSquadroById;
    private static PDOStatement $selectAllPartieSquadro;
    private static PDOStatement $selectAllPartieSquadroByPlayerName;
    private static PDOStatement $selectLastGameId;

    /******** Gestion des requêtes relatives à PartieSquadro *************/

    /**
     * initialisation et execution de $createPartieSquadro la requête préparée pour enregistrer une nouvelle partie
     */
    public static function createPartieSquadro(string $playerName, string $json): void
    {
        if (!isset(self::$pdo)) {
            throw new \Exception("La connexion à la base de données n'a pas été initialisée.");
        }

        // Récupérer l'ID du joueur à partir de son nom
        $player = self::selectPlayerByName($playerName);
        if (!$player) {
            throw new \Exception("Joueur non trouvé.");
        }

        // Préparation de la requête SQL pour insérer une nouvelle partie
        self::$createPartieSquadro = self::$pdo->prepare(
            "INSERT INTO PartieSquadro (playerOne, gameStatus, json) VALUES (:playerOne, :gameStatus, :json)"
        );
        $partie = PartieSquadro::fromJson($json);
        self::$createPartieSquadro->execute([
            'playerOne' => $player->getId(),
            'gameStatus' => 'waitingForPlayer',
            'json' => $json  // On stocke temporairement le JSON sans ID correct
        ]);
    
        // Récupérer l'ID auto-incrémenté
        $partieID = self::$pdo->lastInsertId();
    
        // Mettre à jour l'objet PartieSquadro avec l'ID correct
        $partie->setPartieId((int) $partieID);
    
        // Mettre à jour la colonne JSON avec l'ID correct
        $updateJsonQuery = self::$pdo->prepare(
            "UPDATE PartieSquadro SET json = :json WHERE partieid = :id"
        );
        $updateJsonQuery->execute([
            'json' => $partie->toJson(), // Maintenant, le JSON a le bon ID
            'id' => $partieID
        ]);
    }


    /**
     * initialisation et execution de $savePartieSquadro la requête préparée pour changer
     * l'état de la partie et sa représentation json
     */
    public static function savePartieSquadro(string $gameStatus, string $json, int $partieId): void
    {
        if (!isset(self::$pdo)) {
            throw new \Exception("La connexion à la base de données n'a pas été initialisée.");
        }

        self::$savePartieSquadro = self::$pdo->prepare(
            "UPDATE PartieSquadro SET gameStatus = :gameStatus, json = :json WHERE partieId = :partieId"
        );

        self::$savePartieSquadro->execute([
            'gameStatus' => $gameStatus,
            'json' => $json,
            'partieId' => $partieId
        ]);
    }

    /**
     * initialisation et execution de $addPlayerToPartieSquadro la requête préparée pour intégrer le second joueur
     */
    public static function addPlayerToPartieSquadro(string $playerName, string $json, int $gameId): void
    {
        if (!isset(self::$pdo)) {
            throw new \Exception("La connexion à la base de données n'a pas été initialisée.");
        }

        // Récupérer l'ID du joueur à partir de son nom
        $player = self::selectPlayerByName($playerName);
        if (!$player) {
            throw new \Exception("Joueur non trouvé.");
        }

        self::$addPlayerToPartieSquadro = self::$pdo->prepare(
            "UPDATE PartieSquadro SET playerTwo = :playerTwo, json = :json WHERE partieId = :gameId"
        );

        self::$addPlayerToPartieSquadro->execute([
            'playerTwo' => $player->getId(),
            'json' => $json,
            'gameId' => $gameId
        ]);
    }


    /**
     * initialisation et execution de $selectPartieSquadroById la requête préparée pour récupérer
     * une instance de PartieSquadro en fonction de son identifiant
     */
    public static function getPartieSquadroById(int $gameId): ?PartieSquadro
    {
        if (!isset(self::$pdo)) {
            throw new \Exception("La connexion à la base de données n'a pas été initialisée.");
        }

        self::$selectPartieSquadroById = self::$pdo->prepare(
            "SELECT * FROM PartieSquadro WHERE partieId = :gameId"
        );

        self::$selectPartieSquadroById->execute(['gameId' => $gameId]);
        $row = self::$selectPartieSquadroById->fetch(PDO::FETCH_ASSOC);

        return $row ? PartieSquadro::fromJson($row['json']) : null;
    }
    /**
     * initialisation et execution de $selectAllPartieSquadro la requête préparée pour récupérer toutes
     * les instances de PartieSquadro
     */
    public static function getAllPartieSquadro(): array
    {
        if (!isset(self::$pdo)) {
            throw new \Exception("La connexion à la base de données n'a pas été initialisée.");
        }

        self::$selectAllPartieSquadro = self::$pdo->prepare(
            "SELECT * FROM PartieSquadro"
        );

        self::$selectAllPartieSquadro->execute();
        $parties = [];

        while ($row = self::$selectAllPartieSquadro->fetch(PDO::FETCH_ASSOC)) {
            $parties[] = PartieSquadro::fromJson($row['json']);
        }

        return $parties;
    }

    /**
     * initialisation et execution de $selectAllPartieSquadroByPlayerName la requête préparée pour récupérer les instances
     * de PartieSquadro accessibles au joueur $playerName
     * ne pas oublier les parties "à un seul joueur"
     */
    public static function getAllPartieSquadroByPlayerName(string $playerName): array
    {
        if (!isset(self::$pdo)) {
            throw new \Exception("La connexion à la base de données n'a pas été initialisée.");
        }

        // Récupérer l'ID du joueur à partir de son nom
        $player = self::selectPlayerByName($playerName);
        if (!$player) {
            throw new \Exception("Joueur non trouvé.");
        }

        self::$selectAllPartieSquadroByPlayerName = self::$pdo->prepare(
            "SELECT * FROM PartieSquadro WHERE playerOne = :playerId OR playerTwo = :playerId"
        );

        self::$selectAllPartieSquadroByPlayerName->execute(['playerId' => $player->getId()]);
        $parties = [];

        while ($row = self::$selectAllPartieSquadroByPlayerName->fetch(PDO::FETCH_ASSOC)) {
            $parties[] = PartieSquadro::fromJson($row['json']);
        }

        return $parties;
    }
    /**
     * initialisation et execution de la requête préparée pour récupérer
     * l'identifiant de la dernière partie ouverte par $playername
     */
    public static function getLastGameIdForPlayer(string $playerName): int
    {
        if (!isset(self::$pdo)) {
            throw new \Exception("La connexion à la base de données n'a pas été initialisée.");
        }

        // Récupérer l'ID du joueur à partir de son nom
        $player = self::selectPlayerByName($playerName);
        if (!$player) {
            throw new \Exception("Joueur non trouvé.");
        }

        self::$selectLastGameId = self::$pdo->prepare(
            "SELECT partieid FROM PartieSquadro WHERE playerone = :playerId OR playertwo = :playerId ORDER BY partieid DESC LIMIT 1"
        );

        self::$selectLastGameId->execute(['playerId' => $player->getId()]);
        $row = self::$selectLastGameId->fetch(PDO::FETCH_ASSOC);

        return $row ? $row['partieid'] : 0;
    }

}