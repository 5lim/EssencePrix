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
 * On appelle obtenirTableDepartements() puis on filtre
 * uniquement les départements dont le slug de région
 * correspond à ce qu'on cherche.
 *
 * Exemple : obtenirDepartements('ile_de_france')
 * retourne ['75' => 'Paris', '77' => 'Seine-et-Marne', ...]
 *
 * @param string $slug_region  Le slug de la région (ex: 'ile_de_france')
 * @return array               Tableau [code_dept => nom_dept]
 */
function obtenirDepartements($slug_region)
{
    $table    = obtenirTableDepartements();
    $resultat = [];

    foreach ($table as $code => $infos) {
        // $infos[0] = nom du département
        // $infos[1] = slug de la région
        if ($infos[1] === $slug_region) {
            $resultat[$code] = $infos[0];
        }
    }

    // Tri par code département pour un affichage ordonné
    ksort($resultat);
    return $resultat;
}

/**
 * @brief Retourne toutes les villes d'un département depuis le CSV.
 *
 * On ouvre le fichier CSV ligne par ligne avec fgetcsv().
 * On compare les 2 premiers caractères du code INSEE
 * avec le code département demandé.
 *
 * Le CSV a 5 colonnes :
 * [0] code_commune_insee  ex: "75056"
 * [1] nom_de_la_commune   ex: "PARIS"
 * [2] code_postal         ex: "75001"
 * [3] latitude            ex: "48.856613"
 * [4] longitude           ex: "2.352222"
 *
 * @param string $code_dept  Code du département (ex: '75', '2A')
 * @return array             Tableau de ['nom', 'code_postal', 'latitude', 'longitude']
 */
function obtenirVilles($code_dept)
{
    // __DIR__ = dossier du fichier actuel (include/)
    // .. remonte d'un niveau pour atteindre data/
    $chemin = __DIR__ . '/../data/clean_postcodes.csv';
    $villes = [];

    $fichier = fopen($chemin, 'r');

    if ($fichier === false) {
        return $villes;
    }

    // On saute la première ligne (les en-têtes du CSV)
    fgetcsv($fichier);

    // On lit le fichier ligne par ligne jusqu'à la fin
    while (($ligne = fgetcsv($fichier)) !== false) {

        // Les 2 premiers caractères du code INSEE = code département
        // substr("75056", 0, 2) donne "75"
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

    // Tri alphabétique par nom de ville
    usort($villes, function($a, $b) {
        return strcmp($a['nom'], $b['nom']);
    });

    return $villes;
}

/* ============================================================
   GROUPE 3 — API CARBURANTS (JSON)
   On appelle l'API officielle du gouvernement pour récupérer
   les stations-service et leurs prix en temps réel.
   L'API retourne du JSON qu'on décode en tableau PHP.
   ============================================================ */

/**
 * @brief Récupère les stations-service d'une ville via l'API JSON.
 *
 * On appelle l'API data.economie.gouv.fr en filtrant par code postal.
 * file_get_contents() télécharge la réponse JSON.
 * json_decode() la transforme en tableau PHP.
 *
 * @param string $code_postal  Code postal de la ville (ex: '75001')
 * @return array               Tableau de stations avec adresse et prix
 */
function obtenirStations($code_postal)
{
    // urlencode() sécurise le code postal dans l'URL
    $url = 'https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/'
         . 'prix-des-carburants-en-france-flux-instantane-v2/records'
         . '?refine=cp%3A' . urlencode($code_postal)
         . '&limit=100';

    $reponse = file_get_contents($url);

    if ($reponse === false) {
        return [];
    }

    // true = on veut un tableau PHP, pas un objet
    $donnees = json_decode($reponse, true);

    if (!isset($donnees['results'])) {
        return [];
    }

    $stations = [];
    foreach ($donnees['results'] as $s) {
        $stations[] = formaterStation($s);
    }

    return $stations;
}

/**
 * @brief Récupère les stations de tout un département via l'API JSON.
 *
 * Même fonctionnement que obtenirStations() mais on filtre
 * par code département au lieu du code postal.
 *
 * @param string $code_dept  Code du département (ex: '75')
 * @return array             Tableau de stations formatées
 */
function obtenirStationsDepartement($code_dept)
{
    $url = 'https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/'
         . 'prix-des-carburants-en-france-flux-instantane-v2/records'
         . '?refine=dep%3A' . urlencode($code_dept)
         . '&limit=100';

    $reponse = file_get_contents($url);

    if ($reponse === false) {
        return [];
    }

    $donnees = json_decode($reponse, true);

    if (!isset($donnees['results'])) {
        return [];
    }

    $stations = [];
    foreach ($donnees['results'] as $s) {
        $stations[] = formaterStation($s);
    }

    return $stations;
}

/**
 * @brief Met en forme les données brutes d'une station depuis l'API.
 *
 * L'API retourne des champs techniques peu lisibles.
 * Cette fonction extrait uniquement ce qui nous intéresse :
 * adresse, ville, code postal, coordonnées GPS, prix par carburant.
 *
 * On utilise isset() pour chaque champ car une station peut ne pas
 * vendre tous les carburants — le champ est alors absent de la réponse.
 *
 * @param array $s  Données brutes d'une station (un élément de 'results')
 * @return array    Station avec les champs utiles seulement
 */
function formaterStation($s)
{
    // Les coordonnées GPS sont dans un sous-tableau 'geom'
    $lat = 0;
    $lon = 0;
    if (isset($s['geom'])) {
        $lat = $s['geom']['lat'];
        $lon = $s['geom']['lon'];
    }

    // Pour chaque carburant : null si la station ne le vend pas
    $prix_gazole = null;
    if (isset($s['gazole_prix'])) {
        $prix_gazole = $s['gazole_prix'];
    }

    $prix_sp95 = null;
    if (isset($s['sp95_prix'])) {
        $prix_sp95 = $s['sp95_prix'];
    }

    $prix_e10 = null;
    if (isset($s['e10_prix'])) {
        $prix_e10 = $s['e10_prix'];
    }

    $prix_sp98 = null;
    if (isset($s['sp98_prix'])) {
        $prix_sp98 = $s['sp98_prix'];
    }

    $prix_e85 = null;
    if (isset($s['e85_prix'])) {
        $prix_e85 = $s['e85_prix'];
    }

    $prix_gplc = null;
    if (isset($s['gplc_prix'])) {
        $prix_gplc = $s['gplc_prix'];
    }

    $adresse = 'Adresse inconnue';
    if (isset($s['adresse'])) {
        $adresse = $s['adresse'];
    }

    $ville = '';
    if (isset($s['ville'])) {
        $ville = $s['ville'];
    }

    $cp = '';
    if (isset($s['cp'])) {
        $cp = $s['cp'];
    }

    $h24 = null;
    if (isset($s['horaires_automate_24_24'])) {
        $h24 = $s['horaires_automate_24_24'];
    }

    return [
        'adresse'   => $adresse,
        'ville'     => $ville,
        'cp'        => $cp,
        'latitude'  => $lat,
        'longitude' => $lon,
        'h24'       => $h24,
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
   GROUPE 4 — GÉOLOCALISATION PAR IP (JSON)
   Pour le bouton "Près de moi", on récupère la position GPS
   approximative du visiteur depuis son adresse IP.
   C'est la même API que dans tech.php, on la réutilise ici.
   ============================================================ */

/**
 * @brief Récupère la position GPS approximative du visiteur depuis son IP.
 *
 * On appelle l'API ipinfo.io qui retourne un flux JSON.
 * Le champ 'loc' contient latitude et longitude séparées par une virgule.
 * Exemple de réponse : { "loc": "48.8534,2.3488", "city": "Paris", ... }
 *
 * @param string $ip  Adresse IP du visiteur (depuis $_SERVER['REMOTE_ADDR'])
 * @return array      Tableau avec latitude, longitude, ville — ou tableau vide si erreur
 */
function geolocalisationParIP($ip)
{
    $url     = 'https://ipinfo.io/' . $ip . '/geo';
    $reponse = file_get_contents($url);

    if ($reponse === false) {
        return [];
    }

    $infos = json_decode($reponse, true);

    // 'loc' contient "latitude,longitude" ex: "48.8534,2.3488"
    if (!isset($infos['loc'])) {
        return [];
    }

    // explode sépare la chaîne en tableau sur la virgule
    $coords = explode(',', $infos['loc']);

    if (count($coords) !== 2) {
        return [];
    }

    $ville  = '';
    $region = '';
    $pays   = '';

    if (isset($infos['city'])) {
        $ville = $infos['city'];
    }
    if (isset($infos['region'])) {
        $region = $infos['region'];
    }
    if (isset($infos['country'])) {
        $pays = $infos['country'];
    }

    return [
        'latitude'  => $coords[0],
        'longitude' => $coords[1],
        'ville'     => $ville,
        'region'    => $region,
        'pays'      => $pays,
    ];
}

/* ============================================================
   GROUPE 5 — STOCKAGE CSV DES CONSULTATIONS
   Le prof demande de sauvegarder les villes consultées
   dans un fichier CSV côté serveur avec la date et l'heure.
   On s'en sert ensuite pour la page statistiques.php.
   ============================================================ */

/**
 * @brief Enregistre une consultation dans le fichier CSV.
 *
 * On ajoute une ligne au fichier data/consultations.csv à chaque
 * fois qu'un utilisateur lance une recherche de stations.
 * Le mode 'a' (append) ajoute à la fin sans écraser le fichier.
 *
 * Format d'une ligne :
 * "2026-04-28 14:32:00","PARIS","75001","75","ile_de_france"
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

    // date('Y-m-d H:i:s') = date et heure actuelles ex: "2026-04-28 14:32:00"
    $ligne = [
        date('Y-m-d H:i:s'),
        $ville,
        $code_postal,
        $dept,
        $region,
    ];

    // fputcsv formate et écrit le tableau en ligne CSV
    fputcsv($fichier, $ligne);
    fclose($fichier);
}

/**
 * @brief Retourne le nombre total de consultations enregistrées.
 *
 * file() lit toutes les lignes dans un tableau.
 * count() retourne le nombre d'éléments du tableau.
 *
 * @return int  Nombre total de lignes dans le CSV (0 si fichier absent)
 */
function obtenirTotalConsultations()
{
    $chemin = __DIR__ . '/../data/consultations.csv';

    if (!file_exists($chemin)) {
        return 0;
    }

    $lignes = file($chemin, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return count($lignes);
}

/**
 * @brief Retourne les villes les plus consultées depuis le CSV.
 *
 * On lit le CSV ligne par ligne, on compte combien de fois
 * chaque ville apparaît, puis on trie par fréquence décroissante.
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

    while (($ligne = fgetcsv($fichier)) !== false) {

        // Format : [0]=date [1]=ville [2]=cp [3]=dept [4]=region
        if (count($ligne) < 5) {
            continue;
        }

        // Clé unique "VILLE|75" pour distinguer les homonymes
        $cle = $ligne[1] . '|' . $ligne[3];

        if (!isset($compteur[$cle])) {
            $compteur[$cle] = [
                'ville' => $ligne[1],
                'dept'  => $ligne[3],
                'nb'    => 0,
            ];
        }

        $compteur[$cle]['nb']++;
    }

    fclose($fichier);

    // Tri décroissant par nombre de consultations
    usort($compteur, function($a, $b) {
        return $b['nb'] - $a['nb'];
    });

    return array_slice($compteur, 0, $nb_max);
}