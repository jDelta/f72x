<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 *
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat;

use F72X\F72X;
use F72X\Company;
use SoapClient;
use SoapHeader;
use SoapVar;

class SunatSoapClient extends SoapClient {

    public function __construct($wsdl, $options) {
        $prodMode = F72X::isProductionMode();
        $ruc = Company::getRUC();
        $solUser = $prodMode ? Company::getSolUser() : SunatVars::SUNAT_SOL_USER_BETA;
        $solKey = $prodMode ? Company::getSolKey() : SunatVars::SUNAT_SOL_KEY_BETA;

        $nsWsse = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $WSHeader = <<<SEC
<wsse:Security xmlns:wsse="$nsWsse">
    <wsse:UsernameToken>
        <wsse:Username>$ruc$solUser</wsse:Username>
        <wsse:Password>$solKey</wsse:Password>
    </wsse:UsernameToken>
</wsse:Security>
SEC;

        $headers = new SoapHeader($nsWsse, 'Security', new SoapVar($WSHeader, XSD_ANYXML));
        //set the Headers of Soap Client.
        $this->__setSoapHeaders($headers);
        parent::__construct($wsdl, $options);
    }

    public static function getService() {
        $serviceUrl = F72X::isProductionMode() ?
                SunatVars::SUNAT_SERVICE_URL_PROD : SunatVars::SUNAT_SERVICE_URL_BETA;

        return new SunatSoapClient("$serviceUrl?wsdl", ['trace' => true]);
    }

    public static function getCdrService() {
        $serviceUrl = SunatVars::SUNAT_CDR_SERVICE_URL;
        return new SunatSoapClient("$serviceUrl?wsdl", ['trace' => true]);
    }

}
