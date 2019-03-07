<?php

return [
    'legalValidity'  => true,
    'documentNumber' => 1,            // Número correlativo de la comunicación de baja
    'issueDate'      => '2011-04-02', // Fecha de generación del documento - ISO 8601 date
    'referenceDate'  => '2011-04-01', // Fecha de emisión de los comprobantes - ISO 8601 date
    'items' => [
        [
            'documentType'   => '01',              // Cátalogo #1: Tipo de comprobante
            'documentSeries' => 'F001',            // Serie de comprobante
            'documentNumber' => 1,                 // Número del comprobante
            'voidReason'     => 'Error en sistema' // Motivo de baja
        ],
        [
            'documentType'   => '01',
            'documentSeries' => 'F001',
            'documentNumber' => 15,
            'voidReason'     => 'Cancelación'
        ]
    ]
];