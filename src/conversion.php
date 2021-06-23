<?php

define('DS', DIRECTORY_SEPARATOR);

use App\FileManager;
use Masterminds\HTML5;

require "../../vendor/autoload.php";
require "../../config/app.php";
require "../../config/helpers.php";
require "./config.php";

$html_source_file = '.'.DS.'source-file.html';
$project_relative_url = $projects_dirname . '/' . $project_dirname;
$project_url = getSiteBaseUrl() . '/' . $projects_dirname . '/' . $project_dirname;

$file_manager = new FileManager(DS);

$meta_desc_content = '';
$title_content = '';
$title_words = [];
$body_class = '';
$body_id = '';
$generic_class = '';
$slug='';
$elements_for_prevnext = [];
$prev_url = '';
$prev_url_title = '';
$next_url = '';
$next_url_title = '';
$menu_num = 0;
$pdf_list = [
    'annexes' => 'Annexes',
    'accueil' => 'Accueil',
    'glossaire' => 'Glossaire',
    'credits' => 'Crédits',
];

/**
 * Préparation du dossier du projet
 */
// Si il n'existe pas, création du dossier du projet avec le nom contenu dans config.php
if (!file_exists($projects_path . DS . $project_dirname)) {
    mkdir($projects_path . DS . $project_dirname, 0777, true);
} else {
    echo "Le dossier existe déjà\n";
}

// Copies des assets commum à tous les projets
$file_manager->recurseCopyWithExclusion($root_app_path . DS . 'assets', $projects_path . DS . $project_dirname, ['sass', 'node_modules', 'src'], ['js', 'json', 'babelrc', 'php']);

// copie des assets du projet
$file_manager->recurseCopyWithExclusion($generators_path . DS . $project_dirname, $projects_path . DS . $project_dirname, ['page_html'], ['php', 'html']);

// copie de config dans le dossier commun.
$file_manager->copyFileToDir($generators_path . DS . $project_dirname . DS . 'config.php', $projects_path . DS . $project_dirname . DS . 'common' . DS . 'config.php');

// Copie du htaccess
$file_manager->copyFileToDir($root_app_path . DS . 'assets' .DS. '.htaccess',$projects_path . DS . $project_dirname . DS . '.htaccess');


// Déplacer le fichier sitemap.php
rename($projects_path . DS . $project_dirname . DS . 'common' . DS . 'sitemap.php', $projects_path . DS . $project_dirname . DS . 'sitemap.php');

// Créer les fichiers custom.js et custom.css si ils n'existe pas
$custom_css = $projects_path . DS . $project_dirname . DS . 'css' . DS . 'custom.css';
$custom_js = $projects_path . DS . $project_dirname . DS . 'script' . DS . 'custom.js';
if(!file_exists($custom_css)) {
    file_put_contents($custom_css, '');
} else {
    echo "le fichier custom.css existe déjà\n";
}
if(!file_exists($custom_js)) {
    file_put_contents($custom_js, '');
} else {
    echo "le fichier custom.js existe déjà\n";
}

/**
 * Génération des pages du sites en fichier php
 */
// Initialisation des objets et variables.
$html5 = new HTML5($html5_options);
$html_source = $html5->loadHTMLFile($html_source_file, $parser_options);
$page_path = new DOMXpath($html_source);
$html_source_body = $html_source->getElementsByTagName('body')->item(0);


// Changer la balise object en img
$object_elements = $page_path->query("//object", $html_source_body);
if( $object_elements->count() > 0 ) {
    foreach ($object_elements as $k => $object) {
        $uri = $object->getAttribute('data');
        $img = $html_source->createElement('img');
        $img->setAttribute('class', 'image');
        $img->setAttribute('src', $uri);
        $img->setAttribute('alt', $title_content);

        $object->parentNode->setAttribute('class', 'image');
        $object->parentNode->replaceChild($img, $object);
    }
}
$html_source->preserveWhiteSpace = false;
$html_source->formatOutput = true;
$html_source->saveHTML();

// Changer l'url des images (chemin relatif) et ajouter l'élément pour fancybox
$img_elements = $page_path->query("//img", $html_source_body);
if( $img_elements->count() > 0 ) {
    foreach ($img_elements as $img_element) {
        $uri = $img_element->getAttribute('src');
        $alt = $img_element->getAttribute('alt');
        $uri_parts = explode('/', $uri);
        $parent_class = $img_element->parentNode->getAttribute('class');
        $a = createFancyLink($html_source, './' . $uri_parts[1] . '/' . $uri_parts[2]);

        $new_img = $html_source->createElement('img');
        $new_img->setAttribute('class', $parent_class);
        $new_img->setAttribute('src', './' . $uri_parts[1] . '/' . $uri_parts[2]);
        $new_img->setAttribute('alt', empty($alt) ? $title_content : $alt);

        $a->appendChild($new_img);
        $img_element->parentNode->replaceChild($a, $img_element);
    }
}
$html_source->preserveWhiteSpace = false;
$html_source->formatOutput = true;
$html_source->saveHTML();

// Supprime les <p class="image"></p> vide suite au traitement des balises object
$empty_p_image = $page_path->query("//p[@class='image']");
foreach ($empty_p_image as $p) {
    if($p->childNodes->length === 0) {
        $p->parentNode->removeChild($p);
    }
}
$html_source->preserveWhiteSpace = false;
$html_source->formatOutput = true;
$html_source->saveHTML();


// Prépare les différentes variables de page et génère le fichier de la page.
foreach($page_path->query("//body/div", $html_source_body) as $k => $element){
    $k++;

    // Générer le contenu de la balise meta description
    $p_elements = $element->getElementsByTagName('p');
    foreach ($p_elements as $p) {
        if($p->getAttribute('class') === 'paragraphe') {
            $meta_desc_content = shorten_text($p->textContent,200, '', true);
            break;
        }
    }

    // Générer les variables de page
    $h1_elements = $element->getElementsByTagName('h1');
    foreach ($h1_elements as $h1) {
        $title_content = $h1->textContent;
        $slug = sanitizeString($title_content, $char_table);
        $title_words = explode('-', $slug);
        $generic_class = generateSlugWithLimitNumber($title_words, 2);
        $body_class = 'contenu-'. $generic_class;
    }

    // Ajoute les class="ancre ancre1" en id="ancre1"
    $par_elements = $element->getElementsByTagName('p');
    foreach ($par_elements as $par) {
        $p_class_name_attr = $par->getAttribute('class');
        $p_classes_name = explode(' ', $p_class_name_attr);
        // pour les ancres
        if ($p_classes_name[0] === 'ancre') {
            $par->setAttribute('id', $p_classes_name[1]);
        }
        // pour les cartes
        if ($p_classes_name[0] === 'titre-figure') {
            $span_elements = $element->getElementsByTagName('span');
            foreach ($span_elements as $span) {
                $span_class_name_attr = $span->getAttribute('class');
                $span_classes_name = explode(' ', $span_class_name_attr);
                if(isset($span_classes_name[1]) && strpos('titre-carte', $span_classes_name[1]) === false){
                    $span->setAttribute('id', $span_classes_name[1]);
                }
            }
        }
        // Fait le lien entre les ancres des cartes et le leins vers celle-ci
        $em_elements = $element->getElementsByTagName('em');
        foreach ($em_elements as $em) {
            $em_class_name_attr = $em->getAttribute('class');
            $em_classes_name = explode(' ', $em_class_name_attr);
            if ($em_classes_name[0] === 'texte') {
                if(isset($em_classes_name[1]) && strpos('texte-carte', $em_classes_name[1]) === false){

                    $class_attrs = explode('-', $em_classes_name[1]);
                    $em_text = $em->textContent;
                    $a = $html_source->createElement('a');
                    $a->setAttribute('class', 'lien lien-' . $class_attrs[1]);
                    $a->setAttribute('href', '#titre-' . $class_attrs[1]);
                    $a->setAttribute('alt', 'Aller vers #titre-' . $class_attrs[1]);
                    $a->textContent = $em_text;

                    $em->textContent = '';
                    $em->appendChild($a);

                }
            }
        }
    }

    // Générer le fichier de la page si il y a un titre de niveau 1
    if($h1_elements->length > 0) {
        $menu_num ++;
        $pdf_list[$menu_num.'-'.$slug] = $title_content;
        $page = '<?php require "common/config.php" ?>'. PHP_EOL;
        $page .= '<?php $meta_desc_content="'.$meta_desc_content.'" ?>'. PHP_EOL;
        $page .= '<?php $title_content="'. $title_content.' | '.$config["site-name"].'" ?>'. PHP_EOL;
        $page .= '<?php $body_class="'.$body_class.'" ?>'. PHP_EOL;
        $page .= '<?php $body_id="page-'.(string)$menu_num.'" ?>'. PHP_EOL;
        $page .= '<?php $slug="'.$menu_num.'-'.$slug.'" ?>'. PHP_EOL;
        $page .= '<?php $page_num='.$k.' ?>'. PHP_EOL;
        $page .= '<?php $site_url=getSiteBaseUrl().'. "'/$project_relative_url/$menu_num-$slug.php'".'?>'. PHP_EOL;
        $page .= '<?php $project_url=getSiteBaseUrl().'. "'/$project_relative_url'".'?>'. PHP_EOL;
        $page .= '<?php require "common/pdf_list.php" ?>'. PHP_EOL;
        $page .= '<?php require "common/$project_header" ?>'. PHP_EOL;
        $page .= $element->ownerDocument->saveHTML($element) . PHP_EOL;
        $page .= '<?php require "common/$project_footer" ?>';

        $file_name = generateSlugWithLimitNumber($title_words, 5);
        file_put_contents($root_app_path . DS . $project_relative_url . DS . $menu_num . '-' . $file_name . '.php', $page);
    }

}

// Génère la liste des fichier pdf slug=>titre
file_put_contents( $projects_path . DS . $project_dirname . DS . 'common' . DS . 'pdf_list.php', '<?php $pdf_list = '.var_export( $pdf_list, true ).";\n" );


/**
 * Génère le menu dans un fichier
 */
// Préparer la structure HTML du menu
$menu_html = $html5->loadHTML('<div id="main-menu-content"></div>', $parser_options);
$layout_menu = $menu_html->getElementById('main-menu-content');
$menu_ul_niv1 = $menu_html->createElement('ul');
$menu_ul_niv1->setAttribute('class', 'menu-wrapper ul-niv-1');
$menu_home_li = $menu_html->createElement('li');
$menu_home_li->setAttribute('class', 'li-niv-1');
$menu_home_li_a = $menu_html->createElement('a', 'Accueil');
$menu_home_li_a->setAttribute('title', 'Accueil');
$menu_home_li_a->setAttribute('href', './index.php');
$menu_nav_li = $menu_html->createElement('li');
$menu_nav_li->setAttribute('class', 'li-niv-1');
$menu_ul_niv2 = $menu_html->createElement('ul');
$menu_ul_niv2->setAttribute('class', 'menu-wrapper ul-niv-2');

$menu_home_li->appendChild($menu_home_li_a);
$menu_ul_niv1->appendChild($menu_home_li);

// Remplie la structure HTML du menu les liens doivent être relatifs $projects_dirname/$project_dirname
$titles = $html_source->getElementsByTagName('h1');
foreach ($titles as $k => $title) {
    $k ++;
    $num = (string)$k;
    $t_content = $title->textContent;
    $sanitized_str = sanitizeString($t_content, $char_table);
    $words = explode('-', $sanitized_str);
    $class_name = 'menu-item-' . generateSlugWithLimitNumber($words, 2);
    $href_slug = './'.$num .'-'. generateSlugWithLimitNumber($words, 5);

    $menu_li = $menu_html->createElement('li');
    $menu_a = $menu_html->createElement('a', $title->textContent);

    $menu_li->setAttribute('class', $class_name);
    $menu_a->setAttribute('id', 'lien-page-' . $num);
    $menu_a->setAttribute('href', $href_slug .'.php');
    $menu_a->setAttribute('title', $t_content);

    $menu_li->appendChild($menu_a);
    $menu_ul_niv2->appendChild($menu_li);

    $menu_nav_li->appendChild($menu_ul_niv2);
    $menu_ul_niv1->appendChild($menu_nav_li);
}

$layout_menu->appendChild($menu_ul_niv1);

$menu_html->preserveWhiteSpace = false;
$menu_html->formatOutput = true;

// Créer le fichier du menu
$menu_path = new DOMXpath($menu_html);
foreach($menu_path->query("//div[@id='main-menu-content']") as $nav){
    $menu = $nav->ownerDocument->saveHTML($nav);
}
file_put_contents($root_app_path . DS . $project_relative_url . DS .'common'.DS.'menu_base.php', $menu);


/**
 * Génère le fichier liseuse.php
 */
$flipbook_html = $html5->loadHTML('<div id="conteneurLiseuse"></div>', $parser_options);
$flipbook_iframe = $flipbook_html->createElement('iframe');
$flipbook_iframe->setAttribute('id', 'iframe-liseuse');
$flipbook_iframe->setAttribute('src', './liseuse/index.php');
$flipbook_iframe->setAttribute('width', '100%');
$flipbook_iframe->setAttribute('height', '100%');
$flipbook_container = $flipbook_html->getElementById('conteneurLiseuse');
$flipbook_container->appendChild($flipbook_iframe);
$flipbook_html->preserveWhiteSpace = false;
$flipbook_html->formatOutput = true;

$flipbook_path = new DOMXpath($flipbook_html);

foreach($flipbook_path->query("//iframe") as $fp){
    $flipbook = $fp->ownerDocument->saveHTML($fp);
}
$flipbook_page = '<?php require "common/config.php" ?>'. PHP_EOL;
$flipbook_page .= '<?php $meta_desc_content="Visionneuse" ?>'. PHP_EOL;
$flipbook_page .= '<?php $title_content= "Livre | '.$config["site-name"].'" ?>'. PHP_EOL;
$flipbook_page .= '<?php $body_class="liseuse" ?>'. PHP_EOL;
$flipbook_page .= '<?php $body_id="liseuse-page" ?>'. PHP_EOL;
$flipbook_page .= '<?php $slug="livre" ?>'. PHP_EOL;
$flipbook_page .= '<?php $site_url=getSiteBaseUrl().'."'/$project_relative_url/livre.php' ?>". PHP_EOL;
$flipbook_page .= '<?php $project_url=getSiteBaseUrl().'."'/$project_relative_url' ?>". PHP_EOL;
$flipbook_page .= '<?php require "common/pdf_list.php" ?>'. PHP_EOL;
$flipbook_page .= '<?php require "common/$project_header" ?>'. PHP_EOL;
$flipbook_page .= $flipbook . PHP_EOL;
$flipbook_page .= '<?php require "common/$project_footer" ?>';

file_put_contents($root_app_path . DS . $project_relative_url . DS .'livre.php', $flipbook_page);


/**
 * Génère le fichier index_base.php
 */
$index_html = $html5->loadHTML('<div id="conteneurAccueil" class="Bloc-de-texte-standard"></div>', $parser_options);

$index_div_container = $index_html->getElementById('conteneurAccueil');

$index_div_wrap = $index_html->createElement('div');
$index_div_wrap->setAttribute('class', 'text-center');

$index_p = $index_html->createElement('p', 'Acceuil du site');
$index_p->setAttribute('class', 'text-intro');

$index_ul = $index_html->createElement('ul');
$index_ul->setAttribute('class', 'list-page');

$index_div_wrap->appendChild($index_p);
$index_div_wrap->appendChild($index_ul);
$index_div_container->appendChild($index_div_wrap);
$index_html->appendChild($index_div_container);

$index_html->preserveWhiteSpace = false;
$index_html->formatOutput = true;
$index_path = new DOMXpath($index_html);

foreach($index_path->query("//div[@id='conteneurAccueil']") as $ip){
    $index = $ip->ownerDocument->saveHTML($ip);
}

$index_page = '<?php require "common/config.php" ?>'. PHP_EOL;
$index_page .= '<?php $meta_desc_content="Page d\'accueil de la publication" ?>'. PHP_EOL;
$index_page .= '<?php $title_content= "Accueil | '.$config["site-name"].'" ?>'. PHP_EOL;
$index_page .= '<?php $body_class="accueil" ?>'. PHP_EOL;
$index_page .= '<?php $body_id="page-0" ?>'. PHP_EOL;
$index_page .= '<?php $slug="accueil" ?>'. PHP_EOL;
$index_page .= '<?php $page_num=0 ?>'. PHP_EOL;
$index_page .= '<?php $site_url=getSiteBaseUrl().'."'/$project_relative_url/' ?>". PHP_EOL;
$index_page .= '<?php $project_url=getSiteBaseUrl().'."'/$project_relative_url' ?>". PHP_EOL;
$index_page .= '<?php require "common/pdf_list.php" ?>'. PHP_EOL;
$index_page .= '<?php require "common/$project_header" ?>'. PHP_EOL;
$index_page .= $index . PHP_EOL;
$index_page .= '<?php require "common/$project_footer" ?>';

file_put_contents($root_app_path . DS . $project_relative_url . DS .'common'. DS .'index_base.php', $index_page);

// Copier et renommer le ficher index_base.php si il n'existe pas
$src_index_file = $projects_path . DS . $project_dirname . DS . 'common' . DS . 'index_base.php';
$dest_index_file = $projects_path . DS . $project_dirname . DS . 'index.php';
if(!file_exists($dest_index_file)) {
    copy($src_index_file, $dest_index_file);
} else {
    echo "le fichier index.php existe déjà\n";
}