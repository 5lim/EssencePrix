<?php
require 'include/header_inc.php';
require 'include/functions.inc.php';

// --- Récupération des paramètres GET ---

if (isset($_GET['region']) && $_GET['region'] !== '') {
    $region = htmlspecialchars($_GET['region']);
} else {
    $region = '';
}

if (isset($_GET['departement']) && $_GET['departement'] !== '') {
    $departement = htmlspecialchars($_GET['departement']);
} else {
    $departement = '';
}

if (isset($_GET['ville']) && $_GET['ville'] !== '') {
    $ville = htmlspecialchars($_GET['ville']);
} else {
    $ville = '';
}

if (isset($_GET['code_postal']) && $_GET['code_postal'] !== '') {
    $code_postal = htmlspecialchars($_GET['code_postal']);
} else {
    $code_postal = '';
}

// --- Données selon la sélection ---

$departements = [];
if ($region !== '') {
    $departements = obtenirDepartements($region);
}

$villes = [];
if ($departement !== '') {
    $villes = obtenirVilles($departement);
}

// --- Cookie : dernière ville consultée ---
// Format : "PARIS|75001|75"

$derniere_ville = '';
$dernier_cp     = '';
$dernier_dept   = '';

if (isset($_COOKIE['derniere_ville']) && $_COOKIE['derniere_ville'] !== '') {
    $parties = explode('|', $_COOKIE['derniere_ville']);
    if (count($parties) === 3) {
        $derniere_ville = htmlspecialchars($parties[0]);
        $dernier_cp     = htmlspecialchars($parties[1]);
        $dernier_dept   = htmlspecialchars($parties[2]);
    } else {
        setcookie('derniere_ville', '', time() - 1, '/');
    }
}
?>

    <section class="rangee-deux-colonnes">

        <section>
            <h1>Localisation auto</h1>
            <article>
                <p>Trouvez les stations les moins chères dans un rayon de 5 km autour de vous.</p>
                <p>
                    <a href="resultats.php?mode=geolocal" class="btn-geolocal">Près de moi</a>
                </p>
            </article>
        </section>

        <section>
            <h1>Dernière recherche</h1>
            <article>
                <?php if ($derniere_ville !== ''): ?>
                    <p><strong>Ville :</strong> <?= $derniere_ville ?></p>
                    <p><strong>Code postal :</strong> <?= $dernier_cp ?></p>
                    <p>
                        <a href="resultats.php?departement=<?= $dernier_dept ?>&amp;ville=<?= urlencode($derniere_ville) ?>&amp;code_postal=<?= $dernier_cp ?>" class="btn-secondary">
                            Relancer cette recherche
                        </a>
                    </p>
                <?php else: ?>
                    <p>Aucune recherche mémorisée.</p>
                <?php endif; ?>
            </article>
        </section>

    </section>

    <section id="carte">

        <h1>Sélectionnez votre région</h1>

        <article>

            <figure>
                <img src="images/carte_regions.png"
                     alt="Carte des régions de France"
                     usemap="#image-map"
                     id="carte-regions-img"
                     width="1200"
                     height="1181">
                <figcaption>
                    Région sélectionnée :
                    <?php
                    if ($region !== '') {
                        echo ucwords(str_replace('_', ' ', $region));
                    } else {
                        echo 'Aucune';
                    }
                    ?>
                </figcaption>
            </figure>

            <?php
            // Le bloc <map> avec toutes les coordonnées est le travail du binôme.
            // Copier-coller ici exactement le bloc <map>...</map> de son index.php
            ?>

        </article>

    </section>

    <section>

        <h1>Recherche par ville</h1>

        <article>

            <?php
            // ÉTAPE 1 : sélection du département
            // On affiche la liste des départements sous forme de liens <a>
            // Chaque lien recharge la page avec ?region=...&departement=XX
            ?>

            <?php if ($region !== ''): ?>

                <p><strong>Département :</strong></p>

                <nav>
                    <?php foreach ($departements as $code => $nom): ?>
                        <a href="index.php?region=<?= htmlspecialchars($region) ?>&amp;departement=<?= htmlspecialchars($code) ?>#carte"
                           class="<?php if ($departement === $code) { echo 'active'; } ?>">
                            <?= htmlspecialchars($code) ?> — <?= htmlspecialchars($nom) ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

            <?php else: ?>

                <p>Cliquez sur une région sur la carte pour afficher les départements.</p>

            <?php endif; ?>

        </article>

        <?php
        // ÉTAPE 2 : sélection de la ville
        // On affiche la liste des villes sous forme de liens <a>
        // Chaque lien envoie directement vers resultats.php avec tous les paramètres
        ?>

        <?php if ($departement !== ''): ?>

            <article>

                <p><strong>Ville :</strong></p>

                <nav>
                    <?php foreach ($villes as $v): ?>
                        <a href="resultats.php?region=<?= htmlspecialchars($region) ?>&amp;departement=<?= htmlspecialchars($departement) ?>&amp;ville=<?= urlencode($v['nom']) ?>&amp;code_postal=<?= htmlspecialchars($v['code_postal']) ?>"
                           class="<?php if ($ville === $v['nom']) { echo 'active'; } ?>">
                            <?= htmlspecialchars($v['nom']) ?> (<?= htmlspecialchars($v['code_postal']) ?>)
                        </a>
                    <?php endforeach; ?>
                </nav>

            </article>

        <?php endif; ?>

    </section>

<?php require 'include/footer_inc.php'; ?>