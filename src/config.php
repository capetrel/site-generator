<?php

$config = [
    'site-name' => 'Example',
    'site-slogan' => 'slogan du site',
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

function generatePdfListSingleItem(string $pdf_name, array $pdf_list): string {
    $item_parts = glob("./pdf/pages/$pdf_name*");
    if ($item_parts) {
        $item_path_parts = explode('/', $item_parts[0]);
        $item_filename_parts = explode('.', $item_path_parts[3]);
        $item_title = array_key_exists($item_filename_parts[0], $pdf_list) ? $pdf_list[$item_filename_parts[0]] : $item_filename_parts[0];
        return <<<HTML
<li>
    <div class="category">$item_title</div>
    <ul class="sous-partie">
        <li>
            <a href="$item_parts[0]" title="pdf" target="_blank">
                $item_title
            </a>
        </li>
    </ul>
</li>
HTML;
    } else {
        return '';
    }
}

/**
 * Affiche un élément de liste de type li contenant une liste de fichier PDF à partir d'un dossier
 * @param string $folder_name nom ou partie du nom du pdf dans le dossier 'pages'
 * @param array $pdf_list liste des pdf qui permet d'afficher un titre propre
 * @return string l'item html (li) avec le lien et le nom clarifié
 */
function generatePdfListMultipleItems(string $folder_name, array $pdf_list): string {
    $folder = glob("./pdf/pages/$folder_name");
    $html = '';
    if ($folder) {
        $folder_path_parts = explode('/', $folder[0]);
        $folder_files_list = glob("./pdf/pages/$folder_name/*.pdf");
        $filename = array_key_exists($folder_path_parts[3], $pdf_list) ? $pdf_list[$folder_path_parts[3]] : $folder_path_parts[3];
        $html .= '<li>';
        $html .= '<div class="category">'. $folder_path_parts[3] .'</div>';
        $html .= '<ul class="sous-partie">';
        $html .= '<li>';
        $html .= '<a href="'.$folder[0].'/annexes.pdf" title="pdf" target="_blank">';
        $html .= $filename;
        $html .= '</a>';
        $html .= '</li>';

        foreach ($folder_files_list as $file) {
            $file_path_parts = explode('/', $file);
            $filename_parts = explode('-', $file_path_parts[4]);
            if(isset($filename_parts[1])) {
                $content = explode('.', $file_path_parts[4]);
                $name = array_key_exists($content[0], $pdf_list) ? $pdf_list[$content[0]] : $file_path_parts[4];
                $html .= '<li>';
                $html .= '<a href="'.$file.'" title="pdf" target="_blank">';
                $html .= $name;
                $html .= '</a>';
                $html .= '</li>';
            }
        }

        $html .= '</ul>';
        $html .= '</li>';

        return $html;
    } else {
        return '';
    }
}

$project_header = file_exists(dirname(__DIR__) . '/common/header.php') ? 'header.php' : 'header_base.php';
$project_footer = file_exists(dirname(__DIR__) . '/common/footer.php') ? 'footer.php' : 'footer_base.php';