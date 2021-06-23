<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="slug" content="<?= $slug ?>"/>
    <meta name="description" content="<?= $meta_desc_content ?>">
    <title><?= $title_content ?></title>
    <link rel="shortcut icon" href="<?= $project_url ?>/image/app/favicon.png" type="image/png" />
    <link href="./css/style.css" rel="stylesheet">
    <link href="./css/custom.css" rel="stylesheet">

    <!-- Control the behavior of search engine crawling and indexing -->
    <meta name="robots" content="index,follow"><!-- All Search Engines -->
    <meta name="googlebot" content="index,follow"><!-- Google Specific --> 

  </head>
  <body id="<?= $body_id ?>" class="<?= $body_class ?>">
    
    <header class="sticky-force">
      <div class="left">
        <div class="logo">
          <a href="./index.php" title="Accueil">Accueil</a>
        </div>
      </div>
    </header>  
    
    <div id="main">
      <div class="left">
        <div id="main-menu">
          <button id="nav-icon">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
          </button>
          <?php
            $menu = file_exists(dirname(__DIR__) . '/common/menu.php') ? 'menu.php' : 'menu_base.php';
            require $menu;
          ?>
        </div>
      </div>
      <div class="right"> 
        <div class="contenu">   
          
          
          