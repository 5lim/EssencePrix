<?php
$themes_valides = ['jour', 'nuit'];
$chemin_cookie  = '/';

if (isset($_GET['theme'])) {
    $theme_demande = $_GET['theme'];

    if (in_array($theme_demande, $themes_valides)) {
        setcookie('theme', $theme_demande, time() + 60 * 60 * 24 * 30, $chemin_cookie);
        $_COOKIE['theme'] = $theme_demande;
    }

    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

if (isset($_COOKIE['theme']) && in_array($_COOKIE['theme'], $themes_valides)) {
    $theme_actuel = $_COOKIE['theme'];
} else {
    $theme_actuel = 'jour';
}

$params_actuels = $_GET;
unset($params_actuels['theme']);

$url_jour = '?' . http_build_query(array_merge($params_actuels, ['theme' => 'jour']));
$url_nuit = '?' . http_build_query(array_merge($params_actuels, ['theme' => 'nuit']));

$page_actuelle = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CarburantsFrance</title>

    <link rel="stylesheet" href="style_p.css" />

    <?php
    if ($theme_actuel === 'nuit') {
        echo '<link rel="stylesheet" href="style_p_nuit.css" />';
    }
    ?>

    <script src="carte.js" defer="defer"></script>
</head>

<body>

<header>
    <img src="images/pompe-a-essence.png" alt="CarburantFrance" class ="img-header"/>

    <nav aria-label="Navigation principale">
        <a href="index.php" class="<?php if ($page_actuelle === 'index.php') echo 'active'; ?>" style="margin:2%;">Accueil</a>
        <a href="resultats.php" class="<?php if ($page_actuelle === 'resultats.php') echo 'active'; ?>" style="margin:2%;">Résultats</a>
        <a href="statistiques.php" class="<?php if ($page_actuelle === 'statistiques.php') echo 'active'; ?>" style="margin:2%;">Statistiques</a>
        <a href="tech.php" class="<?php if ($page_actuelle === 'tech.php') echo 'active'; ?>" style="margin:2%;">Tech</a>
    </nav>

    <?php
    if ($theme_actuel === 'nuit') {
        echo '<a href="' . htmlspecialchars($url_jour) . '" class="btn-theme">';
        echo '<img src="images/soleil.png" alt="Mode jour" width="24" height="24" />';
        echo '</a>';
    } else {
        echo '<a href="' . htmlspecialchars($url_nuit) . '" class="btn-theme">';
        echo '<img src="images/lune.png" alt="Mode nuit" width="24" height="24" />';
        echo '</a>';
    }
    ?>
</header>

<main>