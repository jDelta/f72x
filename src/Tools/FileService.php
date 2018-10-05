<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Tools;

use ZipArchive;
use F72X\Company;

class FileService {

    public static function doZip($filename) {
        $fileDir = Company::getRepositoryPath();
        $zipFilename = explode('.', $filename)[0] . '.zip';
        $zip = new ZipArchive();
        if ($zip->open("$fileDir/zip/$zipFilename", ZipArchive::CREATE) === TRUE) {
            $zip->addFile("$fileDir/sxml/S-$filename", $filename);
            $zip->close();
        }
    }

    public static function getBase64($filePath) {
        return base64_encode(file_get_contents($filePath));
    }

}
