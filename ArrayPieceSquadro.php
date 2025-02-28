<?php
/**
 * Classe ArrayPieceSquadro
 * Cette classe ArrayPieceSquadro va nous permettre de manipuler plus facilement les pièces du jeux regroupées dans un tableau. Elle implémente deux interfaces : ArrayAccess et Countable.
 * @author ADAM Abbas Abbas
 * @author Juba Nemri
 * 
 */
require_once 'PieceSquadro.php';
class ArrayPieceSquadro implements \ArrayAccess, \Countable
{
    private array $pieces = [];

    // Ajouter une pièce au tableau
    public function add(PieceSquadro $piece): void
    {
        $this->pieces[] = $piece;
    }

    // Supprimer une pièce par index
    public function remove(int $index): void
    {
        if (isset($this->pieces[$index])) {
            unset($this->pieces[$index]);
            $this->pieces = array_values($this->pieces); // Réindexer le tableau
        }
    }

    // Représentation sous forme de chaîne
    public function __toString(): string
    {
        return json_encode($this->toJson(), JSON_PRETTY_PRINT);
    }

    // // Convertir en JSON
    // public function toJson(): string {
    //     return json_encode($this->pieces);
    // }

    // Convertir en JSON
    public function toJson(): string
    {
        return json_encode(array_map(function ($piece) {
            return json_decode($piece->toJson(), true);
        }, $this->pieces));
    }

    // ajout de la fonctionnalité static
    public static function fromJson(string $json): self
    {
        $array = json_decode($json, true);
        $instance = new self();
        foreach ($array as $pieceData) {
            $instance->add(PieceSquadro::fromJson(json_encode($pieceData)));
        }
        return $instance;
    }

    // Implémentation de ArrayAccess
    public function offsetExists($offset): bool
    {
        return isset($this->pieces[$offset]);
    }

    public function offsetGet($offset): ?PieceSquadro
    {
        return $this->pieces[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof PieceSquadro) {
            throw new InvalidArgumentException("Value must be an instance of PieceSquadro.".$value);
        }
        if ($offset === null) {
            $this->pieces[] = $value;
        } else {
            $this->pieces[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->pieces[$offset]);
    }

    // Implémentation de Countable
    public function count(): int
    {
        return count($this->pieces);
    }
}
?>