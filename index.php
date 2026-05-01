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
// Format stocké : "PARIS|75001|75"

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
        // Cookie mal formé, on le supprime
        setcookie('derniere_ville', '', time() - 1, '/');
    }
}
?>

    <section class="rangee-deux-colonnes">

        <section>
            <h1>Localisation auto</h1>
            <article>
                <p>Trouvez les stations les moins chères autour de vous.</p>
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
            // Copier-coller ici le bloc <map>...</map> de l'index.php du binôme
            ?>

        </article>

    </section>

    <section>

        <h1>Recherche par ville</h1>

        <article>

            <?php if ($region !== ''): ?>

                <form method="get" action="index.php">

                    <?php
                    // Champ caché pour garder la région quand on soumet le département
                    ?>
                    <input type="hidden" name="region" value="<?= htmlspecialchars($region) ?>">

                    <p>
                        <label for="select-departement">Département</label>
                        <select id="select-departement" name="departement">
                            <option value="">— Choisir un département —</option>
                            <?php foreach ($departements as $code => $nom): ?>
                                <option value="<?= htmlspecialchars($code) ?>"
                                    <?php if ($departement === $code) { echo 'selected'; } ?>>
                                    <?= htmlspecialchars($code) ?> — <?= htmlspecialchars($nom) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <input type="submit" value="Valider" class="btn-primary">
                    </p>

                </form>

            <?php else: ?>

                <p>Cliquez sur une région sur la carte pour commencer.</p>

            <?php endif; ?>

        </article>

        <?php if ($departement !== ''): ?>

            <article>

                <form method="get" action="resultats.php">

                    <?php
                    // On garde région et département pour resultats.php
                    ?>
                    <input type="hidden" name="region"      value="<?= htmlspecialchars($region) ?>">
                    <input type="hidden" name="departement" value="<?= htmlspecialchars($departement) ?>">

                    <p>
                        <label for="select-ville">Ville</label>
                        <select id="select-ville" name="ville_cp">
                            <option value="">— Choisir une ville —</option>
                            <?php foreach ($villes as $v): ?>
                                <?php
                                // On met ville ET code postal ensemble dans la value
                                // séparés par | pour pouvoir les récupérer dans resultats.php
                                // avec explode()
                                // Ex : value="PARIS|75001"
                                $valeur = $v['nom'] . '|' . $v['code_postal'];
                                ?>
                                <option value="<?= htmlspecialchars($valeur) ?>">
                                    <?= htmlspecialchars($v['nom']) ?>
                                    (<?= htmlspecialchars($v['code_postal']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>

                    <p>
                        <input type="submit" value="Rechercher" class="btn-primary">
                    </p>

                </form>

            </article>

        <?php endif; ?>

    </section>

<?php require 'include/footer_inc.php'; ?>