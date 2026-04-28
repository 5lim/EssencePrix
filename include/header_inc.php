<?php
$themes_valides = ['jour', 'nuit'];
$chemin_cookie  = '/';

if (isset($_GET['theme'])) {
    $theme_demande = $_GET['theme'];

    if (in_array($theme_demande, $themes_valides)) {
        setcookie('theme', $theme_demande, time() + 60 * 60 * 24 * 30, $chemin_cookie);
        $_COOKIE['theme'] = $theme_demande;
    }

    // Redirection pour nettoyer l'URL
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarburantsFrance</title>
    <link rel="stylesheet" href="style_p.css">
    <?php if ($theme_actuel === 'nuit'){
            echo "<link rel='stylesheet' href='style_p_nuit.css'>";
        }    
    ?>
</head>

<body>

<header>
    <h1>CarburantsFrance</h1>

    <nav aria-label="Navigation principale">
        <a href="index.php">Accueil</a>
        <a href="resultats.php">Résultats</a>
        <a href="statistiques.php">Statistiques</a>
        <a href="tech.php">Tech</a>
    </nav>

    <?php if ($theme_actuel === 'nuit'): ?>
        <a href="<?= htmlspecialchars($url_jour) ?>" class="btn-theme">
            <img src="img/soleil.png" alt="Mode jour" width="24" height="24">
        </a>
    <?php else: ?>
        <a href="<?= htmlspecialchars($url_nuit) ?>" class="btn-theme">
            <img src="img/lune.png" alt="Mode nuit" width="24" height="24">
        </a>
    <?php endif; ?>
</header>

<main>