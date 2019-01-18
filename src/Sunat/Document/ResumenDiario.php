<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat\Document;

use F72X\Sunat\Operations;

class ResumenDiario extends AbstractSummary {

    protected $idPrefix = 'RC';
    protected $xmlTplName = 'SummaryDocuments.xml';
    protected $lineDefaults = [
        'currencyCode' => 'PEN',
        'taxableOperations' => 0,
        'exemptedOperations' => 0,
        'unaffectedOperations' => 0,
        'freeOperations' => 0,
        'totalCharges' => 0,
        'totalIsc' => 0,
        'totalIgv' => 0,
        'totalOtherTaxes' => 0,
        'affectedDocType' => null,
        'affectedDocSeries' => null,
        'affectedDocNumber' => null,
        'perceptionRegimeType' => null,
        'perceptionPercentage' => null,
        'perceptionBaseAmount' => null,
        'perceptionAmount' => null,
        'perceptionIncludedAmount' => null
    ];

    public function parseInputLine(array $rawInputLine) {
        $parsedFields = [
            'payableAmount'            => Operations::formatAmount($rawInputLine['payableAmount']),
            'taxableOperations'        => Operations::formatAmount($rawInputLine['taxableOperations']),
            'exemptedOperations'       => Operations::formatAmount($rawInputLine['exemptedOperations']),
            'unaffectedOperations'     => Operations::formatAmount($rawInputLine['unaffectedOperations']),
            'freeOperations'           => Operations::formatAmount($rawInputLine['freeOperations']),
            'totalCharges'             => Operations::formatAmount($rawInputLine['totalCharges']),
            'totalIsc'                 => Operations::formatAmount($rawInputLine['totalIsc']),
            'totalIgv'                 => Operations::formatAmount($rawInputLine['totalIgv']),
            'totalOtherTaxes'          => Operations::formatAmount($rawInputLine['totalOtherTaxes']),
            'perceptionPercentage'     => Operations::formatAmount($rawInputLine['perceptionPercentage']),
            'perceptionBaseAmount'     => Operations::formatAmount($rawInputLine['perceptionBaseAmount']),
            'perceptionAmount'         => Operations::formatAmount($rawInputLine['perceptionAmount']),
            'perceptionIncludedAmount' => Operations::formatAmount($rawInputLine['perceptionIncludedAmount']),
        ];
        return array_merge($rawInputLine, $parsedFields);
    }

}
