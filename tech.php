<?php
require 'include/header_inc.php';

$json_ghibli = file_get_contents('https://ghibliapi.vercel.app/films');
$films       = json_decode($json_ghibli, true);
$index       = array_rand($films);
$film        = $films[$index];


$ip         = $_SERVER['REMOTE_ADDR'];
$json_flux  = file_get_contents('https://ipinfo.io/' . $ip . '/geo');
$infos_flux = json_decode($json_flux, true);


$json_api       = file_get_contents('https://ipinfo.io/' . $ip . '?token=c3ce06468ac6c3');
$infos_api_json = json_decode($json_api, true);

$xml_brut      = file_get_contents('https://api.whatismyip.com/ip-address-lookup.php?key=374b59bb7c81e8a165dcd3b9c0d119bf&input=' . $ip . '&output=xml');
$infos_api_xml = false;

if ($xml_brut !== false && str_starts_with(trim($xml_brut), '<')) {
    $infos_api_xml = simplexml_load_string($xml_brut);
}
?>

    <section>

        <h1>API Ghibli — film aléatoire</h1>

        <article>
            <h2><?= htmlspecialchars($film['title']) ?></h2>

            <p><em><?= htmlspecialchars($film['original_title']) ?></em>
               — <?= htmlspecialchars($film['original_title_romanised']) ?></p>

            <p><strong>Année de sortie :</strong> <?= htmlspecialchars($film['release_date']) ?></p>

            <p><?= htmlspecialchars($film['description']) ?></p>

            <figure>
                <img src="<?= htmlspecialchars($film['image']) ?>" alt="Affiche de <?= htmlspecialchars($film['title']) ?>">
                <figcaption>Affiche officielle</figcaption>
            </figure>

            <figure>
                <img src="<?= htmlspecialchars($film['movie_banner']) ?>" alt="Bannière de <?= htmlspecialchars($film['title']) ?>">
                <figcaption>Bannière du film</figcaption>
            </figure>

            <p>
                <a href="tech.php">Afficher un autre film</a>
            </p>
        </article>

    </section>

    <section>

        <h1>API IP — informations du visiteur</h1>

        <article>
            <h2>Flux JSON public</h2>

            <p><strong>Adresse IP :</strong> <?= htmlspecialchars($infos_flux['ip']     ?? '—') ?></p>
            <p><strong>Ville :</strong>       <?= htmlspecialchars($infos_flux['city']   ?? '—') ?></p>
            <p><strong>Code postal :</strong> <?= htmlspecialchars($infos_flux['postal'] ?? '—') ?></p>
            <p><strong>Région :</strong>      <?= htmlspecialchars($infos_flux['region'] ?? '—') ?></p>
            <p><strong>Pays :</strong>        <?= htmlspecialchars($infos_flux['country']?? '—') ?></p>
        </article>

        <article>
            <h2>API ipinfo — données de base</h2>

            <p><strong>Adresse IP :</strong> <?= htmlspecialchars($infos_api_json['ip']      ?? '—') ?></p>
            <p><strong>Pays :</strong>        <?= htmlspecialchars($infos_api_json['country'] ?? '—') ?></p>
        </article>

        <article>
            <h2>API ipinfo — données enrichies (token)</h2>

            <?php if (isset($infos_api_json['city'])): ?>
                <?php $coords = explode(',', $infos_api_json['loc'] ?? ','); ?>
                <p><strong>Ville :</strong>        <?= htmlspecialchars($infos_api_json['city']     ?? '—') ?></p>
                <p><strong>Région :</strong>        <?= htmlspecialchars($infos_api_json['region']   ?? '—') ?></p>
                <p><strong>Code postal :</strong>   <?= htmlspecialchars($infos_api_json['postal']   ?? '—') ?></p>
                <p><strong>Latitude :</strong>      <?= htmlspecialchars($coords[0]                  ?? '—') ?></p>
                <p><strong>Longitude :</strong>     <?= htmlspecialchars($coords[1]                  ?? '—') ?></p>
                <p><strong>Fuseau horaire :</strong><?= htmlspecialchars($infos_api_json['timezone'] ?? '—') ?></p>
            <?php else: ?>
                <p>Quota journalier épuisé.</p>
            <?php endif; ?>
        </article>

        <article>
            <h2>Flux XML</h2>

            <?php if ($infos_api_xml): ?>
                <p><strong>Pays :</strong>      <?= htmlspecialchars((string) $infos_api_xml->country_name) ?></p>
                <p><strong>Région :</strong>    <?= htmlspecialchars((string) $infos_api_xml->region)       ?></p>
                <p><strong>Ville :</strong>     <?= htmlspecialchars((string) $infos_api_xml->city)         ?></p>
                <p><strong>Latitude :</strong>  <?= htmlspecialchars((string) $infos_api_xml->latitude)     ?></p>
                <p><strong>Longitude :</strong> <?= htmlspecialchars((string) $infos_api_xml->longitude)    ?></p>
                <p><strong>FAI :</strong>       <?= htmlspecialchars((string) $infos_api_xml->isp)          ?></p>
            <?php else: ?>
                <p>Quota journalier épuisé.</p>
            <?php endif; ?>
        </article>

    </section>

<?php require 'include/footer_inc.php'; ?>