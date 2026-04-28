<?php
require 'include/header_inc.php';
require 'include/functions.inc.php';

// --- Récupération des paramètres GET ---

if (isset($_GET['mode']) && $_GET['mode'] !== '') {
    $mode = htmlspecialchars($_GET['mode']);
} else {
    $mode = '';
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

if (isset($_GET['departement']) && $_GET['departement'] !== '') {
    $departement = htmlspecialchars($_GET['departement']);
} else {
    $departement = '';
}

if (isset($_GET['region']) && $_GET['region'] !== '') {
    $region = htmlspecialchars($_GET['region']);
} else {
    $region = '';
}

if (isset($_GET['tri']) && $_GET['tri'] !== '') {
    $tri = htmlspecialchars($_GET['tri']);
} else {
    $tri = 'prix';
}

// --- Mode géolocalisation par IP ---
// Si l'utilisateur a cliqué "Près de moi", on détecte sa position via son IP

$geo = [];
if ($mode === 'geolocal') {
    $ip  = $_SERVER['REMOTE_ADDR'];
    $geo = geolocalisationParIP($ip);

    // Si la géolocalisation a fonctionné, on utilise le code postal détecté
    if (!empty($geo) && isset($geo['ville'])) {
        $ville       = $geo['ville'];
        $code_postal = '';  // l'API IP ne donne pas toujours le CP exact
        $departement = '';
    }
}

// --- Récupération des stations ---

$stations = [];

if ($mode === 'geolocal' && !empty($geo)) {
    // Mode géolocalisation : on cherche par département détecté
    // On extrait le département depuis la région détectée si possible
    // Sinon on laisse vide et on affiche un message
    if (isset($geo['region']) && $geo['region'] !== '') {
        // On essaie de trouver les stations autour de la position GPS
        // En cherchant par le code postal si on l'a, sinon message d'erreur
        if ($code_postal !== '') {
            $stations = obtenirStations($code_postal);
        }
    }
} elseif ($code_postal !== '') {
    // Mode normal : recherche par code postal de la ville choisie
    $stations = obtenirStations($code_postal);
} elseif ($departement !== '') {
    // Fallback : si on n'a pas de code postal, on cherche tout le département
    $stations = obtenirStationsDepartement($departement);
}

// --- Tri des stations ---
// On trie par prix du Gazole (le plus courant) ou on laisse l'ordre de l'API

if ($tri === 'prix' && !empty($stations)) {
    usort($stations, function($a, $b) {
        // Les stations sans prix Gazole vont à la fin
        $prix_a = $a['prix']['Gazole'];
        $prix_b = $b['prix']['Gazole'];

        if ($prix_a === null && $prix_b === null) {
            return 0;
        }
        if ($prix_a === null) {
            return 1;
        }
        if ($prix_b === null) {
            return -1;
        }

        // Comparaison des prix : le moins cher en premier
        if ($prix_a < $prix_b) {
            return -1;
        }
        if ($prix_a > $prix_b) {
            return 1;
        }
        return 0;
    });
}

// --- Enregistrement de la consultation dans le CSV ---
// Et sauvegarde dans le cookie pour "Dernière recherche"

if ($ville !== '' && $code_postal !== '' && $departement !== '' && $region !== '') {
    // Écriture dans le fichier CSV côté serveur
    enregistrerConsultation($ville, $code_postal, $departement, $region);

    // Sauvegarde dans le cookie côté client (30 jours)
    // Format : "PARIS|75001|75"
    $valeur_cookie = $ville . '|' . $code_postal . '|' . $departement;
    setcookie('derniere_ville', $valeur_cookie, time() + 60 * 60 * 24 * 30, '/');
}

// --- Prix minimum toutes stations (pour mettre en valeur le moins cher) ---
// On cherche le prix minimum par carburant pour afficher le badge "moins cher"

$prix_min = [
    'Gazole' => null,
    'SP95'   => null,
    'E10'    => null,
    'SP98'   => null,
    'E85'    => null,
    'GPLc'   => null,
];

foreach ($stations as $s) {
    foreach ($prix_min as $carb => $min_actuel) {
        $p = $s['prix'][$carb];
        if ($p !== null) {
            if ($min_actuel === null || $p < $min_actuel) {
                $prix_min[$carb] = $p;
            }
        }
    }
}
?>

    <section>

        <h1>Votre recherche</h1>

        <article>
            <p>
                <strong>Zone :</strong>
                <?php
                if ($ville !== '') {
                    echo htmlspecialchars($ville);
                    if ($code_postal !== '') {
                        echo ' (' . htmlspecialchars($code_postal) . ')';
                    }
                } elseif ($departement !== '') {
                    echo 'Département ' . htmlspecialchars($departement);
                } elseif ($mode === 'geolocal') {
                    echo 'Autour de votre position';
                } else {
                    echo '—';
                }
                ?>
            </p>
            <p>
                <strong>Tri :</strong>
                <a href="resultats.php?<?= http_build_query(array_merge($_GET, ['tri' => 'prix'])) ?>"
                   class="<?php if ($tri === 'prix') { echo 'active'; } ?>">
                    Prix
                </a>
                <a href="resultats.php?<?= http_build_query(array_merge($_GET, ['tri' => 'defaut'])) ?>"
                   class="<?php if ($tri === 'defaut') { echo 'active'; } ?>">
                    Défaut
                </a>
            </p>
        </article>

    </section>

    <section>

        <h1>Stations proches</h1>

        <?php if (empty($stations)): ?>

            <article>
                <p>Aucune station trouvée pour cette recherche.</p>
                <p>
                    <a href="index.php" class="btn-secondary">Retour à la recherche</a>
                </p>
            </article>

        <?php else: ?>

            <?php foreach ($stations as $s): ?>

                <article>

                    <h2>
                        <?= htmlspecialchars($s['adresse']) ?>
                        —
                        <?= htmlspecialchars($s['ville']) ?>

                        <?php if ($s['h24'] === 'Oui'): ?>
                            <em class="badge badge-vert">24h/24</em>
                        <?php endif; ?>
                    </h2>

                    <p>
                        <strong>Code postal :</strong>
                        <?= htmlspecialchars($s['cp']) ?>
                    </p>

                    <ul class="prix-grille">

                        <?php foreach ($s['prix'] as $carburant => $prix): ?>

                            <li class="prix-tag <?php if ($prix !== null && $prix === $prix_min[$carburant]) { echo 'moins-cher'; } ?>">
                                <strong><?= htmlspecialchars($carburant) ?></strong>
                                <p>
                                    <?php
                                    if ($prix !== null) {
                                        echo number_format($prix, 3, ',', '') . '&nbsp;€/L';
                                    } else {
                                        echo '—&nbsp;€/L';
                                    }
                                    ?>
                                </p>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

    <section>
        <h1>Retour</h1>
        <article>
            <p>
                <a href="index.php" class="btn-secondary">Nouvelle recherche</a>
            </p>
        </article>
    </section>

<?php require 'include/footer_inc.php'; ?>