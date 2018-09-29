<?php

namespace F72X\Tools;

use F72X\Company;
use Greenter\XMLSecLibs\Sunat\SignedXml;

class XmlDSig {

    /**
     * 
     * @param string $xmlFileName
     */
    public static function sign($xmlFileName) {
        // Directories
        $repository = Company::getRepositoryPath();
        $cert = Company::getCertPath();
        // Load the XML to be signed
        $xmlPath = "$repository/xml/$xmlFileName";

        $signer = new SignedXml();
        $signer->setCertificateFromFile($cert);

        $xmlSigned = $signer->signFromFile($xmlPath);
        file_put_contents("$repository/sxml/S-$xmlFileName", $xmlSigned);
    }

}
