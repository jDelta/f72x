<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat\Document;

use F72X\UblComponent\Invoice;
use F72X\Sunat\DetailMatrix;

abstract class SunatDocument extends Invoice {

    /** @CAT1 Código tipo de documento*/
    const DOC_FACTURA       = '01';
    const DOC_BOLETA        = '03';
    const DOC_NOTA_CREDITO  = '07';
    const DOC_NOTA_DEBITO   = '08';

    const UBL_VERSION_ID = '2.1';
    const CUSTUMIZATION_ID = '2.0';

    /** @var DetailMatrix */
    private $DetailMatrix;

    public function getDetailMatrix() {
        return $this->DetailMatrix;
    }

    public function setDetailMatrix(DetailMatrix $DetailMatrix) {
        $this->DetailMatrix = $DetailMatrix;
        return $this;
    }

}
