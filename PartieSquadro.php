<?php
require 'PlateauSquadro.php';
class PartieSquadro
{
    const PLAYER_ONE = 0;
    const PLAYER_TWO = 1;

    private int $partieId = 0;
    private array $joueurs = [];
    public int $joueurActif;
    public string $gameStatus = 'initialized';
    private PlateauSquadro $plateau;

    public function __construct(JoueurSquadro $playerOne)
    {
        $this->joueurs[self::PLAYER_ONE] = $playerOne;
        $this->joueurActif = self::PLAYER_ONE;
        $this->plateau = new PlateauSquadro();
    }

    public function addJoueur(JoueurSquadro $player)
    {
        if (count($this->joueurs) < 2 )  {
            $this->joueurs[self::PLAYER_TWO] = $player;
        }
    }

    public function getJoueurActif(): JoueurSquadro
    {
        return $this->joueurs[$this->joueurActif-1];
    }

    public function getNomJoueurActif($nom): string
    {
        return $this->joueurs[$this->joueurActif]->getNom() == $nom ? $nom : '';
    }

    public function __toString(): string
    {
        return $this->plateau->__toString();
    }

    public function getPartieId(): int
    {
        return $this->partieId;
    }

    public function setPartieId(int $id): void
    {
        $this->partieId = $id;
    }

    public function getJoueurs(): array
    {
        return $this->joueurs;
    }

    public function toJson(): string
    {
        return json_encode([
            'partieId' => $this->partieId,
            'joueurs' => array_map(function ($joueur) {
                return  [
                    'nom' => $joueur->getNom(),
                    'id' => $joueur->getId()
                ];
            }, $this->joueurs),
            'joueurActif' => $this->joueurActif,
            'gameStatus' => $this->gameStatus,
            'plateau' => $this->plateau->toJson()
        ]);
    }

  
    public static function fromJson(string $json): PartieSquadro
    {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Erreur de décodage JSON: " . json_last_error_msg());
        }

        if (!isset($data['joueurs'][self::PLAYER_ONE]['nom'], $data['joueurs'][self::PLAYER_ONE]['id'])) {
            throw new \Exception("Données JSON invalides pour le joueur 1.");
        }

        // Créez le premier joueur
        $playerOne = new JoueurSquadro($data['joueurs'][self::PLAYER_ONE]['nom'], $data['joueurs'][self::PLAYER_ONE]['id']);

        // Créez la partie
        $partie = new self($playerOne);
        $partie->setPartieId($data['partieId']);

        // Ajoutez le deuxième joueur s'il existe
        if (isset($data['joueurs'][self::PLAYER_TWO]['nom'], $data['joueurs'][self::PLAYER_TWO]['id'])) {
            $playerTwo = new JoueurSquadro($data['joueurs'][self::PLAYER_TWO]['nom'], $data['joueurs'][self::PLAYER_TWO]['id']);
            $partie->addJoueur($playerTwo);
        }

        // Définissez le joueur actif et le statut du jeu
        $partie->joueurActif = $data['joueurActif'];
        $partie->gameStatus = $data['gameStatus'];

        // Désérialisez le plateau
        if (isset($data['plateau'])) {
            $partie->plateau = PlateauSquadro::fromJson($data['plateau']); 
        } else {
            throw new \Exception("Données JSON invalides: plateau manquant.");
        }

        return $partie;
    }


}