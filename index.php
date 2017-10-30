<?php

require_once('app/parser/NodeVisitor.php');

$parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);

$dir = __DIR__ . DIRECTORY_SEPARATOR;
$phpModelsDir = $dir . 'test' . DIRECTORY_SEPARATOR . 'model';
$tsModelsDir = $dir . 'test' . DIRECTORY_SEPARATOR . 'ts-models';

//// iterate over all .php files in the directory
$phpModelFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($phpModelsDir));
$phpModelFiles = new RegexIterator($phpModelFiles, '/\.php$/');

foreach ($phpModelFiles as $file) {
    try {
        $code = file_get_contents($file);

        $traverser = new PhpParser\NodeTraverser;
        $visitor = new parser\NodeVisitor;
        $traverser->addVisitor($visitor);

        $stmts = $parser->parse($code);
        $stmts = $traverser->traverse($stmts);

        // Replace `php file` dir path with ts models dir path.
        $tsModelsPath = substr_replace($file, $tsModelsDir, 0, strlen($phpModelsDir));
        $fileInfo = pathinfo($tsModelsPath);
        $tsFileName = $fileInfo['dirname'] . DIRECTORY_SEPARATOR . camel2dashed($fileInfo['filename']) . '.model.ts';

        if (!file_exists($fileInfo['dirname'])) {
            mkdir($fileInfo['dirname']);
        }

        file_put_contents($tsFileName, $visitor->getTypescriptClass());

    } catch (PhpParser\Error $e) {
        echo 'Parse Error: ', $e->getMessage();
    }

}


function camel2dashed($fileName){
    return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $fileName));
}
