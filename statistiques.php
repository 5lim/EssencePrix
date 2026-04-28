<?php
require 'include/header_inc.php';
require 'include/functions.inc.php';

// --- Récupération des données depuis le CSV ---

$total       = obtenirTotalConsultations();
$top_villes  = obtenirTopVilles(10);

// La ville la plus consultée est la première du tableau (déjà triée)
$ville_top = '';
if (!empty($top_villes)) {
    $ville_top = $top_villes[0]['ville'];
}

// Pour l'histogramme, on a besoin du maximum pour calculer les proportions
// Ex: si Paris a 50 consultations et Lyon 20, la barre de Paris = 100%, Lyon = 40%
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

            <?php if (empty($top_villes)): ?>

                <p>Aucune consultation enregistrée pour le moment.</p>

            <?php else: ?>

                <ul class="histogramme">

                    <?php foreach ($top_villes as $ligne): ?>

                        <li>
                            <strong><?= htmlspecialchars($ligne['ville']) ?> (<?= htmlspecialchars($ligne['dept']) ?>)</strong>

                            <?php
                            // On calcule la valeur proportionnelle pour la barre <meter>
                            // Si Paris a 50 visites et c'est le max, sa valeur = 100
                            // Si Lyon a 20 visites : (20 / 50) * 100 = 40
                            if ($max_consultations > 0) {
                                $valeur_metre = ($ligne['nb'] / $max_consultations) * 100;
                            } else {
                                $valeur_metre = 0;
                            }
                            ?>

                            <meter class="histo-barre"
                                   value="<?= $valeur_metre ?>"
                                   max="100">
                            </meter>

                            <p><?= $ligne['nb'] ?></p>
                        </li>

                    <?php endforeach; ?>

                </ul>

            <?php endif; ?>

        </article>

    </section>

<?php require 'include/footer_inc.php'; ?>