<?php
/**
 * This file is part of isou project.
 *
 * Désactive la vérification phpcs sur ce fichier.
 * phpcs:disable
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta http-equiv="refresh" content="5;URL=www/" />

    <title>ISOU - Problème de configuration</title>

    <link rel="shortcut icon" type="image/png" href="www/themes/bootstrap3/favicon.png" />

    <link href="//unpkg.com/bootstrap@3.3/dist/css/bootstrap.min.css" type="text/css" media="screen" rel="stylesheet" />
</head>
<body role="document">

    <div class="container">
        <header class="page-header" role="banner">
            <h1 id="isou-header">ISOU</h1>
        </header>

        <p class="alert alert-danger"><strong>Problème de configuration détecté !</strong><br />Merci de faire pointer votre serveur web sur le répertoire <code><?php echo __DIR__;?>/www</code>.</p>
        <p>Vous devriez être automatiquement redirigé vers <a href="www/">cette page</a> d'ici quelques secondes.</p>
    </div>

</body>
</html>
