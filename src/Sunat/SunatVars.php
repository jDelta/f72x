<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

class SunatVars {
    const SUNAT_SERVICE_URL_PROD = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
    const SUNAT_SERVICE_URL_BETA = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
    const SUNAT_SOL_USER_BETA    = 'MODDATOS';
    const SUNAT_SOL_KEY_BETA     = 'moddatos';
    const DIR_CATS               = __DIR__ . '/catalogo';
    const IGV         = 0.18;
    const IGV_PERCENT = 18.00;

}
