<?php
require 'include/header_inc.php';
require 'include/functions_inc.php';

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

$departements = [];
if ($region !== '') {
    $departements = obtenirDepartements($region);
}

$villes = [];
if ($departement !== '') {
    $villes = obtenirVilles($departement);
}

$derniere_ville = '';
$dernier_cp = '';
$dernier_dept = '';
$derniere_lat = '';
$derniere_lon = '';

if (isset($_COOKIE['derniere_ville']) && $_COOKIE['derniere_ville'] !== '') {
    $parties = explode('|', $_COOKIE['derniere_ville']);
    if (count($parties) === 5) {
        $derniere_ville = htmlspecialchars($parties[0]);
        $dernier_cp     = htmlspecialchars($parties[1]);
        $dernier_dept   = htmlspecialchars($parties[2]);
        $derniere_lat   = htmlspecialchars($parties[3]);
        $derniere_lon   = htmlspecialchars($parties[4]);
    }
    else {
        setcookie('derniere_ville', '', time() - 1, '/');
    }
}

$noms_regions = [
    'ile_de_france' => 'Île-de-France',
    'haut_de_france' => 'Hauts-de-France',
    'grand_est' => 'Grand Est',
    'normandie' => 'Normandie',
    'bretagne' => 'Bretagne',
    'pays_de_la_loire' => 'Pays de la Loire',
    'centre_val_de_loire' => 'Centre-Val de Loire',
    'bourgogne_franche_comte' => 'Bourgogne-Franche-Comté',
    'auvergne_rhone_alpes' => 'Auvergne-Rhône-Alpes',
    'provence_alpes_cote_azur' => 'Provence-Alpes-Côte d\'Azur',
    'occitanie' => 'Occitanie',
    'nouvelle_aquitaine' => 'Nouvelle-Aquitaine',
    'corse' => 'Corse',
];

if (isset($noms_regions[$region])) {
    $nom_region = $noms_regions[$region];
} else {
    $nom_region = '';
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
                <?php
                if ($derniere_ville !== '') {
                    echo '<p><strong>Ville :</strong> ' . $derniere_ville . '</p>';
                    echo '<p><strong>Code postal :</strong> ' . $dernier_cp . '</p>';
                    echo '<p>';
                    echo '<a href="resultats.php?departement=' . $dernier_dept . '&amp;ville=' . urlencode($derniere_ville) . '&amp;code_postal=' . $dernier_cp . '&amp;lat=' . $derniere_lat . '&amp;lon=' . $derniere_lon . '">';
                    echo 'Relancer cette recherche';
                    echo '</a>';
                    echo '</p>';
                } else {
                    echo '<p>Aucune recherche mémorisée.</p>';
                }
                ?>
            </article>
        </section>

    </section>

    <section id="carte">

        <h1>Sélectionnez votre région</h1>

        <article>

            <figure style="position: relative; display: inline-block;">

                <img src="images/france.jpg" alt="Carte des régions de France" usemap="#image-map" id="carte-regions-img"/>

                <map name="image-map">
                    <area shape="poly" coords="251,125,242,121,223,118,211,118,209,129,210,139,213,145,220,154,226,156,235,156,236,164,249,161,253,155,261,152,265,144,259,131"
                          href="index.php?region=ile_de_france#carte" alt="Île-de-France"
                          onmouseenter="afficher('ile_de_france')" onmouseleave="masquer('ile_de_france')"/>

                    <area shape="poly" coords="208,80,214,91,216,104,213,116,224,117,242,122,253,122,262,132,269,124,266,113,277,110,278,98,285,93,282,85,279,72,263,61,249,45,233,36,208,45"
                          href="index.php?region=haut_de_france#carte" alt="Hauts-de-France"
                          onmouseenter="afficher('haut_de_france')" onmouseleave="masquer('haut_de_france')"/>

                    <area shape="poly" coords="299,75,287,83,280,97,276,108,267,117,264,130,262,136,262,144,262,152,267,158,271,162,275,169,282,171,290,170,299,169,311,179,317,183,328,178,331,173,338,167,345,168,353,169,360,172,366,178,369,184,379,189,381,181,385,172,388,159,393,142,400,128,395,114,354,106,343,99,319,90,304,85"
                          href="index.php?region=grand_est#carte" alt="Grand Est"
                          onmouseenter="afficher('grand_est')" onmouseleave="masquer('grand_est')"/>

                    <area shape="poly" coords="214,117,214,102,214,92,206,80,188,86,170,93,168,99,168,105,161,111,148,109,137,105,131,94,114,92,114,104,118,114,120,120,119,133,124,140,131,144,145,147,158,144,162,152,174,149,178,158,187,160,188,154,192,150,188,142,195,138,202,138,207,130"
                          href="index.php?region=normandie#carte" alt="Normandie"
                          onmouseenter="afficher('normandie')" onmouseleave="masquer('normandie')"/>

                    <area shape="poly" coords="41,171,66,182,83,182,94,187,101,185,106,179,112,178,121,174,128,176,129,169,134,167,133,157,133,147,127,145,120,139,111,137,100,139,91,135,83,133,75,130,67,128,57,128,50,129,38,132,29,137,27,143,24,155,33,164"
                          href="index.php?region=bretagne#carte" alt="Bretagne"
                          onmouseenter="afficher('bretagne')" onmouseleave="masquer('bretagne')"/>

                    <area shape="poly" coords="146,236,145,228,143,219,138,212,147,212,148,208,159,205,165,205,167,196,171,187,177,184,182,182,186,175,189,166,189,159,179,158,175,151,164,153,159,145,144,148,134,147,134,165,129,176,122,175,106,180,95,188,95,200,101,212,109,227,114,236,126,242"
                          href="index.php?region=pays_de_la_loire#carte" alt="Pays de la Loire"
                          onmouseenter="afficher('pays_de_la_loire')" onmouseleave="masquer('pays_de_la_loire')"/>

                    <area shape="poly" coords="164,205,172,213,180,212,189,228,202,239,213,235,227,235,238,231,250,221,249,197,248,188,248,180,253,171,249,160,234,165,233,157,221,155,211,142,207,133,201,139,189,143,192,151,188,158,188,169,185,177,181,182,172,188,167,194"
                          href="index.php?region=centre_val_de_loire#carte" alt="Centre-Val de Loire"
                          onmouseenter="afficher('centre_val_de_loire')" onmouseleave="masquer('centre_val_de_loire')"/>

                    <area shape="poly" coords="251,220,250,200,247,181,254,171,250,161,254,155,261,154,271,162,274,170,281,172,297,170,315,183,328,179,337,168,350,170,359,173,365,179,366,186,366,193,363,204,356,213,349,222,343,230,337,238,324,240,317,234,307,232,304,246,299,240,293,242,283,247,279,239,278,232,269,223,262,225,255,225"
                          href="index.php?region=bourgogne_franche_comte#carte" alt="Bourgogne-Franche-Comté"
                          onmouseenter="afficher('bourgogne_franche_comte')" onmouseleave="masquer('bourgogne_franche_comte')"/>

                    <area shape="poly" coords="230,237,235,245,240,254,234,261,237,272,235,281,225,291,225,298,226,309,236,311,244,299,250,311,255,300,263,299,266,304,272,302,277,308,286,325,294,325,300,328,307,327,315,327,321,327,326,330,334,331,330,323,330,315,335,308,339,305,348,301,352,297,346,293,354,290,366,289,378,281,377,272,377,261,375,247,367,239,353,232,340,235,331,241,321,241,317,234,305,233,304,246,298,241,281,249,278,242,277,232,268,224,262,225,253,225,246,224"
                          href="index.php?region=auvergne_rhone_alpes#carte" alt="Auvergne-Rhône-Alpes"
                          onmouseenter="afficher('auvergne_rhone_alpes')" onmouseleave="masquer('auvergne_rhone_alpes')"/>

                    <area shape="poly" coords="347,293,352,298,346,302,337,308,332,313,331,323,335,328,331,334,323,331,314,330,304,329,308,339,303,346,301,354,295,364,308,364,324,370,336,372,343,372,352,372,362,371,367,370,372,365,379,357,388,350,396,342,398,333,398,326,387,328,379,326,374,322,373,313,373,305,375,299,367,295,363,291"
                          href="index.php?region=provence_alpes_cote_azur#carte" alt="Provence-Alpes-Côte d'Azur"
                          onmouseenter="afficher('provence_alpes_cote_azur')" onmouseleave="masquer('provence_alpes_cote_azur')"/>

                    <area shape="poly" coords="149,389,158,393,171,395,179,395,179,389,198,395,212,402,225,409,243,407,258,404,255,395,256,387,258,379,267,370,281,366,283,361,292,364,295,359,297,353,301,347,302,339,303,334,301,329,290,326,281,323,276,312,273,307,266,304,260,300,253,308,249,314,244,308,244,302,236,310,229,312,224,304,220,298,213,300,206,297,199,306,193,313,190,319,186,332,175,337,164,341,156,344,151,352,154,362,159,364,158,370,155,376,150,384"
                          href="index.php?region=occitanie#carte" alt="Occitanie"
                          onmouseenter="afficher('occitanie')" onmouseleave="masquer('occitanie')"/>

                    <area shape="poly" coords="104,366,114,351,117,337,121,314,125,291,126,271,123,250,118,240,139,238,145,238,144,230,143,224,139,211,147,213,151,206,165,208,170,213,180,215,186,223,189,230,196,236,201,240,207,238,213,236,223,236,229,240,235,247,238,253,232,262,236,271,235,279,228,288,221,296,216,299,205,297,199,306,192,312,188,317,185,323,185,331,178,334,158,344,151,355,150,362,159,363,153,371,149,384,135,383,128,383,120,379,114,377"
                          href="index.php?region=nouvelle_aquitaine#carte" alt="Nouvelle-Aquitaine"
                          onmouseenter="afficher('nouvelle_aquitaine')" onmouseleave="masquer('nouvelle_aquitaine')"/>

                    <area shape="poly" coords="411,342,395,356,391,365,387,380,390,396,400,409,414,413,418,402,419,389,424,373,427,355,419,342"
                          href="index.php?region=corse#carte" alt="Corse"
                          onmouseenter="afficher('corse')" onmouseleave="masquer('corse')"/>
                </map>

                <img class="region-overlay" data-region="ile_de_france"            src="images/ile_de_france.jpg"            alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="haut_de_france"           src="images/haut_de_france.jpg"           alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="grand_est"                src="images/grand_est.jpg"                alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="normandie"                src="images/normandie.jpg"                alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="bretagne"                 src="images/bretagne.jpg"                 alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="pays_de_la_loire"         src="images/pays_de_la_loire.jpg"         alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="centre_val_de_loire"      src="images/centre_val_de_loire.jpg"      alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="bourgogne_franche_comte"  src="images/bourgogne_franche_comte.jpg"  alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="auvergne_rhone_alpes"     src="images/auvergne_rhone_alpes.jpg"     alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="provence_alpes_cote_azur" src="images/provence_alpes_cote_azur.jpg" alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="occitanie"                src="images/occitanie.jpg"                alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="nouvelle_aquitaine"       src="images/nouvelle_aquitaine.jpg"       alt="" aria-hidden="true"/>
                <img class="region-overlay" data-region="corse"                    src="images/corse.jpg"                    alt="" aria-hidden="true"/>

            </figure>

            <p>
                <?php
                if ($nom_region !== '') {
                    echo 'Région sélectionnée : <strong>' . $nom_region . '</strong>';
                }
                ?>
            </p>

        </article>

    </section>

    <section>

        <h1>Recherche par ville</h1>

        <article>

            <?php
            if ($region !== '') {
                if ($departement !== '') {
                    echo '<p>';
                    echo '<strong>Département :</strong> ';
                    echo htmlspecialchars($departement) . ' — ';
                    if (isset($departements[$departement])) {
                        echo htmlspecialchars($departements[$departement]);
                    }
                    echo '</p>';
                    echo '<p>';
                    echo '<a href="index.php?region=' . htmlspecialchars($region) . '#carte" class="btn-secondary">Changer de département</a>';
                    echo '</p>';
                } else {
                    echo '<form method="get" action="index.php#villes">';
                    echo '<input type="hidden" name="region" value="' . htmlspecialchars($region) . '"/>';
                    echo '<p>';
                    echo '<label for="select-departement">Département</label>';
                    echo '<select id="select-departement" name="departement" required="required">';
                    echo '<option value=""> Choisir un département </option>';
                    foreach ($departements as $code => $nom) {
                        echo '<option value="' . htmlspecialchars($code) . '">';
                        echo htmlspecialchars($code) . ' — ' . htmlspecialchars($nom);
                        echo '</option>';
                    }
                    echo '</select>';
                    echo '</p>';
                    echo '<p><input type="submit" value="Valider" class="btn-primary"/></p>';
                    echo '</form>';
                }
            } else {
                echo '<p>Cliquez sur une région sur la carte pour commencer.</p>';
            }
            ?>

        </article>

        <?php
        if ($departement !== '') {
            echo '<article id="villes">';
            echo '<form method="get" action="resultats.php">';
            echo '<input type="hidden" name="region" value="' . htmlspecialchars($region) . '"/>';
            echo '<input type="hidden" name="departement" value="' . htmlspecialchars($departement) . '"/>';
            echo '<p>';
            echo '<label for="select-ville">Ville</label>';
            echo '<select id="select-ville" name="ville_cp" required="required">';
            echo '<option value=""> Choisir une ville </option>';
            foreach ($villes as $v) {
                $valeur = $v['nom'] . '|' . $v['code_postal'] . '|' . $v['latitude'] . '|' . $v['longitude'];
                echo '<option value="' . htmlspecialchars($valeur) . '">';
                echo htmlspecialchars($v['nom']) . ' (' . htmlspecialchars($v['code_postal']) . ')';
                echo '</option>';
            }
            echo '</select>';
            echo '</p>';
            echo '<p><input type="submit" value="Rechercher" class="btn-primary"/></p>';
            echo '</form>';
            echo '</article>';
        }
        ?>

    </section>

<?php require 'include/footer_inc.php'; ?>