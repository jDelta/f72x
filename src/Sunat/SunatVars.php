<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat;

class SunatVars {

    const SUNAT_CDR_SERVICE_URL  = 'https://e-factura.sunat.gob.pe/ol-it-wsconscpegem/billConsultService?wsdl';
    const SUNAT_SERVICE_URL_PROD = 'https://comprobante.pe/xwsdl/billlService.php'; // Customized the provided for SUNAT doesn't work
    const SUNAT_SERVICE_URL_BETA = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
    const SUNAT_SOL_USER_BETA = 'MODDATOS';
    const SUNAT_SOL_KEY_BETA  = 'moddatos';
    const IGV         = 0.18;
    const IGV_PERCENT = 18.00;

}
