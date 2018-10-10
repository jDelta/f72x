<?php

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
