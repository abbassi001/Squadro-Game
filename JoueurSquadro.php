<?php
class JoueurSquadro {
    private $nom;
    private $id;

    public function __construct(string $nom, int $id = null) {
        $this->nom = $nom;
        $this->id = $id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function toJson(): string {
        return json_encode(  [
            'nom' => $this->nom,
            'id' => $this->id
        ]);
    }

    public static function fromJson(string $json): JoueurSquadro {
        $data = json_decode($json, true);
        return new self($data['nom'], $data['id']);
    }
}