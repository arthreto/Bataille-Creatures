<?php
// Charger les classes AVANT session_start() : la session doit
// connaître les classes Creature/Combat

// un p'tit soucis avec votre session... vous faites bcp de modifs..
//http://localhost/2026_TP_blablabla/public/index.php?reset=1


use src\Combat;
use src\Creature;

require_once __DIR__ . '/../src/Creature.php';
require_once __DIR__ . '/../src/Combat.php';

session_start();

const TOURS_PAR_DEFAUT = 10;

// ---------- Réinitialiser la partie ----------
if (isset($_GET['reset'])) {
    session_unset();
    header('Location: index.php');
    exit;
}

// ---------- Initialiser une nouvelle partie ----------
if (!isset($_SESSION['combat'])) {
    // instancier une nouvelle créature (le canard )
    // la stocker dans la session 'creature1'
    $_SESSION['creature1'] = new Creature("Canard", 30, 100);

    // instancier une nouvelle créature (le chat )
    // la stocker dans la session 'creature2'
    $_SESSION['creature2'] = new Creature("Chat", 20, 150);

    // instancier un nouveau combat ayant le nombre de tours par défaut
    // la stocker dans la session 'combat'
    $_SESSION['combat'] = new Combat();

    // Le joueur actif est random ( soit le 1 , soit le 2 !)
    // il est stocké dans la session 'joueurActif'
    $_SESSION['joueurActif'] = random_int(1,2);

    // on commence, donc pas de gagant pour le moment !
    // la session 'gagnant' est donc à null
    $_SESSION['gagnant'] = null;

    // On récupère dans une variable $premier, le nom du premier joueur.
    $premier = $_SESSION['creature' . $_SESSION['joueurActif']]->getNom();

    $_SESSION['message'] = "Le combat commence ! $premier commence l'affrontement…";
}


// on récupère les créature présentent dans les sessions dans les variables
// $creature1 et $creature2
// et le combat actuel dans la variable $combat

/** @var Creature $creature1 */
$creature1 = $_SESSION['creature1'];
/** @var Creature $creature2 */
$creature2 = $_SESSION['creature2'];
/** @var Combat $combat */
$combat = $_SESSION['combat'];

// ---------- Traiter l'action envoyée par le joueur actif ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$_SESSION['gagnant'] && isset($_POST['action'])) {

    // affectation du joueur actif dans $joueurActif
   $joueurActif = $_SESSION['joueurActif'];
    // l'attaquant $attaquant récupère la créature qui joue actuellement
    if ($_SESSION['joueurActif'] == 1) {
        $attaquant = $creature1;
        $defenseur = $creature2;
    } else {
        $attaquant = $creature2;
        $defenseur = $creature1;
    }
    // affectation de $defenseur avec le défenseur récupère l'autre !!)

    $tourConsomme = false;

    if ($_POST['action'] === 'attaquer') {
        // affectation $degat avec les points d'attaque de l'attaquant !
        $degats = $attaquant->getPointAttaque();;

        // on lance le combat avec l'attaquant et le défenseur
        $combat->combattre($attaquant, $defenseur);

        $_SESSION['message'] = $attaquant->getNom() . ' attaque ' . $defenseur->getNom()
                . ' et inflige ' . $degats . ' dégâts !';
        $tourConsomme = true;

    }
    if ($tourConsomme) {
        // vérifier si :
        // --> le défenseur est mort
        // --> le combat est terminé
        // auquel cas, on affecte le gagnant dans 'gagnant' de la session actuelle
        // --> sinon c'est juste un changement de joueur actif !
        if($combat->estTermine() || $defenseur->estMort()) {
            $_SESSION['gagnant'] = $attaquant->getNom();
        } else {
            if ($_SESSION['joueurActif'] == 1) {
                $_SESSION['joueurActif'] = 2;
            } else {
                $_SESSION['joueurActif'] = 1;
            }
        }
    }

    // Empêche la re-soumission du formulaire au rechargement (Post/Redirect/Get)
    header('Location: index.php');
    exit;
}

// ---------- Préparer les valeurs d'affichage ----------
$joueurActif = $_SESSION['joueurActif'];
$gagnant     = $_SESSION['gagnant'];
$message     = $_SESSION['message'];

function pourcentagePv(Creature $c, int $pvMax): int {

    return (int) max(0, min(100, round($c->getPv() / $pvMax * 100)));
}

// PV de départ = PV maximum pour ce TP (pas de croissance au-delà de la valeur initiale)
$pvMax1 = 100;
$pvMax2 = 150;

$estActif1 = $joueurActif == 1 && $gagnant == null && $creature1->estVivant();

// affectation de $estActif1 :
// à vrai si le joueur actif est le joueur1, qu'il n'y a pas de gagnant
// et que la créature 1 est toujours vivante ;  faux sinon;

// affectation de $estActif2 :
// voir affectation ci-dessus mais pour le 2 !
$estActif2 = $joueurActif == 2 && $gagnant == null && $creature2->estVivant();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bataille de Créatures</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;800&family=Nunito:wght@500;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap">

    <div class="banner">
        <span class="eyebrow">Arène — Combat en cours</span>
        <h1>Bataille de Créatures</h1>
        <div class="rule"></div>
    </div>

    <div class="log-box">
        <div>
            <div class="log-text"><?= htmlspecialchars($message) ?></div>
            <span class="log-sub">
        Tour
                <?= $combat->getNbtour() ?>
                /<?= TOURS_PAR_DEFAUT ?>
      </span>
        </div>
    </div>

    <div class="arena">

        <!-- CARTE JOUEUR 1 -->
        <!-- Si estActif1 est active cela afficeh active ou rien et si la creature1 est morte cela affiche ko ou rien -->
       <?php // Expliquer la ligne ci-dessous ?>
        <div class="card <?= $estActif1 ? 'active' : '' ?> <?= $creature1->estMort() ? 'ko' : '' ?>" data-player="1">
            <span class="side-tag">Joueur 1</span>
            <div class="creature-frame">
                <img src="images/canard.jpg" alt="<?= htmlspecialchars( $creature1->getNom() ) ?>">
            </div>
            <div class="creature-name"><?= htmlspecialchars($creature1->getNom()) ?></div>
            <div class="hp-row">
                <span>PV</span>
                <div class="hp-bar"><div class="hp-fill" style="width:<?= pourcentagePv($creature1, $pvMax1) ?>%"></div></div>
                <span class="hp-val"><?= max(0, $creature1->getPv()) ?>/<?= $pvMax1 ?></span>
            </div>
            <div class="pa-row">Attaque : <b><?= $creature2->getPointAttaque() ?></b></div>

            <div class="action-zone">
                <?php if ($estActif1): ?>
                    <form method="post" class="btn-row">
                        <button class="btn btn-attack" type="submit" name="action" value="attaquer">⚔ Attaquer</button>
<!--                    <button class="btn btn-heal" type="submit" name="action" value="soigner">✨ Se soigner</button>-->
                    </form>
                <?php else: ?>
                    <div class="waiting-msg"><?= $creature1->estMort() ? 'Hors combat' : 'En attente…' ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="vs-emblem">VS</div>

        <!-- CARTE JOUEUR 2 -->
        <div class="card <?= $estActif2 ? 'active' : '' ?> <?= $creature2->estMort() ? 'ko' : '' ?>" data-player="2">
            <span class="side-tag">Joueur 2</span>
            <div class="creature-frame">
                <img src="images/chat.jpg" alt="<?= htmlspecialchars($creature2->getNom()) ?>">
            </div>
            <div class="creature-name"><?= htmlspecialchars($creature2->getNom()) ?></div>
            <div class="hp-row">
                <span>PV</span>
                <div class="hp-bar"><div class="hp-fill" style="width:<?= pourcentagePv($creature2, $pvMax2) ?>%"></div></div>
                <span class="hp-val"><?= max(0, $creature2->getPv()) ?>/<?= $pvMax2 ?></span>
            </div>
            <div class="pa-row">Attaque : <b><?= $creature1->getPointAttaque() ?></b></div>

            <div class="action-zone">
                <?php if ($estActif2): ?>
                    <form method="post" class="btn-row">
                        <button class="btn btn-attack" type="submit" name="action" value="attaquer">⚔ Attaquer</button>
<!--                     <button class="btn btn-heal" type="submit" name="action" value="soigner">✨ Se soigner</button>-->
                    </form>
                <?php else: ?>
                    <div class="waiting-msg"><?= $creature2->estMort() ? 'Hors combat' : 'En attente…' ?></div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="turn-banner">
        <?php if ($gagnant === 'egalite'): ?>
            Fin du combat — nombre de tours écoulé
        <?php elseif ($gagnant): ?>
            🏆 <b><?= htmlspecialchars($gagnant) ?></b> remporte le combat !

        <?php else: // Si le joueur actif est le 1 cela affiché le nom de la creature 1 ou le nom de la creature 2 //  ?>
            Tour de <b><?= htmlspecialchars($joueurActif === 1 ? $creature1->getNom() : $creature2->getNom()) ?></b> — à lui de jouer
        <?php endif; ?>
    </div>

    <div class="reset-row">
        <!-- Explique la ligne ci-dessous -->
        <a class="reset-btn" href="index.php?reset=1">↺ Nouvelle partie</a>
    </div>

</div>
</body>
</html>
