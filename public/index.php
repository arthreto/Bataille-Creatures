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
const PV_MAXIMUM = 200;
const ATTAQUE_MAXIMUM = 30;

// ---------- Réinitialiser la partie ----------
if (isset($_GET['reset'])) {
    session_unset();
    header('Location: index.php');
    exit;
}

// ---------- Initialiser une nouvelle partie ----------
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = "Chaque joueur choisit son nom, ses PV et ses points d'attaque, puis confirme…";
}
if (!isset($_SESSION['image1'])) {
    $_SESSION['image1'] = 'canard.jpg';
}
if (!isset($_SESSION['image2'])) {
    $_SESSION['image2'] = 'chat.jpg';
}

// toutes les images disponibles dans le dossier des créatures
$images = array_map('basename', glob(__DIR__ . '/images/creatures/*.jpg'));

// ---------- Choisir l'image d'une créature ----------
if (isset($_GET['image'])) {
    $_SESSION['image' . $_GET['joueur']] = $_GET['image'];
    header('Location: index.php');
    exit;
}

// ---------- Confirmer la créature d'un joueur ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirmer') {

    $joueur = $_POST['joueur'];

    // instancier une nouvelle créature avec les valeurs saisies par le joueur
    // la stocker dans la session 'creature1' ou 'creature2'
    $_SESSION['creature' . $joueur] = new Creature($_POST['nom'], $_POST['attaque'], $_POST['pv']);

    $_SESSION['message'] = $_SESSION['creature' . $joueur]->getNom() . " entre dans l'arène !";

    if (isset($_SESSION['creature1'], $_SESSION['creature2'])) {

        // instancier un nouveau combat ayant le nombre de tours par défaut
        // la stocker dans la session 'combat'
        $_SESSION['combat'] = new Combat(TOURS_PAR_DEFAUT);

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

    header('Location: index.php');
    exit;
}

$preparation = !isset($_SESSION['combat']);

// on récupère les créature présentent dans les sessions dans les variables
// $creature1 et $creature2
// et le combat actuel dans la variable $combat

if (!$preparation) {
    /** @var Creature $creature1 */
    $creature1 = $_SESSION['creature1'];
    /** @var Creature $creature2 */
    $creature2 = $_SESSION['creature2'];
    /** @var Combat $combat */
    $combat = $_SESSION['combat'];
}

// ---------- Traiter l'action envoyée par le joueur actif ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$preparation && !$_SESSION['gagnant'] && isset($_POST['action'])) {

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
    if ($_POST['action'] === 'soigner') {
        // on soigne l'attaquant, il perd son tour d'attaque
        $attaquant->seSoigner();

        $_SESSION['message'] = $attaquant->getNom() . ' se soigne et remonte à '
                . $attaquant->getPv() . ' PV !';
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
$message = $_SESSION['message'];

function pourcentagePv(Creature $c): int {

    return (int) max(0, min(100, round($c->getPv() / PV_MAXIMUM * 100)));
}

if (!$preparation) {
    $joueurActif = $_SESSION['joueurActif'];
    $gagnant     = $_SESSION['gagnant'];

    $estActif1 = $joueurActif == 1 && $gagnant == null && $creature1->estVivant();

    // affectation de $estActif1 :
    // à vrai si le joueur actif est le joueur1, qu'il n'y a pas de gagnant
    // et que la créature 1 est toujours vivante ;  faux sinon;

    // affectation de $estActif2 :
    // voir affectation ci-dessus mais pour le 2 !
    $estActif2 = $joueurActif == 2 && $gagnant == null && $creature2->estVivant();
}

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
        <span class="eyebrow"><?= $preparation ? 'Arène — Préparation' : 'Arène — Combat en cours' ?></span>
        <h1>Bataille de Créatures</h1>
        <div class="rule"></div>
    </div>

    <div class="log-box">
        <div>
            <div class="log-text"><?= htmlspecialchars($message) ?></div>
            <?php if (!$preparation): ?>
            <span class="log-sub">
        Tour
                <?= $combat->getNbtour() ?>
                /<?= TOURS_PAR_DEFAUT ?>
      </span>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($preparation): ?>

    <div class="arena">

        <!-- PRÉPARATION JOUEUR 1 -->
        <div class="card" data-player="1">
            <span class="side-tag">Joueur 1</span>
            <?php if (isset($_GET['choix']) && $_GET['choix'] == 1): ?>
                <div class="img-grid">
                    <?php foreach ($images as $image): ?>
                        <a href="index.php?joueur=1&image=<?= $image ?>"><img src="images/creatures/<?= $image ?>" alt="<?= $image ?>"></a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <a class="creature-frame" href="index.php?choix=1">
                    <img src="images/creatures/<?= $_SESSION['image1'] ?>" alt="Joueur 1">
                </a>
            <?php endif; ?>
            <?php if (isset($_SESSION['creature1'])): ?>
                <div class="creature-name"><?= htmlspecialchars($_SESSION['creature1']->getNom()) ?></div>
                <div class="pa-row">Prêt — en attente de l'adversaire</div>
            <?php else: ?>
                <form method="post" class="create-form">
                    <input type="hidden" name="joueur" value="1">
                    <label>Nom <input type="text" name="nom" value="<?= str_replace('.jpg', '', $_SESSION['image1']) ?>" maxlength="20" required></label>
                    <label>PV <input type="number" name="pv" value="100" min="1" max="<?= PV_MAXIMUM ?>" required></label>
                    <label>Attaque <input type="number" name="attaque" value="30" min="1" max="<?= ATTAQUE_MAXIMUM ?>" required></label>
                    <button class="btn btn-attack" type="submit" name="action" value="confirmer">✔ Confirmer</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="vs-emblem">VS</div>

        <!-- PRÉPARATION JOUEUR 2 -->
        <div class="card" data-player="2">
            <span class="side-tag">Joueur 2</span>
            <?php if (isset($_GET['choix']) && $_GET['choix'] == 2): ?>
                <div class="img-grid">
                    <?php foreach ($images as $image): ?>
                        <a href="index.php?joueur=2&image=<?= $image ?>"><img src="images/creatures/<?= $image ?>" alt="<?= $image ?>"></a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <a class="creature-frame" href="index.php?choix=2">
                    <img src="images/creatures/<?= $_SESSION['image2'] ?>" alt="Joueur 2">
                </a>
            <?php endif; ?>
            <?php if (isset($_SESSION['creature2'])): ?>
                <div class="creature-name"><?= htmlspecialchars($_SESSION['creature2']->getNom()) ?></div>
                <div class="pa-row">Prêt — en attente de l'adversaire</div>
            <?php else: ?>
                <form method="post" class="create-form">
                    <input type="hidden" name="joueur" value="2">
                    <label>Nom <input type="text" name="nom" value="<?= str_replace('.jpg', '', $_SESSION['image2']) ?>" maxlength="20" required></label>
                    <label>PV <input type="number" name="pv" value="150" min="1" max="<?= PV_MAXIMUM ?>" required></label>
                    <label>Attaque <input type="number" name="attaque" value="20" min="1" max="<?= ATTAQUE_MAXIMUM ?>" required></label>
                    <button class="btn btn-attack" type="submit" name="action" value="confirmer">✔ Confirmer</button>
                </form>
            <?php endif; ?>
        </div>

    </div>

    <div class="turn-banner">
        Les deux créatures doivent être confirmées pour lancer le combat
    </div>

    <?php else: ?>

    <div class="arena">

        <!-- CARTE JOUEUR 1 -->
        <!-- Si estActif1 est active cela afficeh active ou rien et si la creature1 est morte cela affiche ko ou rien -->
       <?php // Expliquer la ligne ci-dessous ?>
        <div class="card <?= $estActif1 ? 'active' : '' ?> <?= $creature1->estMort() ? 'ko' : '' ?>" data-player="1">
            <span class="side-tag">Joueur 1</span>
            <div class="creature-frame">
                <img src="images/creatures/<?= $_SESSION['image1'] ?>" alt="<?= htmlspecialchars( $creature1->getNom() ) ?>">
            </div>
            <div class="creature-name"><?= htmlspecialchars($creature1->getNom()) ?></div>
            <div class="hp-row">
                <span>PV</span>
                <div class="hp-bar"><div class="hp-fill" style="width:<?= pourcentagePv($creature1) ?>%"></div></div>
                <span class="hp-val"><?= max(0, $creature1->getPv()) ?></span>
            </div>
            <div class="pa-row">Attaque : <b><?= $creature1->getPointAttaque() ?></b></div>

            <div class="action-zone">
                <?php if ($estActif1): ?>
                    <form method="post" class="btn-row">
                        <button class="btn btn-attack" type="submit" name="action" value="attaquer">⚔ Attaquer</button>
                        <button class="btn btn-heal" type="submit" name="action" value="soigner">✨ Se soigner</button>
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
                <img src="images/creatures/<?= $_SESSION['image2'] ?>" alt="<?= htmlspecialchars($creature2->getNom()) ?>">
            </div>
            <div class="creature-name"><?= htmlspecialchars($creature2->getNom()) ?></div>
            <div class="hp-row">
                <span>PV</span>
                <div class="hp-bar"><div class="hp-fill" style="width:<?= pourcentagePv($creature2) ?>%"></div></div>
                <span class="hp-val"><?= max(0, $creature2->getPv()) ?></span>
            </div>
            <div class="pa-row">Attaque : <b><?= $creature2->getPointAttaque() ?></b></div>

            <div class="action-zone">
                <?php if ($estActif2): ?>
                    <form method="post" class="btn-row">
                        <button class="btn btn-attack" type="submit" name="action" value="attaquer">⚔ Attaquer</button>
                        <button class="btn btn-heal" type="submit" name="action" value="soigner">✨ Se soigner</button>
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

    <?php endif; ?>

    <div class="reset-row">
        <!-- Explique la ligne ci-dessous -->
        <a class="reset-btn" href="index.php?reset=1">↺ Nouvelle partie</a>
    </div>

</div>
</body>
</html>
