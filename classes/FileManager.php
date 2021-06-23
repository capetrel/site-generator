<?php

namespace App;

use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileManager
{

    private string $DS;

    public function __construct(string $DS = '/')
    {
        $this->DS = $DS;
    }

    /**
     * Fait une copie d'un fichier dans un dossier cible, optionnel ajout d'un suffix
     * @param string $source Chemin du fichier source
     * @param string $target_dir Chemin du fichier de destination
     */
    public function copyFileToDir(string $source, string $target_dir): void{
        if(file_exists ($source)){
                copy($source, $target_dir);
        }
    }

    /**
     * Copie des fichiers d'un dossier source vers un dossier cible. Option ajout d'un suffix au nom du fichier
     * @param string $src Dossier source
     * @param string $dst Dossier cible
     * @param string $suffix Optionnel suffix dans le nom du fichier.
     * @throws Exception Vérification du type passer à suffix
     */
    public function recurseCopy(string $src, string $dst, string $suffix = ''): void {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file !== '.' ) && ( $file !== '..' )) {
                if(is_string($suffix)) {
                    if ( is_dir($src . $this->DS . $file) ) {
                        $this->recurseCopy($src . $this->DS . $file, $dst . $this->DS . $suffix . $file);
                    }
                    else {
                        copy($src . $this->DS . $file,$dst . $this->DS . $suffix . $file);
                    }
                } else {
                    throw new Exception("L'argument $suffix n'est pas une chaîne de caractères");
                }
            }
        }
        closedir($dir);
    }

    public function recurseCopyWithExclusion(string $src, string $dst, array $excluded_dirs = [], array $excluded_files = []) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file !== '.' ) && ( $file !== '..' ) && ( $file !== '.htaccess' )) {
                $file_info = pathinfo($file);
                if ( is_dir($src . $this->DS . $file) && !in_array($file, $excluded_dirs) ) {
                    $this->recurseCopy($src . $this->DS . $file, $dst . $this->DS . $file);
                } else {
                    if( isset($file_info['extension']) && !in_array($file_info['extension'], $excluded_files)) {
                        copy($src . $this->DS . $file,$dst . $this->DS . $file);
                    }
                }
            }
        }
        closedir($dir);
    }

    /**
     * @param string $dir chemin du dossier à vider
     * @return bool
     */
    public function deleteFiles(string $dir) {
        $di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ( $ri as $file ) {
            $file->isDir() ?  rmdir($file) : unlink($file);
        }
        return true;
    }


}