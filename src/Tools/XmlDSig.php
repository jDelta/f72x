<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Tools;

use F72X\Company;
use F72X\Repository;
use Greenter\XMLSecLibs\Sunat\SignedXml;

class XmlDSig {

    /**
     * 
     * @param string $documentName
     */
    public static function sign($documentName) {
        $cert = Company::getCertPath();
        // Load the XML to be signed
        $xmlPath = Repository::getXmlPath($documentName);

        $signer = new SignedXml();
        $signer->setCertificateFromFile($cert);

        $signedXml = $signer->signFromFile($xmlPath);
        Repository::saveSignedXml($documentName, $signedXml);
    }

}
