<?php

$config = [
    'site-name' => 'Example',
    'site-slogan' => 'Example',
];

$project_dirname = 'example';

/**
 * Génère l'url de base du site en fonction du serveur. Pas de barre oblique à la fin
 * @return string url du site
 */
function getSiteBaseUrl(){
    $site_domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
    if(isset($_SERVER['HTTPS'])){
        return $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] !== 'off') ? 'https://'. $site_domain : 'http://'. $site_domain;
    }
    else{
        return $protocol = 'http://'. $site_domain;
    }
}

$project_header = file_exists(dirname(__DIR__) . '/common/header.php') ? 'header.php' : 'header_base.php';
$project_footer = file_exists(dirname(__DIR__) . '/common/footer.php') ? 'footer.php' : 'footer_base.php';