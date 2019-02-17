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
use F72X\Sunat\Catalogo;

/**
 * AbstractSummary
 * 
 * Base class for Resumen Diario y Comunicación de baja.
 */
abstract class AbstractPerRet extends AbstractDocument {

    protected $isPercepcion = false;
    protected $isRetencion = false;

    /**
     * The catalog number of Retencion or Retencion Regime
     * 
     * Use:
     *  22: Percepción
     *  23: Retención
     * 
     * @var string 
     */
    protected $regimeCatNumber;

    protected function parseInput(array $in) {
        $out = $in;
        $lines  = $this->parseInputLines($in['lines']);
        $out['percent'] = Catalogo::getCatItemFieldValue($this->regimeCatNumber, $in['systemCode'], 'tasa');
        $out['lines'] = $lines;
        // Calculate totals
        $totalInvoiceAmount = 0;
        $totalCashed = 0;
        foreach ($lines as $line) {
            $totalInvoiceAmount += $line['operationAmount'];
            $totalCashed += $line['netTotal'];
        }
        $out['totalInvoiceAmount'] = $totalInvoiceAmount;
        $out['total'] = $totalCashed;
        return $out;
    }
    public function parseInputLine(array $in) {
        $bodyInput = $this->getRawData();
        $percent = Catalogo::getCatItemFieldValue($this->regimeCatNumber, $bodyInput['systemCode'], 'tasa');
        $out = $in;
        // Set amount and netTotalCashed
        $exchangeCalculationRate = ($out['currencyCode'] == self::LOCAL_CURRENCY_CODE) ? 1 : $out['exchangeRate'];
        $paymentBase = $out['payment']['paidAmount'] * $exchangeCalculationRate;
        $opAmount = ($paymentBase * $percent)/100;
        // Percepcion base + amount
        // Retencion  base - amount
        $operator = $this->isPercepcion ? 1 : -1;
        $out['operationAmount'] = $opAmount;
        $out['netTotal'] = $paymentBase + ($operator * $opAmount);
        return $out;
    }
    public function setBodyFields() {
        $data = $this->getParsedData();
        // Lines
        $this->lines = $data['lines'];
    }

    public function getDataForXml() {
        $in = $this->getParsedData();
        // Get base fields
        $baseFields = $this->getBaseFieldsForXml();
        $fields = [
            // Regimén
            'systemCode'         => $in['systemCode'],
            'percent'            => Operations::formatAmount($in['percent']),
            'totalInvoiceAmount' => Operations::formatAmount($in['totalInvoiceAmount']),
            'total'              => Operations::formatAmount($in['total']),
            'note'               => $in['note'],
            'customer'           => $in['customer']
        ];
        return array_merge($baseFields, $fields);
    }

    protected function getLineForXml(array $in) {
        $out = $in;
        // Parse payment
        $payment = $out['payment'];
        $out['payment']['paidAmount'] = Operations::formatAmount($payment['paidAmount']);
        $out['totalInvoiceAmount'] = Operations::formatAmount($in['totalInvoiceAmount']);
        $out['netTotal'] = Operations::formatAmount($in['netTotal']);
        // Per Ret information
        
        return $out;
    }
}
