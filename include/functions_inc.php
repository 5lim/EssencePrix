<?php

/**
 * @file functions.inc.php
 * @brief Fonctions utilitaires du site CarburantsFrance
 *
 * Ce fichier contient toutes les fonctions PHP du projet :
 * lecture du CSV des communes, appel à l'API carburants en JSON,
 * géolocalisation par IP, et stockage CSV des consultations.
 *
 * @author Gamard Téo et Haroud Mohamed-Salim
 * @version 1.0
 */

/* ============================================================
   GROUPE 1 — TABLE DES RÉGIONS ET DÉPARTEMENTS
   Le CSV ne contient pas de colonne "région".
   On utilise donc un tableau PHP qui fait le lien entre
   chaque code département (ex: "75") et sa région (ex: "ile_de_france").
   Les 2 premiers caractères du code INSEE = code département.
   ============================================================ */

/**
 * @brief Retourne la table de correspondance département → région.
 *
 * Chaque entrée associe un code département (2 caractères)
 * à un tableau contenant le nom du département et le slug de sa région.
 * Le slug est la valeur utilisée dans les URLs (ex: ?region=ile_de_france).
 *
 * @return array  Tableau associatif [code_dept => [nom, slug_region]]
 */
function obtenirTableDepartements()
{
    return [
        '01' => ['Ain',                       'auvergne_rhone_alpes'],
        '02' => ['Aisne',                      'haut_de_france'],
        '03' => ['Allier',                     'auvergne_rhone_alpes'],
        '04' => ['Alpes-de-Haute-Provence',    'provence_alpes_cote_azur'],
        '05' => ['Hautes-Alpes',               'provence_alpes_cote_azur'],
        '06' => ['Alpes-Maritimes',            'provence_alpes_cote_azur'],
        '07' => ['Ardèche',                    'auvergne_rhone_alpes'],
        '08' => ['Ardennes',                   'grand_est'],
        '09' => ['Ariège',                     'occitanie'],
        '10' => ['Aube',                       'grand_est'],
        '11' => ['Aude',                       'occitanie'],
        '12' => ['Aveyron',                    'occitanie'],
        '13' => ['Bouches-du-Rhône',           'provence_alpes_cote_azur'],
        '14' => ['Calvados',                   'normandie'],
        '15' => ['Cantal',                     'auvergne_rhone_alpes'],
        '16' => ['Charente',                   'nouvelle_aquitaine'],
        '17' => ['Charente-Maritime',          'nouvelle_aquitaine'],
        '18' => ['Cher',                       'centre_val_de_loire'],
        '19' => ['Corrèze',                    'nouvelle_aquitaine'],
        '21' => ['Côte-d\'Or',                 'bourgogne_franche_comte'],
        '22' => ['Côtes-d\'Armor',             'bretagne'],
        '23' => ['Creuse',                     'nouvelle_aquitaine'],
        '24' => ['Dordogne',                   'nouvelle_aquitaine'],
        '25' => ['Doubs',                      'bourgogne_franche_comte'],
        '26' => ['Drôme',                      'auvergne_rhone_alpes'],
        '27' => ['Eure',                       'normandie'],
        '28' => ['Eure-et-Loir',               'centre_val_de_loire'],
        '29' => ['Finistère',                  'bretagne'],
        '2A' => ['Corse-du-Sud',               'corse'],
        '2B' => ['Haute-Corse',                'corse'],
        '30' => ['Gard',                       'occitanie'],
        '31' => ['Haute-Garonne',              'occitanie'],
        '32' => ['Gers',                       'occitanie'],
        '33' => ['Gironde',                    'nouvelle_aquitaine'],
        '34' => ['Hérault',                    'occitanie'],
        '35' => ['Ille-et-Vilaine',            'bretagne'],
        '36' => ['Indre',                      'centre_val_de_loire'],
        '37' => ['Indre-et-Loire',             'centre_val_de_loire'],
        '38' => ['Isère',                      'auvergne_rhone_alpes'],
        '39' => ['Jura',                       'bourgogne_franche_comte'],
        '40' => ['Landes',                     'nouvelle_aquitaine'],
        '41' => ['Loir-et-Cher',               'centre_val_de_loire'],
        '42' => ['Loire',                      'auvergne_rhone_alpes'],
        '43' => ['Haute-Loire',                'auvergne_rhone_alpes'],
        '44' => ['Loire-Atlantique',           'pays_de_la_loire'],
        '45' => ['Loiret',                     'centre_val_de_loire'],
        '46' => ['Lot',                        'occitanie'],
        '47' => ['Lot-et-Garonne',             'nouvelle_aquitaine'],
        '48' => ['Lozère',                     'occitanie'],
        '49' => ['Maine-et-Loire',             'pays_de_la_loire'],
        '50' => ['Manche',                     'normandie'],
        '51' => ['Marne',                      'grand_est'],
        '52' => ['Haute-Marne',                'grand_est'],
        '53' => ['Mayenne',                    'pays_de_la_loire'],
        '54' => ['Meurthe-et-Moselle',         'grand_est'],
        '55' => ['Meuse',                      'grand_est'],
        '56' => ['Morbihan',                   'bretagne'],
        '57' => ['Moselle',                    'grand_est'],
        '58' => ['Nièvre',                     'bourgogne_franche_comte'],
        '59' => ['Nord',                       'haut_de_france'],
        '60' => ['Oise',                       'haut_de_france'],
        '61' => ['Orne',                       'normandie'],
        '62' => ['Pas-de-Calais',              'haut_de_france'],
        '63' => ['Puy-de-Dôme',               'auvergne_rhone_alpes'],
        '64' => ['Pyrénées-Atlantiques',       'nouvelle_aquitaine'],
        '65' => ['Hautes-Pyrénées',            'occitanie'],
        '66' => ['Pyrénées-Orientales',        'occitanie'],
        '67' => ['Bas-Rhin',                   'grand_est'],
        '68' => ['Haut-Rhin',                  'grand_est'],
        '69' => ['Rhône',                      'auvergne_rhone_alpes'],
        '70' => ['Haute-Saône',               'bourgogne_franche_comte'],
        '71' => ['Saône-et-Loire',            'bourgogne_franche_comte'],
        '72' => ['Sarthe',                     'pays_de_la_loire'],
        '73' => ['Savoie',                     'auvergne_rhone_alpes'],
        '74' => ['Haute-Savoie',              'auvergne_rhone_alpes'],
        '75' => ['Paris',                      'ile_de_france'],
        '76' => ['Seine-Maritime',             'normandie'],
        '77' => ['Seine-et-Marne',            'ile_de_france'],
        '78' => ['Yvelines',                   'ile_de_france'],
        '79' => ['Deux-Sèvres',              'nouvelle_aquitaine'],
        '80' => ['Somme',                      'haut_de_france'],
        '81' => ['Tarn',                       'occitanie'],
        '82' => ['Tarn-et-Garonne',           'occitanie'],
        '83' => ['Var',                        'provence_alpes_cote_azur'],
        '84' => ['Vaucluse',                   'provence_alpes_cote_azur'],
        '85' => ['Vendée',                     'pays_de_la_loire'],
        '86' => ['Vienne',                     'nouvelle_aquitaine'],
        '87' => ['Haute-Vienne',             'nouvelle_aquitaine'],
        '88' => ['Vosges',                     'grand_est'],
        '89' => ['Yonne',                      'bourgogne_franche_comte'],
        '90' => ['Territoire de Belfort',      'bourgogne_franche_comte'],
        '91' => ['Essonne',                    'ile_de_france'],
        '92' => ['Hauts-de-Seine',           'ile_de_france'],
        '93' => ['Seine-Saint-Denis',          'ile_de_france'],
        '94' => ['Val-de-Marne',             'ile_de_france'],
        '95' => ['Val-d\'Oise',              'ile_de_france'],
    ];
}

/* ============================================================
   GROUPE 2 — LECTURE DU CSV DES COMMUNES
   On lit clean_postcodes.csv pour remplir les selects
   département et ville dans index.php.
   ============================================================ */

/**
 * @brief Retourne les départements qui appartiennent à une région.
 *
 * @param string $slug_region  Le slug de la région (ex: 'ile_de_france')
 * @return array               Tableau [code_dept => nom_dept]
 */
function obtenirDepartements($slug_region)
{
    $table    = obtenirTableDepartements();
    $resultat = [];

    foreach ($table as $code => $infos) {
        if ($infos[1] === $slug_region) {
            $resultat[$code] = $infos[0];
        }
    }

    ksort($resultat);
    return $resultat;
}

/**
 * @brief Retourne toutes les villes d'un département depuis le CSV.
 *
 * @param string $code_dept  Code du département (ex: '75', '2A')
 * @return array             Tableau de ['nom', 'code_postal', 'latitude', 'longitude']
 */
function obtenirVilles($code_dept)
{
    $chemin = __DIR__ . '/../data/clean_postcodes.csv';
    $villes = [];

    $fichier = fopen($chemin, 'r');

    if ($fichier === false) {
        return $villes;
    }

    fgetcsv($fichier, 0, ',', '"', '\\');

    while (($ligne = fgetcsv($fichier, 0, ',', '"', '\\')) !== false) {

        $dept_de_la_ligne = substr($ligne[0], 0, 2);

        if ($dept_de_la_ligne === $code_dept) {
            $villes[] = [
                'nom'         => $ligne[1],
                'code_postal' => $ligne[2],
                'latitude'    => $ligne[3],
                'longitude'   => $ligne[4],
            ];
        }
    }

    fclose($fichier);

    usort($villes, function($a, $b) {
        return strcmp($a['nom'], $b['nom']);
    });

    return $villes;
}

/* ============================================================
   GROUPE 3 — API CARBURANTS (JSON)
   ============================================================ */

/**
 * @brief Récupère les stations-service d'une ville via l'API JSON.
 *
 * @param string $code_postal  Code postal de la ville (ex: '75001')
 * @return array               Tableau de stations avec adresse et prix
 */
function obtenirStations($code_postal)
{
    $url = 'https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/'
         . 'prix-des-carburants-en-france-flux-instantane-v2/records'
         . '?refine=cp%3A' . urlencode($code_postal)
         . '&limit=100';

    $reponse = file_get_contents($url);

    if ($reponse === false) {
        return [];
    }

    $donnees = json_decode($reponse, true);

    if (!isset($donnees['results'])) {
        return [];
    }

    return array_map('formaterStation', $donnees['results']);
}

/**
 * @brief Récupère les stations de tout un département via l'API JSON, triées par l'API.
 *
 * @param string $code_dept  Code du département (ex: '75')
 * @param string $order_by   Tri : 'Gazole', 'SP95', 'E10', 'SP98', 'E85', 'GPLc'
 * @return array             Tableau de stations formatées
 */
function obtenirStationsDepartement($code_dept, $order_by = 'Gazole')
{
    $champs_tri = [
        'Gazole' => 'gazole_prix',
        'SP95'   => 'sp95_prix',
        'E10'    => 'e10_prix',
        'SP98'   => 'sp98_prix',
        'E85'    => 'e85_prix',
        'GPLc'   => 'gplc_prix',
    ];

    if (isset($champs_tri[$order_by])) {
        $tri = $champs_tri[$order_by];
    } else {
        $tri = 'gazole_prix';
    }

    $url = 'https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/'
         . 'prix-des-carburants-en-france-flux-instantane-v2/records'
         . '?refine=dep:' . urlencode($code_dept)
         . '&limit=100';

    $reponse = file_get_contents($url);

    if ($reponse === false) {
        return [];
    }

    $donnees = json_decode($reponse, true);

    if (!isset($donnees['results'])) {
        return [];
    }

    $stations = array_map('formaterStation', $donnees['results']);

    // Tri en PHP par le carburant choisi
    usort($stations, function($a, $b) use ($order_by) {
        $pa = $a['prix'][$order_by];
        $pb = $b['prix'][$order_by];
        if ($pa === null && $pb === null) return 0;
        if ($pa === null) return 1;
        if ($pb === null) return -1;
        return $pa <=> $pb;
    });

    return $stations;
}

/**
 * @brief Récupère les stations dans un rayon autour d'un point GPS, triées par l'API.
 *
 * @param float  $lat       Latitude du centre de la recherche
 * @param float  $lon       Longitude du centre de la recherche
 * @param int    $rayon_km  Rayon de recherche en kilomètres (défaut: 10)
 * @param string $order_by  Tri : 'distance', 'Gazole', 'SP95', 'E10', 'SP98', 'E85', 'GPLc'
 * @return array            Tableau de stations formatées
 */
function obtenirStationsProches($lat, $lon, $rayon_km = 10, $order_by = 'distance')
{
    $where = "within_distance(geom, geom'POINT($lon $lat)', {$rayon_km}km)";

    $champs_tri = [
        'distance' => "distance(geom, geom'POINT($lon $lat)')",
        'Gazole'   => 'gazole_prix',
        'SP95'     => 'sp95_prix',
        'E10'      => 'e10_prix',
        'SP98'     => 'sp98_prix',
        'E85'      => 'e85_prix',
        'GPLc'     => 'gplc_prix',
    ];

    if (isset($champs_tri[$order_by])) {
        $tri = $champs_tri[$order_by];
    } else {
        $tri = $champs_tri['distance'];
    }

    $url = 'https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/'
         . 'prix-des-carburants-en-france-flux-instantane-v2/records'
         . '?where='    . rawurlencode($where)
         . '&order_by=' . rawurlencode($tri)
         . '&limit=100';

    $reponse = file_get_contents($url);

    if ($reponse === false) {
        return [];
    }

    $donnees = json_decode($reponse, true);

    if (!isset($donnees['results'])) {
        return [];
    }

    return array_map('formaterStation', $donnees['results']);
}

/**
 * @brief Met en forme les données brutes d'une station depuis l'API.
 *
 * @param array $s  Données brutes d'une station (un élément de 'results')
 * @return array    Station avec les champs utiles seulement
 */
function formaterStation($s)
{
    $lat = 0;
    $lon = 0;
    if (isset($s['geom'])) {
        $lat = $s['geom']['lat'];
        $lon = $s['geom']['lon'];
    }

    if (isset($s['adresse'])) {
        $adresse = $s['adresse'];
    } else {
        $adresse = 'Adresse inconnue';
    }

    if (isset($s['ville'])) {
        $ville = $s['ville'];
    } else {
        $ville = '';
    }

    if (isset($s['cp'])) {
        $cp = $s['cp'];
    } else {
        $cp = '';
    }

    if (isset($s['horaires_automate_24_24'])) {
        $h24 = $s['horaires_automate_24_24'];
    } else {
        $h24 = null;
    }

    if (isset($s['services_service']) && is_array($s['services_service'])) {
        $services = implode(', ', $s['services_service']);
    } elseif (isset($s['services_service'])) {
        $services = $s['services_service'];
    } else {
        $services = '';
    }

    if (isset($s['horaires']) && is_array($s['horaires'])) {
        $horaires = $s['horaires'];
    } elseif (isset($s['horaires'])) {
        $horaires = json_decode($s['horaires'], true);
    } else {
        $horaires = null;
    }

    if (isset($s['gazole_prix'])) {
        $prix_gazole = $s['gazole_prix'];
    } else {
        $prix_gazole = null;
    }

    if (isset($s['sp95_prix'])) {
        $prix_sp95 = $s['sp95_prix'];
    } else {
        $prix_sp95 = null;
    }

    if (isset($s['e10_prix'])) {
        $prix_e10 = $s['e10_prix'];
    } else {
        $prix_e10 = null;
    }

    if (isset($s['sp98_prix'])) {
        $prix_sp98 = $s['sp98_prix'];
    } else {
        $prix_sp98 = null;
    }

    if (isset($s['e85_prix'])) {
        $prix_e85 = $s['e85_prix'];
    } else {
        $prix_e85 = null;
    }

    if (isset($s['gplc_prix'])) {
        $prix_gplc = $s['gplc_prix'];
    } else {
        $prix_gplc = null;
    }

    return [
        'adresse'   => $adresse,
        'ville'     => $ville,
        'cp'        => $cp,
        'latitude'  => $lat,
        'longitude' => $lon,
        'h24'       => $h24,
        'services'  => $services,
        'horaires'  => $horaires,
        'prix'      => [
            'Gazole' => $prix_gazole,
            'SP95'   => $prix_sp95,
            'E10'    => $prix_e10,
            'SP98'   => $prix_sp98,
            'E85'    => $prix_e85,
            'GPLc'   => $prix_gplc,
        ],
    ];
}

/* ============================================================
   GROUPE 3b — API CARBURANTS (XML)
   ============================================================ */

/**
 * @brief Récupère les stations via le flux XML officiel.
 *
 * @param string $code_postal  Code postal de la ville (ex: '75001')
 * @return SimpleXMLElement|null  Objet XML ou null si erreur
 */
function obtenirStationsXML($code_postal)
{
    $url = 'https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/'
         . 'prix-des-carburants-en-france-flux-instantane-v2/records'
         . '?refine=cp%3A' . urlencode($code_postal)
         . '&limit=10&format=xml';

    $reponse = file_get_contents($url);

    if ($reponse === false || !str_starts_with(trim($reponse), '<')) {
        return null;
    }

    $xml = simplexml_load_string($reponse);

    if ($xml === false) {
        return null;
    }

    if (isset($xml->record)) {
        return $xml->record;
    } else {
        return null;
    }
}

/* ============================================================
   GROUPE 4 — GÉOLOCALISATION PAR IP (JSON)
   ============================================================ */

/**
 * @brief Récupère la position GPS approximative du visiteur depuis son IP.
 *
 * @param string $ip  Adresse IP du visiteur
 * @return array      Tableau avec latitude, longitude, ville, code_postal
 */
function geolocalisationParIP($ip)
{
    $url     = 'https://ipinfo.io/' . $ip . '/geo';
    $reponse = file_get_contents($url);

    if ($reponse === false) {
        return [];
    }

    $infos = json_decode($reponse, true);

    if (!isset($infos['loc'])) {
        return [];
    }

    $coords = explode(',', $infos['loc']);

    if (count($coords) !== 2) {
        return [];
    }

    if (isset($infos['city'])) {
        $ville = $infos['city'];
    } else {
        $ville = '';
    }

    if (isset($infos['region'])) {
        $region = $infos['region'];
    } else {
        $region = '';
    }

    if (isset($infos['country'])) {
        $pays = $infos['country'];
    } else {
        $pays = '';
    }

    if (isset($infos['postal'])) {
        $code_postal = $infos['postal'];
    } else {
        $code_postal = '';
    }

    return [
        'latitude'    => $coords[0],
        'longitude'   => $coords[1],
        'ville'       => $ville,
        'region'      => $region,
        'pays'        => $pays,
        'code_postal' => $code_postal,
    ];
}

/* ============================================================
   GROUPE 5 — STOCKAGE CSV DES CONSULTATIONS
   ============================================================ */

/**
 * @brief Enregistre une consultation dans le fichier CSV.
 *
 * @param string $ville       Nom de la ville consultée
 * @param string $code_postal Code postal de la ville
 * @param string $dept        Code du département
 * @param string $region      Slug de la région
 * @return void
 */
function enregistrerConsultation($ville, $code_postal, $dept, $region)
{
    $chemin  = __DIR__ . '/../data/consultations.csv';
    $fichier = fopen($chemin, 'a');

    if ($fichier === false) {
        return;
    }

    fputcsv($fichier, [date('Y-m-d H:i:s'), $ville, $code_postal, $dept, $region], ',', '"', '\\');
    fclose($fichier);
}

/**
 * @brief Retourne le nombre total de consultations enregistrées.
 *
 * @return int
 */
function obtenirTotalConsultations()
{
    $chemin = __DIR__ . '/../data/consultations.csv';

    if (!file_exists($chemin)) {
        return 0;
    }

    return count(file($chemin, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}

/**
 * @brief Retourne les villes les plus consultées depuis le CSV.
 *
 * @param int $nb_max  Nombre de villes à retourner (défaut: 10)
 * @return array       Tableau de ['ville'=>..., 'dept'=>..., 'nb'=>...]
 */
function obtenirTopVilles($nb_max = 10)
{
    $chemin = __DIR__ . '/../data/consultations.csv';

    if (!file_exists($chemin)) {
        return [];
    }

    $fichier = fopen($chemin, 'r');

    if ($fichier === false) {
        return [];
    }

    $compteur = [];

    while (($ligne = fgetcsv($fichier, 0, ',', '"', '\\')) !== false) {

        if (count($ligne) < 5) {
            continue;
        }

        $cle = $ligne[1] . '|' . $ligne[3];

        if (!isset($compteur[$cle])) {
            $compteur[$cle] = ['ville' => $ligne[1], 'dept' => $ligne[3], 'nb' => 0];
        }

        $compteur[$cle]['nb']++;
    }

    fclose($fichier);

    usort($compteur, function($a, $b) {
        return $b['nb'] - $a['nb'];
    });

    return array_slice($compteur, 0, $nb_max);
}


function getIP(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
    return $ip;
}
?>