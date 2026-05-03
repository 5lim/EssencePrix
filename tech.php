<?php
require 'include/header_inc.php';
require 'include/functions_inc.php';
$json_ghibli = file_get_contents('https://ghibliapi.vercel.app/films');
$films = json_decode($json_ghibli, true);
$index = array_rand($films);
$film = $films[$index];


$ip = getIP();
$json_flux = file_get_contents('https://ipinfo.io/' . $ip . '/geo');
$infos_flux = json_decode($json_flux, true);


$json_api = file_get_contents('https://ipinfo.io/' . $ip . '?token=c3ce06468ac6c3');
$infos_api_json = json_decode($json_api, true);

$xml_brut = file_get_contents('https://api.whatismyip.com/ip-address-lookup.php?key=374b59bb7c81e8a165dcd3b9c0d119bf&input=' . $ip . '&output=xml');
$infos_api_xml = false;
$xml_erreur = '';

if ($xml_brut === false) {
    $xml_erreur = 'Impossible de joindre l\'API.';
} elseif (!str_starts_with(trim($xml_brut), '<')) {
    $xml_erreur = 'Réponse inattendue : ' . htmlspecialchars(substr($xml_brut, 0, 200));
} else {
    $infos_api_xml = simplexml_load_string($xml_brut);
    if ($infos_api_xml === false) {
        $xml_erreur = 'XML invalide.';
    }
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
                <img src="<?= htmlspecialchars($film['image']) ?>" alt="Affiche de <?= htmlspecialchars($film['title']) ?>"/>
                <figcaption>Affiche officielle</figcaption>
            </figure>

            <figure>
                <img src="<?= htmlspecialchars($film['movie_banner']) ?>" alt="Bannière de <?= htmlspecialchars($film['title']) ?>"/>
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

            <p><strong>Adresse IP :</strong>
                <?php
                if (isset($infos_flux['ip']) && $infos_flux['ip'] !== '') {
                    echo htmlspecialchars($infos_flux['ip']);
                } else {
                    echo '—';
                }
                ?>
            </p>
            <p><strong>Ville :</strong>
                <?php
                if (isset($infos_flux['city']) && $infos_flux['city'] !== '') {
                    echo htmlspecialchars($infos_flux['city']);
                } else {
                    echo '—';
                }
                ?>
            </p>
            <p><strong>Code postal :</strong>
                <?php
                if (isset($infos_flux['postal']) && $infos_flux['postal'] !== '') {
                    echo htmlspecialchars($infos_flux['postal']);
                } else {
                    echo '—';
                }
                ?>
            </p>
            <p><strong>Région :</strong>
                <?php
                if (isset($infos_flux['region']) && $infos_flux['region'] !== '') {
                    echo htmlspecialchars($infos_flux['region']);
                } else {
                    echo '—';
                }
                ?>
            </p>
            <p><strong>Pays :</strong>
                <?php
                if (isset($infos_flux['country']) && $infos_flux['country'] !== '') {
                    echo htmlspecialchars($infos_flux['country']);
                } else {
                    echo '—';
                }
                ?>
            </p>
        </article>

        <article>
            <h2>API ipinfo — données de base</h2>

            <p><strong>Adresse IP :</strong>
                <?php
                if (isset($infos_api_json['ip']) && $infos_api_json['ip'] !== '') {
                    echo htmlspecialchars($infos_api_json['ip']);
                } else {
                    echo '—';
                }
                ?>
            </p>
            <p><strong>Pays :</strong>
                <?php
                if (isset($infos_api_json['country']) && $infos_api_json['country'] !== '') {
                    echo htmlspecialchars($infos_api_json['country']);
                } else {
                    echo '—';
                }
                ?>
            </p>
        </article>

        <article>
            <h2>API ipinfo — données enrichies (token)</h2>

            <?php
            if (isset($infos_api_json['city'])) {
                if (isset($infos_api_json['loc'])) {
                    $coords = explode(',', $infos_api_json['loc']);
                } else {
                    $coords = ['—', '—'];
                }

                echo '<p><strong>Ville :</strong> ';
                if (isset($infos_api_json['city']) && $infos_api_json['city'] !== '') {
                    echo htmlspecialchars($infos_api_json['city']);
                } else {
                    echo '—';
                }
                echo '</p>';

                echo '<p><strong>Région :</strong> ';
                if (isset($infos_api_json['region']) && $infos_api_json['region'] !== '') {
                    echo htmlspecialchars($infos_api_json['region']);
                } else {
                    echo '—';
                }
                echo '</p>';

                echo '<p><strong>Code postal :</strong> ';
                if (isset($infos_api_json['postal']) && $infos_api_json['postal'] !== '') {
                    echo htmlspecialchars($infos_api_json['postal']);
                } else {
                    echo '—';
                }
                echo '</p>';

                echo '<p><strong>Latitude :</strong> ';
                if (isset($coords[0]) && $coords[0] !== '') {
                    echo htmlspecialchars($coords[0]);
                } else {
                    echo '—';
                }
                echo '</p>';

                echo '<p><strong>Longitude :</strong> ';
                if (isset($coords[1]) && $coords[1] !== '') {
                    echo htmlspecialchars($coords[1]);
                } else {
                    echo '—';
                }
                echo '</p>';

                echo '<p><strong>Fuseau horaire :</strong> ';
                if (isset($infos_api_json['timezone']) && $infos_api_json['timezone'] !== '') {
                    echo htmlspecialchars($infos_api_json['timezone']);
                } else {
                    echo '—';
                }
                echo '</p>';
            } else {
                echo '<p>Quota journalier épuisé.</p>';
            }
            ?>
        </article>

        <article>
            <h2>Flux XML</h2>

            <?php
            if ($infos_api_xml && (string)$infos_api_xml->query_status->query_status_code === 'OK') {
                echo '<p><strong>Adresse IP :</strong> ' . htmlspecialchars((string) $infos_api_xml->server_data->ip) . '</p>';
                echo '<p><strong>Pays :</strong> ' . htmlspecialchars((string) $infos_api_xml->server_data->country) . '</p>';
                echo '<p><strong>Région :</strong> ' . htmlspecialchars((string) $infos_api_xml->server_data->region) . '</p>';
                echo '<p><strong>Ville :</strong> ' . htmlspecialchars((string) $infos_api_xml->server_data->city) . '</p>';
                echo '<p><strong>Code postal :</strong> ' . htmlspecialchars((string) $infos_api_xml->server_data->postalcode) . '</p>';
                echo '<p><strong>FAI :</strong> ' . htmlspecialchars((string) $infos_api_xml->server_data->isp) . '</p>';
                echo '<p><strong>Latitude :</strong> ' . htmlspecialchars((string) $infos_api_xml->server_data->latitude) . '</p>';
                echo '<p><strong>Longitude :</strong> ' . htmlspecialchars((string) $infos_api_xml->server_data->longitude) .  '</p>';
            } else {
                echo '<p>Données indisponibles.';
                if ($xml_erreur !== '') {
                    echo ' (' . $xml_erreur . ')';
                }
                echo '</p>';
            }
            ?>
        </article>

    </section>

<?php require 'include/footer_inc.php'; ?>