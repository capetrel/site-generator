<?php
header('Content-type: application/xml');
require "common/config.php";
$title_content= $config["site-name"]." | Carte du site";
$page_url=getSiteBaseUrl().'/edition-numerique/'.$project_dirname.'/sitemap.xml';
$project_url=getSiteBaseUrl().'/edition-numerique/'.$project_dirname;
$dir = dirname(__FILE__);
$pages = [];

// Parcourir les fichiers php
$files = glob($dir . DIRECTORY_SEPARATOR . "*.php");

// récupérer le nom du fichier et sa date de dernière modification
foreach ($files as $k => $file) {
    $file_info = pathinfo($file);
    if($file_info['filename'] !== 'file_indexer') {
        $pages[$k]['file'] = $file_info['filename'];
        $pages[$k]['last'] = date('Y-m-d', filemtime($file));
    }
}
// Générer le site map
$output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
echo $output;
?>
    <url>
        <loc><?= $project_url ?></loc>
        <lastmod><?= $pages[0]['last'] ?></lastmod>
        <priority>1</priority>
    </url>
    <?php foreach($pages as $page): ?>
        <url>
            <loc><?= $project_url . '/' . $page['file'] ?></loc>
            <lastmod><?= $page['last'] ?></lastmod>
            <priority>0.5</priority>
        </url>
    <?php endforeach; ?>
</urlset>