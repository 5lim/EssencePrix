<?php
    // Partie API Ghibli
    $json  = file_get_contents('https://ghibliapi.vercel.app/films');
    $films = json_decode($json, true);
    $index = array_rand($films);
    $film  = $films[$index];

    // Partie API IP
    $ip        = $_SERVER['REMOTE_ADDR'];
    $json_flux = file_get_contents('https://ipinfo.io/'.$ip.'/geo');
    $infos_flux = json_decode($json_flux, true);
    $json_api  = file_get_contents('https://ipinfo.io/'.$ip.'?token=c3ce06468ac6c3');
    $infos_api_json = json_decode($json_api, true);
    $xml_api = file_get_contents('https://api.whatismyip.com/ip-address-lookup.php?key=374b59bb7c81e8a165dcd3b9c0d119bf&input='.$ip.'&output=xml');
    $infos_api_xml = simplexml_load_string($xml_api);

    //champ pour le xml
    $pays_xml = $infos_api_xml->country_name;
    $region_xml = $infos_api_xml->region;
    $ville_xml = $infos_api_xml->city;
    $latitude_xml = $infos_api_xml->latitude;
    $longitude_xml = $infos_api_xml->longitude;
    $isp_xml = $infos_api_xml->isp;
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta name="author" content="Gamard et Haroud"/>
        <meta charset="UTF-8">
        <title>API Ghibli / IP</title>
        <link rel="stylesheet" href="style_p.css">
    </head>
    <body>

        <header>
            <h1>Page Tech</h1>
        </header>

        <main>
            <section>
                <h1><strong>Partie API Ghibli</strong></h1>
                <article>
                    <h2><?php echo $film['title']; ?></h2>
                    <p><?php echo $film['original_title']; ?></p>
                    <p><?php echo $film['original_title_romanised']; ?></p>
        			<p><strong>Année de sortie :</strong> <?php echo $film['release_date']; ?><p>
        			<p><?php echo $film['description']; ?></p>

                    <figure>
                        <img src="<?php echo $film['image']; ?>" alt="Affiche de <?php echo $film['title']; ?>">
                        <figcaption>Affiche officielle</figcaption>
                    </figure>

                    <figure>
                        <img src="<?php echo $film['movie_banner']; ?>" alt="Bannière de <?php echo $film['title']; ?>">
                        <figcaption>Bannière du film</figcaption>
                    </figure>

                    <p><a href="tech.php">Afficher un autre film</a></p>
                </article>
            </section>

            <section>
                <h1><strong>Partie API IP</strong></h1>
                <article>
                    <h2>Informations récupérer via flux json :</h2>
                    <p>Adresse IP : <?php echo $infos_flux['ip']; ?></p>
                    <p>Ville : <?php echo $infos_flux['city']; ?></p>
                    <p>Code Postal : <?php echo $infos_flux['postal']; ?></p>
                    <p>Region : <?php echo $infos_flux['region']; ?></p>
                    <p>Pays : <?php echo $infos_flux['country']; ?></p>
                </article>

                <article>
                    <h2>Informations récupérer via requête ipinfo : </h2>
                    <p>Adresse IP : <?php echo $infos_api['ip']; ?></p>
                    <p>Pays : <?php echo $infos_api['country']; ?></p>
                </article>
                <article>
                    <h2>Informations supplémentaires premium (10 requête par jour) : </h2>
                    <?php 
                        if (isset($infos_api['city'])) {
                            $coords = explode(',', $infos_api_json['loc']);
                            echo "<p>Ville : ".$infos_api_json['city']."</p>
                                  <p>Region : ".$infos_api_json['region']."</p>
                                  <p>Code Postal: ".$infos_api_json['postal']."</p>
                                  <p>Latitude : ".$coords[0]."</p>
                                  <p>Longitude : ".$coords[1]."</p>
                                  <p>Timezone : ".$infos_api_json['timezone']."</p>";
                        } else{
                            echo "Requête du jour épuisé !";
                        }
                    ?>
                </article>

                <article>
                    <h2>Informations récupérer via flux xml (24 requête par jour)</h2>
                    <?php
                        if (isset($pays_xml)) {
                            echo "<p>Pays : ".$pays_xml."</p>
                                    <p>Region : ".$region_xml."</p>
                                    <p>Ville : ".$ville_xml."</p>
                                    <p>Latitude : ".$latitude_xml."</p>
                                    <p>Longitude : ".$longitude_xml."</p>
                                    <p>Internet Service Provider : ".$isp_xml."</p>";

                        } else{
                            echo "Requête du jour épuisé !";                            
                        }
                    ?>
                </article>
            </section>
        </main>

        <footer>
            <p><a href="index.php">Retour à l'accueil</a></p>
        </footer>

    </body>
</html>