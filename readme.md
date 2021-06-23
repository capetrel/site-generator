Read me
===

## Introduction
Le programme génère des fichiers php à partir d'un fichier html généré à l'aide de Adobe Indesign.
Ces fichiers permettrons d'automatiser une bonne partir de la création d'un site qui correspond à un document pdf.
Il faut que le documentRoot de Apache pointe vers le dossier 'web'. Le programme génère dans le dossier web un dossier avec le nom du projet et sera accessile via l'url "http://domain.tld/web/nom-du-projet"

## Développement
le
Pour des ajustements spécifiques à un projet il faudra utiliser les fichiers custom.css et custom.js dans ce dernier.
Par défaut un projet utilise les fichiers header_base.php, footer_base.php et menu_base.php. Mais si un projet à des besoins particuliers il faudra les copier et les renommer header.php, footer.php et menu.php.  
Ainsi les modifications ne seront pas écrasées en cas de nouvelle génération de projet.

## Styles et javascript
Le style et le javascript sont dans le dossier assets. Laravel mix est installé et permet de de compiler les fichier js et css qui seront ensuite copier. La commande npù doit être lancé depuis le dossier assets. Vu qu'il peut y avoir plusieurs projets il faudra adapter les chemins dans le fichier webpack.mix.js à chaque fois.

## Création d'un nouveau projet
- Dans 'generators' Créer le dossier du projet avec un nom formater uniquement avec des lettres et tiret : nom-du-dossier  
- Copier les fichiers config.php et conversion.php depuis le dossier src dans le dossier nouvellement crée.  
- Les images peuvent êtres incluses dans un dossier nommé 'image', il sera fusionner avec le dossier image du dossier assets (attention a ce que 2 fichier n'est pas le même nom.)
- Dans le fichier config.php du projet, renseigner les variables :
```php
$config = [
    'site-name' => '',
    'site-slogan' => '',
];
$project_dirname = 'nom-du-dossier';
```
$project_dirname doit impérativement correspondre au nom du dossier du projet

Lancer la conversion, uniquement en ligne de commande. Tous les dossiers sont protéger d'accès HTTP excepté le dossier web : 
```batch
cd generators/nom-du-projet/;
php conversion.php
```
S'assurer que le nom du dossier du projet soit le même dans 'generator' et 'web'.  
Dans le dossier 'web'.  
Le script créé le dossier du projet et copie dedans le contenu du dossier assets à l'exception des fichier et dossier de développement.  
Le dossier common est lui aussi copier mais à la racine du projet et non dans le dossier assets.  
Le script copie également les dossiers contenu dans le projet (image...).
Par contre il ne copie aucun fichiers php ou html (sauf le fichier config.php).

Ensuite le script créé :
- Les différents fichiers php liées aux pages du fichier HTML source, avec leurs variables respectives.
- Le code html du menu dans le fichier menu_base.php
- Les fichier common/index_base.php et index.php. le fichier index.php ne sera pas écrasé et index_base.php permet de voir/copier les variables disponilbes.

Enfin il faudra manuellement faire les ajouts de pages spécifique, et ajuster le menu.