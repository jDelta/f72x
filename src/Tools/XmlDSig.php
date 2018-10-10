<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Tools;

use F72X\Company;
use F72X\Repository;
use Greenter\XMLSecLibs\Sunat\SignedXml;

class XmlDSig {

    /**
     * 
     * @param string $billName
     */
    public static function sign($billName) {
        $cert = Company::getCertPath();
        // Load the XML to be signed
        $xmlPath = Repository::getBillPath($billName);

        $signer = new SignedXml();
        $signer->setCertificateFromFile($cert);

        $signedXml = $signer->signFromFile($xmlPath);
        Repository::saveSignedBill($billName, $signedXml);
    }

}
