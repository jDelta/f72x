<?php

return [
    'currencyCode'      => 'PEN',                 // Tipo de moneda (ISO 4217)
    'noteType'          => '07',                  // Tipo de nota, Catálogo #9
    'documentSeries'    => 'C01',                 // Serie C##|D##
    'documentNumber'    => 211,                   // Número correlativo,
    'affectedDocType'   => '01',                  // Tipo de documento al que aplica
                                                  // 01|03|12 : Factura|Boleta|Ticket de maquina registradora
    'affectedDocId'     => 'F001-4355',           // Documento al que aplica Serie-Numero
    'customerDocType'   => '6',                   // Tipo de documento del cliente Catálogo #6
    'customerDocNumber' => '20587896411',         // RUC del cliente
    'customerRegName'   => 'SERVICABINAS S.A.',   // Razón social|Nombre del cliente
    'customerAddress'   => '215 NY STREET',       // Dirección del cliente
    'issueDate'         => '2017-06-25T20:25:41', // Fecha de emisión - ISO 8601 date
    'note'              => 'Unidades defectuosas, no leen CD que contengan archivos MP3',
                                                  // Motivo de emisión de la Nota de Crédito
    'items' => [
        [
            'productCode'        => 'GLG199',    // Código
            'unspsc'             => '52161515',  // Código de producto SUNAT
            'unitCode'           => 'NIU',       // Código de unidad
            'quantity'           => 100,        // Cantidad
            'description'        => 'Grabadora LG Externo Modelo: GE20LU10', // Descripción detallada
            'priceType'          => '01',        // Catálogo #16 [01:Precio Unitario|02:Valor Referencial]
            'taxType'            => '1000',      // Catálogo #5
            'igvAffectationType' => '10',        // Catálogo #7
            'unitPrice'          => 98.00,       // Precio Unitario/Valor refencial
            'igvIncluded'        => true
        ]
    ]
];