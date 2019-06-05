<?php

return [
    'legalValidity'  => true,
    'documentNumber' => 1,
    'issueDate'      => '2017-11-18', // Fecha de generación del resumen diario - ISO 8601 date
    'referenceDate'  => '2017-11-18', // Fecha de emisión de los comprobantes - ISO 8601 date
    'items' => [
        [
            'itemOperation'  => 1, // 1: Modificar | 2: Modificar | 3: Anular
            'documentType'   => '03',    // Cátalogo #1: Tipo de comprobante
            'documentSeries' => 'B1A5',  // Serie de la boleta o nota
            'documentNumber' => 1101001, // Numero de la boleta o nota
            'currencyCode'   => 'PEN',   // Tipo de moneda (ISO 4217)
            'customerDocType'   => '6',           // Tipo de documento Catálogo #6
            'customerDocNumber' => '10098237761', // Numero de documento del cliente
            'taxableOperations'    => 1500, // [?] Total operaciones gravadas
            'exemptedOperations'   => null, // [?] Total operaciones exoneradas
            'unaffectedOperations' => 232,  // [?] Total operaciones inafectas
            'freeOperations'       => 250,  // [?] Total operaciones gratuitas
            'totalCharges'         => 5,    // Total cargos
            'totalIgv'             => 270,  // Total IGV
            'payableAmount'        => 3207, // Importe total de la venta
            'perceptionRegimeType'     => null, // [?] Regímen de la percepción
            'perceptionPercentage'     => null, // [?] Porcentaje de la percepción
            'perceptionBaseAmount'     => null, // [?] Base imponible de la percepcion
            'perceptionAmount'         => null, // [?] Monto de la perception
            'perceptionIncludedAmount' => null  // [?] "Monto total a cobrar incluida la percepción
        ],
        [
            'itemOperation'     => 1,
            'documentType'      => '03',
            'documentSeries'    => 'B1A5',
            'documentNumber'    => 1101002,
            'currencyCode'      => 'PEN',
            'customerDocType'   => '6',
            'customerDocNumber' => '10401308487',
            'taxableOperations'  => 800,
            'exemptedOperations' => 100,
            'totalIgv'           => 144,
            'payableAmount'      => 1044
        ],
        [
            'itemOperation'     => 1,
            'documentType'      => '07',
            'documentSeries'    => 'BC20',
            'documentNumber'    => 171872,
            'currencyCode'      => 'PEN',
            'customerDocType'   => '1',
            'customerDocNumber' => '09728737',
            'taxableOperations' => 1200,
            'totalIgv'          => 216,
            'payableAmount'     => 1416,
            'affectedDocType'   => '03',
            'affectedDocId'     => 'B1A5-1100992'
        ],
        [
            'itemOperation'      => 1,
            'documentType'       => '03',
            'documentSeries'     => 'B1A5',
            'documentNumber'     => 1101004,
            'currencyCode'      => 'PEN',
            'customerDocType'    => '6',
            'customerDocNumber'  => '10304567812',
            'taxableOperations'  => 2000,
            'exemptedOperations' => 1000,
            'freeOperations'     => 250,
            'totalCharges'       => 345,
            'totalIsc'           => 150,
            'totalIgv'           => 360,
            'payableAmount'      => 3855
        ],
        [
            'itemOperation'     => 1,
            'documentType'      => '03',
            'documentSeries'    => 'B1A5',
            'documentNumber'    => 1101005,
            'currencyCode'      => 'PEN',
            'customerDocType'   => '1',
            'customerDocNumber' => '72670972',
            'freeOperations'    => 250,
            'payableAmount'     => 0
        ],
        [
            'itemOperation'     => 1,
            'documentType'      => '03',
            'documentSeries'    => 'BC20',
            'documentNumber'    => 1101007,
            'currencyCode'      => 'PEN',
            'customerDocType'   => '6',
            'customerDocNumber' => '10676663376',
            'taxableOperations' => 1250,
            'totalIgv'          => 216,
            'payableAmount'     => 1475,
            'perceptionRegimeType'     => '01',
            'perceptionPercentage'     => 2,
            'perceptionBaseAmount'     => 1475,
            'perceptionAmount'         => 29.5,
            'perceptionIncludedAmount' => 1504
        ]
    ]
];