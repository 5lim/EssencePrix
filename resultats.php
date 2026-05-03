<?php
require 'include/header_inc.php';
require 'include/functions_inc.php';


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
    $tri = 'distance';
}

if (isset($_GET['lat']) && is_numeric($_GET['lat'])) {
    $lat = (float)$_GET['lat'];
} else {
    $lat = null;
}

if (isset($_GET['lon']) && is_numeric($_GET['lon'])) {
    $lon = (float)$_GET['lon'];
} else {
    $lon = null;
}

if (isset($_GET['rayon']) && is_numeric($_GET['rayon'])) {
    $rayon = (int)$_GET['rayon'];
} else {
    $rayon = 10;
}

if (isset($_GET['carburant']) && $_GET['carburant'] !== '') {
    $carburant_filtre = htmlspecialchars($_GET['carburant']);
} else {
    $carburant_filtre = '';
}

if (isset($_GET['ville_cp']) && $_GET['ville_cp'] !== '') {
    $parties = explode('|', $_GET['ville_cp']);
    if (count($parties) === 4) {
        $ville       = htmlspecialchars($parties[0]);
        $code_postal = htmlspecialchars($parties[1]);
        $lat         = (float)$parties[2];
        $lon         = (float)$parties[3];
    }
}


$geo = [];
if ($mode === 'geolocal') {
    $ip  = getIP();
    $geo = geolocalisationParIP($ip);

    if (!empty($geo)) {
        $ville       = $geo['ville'];
        $code_postal = $geo['code_postal'];
        $lat         = (float)$geo['latitude'];
        $lon         = (float)$geo['longitude'];
    }
}


$carburants_dispo = ['Gazole', 'SP95', 'E10', 'SP98', 'E85', 'GPLc'];

if ($lat !== null && $lon !== null) {
    $tri_valides = array_merge(['distance'], $carburants_dispo);
    if (!in_array($tri, $tri_valides)) {
        $tri = 'distance';
    }
    $stations = obtenirStationsProches($lat, $lon, $rayon, $tri);
} elseif ($code_postal !== '') {
    $stations = obtenirStations($code_postal);
} elseif ($departement !== '') {
    if (!in_array($tri, $carburants_dispo)) {
        $tri = 'Gazole';
    }
    $stations = obtenirStationsDepartement($departement, $tri);
} else {
    $stations = [];
}

$xml_stations = null;
if ($code_postal !== '') {
    $xml_stations = obtenirStationsXML($code_postal);
}


if ($ville !== '' && $code_postal !== '' && $departement !== '' && $region !== '') {
    enregistrerConsultation($ville, $code_postal, $departement, $region);
    setcookie('derniere_ville', $ville . '|' . $code_postal . '|' . $departement . '|' . $lat . '|' . $lon, time() + 60 * 60 * 24 * 30, '/');
}


$prix_min = ['Gazole' => null, 'SP95' => null, 'E10' => null, 'SP98' => null, 'E85' => null, 'GPLc' => null];

foreach ($stations as $s) {
    foreach ($prix_min as $carb => $min) {
        $p = $s['prix'][$carb];
        if ($p !== null && ($min === null || $p < $min)) {
            $prix_min[$carb] = $p;
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

            <?php
            if ($lat !== null && $lon !== null) {
                echo '<form method="get" action="resultats.php" style="margin-top:12px">';
                echo '<input type="hidden" name="mode"        value="' . htmlspecialchars($mode) . '">';
                echo '<input type="hidden" name="ville"       value="' . htmlspecialchars($ville) . '">';
                echo '<input type="hidden" name="code_postal" value="' . htmlspecialchars($code_postal) . '">';
                echo '<input type="hidden" name="departement" value="' . htmlspecialchars($departement) . '">';
                echo '<input type="hidden" name="region"      value="' . htmlspecialchars($region) . '">';
                echo '<input type="hidden" name="lat"         value="' . $lat . '">';
                echo '<input type="hidden" name="lon"         value="' . $lon . '">';
                echo '<input type="hidden" name="tri"         value="' . htmlspecialchars($tri) . '">';
                echo '<input type="hidden" name="carburant"   value="' . htmlspecialchars($carburant_filtre) . '">';
                echo '<label for="select-rayon">Rayon de recherche</label>';
                echo '<select id="select-rayon" name="rayon">';

                if ($rayon === 5) {
                    echo '<option value="5" selected>5 km</option>';
                } else {
                    echo '<option value="5">5 km</option>';
                }

                if ($rayon === 10) {
                    echo '<option value="10" selected>10 km</option>';
                } else {
                    echo '<option value="10">10 km</option>';
                }

                if ($rayon === 20) {
                    echo '<option value="20" selected>20 km</option>';
                } else {
                    echo '<option value="20">20 km</option>';
                }

                if ($rayon === 50) {
                    echo '<option value="50" selected>50 km</option>';
                } else {
                    echo '<option value="50">50 km</option>';
                }

                echo '</select>';
                echo '<input type="submit" value="Appliquer" class="btn-primary">';
                echo '</form>';
            }
            ?>

            <p style="margin-top:12px; margin-bottom:6px"><strong>Filtrer par carburant :</strong></p>
            <nav aria-label="Filtres carburant">
                <?php
                if ($carburant_filtre === '') {
                    $classe_tous = 'filtre-carburant active';
                } else {
                    $classe_tous = 'filtre-carburant';
                }
                echo '<a href="resultats.php?' . http_build_query(array_merge($_GET, ['carburant' => ''])) . '" class="' . $classe_tous . '">Tous</a>';

                foreach ($carburants_dispo as $c) {
                    if ($carburant_filtre === $c) {
                        $classe = 'filtre-carburant active';
                    } else {
                        $classe = 'filtre-carburant';
                    }
                    echo '<a href="resultats.php?' . http_build_query(array_merge($_GET, ['carburant' => $c])) . '" class="' . $classe . '">';
                    echo htmlspecialchars($c);
                    echo '</a>';
                }
                ?>
            </nav>

            <p style="margin-top:16px; margin-bottom:6px"><strong>Trier par :</strong></p>
            <nav aria-label="Tri des résultats">
                <?php
                if ($lat !== null && $lon !== null) {
                    if ($tri === 'distance') {
                        $classe = 'filtre-carburant active';
                    } else {
                        $classe = 'filtre-carburant';
                    }
                    echo '<a href="resultats.php?' . http_build_query(array_merge($_GET, ['tri' => 'distance'])) . '" class="' . $classe . '">Distance</a>';
                }

                foreach ($carburants_dispo as $c) {
                    if ($tri === $c) {
                        $classe = 'filtre-carburant active';
                    } else {
                        $classe = 'filtre-carburant';
                    }
                    echo '<a href="resultats.php?' . http_build_query(array_merge($_GET, ['tri' => $c])) . '" class="' . $classe . '">';
                    echo htmlspecialchars($c);
                    echo '</a>';
                }
                ?>
            </nav>

        </article>

    </section>

    <section>

        <h1>Stations proches
            <?php
            if (!empty($stations)) {
                echo '(' . count($stations) . ')';
            }
            ?>
        </h1>

        <?php
        if (empty($stations)) {
            echo '<article>';
            echo '<p>Aucune station trouvée pour cette recherche.</p>';
            echo '<p><a href="index.php" class="btn-secondary">Retour à la recherche</a></p>';
            echo '</article>';
        } else {
            foreach ($stations as $s) {
                if ($carburant_filtre !== '' && $s['prix'][$carburant_filtre] === null) {
                    continue;
                }

                echo '<article>';
                echo '<h2>';
                echo htmlspecialchars($s['adresse']) . ' — ' . htmlspecialchars($s['ville']);
                if ($s['h24'] === 'Oui') {
                    echo ' <em class="badge badge-vert">24h/24</em>';
                }
                echo '</h2>';

                echo '<p><strong>Code postal :</strong> ' . htmlspecialchars($s['cp']) . '</p>';

                echo '<ul class="prix-grille">';
                foreach ($s['prix'] as $carb => $prix) {
                    if ($carburant_filtre !== '' && $carb !== $carburant_filtre) {
                        continue;
                    }
                    if ($prix !== null && $prix === $prix_min[$carb]) {
                        $classe_min = 'prix-tag moins-cher';
                    } else {
                        $classe_min = 'prix-tag';
                    }
                    echo '<li class="' . $classe_min . '">';
                    echo '<strong>' . htmlspecialchars($carb) . '</strong>';
                    echo '<p>';
                    if ($prix !== null) {
                        echo number_format($prix, 3, ',', '') . '&nbsp;€/L';
                    } else {
                        echo '—';
                    }
                    echo '</p>';
                    echo '</li>';
                }
                echo '</ul>';

                if ($s['services'] !== '') {
                    echo '<p><strong>Services :</strong> ' . htmlspecialchars($s['services']) . '</p>';
                }

                if (!empty($s['horaires']['jour'])) {
                    echo '<p><strong>Horaires :</strong></p>';
                    echo '<ul>';
                    foreach ($s['horaires']['jour'] as $jour) {
                        echo '<li>';
                        echo htmlspecialchars($jour['@nom']) . ' : ';
                        if ($jour['@ferme'] === 'Oui') {
                            echo 'Fermé';
                        } elseif (isset($jour['horaire']['@ouverture'], $jour['horaire']['@fermeture'])) {
                            echo htmlspecialchars($jour['horaire']['@ouverture']);
                            echo ' – ';
                            echo htmlspecialchars($jour['horaire']['@fermeture']);
                        } else {
                            echo '—';
                        }
                        echo '</li>';
                    }
                    echo '</ul>';
                }

                echo '</article>';
            }
        }
        ?>

    </section>

    <?php
    if ($xml_stations !== null) {
        echo '<section class="section-cartes">';
        echo '<h1>Données complémentaires (XML)</h1>';
        echo '<article>';
        echo '<h2>Extrait du flux XML officiel</h2>';
        $nb = 0;
        foreach ($xml_stations as $item) {
            if ($nb >= 3) {
                break;
            }

            if (isset($item->adresse)) {
                $adresse_xml = (string)$item->adresse;
            } elseif (isset($item->fields->adresse)) {
                $adresse_xml = (string)$item->fields->adresse;
            } else {
                $adresse_xml = '';
            }

            if (isset($item->ville)) {
                $ville_xml = (string)$item->ville;
            } elseif (isset($item->fields->ville)) {
                $ville_xml = (string)$item->fields->ville;
            } else {
                $ville_xml = '';
            }

            if ($adresse_xml === '' && $ville_xml === '') {
                continue;
            }
            $nb++;
            echo '<p>' . htmlspecialchars($adresse_xml);
            if ($ville_xml !== '') {
                echo ' — ' . htmlspecialchars($ville_xml);
            }
            echo '</p>';
        }
        echo '</article>';
        echo '</section>';
    }
    ?>

    <section>
        <h1>Retour</h1>
        <article>
            <p><a href="index.php" class="btn-secondary">Nouvelle recherche</a></p>
        </article>
    </section>

<?php require 'include/footer_inc.php'; ?>