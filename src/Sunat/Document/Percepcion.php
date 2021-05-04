<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat\Document;

class Percepcion extends AbstractPerRet {

    protected $isPercepcion = true;
    protected $seriesPrefix = 'P';
    protected $regimeCatNumber = 22;
    protected $xmlTplName = 'Perception.xml';
    public function validateInput(array $inputData) {
        
    }

}
