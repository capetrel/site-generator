<?php
// Tableau de correspondance pour transformer une chaine en url "propre"
$char_table = [
    'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
    'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
    'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
    'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
    'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
    'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
    'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b',
    'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', ' ' => '-', '/' => 'et', '.' => '', ',' => '-', '"'=>'', '\''=>'', ';'=>'',
    ':'=>'', '('=>'', ')'=>'', '|'=>'', '['=>'', ']'=>'', '{'=>'', '}'=>'', '$'=>'', '*'=>'', '!'=>'',
    '<'=>'', '>'=>'', '&'=>'et', '~'=>'', '`'=>'', '='=>'-', '¤'=>'', '€'=>'e', '£'=>'', 'µ'=>'',
    '%'=>'', '§'=>'', '?'=>'', '^'=>'', '’'=>'', '«'=>'', '»'=>'', ' '=>'-'
];


/**
 * Permet de transformer un chaine de caractère en URL
 * @param string $string
 * @param array $array
 * @return string
 */
function sanitizeString(string $string, array $array): string {
    $stripped = preg_replace(['/\s{2,}/', '/[\t\n]/'], ' ', $string);
    return strtolower(strtr($stripped, $array));
}


/**
 * Permet de couper une chaine de caractère pour en avoir un extrait
 * @param $text
 * @param int $max_length
 * @param string $cut_off
 * @param false $keep_word
 * @return false|string
 */
function shorten_text($text, $max_length = 140, $cut_off = '...', $keep_word = false)
{
    if(strlen($text) <= $max_length) {
        return $text;
    }

    if(strlen($text) > $max_length) {
        if($keep_word) {
            $text = substr($text, 0, $max_length + 1);

            if($last_space = strrpos($text, ' ')) {
                $text = substr($text, 0, $last_space);
                $text = rtrim($text);
                $text .=  $cut_off;
            }
        } else {
            $text = substr($text, 0, $max_length);
            $text = rtrim($text);
            $text .=  $cut_off;
        }
    }

    return $text;
}


/**
 * Génère l'url de base du site en fonction du serveur sans barre oblique à la fin
 * @return string
 */
function getBaseUrl(){
    $site_domain = $_SERVER['SERVER_NAME'];
    if(isset($_SERVER['HTTPS'])){
        return $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] !== 'off') ? 'https'. $site_domain : 'http'. $site_domain;
    }
    else{
        return $protocol = 'http://'. $site_domain;
    }
}


/**
 * Permet créer un slug avec n nombre(s) de mots.
 * @param array $words
 * @param int $words_quantity
 * @return false|string
 */
function generateSlugWithLimitNumber(array $words, int $words_quantity): string {
    $limited_slug = '';
    foreach ($words as $k => $item) {
        if ($k < $words_quantity ) {
            $limited_slug .= '-'.$item;
        }
    }
    return substr($limited_slug, 1);
}


/**
 * Créer un noeud spécifique à fancybox de type DOMElement
 * @param DOMDocument $source
 * @param string $uri
 * @return DOMElement
 */
function createFancyLink(DOMDocument $source, string $uri): DOMElement {
    $a = $source->createElement('a');
    $a->setAttribute('href', $uri);
    $a->setAttribute('data-fancybox', 'images');
    //$a->setAttribute('data-width', '1000');
    return $a;
}