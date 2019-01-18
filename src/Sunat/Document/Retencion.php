<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat\Document;

class Retencion extends AbstractPerRet {

    protected $seriesPrefix = 'R';
    protected $xmlTplName = 'Retention.xml';

}
