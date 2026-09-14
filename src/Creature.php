<?php

namespace src;

/**
 * Classe permettant de géré les Créatures
 */
class Creature
{
    private string $nom;
    private int $pointAttaque;
    private int $pv;

    /**
     * Initialise une nouvelle instance de la classe Créature
     * @param string $nom
     * @param int $pointAttaque
     * @param int $pv
     */
    public function __construct(string $nom = "vache", int $pointAttaque = 25, int $pv = 200) {
        $this->nom = $nom;
        $this->pointAttaque = $pointAttaque;
        $this->pv = $pv;
    }

    /**
     * Récupéré le nom de la créature
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * Changer le nom de la créature
     * @param string $nom
     * @return void
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * Récupère les points d'attaques de la créature
     * @return int
     */
    public function getPointAttaque(): int
    {
        return $this->pointAttaque;
    }


    /**
     * Récupère la vie de la créature
     * @return int
     */
    public function getPv(): int
    {
        return $this->pv;
    }

    /**
     * Renvoie true si la créature est vivante
     * @return bool
     */
    public function estVivant(): bool
    {
        return $this->pv > 0;
    }

    /**
     * Renvoie true si la créature est morte
     * @return bool
     */
    public function estMort(): bool
    {
        return $this->pv <= 0;
    }

    /**
     * Fait subir des dégats sur la créature
     * @param int $degatSubis
     * @return void
     */
    public function subirAttaque(int $degatSubis): void
    {
        $this->pv = max(0, $this->pv - $degatSubis);
    }
}