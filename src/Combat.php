<?php

namespace src;
include_once "src/Creature.php";

/**
 * Classe permettant de géré les Combats
 */
class Combat
{
    private int $nbtour;

    /**
     * Initialise une nouvelle instance de la classe Combat
     * @param int tour
     */
    public function __construct(int $tour = 10) {
        $this->nbtour = $tour;
    }

    /**
     * Renvoie le nombre de tour du combat
     * @return int
     */
    public function getNbtour(): int
    {
        return $this->nbtour;
    }

    /**
     * Effectuer un combat avec des créatures vivantes,
     * l'attaquant attaque le défenseur et decremente un tour
     * @param Creature $attaquant Créature attaquant le defenseur
     * @param Creature $defenseur Créature subissant les dégats
     * @return void
     */
    public function combattre(Creature $attaquant, Creature $defenseur): void
    {
        if($attaquant->estVivant() && $defenseur->estVivant()) {
            $defenseur->subirAttaque($attaquant->getPointAttaque());
            $this->nbtour--;
        }
    }

    /**
     * Renvoie si le combat est terminé
     * @return bool Si le nombre de tour est négatif ou est a 0
     */
    public function estTermine(): bool
    {
        return $this->nbtour <= 0;
    }
}