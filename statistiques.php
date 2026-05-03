<?php
require 'include/header_inc.php';
require 'include/functions_inc.php';

$total       = obtenirTotalConsultations();
$top_villes  = obtenirTopVilles(10);

$ville_top = '';
if (!empty($top_villes)) {
    $ville_top = $top_villes[0]['ville'];
}

$max_consultations = 0;
if (!empty($top_villes)) {
    $max_consultations = $top_villes[0]['nb'];
}
?>

    <section>

        <h1>Vue d'ensemble</h1>

        <article>
            <h2>Total des recherches</h2>
            <p>
                <strong>
                    <?php
                    if ($total > 0) {
                        echo $total;
                    } else {
                        echo '0';
                    }
                    ?>
                </strong>
                recherche<?php if ($total > 1) { echo 's'; } ?> effectuée<?php if ($total > 1) { echo 's'; } ?>
            </p>
        </article>

        <article>
            <h2>Ville la plus consultée</h2>
            <p>
                <strong>
                    <?php
                    if ($ville_top !== '') {
                        echo htmlspecialchars($ville_top);
                    } else {
                        echo '—';
                    }
                    ?>
                </strong>
            </p>
        </article>

    </section>

    <section>

        <h1>Villes les plus consultées</h1>

        <article>

            <?php
            if (empty($top_villes)) {
                echo '<p>Aucune consultation enregistrée pour le moment.</p>';
            } else {
                echo '<ul class="histogramme">';
                foreach ($top_villes as $ligne) {
                    echo '<li>';
                    echo '<strong>' . htmlspecialchars($ligne['ville']) . ' (' . htmlspecialchars($ligne['dept']) . ')</strong>';

                    if ($max_consultations > 0) {
                        $valeur_metre = ($ligne['nb'] / $max_consultations) * 100;
                    } else {
                        $valeur_metre = 0;
                    }

                    echo '<meter class="histo-barre" value="' . $valeur_metre . '" max="100"></meter>';
                    echo '<p>' . $ligne['nb'] . '</p>';
                    echo '</li>';
                }
                echo '</ul>';
            }
            ?>

        </article>

    </section>

<?php require 'include/footer_inc.php'; ?>