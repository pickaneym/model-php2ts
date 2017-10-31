<?php

require_once('global.php');
require_once(__DIR__ . DS . 'parser/Parser.php');

if (!file_exists(ROOT_PATH . DS . 'tmp')) {
    mkdir(ROOT_PATH . DS . 'tmp');
}

$storeFolder = 'tmp';

if (!empty($_FILES)) {

    $tempFile = $_FILES['file']['tmp_name'];

    $targetPath = '..' . DS . $storeFolder . DS;

    $targetFile = $targetPath . $_FILES['file']['name'];

    if ($_FILES['file']['name'] !== 'model.zip') {
        http_response_code(400);
        throw new Exception('Zip file name must be model.zip only!');
    }


    move_uploaded_file($tempFile, $targetFile);

    $zip = new ZipArchive;
    $res = $zip->open($targetFile);
    if ($res === true) {
        $zip->extractTo($targetPath);
        $zip->close();
        convertPHPModelsToTS();
    } else {
        throw new Exception('Something is wrong with your zip file!');
    }
}
