<?php

require 'include/header_inc.php';

if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = null;
}

if (isset($_GET['region'])) {
    $region = $_GET['region'];
} else {
    $region = null;
}

if (isset($_GET['departement'])) {
    $departement = $_GET['departement'];
} else {
    $departement = null;
}

if (isset($_GET['ville'])) {
    $ville = $_GET['ville'];
} else {
    $ville = null;
}

if (isset($_GET['tri'])) {
    $tri = $_GET['tri'];
} else {
    $tri = 'distance';
}

if (isset($_GET['carburants'])) {
    $carburants = $_GET['carburants'];
} else {
    $carburants = [];
}

?>

    <section>

        <h1>Votre recherche</h1>

        <article>
            <p>
                <strong>Zone :</strong>
                <output id="zone-recherche">—</output>
            </p>
            <p>
                <strong>Tri :</strong>
                <a href="resultats.php?<?php echo http_build_query(array_merge($_GET, ['tri' => 'distance'])); ?>"
                   class="<?php if ($tri === 'distance') { echo 'active'; } ?>">
                    Distance
                </a>
                <a href="resultats.php?<?php echo http_build_query(array_merge($_GET, ['tri' => 'prix'])); ?>"
                   class="<?php if ($tri === 'prix') { echo 'active'; } ?>">
                    Prix
                </a>
            </p>
        </article>

        <article>
            <h2>Filtrer par carburant</h2>

            <p>
                <a href="#" class="filtre-carburant <?php if (in_array('gazole', $carburants)) { echo 'active'; } ?>" data-carburant="gazole">Gazole</a>
                <a href="#" class="filtre-carburant <?php if (in_array('sp95',   $carburants)) { echo 'active'; } ?>" data-carburant="sp95">SP95</a>
                <a href="#" class="filtre-carburant <?php if (in_array('e10',    $carburants)) { echo 'active'; } ?>" data-carburant="e10">E10</a>
                <a href="#" class="filtre-carburant <?php if (in_array('sp98',   $carburants)) { echo 'active'; } ?>" data-carburant="sp98">SP98</a>
                <a href="#" class="filtre-carburant <?php if (in_array('e85',    $carburants)) { echo 'active'; } ?>" data-carburant="e85">E85</a>
                <a href="#" class="filtre-carburant <?php if (in_array('gplc',   $carburants)) { echo 'active'; } ?>" data-carburant="gplc">GPLc</a>
            </p>
        </article>

    </section>

    <section>

        <h1>Stations proches</h1>

        <article>
            <h2>
                <b class="station-adresse">12 Rue de la Paix</b>
                —
                <b class="station-ville">Cergy</b>

                <em class="badge badge-vert">24h/24</em>
            </h2>

            <p>
                <strong>Code postal :</strong>
                <data class="station-cp" value="95000">95000</data>
            </p>

            <ul class="prix-grille">

                <li class="prix-tag">
                    <strong>Gazole</strong>
                    <p>—&nbsp;€/L</p>
                </li>

                <li class="prix-tag">
                    <strong>SP95</strong>
                    <p>—&nbsp;€/L</p>
                </li>

                <li class="prix-tag">
                    <strong>E10</strong>
                    <p>—&nbsp;€/L</p>
                </li>

                <li class="prix-tag">
                    <strong>SP98</strong>
                    <p>—&nbsp;€/L</p>
                </li>

                <li class="prix-tag">
                    <strong>E85</strong>
                    <p>—&nbsp;€/L</p>
                </li>

                <li class="prix-tag">
                    <strong>GPLc</strong>
                    <p>—&nbsp;€/L</p>
                </li>

            </ul>
        </article>

    </section>

<?php require 'include/footer_inc.php'; ?>