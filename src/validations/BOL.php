<?php

// Validations for: BOLETA DE VENTA

use F72X\Sunat\Catalogo;

return [
    'currencyCode' => [
        'required' => true,
        'inCat' => Catalogo::CAT_CURRENCY_TYPE
    ],
    'operationType' => [
        'required' => true,
        'inCat' => Catalogo::CAT_FACTURA_TYPE
    ],
    'documentType' => [
        'required' => true
    ],
    'documentSeries' => [
        'required' => true
    ],
    'documentNumber' => [
        'required' => true
    ],
    'issueDate' => [
        'required' => true
    ],
    'customerDocType' => [
        'required' => true,
        'inCat' => Catalogo::CAT_IDENT_DOCUMENT_TYPE
    ],
    'customerDocNumber' => [
        'required' => true,
        'type'     => 'Dni'
    ],
    'customerRegName' => [
        'required' => true
    ],
    'items' => [
        'required' => true,
        'type' => 'Array'
    ]
];
