<?php
// Charger les classes AVANT session_start() : la session doit
// connaître les classes Creature/Combat

// un p'tit soucis avec votre session... vous faites bcp de modifs..
//http://localhost/2026_TP_blablabla/public/index.php?reset=1

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
    ___________
    // instancier une nouvelle créature (le chat )
    // la stocker dans la session 'creature2'
    ___________
    // instancier un nouveau combat ayant le nombre de tours par défaut
    // la stocker dans la session 'combat'
    ___________
    // Le joueur actif est random ( soit le 1 , soit le 2 !)
    // il est stocké dans la session 'joueurActif'
    ___________
    // on commence, donc pas de gagant pour le moment !
    // la session 'gagnant' est donc à null
    ___________

    // On récupère dans une variable $premier, le nom du premier joueur.
    ___________

    $_SESSION['message'] = "Le combat commence ! $premier commence l'affrontement…";
}


// on récupère les créature présentent dans les sessions dans les variables
// $creature1 et $creature2
// et le combat actuel dans la variable $combat

/** @var Creature $creature1 */
___________
/** @var Creature $creature2 */
___________
/** @var Combat $combat */
___________

// ---------- Traiter l'action envoyée par le joueur actif ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$_SESSION['gagnant'] && isset($_POST['action'])) {

    // affectation du joueur actif dans $joueurActif
    ___________
    // l'attaquant $attaquant récupère la créature qui joue actuellement
    ___________
    // affectation de $defenseur avec le défenseur récupère l'autre !!)
    ___________

    $tourConsomme = false;

    if ($_POST['action'] === 'attaquer') {
        // affectation $degat avec les points d'attaque de l'attaquant !
        ___________
        // on lance le combat avec l'attaquant et le défenseur
        ___________

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
        ___________
        ___________
        ___________
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

    return (int) max(0, min(100, round($c->getPointsVie() / $pvMax * 100)));
}

// PV de départ = PV maximum pour ce TP (pas de croissance au-delà de la valeur initiale)
$pvMax1 = 100;
$pvMax2 = 150;

// affectation de $estActif1 :
// à vrai si le joueur actif est le joueur1, qu'il n'y a pas de gagnant
// et que la créature 1 est toujours vivante ;  faux sinon;
 ___________
// affectation de $estActif2 :
// voir affectation ci-dessus mais pour le 2 !
 ___________

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bataille de Créatures</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;800&family=Nunito:wght@500;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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
                <?= // nombre actuel de tour(s) effectué(s) ?>
                /<?= TOURS_PAR_DEFAUT ?>
      </span>
        </div>
    </div>

    <div class="arena">

        <!-- CARTE JOUEUR 1 -->
       <?php // Expliquer la ligne ci-dessous ?>
        <div class="card <?= $estActif1 ? 'active' : '' ?> <?= $creature1->estMort() ? 'ko' : '' ?>" data-player="1">
            <span class="side-tag">Joueur 1</span>
            <div class="creature-frame">
                <img src="images/canard.jpg" alt="<?= htmlspecialchars( // nom de la créature 1 // ) ?>">
            </div>
            <div class="creature-name"><?= htmlspecialchars(// nom de la créature 1 //) ?></div>
            <div class="hp-row">
                <span>PV</span>
                <div class="hp-bar"><div class="hp-fill" style="width:<?= pourcentagePv($creature1, $pvMax1) ?>%"></div></div>
                <span class="hp-val"><?= max(0, // nombre de points de vies de la créature 1 //) ?>/<?= $pvMax1 ?></span>
            </div>
            <div class="pa-row">Attaque : <b><?= // nombre de points d'attaque de la créature 1 // ?></b></div>

            <div class="action-zone">
                <?php if ($estActif1): ?>
                    <form method="post" class="btn-row">
                        <button class="btn btn-attack" type="submit" name="action" value="attaquer">⚔ Attaquer</button>
<!--                    <button class="btn btn-heal" type="submit" name="action" value="soigner">✨ Se soigner</button>-->
                    </form>
                <?php else: ?>
                    <div class="waiting-msg"><?= // 'Hors combat' si la créature est morte,  'En attente…' sinon // ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="vs-emblem">VS</div>

        <!-- CARTE JOUEUR 2 -->
        <div class="card <?= $estActif2 ? 'active' : '' ?> <?= $creature2->estMort() ? 'ko' : '' ?>" data-player="2">
            <span class="side-tag">Joueur 2</span>
            <div class="creature-frame">
                <img src="images/chat.jpg" alt="<?= htmlspecialchars(// nom de la créature 2 //) ?>">
            </div>
            <div class="creature-name"><?= htmlspecialchars(// nom de la créature 2 //) ?></div>
            <div class="hp-row">
                <span>PV</span>
                <div class="hp-bar"><div class="hp-fill" style="width:<?= pourcentagePv($creature2, $pvMax2) ?>%"></div></div>
                <span class="hp-val"><?= max(0, // nombre de points de vies de la créature 2 //) ?>/<?= $pvMax2 ?></span>
            </div>
            <div class="pa-row">Attaque : <b><?= // nombre de points d'attaque de la créature 1 // ?></b></div>

            <div class="action-zone">
                <?php if ($estActif2): ?>
                    <form method="post" class="btn-row">
                        <button class="btn btn-attack" type="submit" name="action" value="attaquer">⚔ Attaquer</button>
<!--                     <button class="btn btn-heal" type="submit" name="action" value="soigner">✨ Se soigner</button>-->
                    </form>
                <?php else: ?>
                    <div class="waiting-msg"><?= // 'Hors combat' si la créature est morte,  'En attente…' sinon // ?></div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="turn-banner">
        <?php if ($gagnant === 'egalite'): ?>
            Fin du combat — nombre de tours écoulé
        <?php elseif ($gagnant): ?>
            🏆 <b><?= htmlspecialchars($gagnant) ?></b> remporte le combat !
        <?php else: // Explique le ligne ci-dessous //  ?>
            Tour de <b><?= htmlspecialchars($joueurActif === 1 ? $creature1->getNom() : $creature2->getNom()) ?></b> — à lui de jouer
        <?php endif; ?>
    </div>

    <div class="reset-row">
        // Explique la ligne ci-dessous //
        <a class="reset-btn" href="index.php?reset=1">↺ Nouvelle partie</a>
    </div>

</div>
</body>
</html>
