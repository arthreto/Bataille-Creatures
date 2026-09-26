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
     * @param string $nom Nom de la créature
     * @param int $pointAttaque Nombre de point d'attaque
     * @param int $pv Point de vie
     */
    public function __construct(string $nom = "vache", int $pointAttaque = 25, int $pv = 200) {
        $this->nom = $nom;
        $this->pointAttaque = $pointAttaque;
        $this->pv = $pv;
        $this->controlePoints();
    }

    /**
     * Récupéré le nom de la créature
     * @return string Renvoie une chaine de texte
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * Changer le nom de la créature
     * @param string $nom Chaine entier du nouveau nom
     * @return void
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * Récupère les points d'attaques de la créature
     * @return int Point D'attaque de la créature
     */
    public function getPointAttaque(): int
    {
        return $this->pointAttaque;
    }


    /**
     * Récupère la vie de la créature
     * @return int Renvoie la quantité d'HP
     */
    public function getPv(): int
    {
        return $this->pv;
    }

    /**
     * Vérifie si les PV de la créature sont positif
     * @return bool Si les PV sont positif
     */
    public function estVivant(): bool
    {
        return $this->pv > 0;
    }

    /**
     * Verifie si les PV de la créature ne sont pas positif
     * @return bool Si les PV sont negatif ou égal a 0
     */
    public function estMort(): bool
    {
        return $this->pv <= 0;
    }

    /**
     * Fait subir des dégats sur la créature
     * @param int $degatSubis Quantité de dégat a faire subir
     * @return void
     */
    public function subirAttaque(int $degatSubis): void
    {
        $this->pv = max(0, $this->pv - $degatSubis);
    }

    /**
     * Soigne la créature de 15 PV si elle n'es pas morte
     */
    public function seSoigner() {
        if($this->estVivant()) {
            $this->pv = min($this->getPv() + 15, 200);
        }
    }

    private function controlePoints() {
        if($this->getPv() < 0) {
            $this->pv = 0;
        } elseif ($this->getPv() > 250) {
            $this->pv = 250;
        }

        if($this->getPointAttaque() < 0) {
            $this->pointAttaque = 0;
        } elseif ($this->getPointAttaque() > 30) {
            $this->pointAttaque = 30;
        }
    }
}