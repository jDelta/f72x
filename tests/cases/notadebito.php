<?php

return [
    'documentSeries'    => 'D01',                 // Serie C##|D##
    'documentNumber'    => 211,                   // Número correlativo,
    'currencyCode'      => 'PEN',                 // Tipo de moneda (ISO 4217)
    'noteType'          => '02',                  // Tipo de nota, Catálogo #10
    'affectedDocType'   => '01',                  // Tipo de documento al que aplica
                                                  // 01|03|12 : Factura|Boleta|Ticket de maquina registradora
    'affectedDocId'     => 'F001-4355',           // Documento al que aplica Serie-Numero
    'customerDocType'   => '6',                   // Tipo de documento del cliente Catálogo #6
    'customerDocNumber' => '20587896411',         // RUC del cliente
    'customerRegName'   => 'SERVICABINAS S.A.',   // Razón social|Nombre del cliente
    'customerAddress'   => '215 NY STREET',       // Dirección del cliente
    'issueDate'         => '2017-06-25T20:25:41', // Fecha de emisión - ISO 8601 date
    'note'              => 'Ampliación de garantía de memoria DDR-3B1333Kingston',
                                                  // Motivo de emisión de la Nota de Dédito
    'items' => [
        [
            'productCode'        => 'GLG199',    // Código
            'unspsc'             => '32101622',  // Código de producto SUNAT
            'unitCode'           => 'ZZ',        // Código de unidad
            'quantity'           => 250,         // Cantidad
            'description'        => 'Ampliación de garantía de memoria DDR-B1333 Kingston', // Descripción detallada
            'priceType'          => '01',        // Catálogo #16 [01:Precio Unitario|02:Valor Referencial]
            'taxType'            => '9997',      // Catálogo #5
            'igvAffectationType' => '20',        // Catálogo #7
            'unitPrice'          => 5,           // Precio Unitario/Valor refencial
            'igvIncluded'        => true
        ]
    ]
];