<?php

require_once('NodeVisitor.php');
require_once(__DIR__ . '/../global.php');

function convertPHPModelsToTS(){
    $parser = new PhpParser\Parser(new \PhpParser\Lexer\Emulative());

    if (!file_exists(TS_MODELS_DIR)) {
        mkdir(TS_MODELS_DIR);
    }

    // iterate over all .php files in the directory
    $phpModelFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(PHP_MODELS_DIR));
    $phpModelFiles = new RegexIterator($phpModelFiles, '/\.php$/');

    foreach ($phpModelFiles as $file) {
        try {
            $code = file_get_contents($file);

            $traverser = new PhpParser\NodeTraverser;
            $visitor = new parser\NodeVisitor;
            $traverser->addVisitor($visitor);

            $stmts = $parser->parse($code);
            $traverser->traverse($stmts);

            // Replace `php file` dir path with ts models dir path.
            $tsModelsPath = substr_replace($file, TS_MODELS_DIR, 0, strlen(PHP_MODELS_DIR));
            $fileInfo = pathinfo($tsModelsPath);
            $tsFileName = $fileInfo['dirname'] . DS . camel2dashed($fileInfo['filename']) .
                          '.model.ts';

            if (!file_exists($fileInfo['dirname'])) {
                mkdir($fileInfo['dirname']);
            }

            file_put_contents($tsFileName, $visitor->getTypescriptClass());

        } catch (PhpParser\Error $e) {
            echo 'Parse Error: ', $e->getMessage();
        }

    }

    $source = TS_MODELS_DIR;
    $zip_file_name = 'ts-models.zip';
    $pathToZip = ROOT_PATH . DS . $zip_file_name;

    if (file_exists($pathToZip)) {
        unlink($pathToZip);
    }

    $za = new FlxZipArchive;
    $res = $za->open($pathToZip, ZipArchive::CREATE);
    if ($res === true) {
        $za->addDir($source, 'ts-models');
        $za->close();
    } else {
        echo 'Could not create a zip archive';
    }

    cleanUpTmpFiles(ROOT_PATH . DS . 'tmp' . DS);
}

/*
 * php delete function that deals with directories recursively
 */
function cleanUpTmpFiles($target){
    if (is_dir($target)) {
        $files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned

        foreach ($files as $file) {
            cleanUpTmpFiles($file);
        }

        // Checks if dir is empty
        if (!(new FilesystemIterator($target))->valid()) {
            rmdir($target);
        }
    } elseif (is_file($target)) {
        unlink($target);
    }
}

// Source: https://stackoverflow.com/a/19451938
class FlxZipArchive extends ZipArchive{

    /** Add a Dir with Files and Subdirs to the archive;
     * @param string $location Real Location;
     * @param string $name Name in Archive;
     */
    public function addDir($location, $name){
        $this->addEmptyDir($name);
        $this->addDirDo($location, $name);
    }

    /**
     * Add Files & Dirs to archive;
     * @param string $location Real Location;
     * @param string $name Name in Archive;
     */
    private function addDirDo($location, $name){
        $name .= '/';
        $location .= '/';
        // Read all Files in Dir
        $dir = opendir($location);
        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            // Rekursiv, If dir: FlxZipArchive::addDir(), else ::File();
            $do = (filetype($location . $file) == 'dir') ? 'addDir' : 'addFile';
            $this->$do($location . $file, $name . $file);
        }
    }
}

