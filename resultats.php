<?php
require 'include/header_inc.php';
require 'include/functions.inc.php';

// --- Paramètres GET ---

$mode        = isset($_GET['mode'])        && $_GET['mode']        !== '' ? htmlspecialchars($_GET['mode'])        : '';
$ville       = isset($_GET['ville'])       && $_GET['ville']       !== '' ? htmlspecialchars($_GET['ville'])       : '';
$code_postal = isset($_GET['code_postal']) && $_GET['code_postal'] !== '' ? htmlspecialchars($_GET['code_postal']) : '';
$departement = isset($_GET['departement']) && $_GET['departement'] !== '' ? htmlspecialchars($_GET['departement']) : '';
$region      = isset($_GET['region'])      && $_GET['region']      !== '' ? htmlspecialchars($_GET['region'])      : '';
$tri         = isset($_GET['tri'])         && $_GET['tri']         !== '' ? htmlspecialchars($_GET['tri'])         : 'prix';

// Récupération du carburant sélectionné pour le filtre d'affichage
// Par défaut on affiche tout, sinon on filtre sur le carburant voulu
$carburant_filtre = isset($_GET['carburant']) && $_GET['carburant'] !== '' ? htmlspecialchars($_GET['carburant']) : '';

// --- Mode géolocalisation ---

$geo = [];
if ($mode === 'geolocal') {
    $ip  = $_SERVER['REMOTE_ADDR'];
    $geo = geolocalisationParIP($ip);

    if (!empty($geo)) {
        $ville = $geo['ville'];
    }
}

// --- Récupération des stations ---
// On sépare bien les deux cas : ville précise (par CP) ou département entier

$stations = [];

if ($mode === 'geolocal' && !empty($geo)) {
    // Mode géoloc : on cherche par le CP retourné par l'API IP si on l'a,
    // sinon on fait une recherche par département (les 2 premiers chiffres du CP)
    if ($code_postal !== '') {
        $stations = obtenirStations($code_postal);
    } elseif ($departement !== '') {
        $stations = obtenirStationsDepartement($departement);
    }
} elseif ($code_postal !== '') {
    $stations = obtenirStations($code_postal);
} elseif ($departement !== '') {
    $stations = obtenirStationsDepartement($departement);
}

// On récupère aussi le flux XML pour l'affichage complémentaire (exigence du sujet)
$xml_stations = null;
if ($code_postal !== '') {
    $xml_stations = obtenirStationsXML($code_postal);
}

// --- Tri ---

if ($tri === 'prix' && !empty($stations)) {
    $carb_tri = $carburant_filtre !== '' ? $carburant_filtre : 'Gazole';
    usort($stations, function($a, $b) use ($carb_tri) {
        $pa = $a['prix'][$carb_tri] ?? PHP_FLOAT_MAX;
        $pb = $b['prix'][$carb_tri] ?? PHP_FLOAT_MAX;
        return $pa <=> $pb;
    });
}

// --- Enregistrement consultation + cookie ---

if ($ville !== '' && $code_postal !== '' && $departement !== '' && $region !== '') {
    enregistrerConsultation($ville, $code_postal, $departement, $region);
    setcookie('derniere_ville', $ville . '|' . $code_postal . '|' . $departement, time() + 60 * 60 * 24 * 30, '/');
}

// --- Prix minimum par carburant (pour mettre en vert le moins cher) ---

$prix_min = ['Gazole' => null, 'SP95' => null, 'E10' => null, 'SP98' => null, 'E85' => null, 'GPLc' => null];

foreach ($stations as $s) {
    foreach ($prix_min as $carb => $min) {
        $p = $s['prix'][$carb];
        if ($p !== null && ($min === null || $p < $min)) {
            $prix_min[$carb] = $p;
        }
    }
}

// Liste des carburants pour les filtres
$carburants_dispo = ['Gazole', 'SP95', 'E10', 'SP98', 'E85', 'GPLc'];
?>

    <section>

        <h1>Votre recherche</h1>

        <article>
            <p>
                <strong>Zone :</strong>
                <?php
                if ($ville !== '') {
                    echo htmlspecialchars($ville);
                    if ($code_postal !== '') echo ' (' . htmlspecialchars($code_postal) . ')';
                } elseif ($departement !== '') {
                    echo 'Département ' . htmlspecialchars($departement);
                } elseif ($mode === 'geolocal') {
                    echo 'Autour de votre position';
                } else {
                    echo '—';
                }
                ?>
            </p>

            <nav aria-label="Filtres carburant">
                <a href="resultats.php?<?= http_build_query(array_merge($_GET, ['carburant' => ''])) ?>"
                   class="filtre-carburant <?= $carburant_filtre === '' ? 'active' : '' ?>">Tous</a>
                <?php foreach ($carburants_dispo as $c): ?>
                    <a href="resultats.php?<?= http_build_query(array_merge($_GET, ['carburant' => $c])) ?>"
                       class="filtre-carburant <?= $carburant_filtre === $c ? 'active' : '' ?>">
                        <?= htmlspecialchars($c) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <p style="margin-top:12px">
                <strong>Tri :</strong>
                <a href="resultats.php?<?= http_build_query(array_merge($_GET, ['tri' => 'prix'])) ?>"
                   class="<?= $tri === 'prix' ? 'active' : '' ?>">Prix croissant</a>
                &nbsp;
                <a href="resultats.php?<?= http_build_query(array_merge($_GET, ['tri' => 'defaut'])) ?>"
                   class="<?= $tri === 'defaut' ? 'active' : '' ?>">Par défaut</a>
            </p>
        </article>

    </section>

    <section>

        <h1>Stations proches <?php if (!empty($stations)) echo '(' . count($stations) . ')'; ?></h1>

        <?php if (empty($stations)): ?>

            <article>
                <p>Aucune station trouvée pour cette recherche.</p>
                <p><a href="index.php" class="btn-secondary">Retour à la recherche</a></p>
            </article>

        <?php else: ?>

            <?php foreach ($stations as $s):
                // Si un filtre carburant est actif et que la station ne le vend pas, on saute
                if ($carburant_filtre !== '' && ($s['prix'][$carburant_filtre] === null)) continue;
            ?>

                <article>

                    <h2>
                        <?= htmlspecialchars($s['adresse']) ?> — <?= htmlspecialchars($s['ville']) ?>
                        <?php if ($s['h24'] === 'Oui'): ?>
                            <em class="badge badge-vert">24h/24</em>
                        <?php endif; ?>
                    </h2>

                    <p><strong>Code postal :</strong> <?= htmlspecialchars($s['cp']) ?></p>

                    <ul class="prix-grille">
                        <?php foreach ($s['prix'] as $carb => $prix):
                            // Si filtre actif, on n'affiche que le carburant sélectionné
                            if ($carburant_filtre !== '' && $carb !== $carburant_filtre) continue;
                        ?>
                            <li class="prix-tag <?= ($prix !== null && $prix === $prix_min[$carb]) ? 'moins-cher' : '' ?>">
                                <strong><?= htmlspecialchars($carb) ?></strong>
                                <p>
                                    <?= $prix !== null ? number_format($prix, 3, ',', '') . '&nbsp;€/L' : '—' ?>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

    <?php if ($xml_stations !== null): ?>
    <section class="section-cartes">

        <h1>Données complémentaires (XML)</h1>

        <article>
            <h2>Extrait du flux XML officiel</h2>
            <?php
            $nb = 0;
            foreach ($xml_stations as $item):
                if ($nb >= 3) break;
                $adresse_xml = (string)($item->adresse ?? $item->fields->adresse ?? '');
                $ville_xml   = (string)($item->ville   ?? $item->fields->ville   ?? '');
                if ($adresse_xml === '' && $ville_xml === '') continue;
                $nb++;
            ?>
                <p><?= htmlspecialchars($adresse_xml) ?><?= $ville_xml !== '' ? ' — ' . htmlspecialchars($ville_xml) : '' ?></p>
            <?php endforeach; ?>
        </article>

    </section>
    <?php endif; ?>

    <section>
        <h1>Retour</h1>
        <article>
            <p><a href="index.php" class="btn-secondary">Nouvelle recherche</a></p>
        </article>
    </section>

<?php require 'include/footer_inc.php'; ?>