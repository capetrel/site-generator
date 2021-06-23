<?php

/**
 * Chemin des différents dossiers utile à l'application
 * Les noms pour les fichiers et dossier ne doivent pas contenir de barre oblique
 * Les chemin utilise la barre oblique DIRECTORY_SEPARATOR mais pas en bout de chaîne.
 * il faudra faire la différence entre les chemins (C:)/chemin/de/test et les URL http(s)://domain.ex/url/de/test
 */

// Chemin ou se situe l'application
$root_app_path = dirname(__DIR__);

// Nom du dossier qui contiendra les projets générés
$projects_dirname = 'web';

// le dossier generators ou se trouve le projet à traiter
$generators_path = $root_app_path . DIRECTORY_SEPARATOR . 'generators';

// le dossier publications ou est généré le projet
$projects_path = $root_app_path . DIRECTORY_SEPARATOR . $projects_dirname;

$html5_options = [
    'encode_entities ' => true,
    'disable_html_ns' => true,
];

$parser_options = [
    LIBXML_HTML_NOIMPLIED,
    LIBXML_HTML_NODEFDTD,
    LIBXML_NOENT,
    LIBXML_NOBLANKS,
    LIBXML_COMPACT
];
