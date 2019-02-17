<?php

// Validations for: RETENCIÓN

use F72X\Sunat\Catalogo;

return [
    'documentSeries' => [
        'required' => true
    ],
    'documentNumber' => [
        'required' => true
    ],
    'issueDate' => [
        'required' => true
    ],
    'systemCode' => [
        'required' => true,
        'inCat'    => Catalogo::CAT_RETENCION_REGIME
    ],
    'note' => [
        'required' => true
    ],
    'customer' => [
        'required' => true,
        'type'     => 'Array'
    ],
    'lines' => [
        'required' => true,
        'type' => 'Array'
    ]
];
