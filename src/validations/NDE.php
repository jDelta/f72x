<?php

// Validations for: FACTURA

use F72X\Sunat\Catalogo;

return [
    'currencyCode' => [
        'required' => true,
        'inCat' => Catalogo::CAT_CURRENCY_TYPE
    ],
    'noteType' => [
        'required' => true,
        'inCat' => Catalogo::CAT_NOTA_DEBITO_TYPE
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
    'affectedDocType' => [
        'required' => true
    ],
    'affectedDocId' => [
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
        'required' => true
    ],
    'customerRegName' => [
        'required' => true
    ],
    'items' => [
        'required' => true,
        'type' => 'Array'
    ]
];
