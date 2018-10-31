<?php

return [
    'currencyCode'      => 'PEN',                 // Tipo de moneda (ISO 4217)
    'type'              => '02',                  // Tipo de nota, Catálogo #10
    'documentSeries'    => 'FD01',                // Serie FC##|FD##|BC##|BD##
    'documentNumber'    => 211,                   // Número correlativo,
    'affectedDocType'   => '01',                  // Tipo de documento al que aplica
                                                  // 01|03|12 : Factura|Boleta|Ticket de maquina registradora
    'affectedDocId'     => 'F001-4355',           // Documento al que aplica Serie-Numero
    'customerDocType'   => '6',                   // Tipo de documento del cliente Catálogo #6
    'customerDocNumber' => '20587896411',         // RUC del cliente
    'customerRegName'   => 'SERVICABINAS S.A.',   // Razón social|Nombre del cliente
    'customerAddress'   => '215 NY STREET',       // Dirección del cliente
    'issueDate'         => '2017-06-25T20:25:41', // Fecha de emisión - ISO 8601 date
    'description'       => 'Ampliación de garantía de memoria DDR-3B1333Kingston',
    'items' => [
        [
            'productCode'        => 'GLG199',    // Código
            'unspsc'             => '32101622',  // Código de producto SUNAT
            'unitCode'           => 'ZZ',        // Código de unidad
            'quantity'           => 250,         // Cantidad
            'description'        => 'Ampliación de garantía de memoria DDR-B1333 Kingston', // Descripción detallada
            'priceType'          => '01',        // Catálogo #16 [01:Precio Unitario|02:Valor Referencial]
            'taxType'            => '1000',      // Catálogo #5
            'igvAffectationType' => '10',        // Catálogo #7
            'unitPrice'          => 5,           // Precio Unitario/Valor refencial
            'igvIncluded'        => true
        ]
    ]
];