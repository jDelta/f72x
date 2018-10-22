<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Tools;

use F72X\Company;
use F72X\Sunat\InvoiceDocument;
use F72X\Sunat\Operations;
use F72X\Sunat\Catalogo;
use F72X\Sunat\SunatVars;

class FSInputGenerator {

    public static function generateFactura(array $data, $companyRUC, $currencyType = 'PEN') {
        $Invoice = new InvoiceDocument($data, Catalogo::CAT1_FACTURA, $currencyType);
        self::generateFSTextInput($Invoice, $companyRUC);
    }

    public static function generateBoleta(array $data, $companyRUC, $currencyType = 'PEN') {
        $Invoice = new InvoiceDocument($data, Catalogo::CAT1_BOLETA, $currencyType);
        self::generateFSTextInput($Invoice, $companyRUC);
    }

    private static function generateFSTextInput(InvoiceDocument $Invoice, $companyRUC) {
        $issueDate = $Invoice->getIssueDate();
        $Items     = $Invoice->getItems();
        $json = [
            'cabecera' => [
                'tipOperacion'      => $Invoice->getOperationType(),
                'fecEmision'        => $issueDate->format('Y-m-d'),
                'horEmision'        => $issueDate->format('H:i:s'),
                'fecVencimiento'    => '-',
                'codLocalEmisor'    => Company::getRegAddressCode(),
                'tipDocUsuario'     => $Invoice->getCustomerDocType(),
                'numDocUsuario'     => $Invoice->getCustomerDocNumber(),
                'rznSocialUsuario'  => $Invoice->getCustomerRegName(),
                'tipMoneda'         => $Invoice->getCurrencyType(),
                
                'sumTotTributos'    => Operations::formatAmount($Invoice->getTotalTaxes()),
                'sumTotValVenta'    => Operations::formatAmount($Invoice->getBillableValue()),
                'sumPrecioVenta'    => Operations::formatAmount($Invoice->getPayableAmount()),
                'sumDescTotal'      => Operations::formatAmount($Invoice->getTotalAllowances()),
                'sumOtrosCargos'    => 0.00,
                'sumTotalAnticipos' => 0.00,
                'sumImpVenta'       => Operations::formatAmount($Invoice->getPayableAmount()),

                'ublVersionId'      => '2.1',
                'customizationId'   => '2.0'
            ],
            'detalle' => [],
            'variablesGlobales' => self::getVariablesGlobales($Invoice)
        ];
        $ln = $Items->getCount();
        for ($rowIndex = 0; $rowIndex < $ln; $rowIndex++) {
            $cat5Item = Catalogo::getCatItem(5, $Items->getTaxTypeCode($rowIndex));
            // IGV percent
            if ($cat5Item['id'] === Catalogo::CAT5_IGV) {
                $porIgvItem = Operations::formatAmount(SunatVars::IGV_PERCENT);
            } else {
                $porIgvItem = '0.00';
            }
            if ($Items->getPriceTypeCode($rowIndex) === Catalogo::CAT16_REF_VALUE) {
                $mtoPrecioVentaUnitario      = '0.00';
                $mtoValorReferencialUnitario = Operations::formatAmount($Items->getUnitValue($rowIndex));
            } else {
                $mtoPrecioVentaUnitario      = Operations::formatAmount($Items->getUnitTaxedValue($rowIndex));
                $mtoValorReferencialUnitario = '0.00';
            }
            $item = [
                'codUnidadMedida'       => $Items->getUnitCode($rowIndex),
                'ctdUnidadItem'         => $Items->getQunatity($rowIndex),
                'codProducto'           => $Items->getProductCode($rowIndex),
                'codProductoSUNAT'      => $Items->getUNPSC($rowIndex),
                'desItem'               => $Items->getDescription($rowIndex),
                'mtoValorUnitario'      => Operations::formatAmount($Items->getUnitBillableValue($rowIndex)),
                'sumTotTributosItem'    => Operations::formatAmount($Items->getIgv($rowIndex)),
                'codTriIGV'             => $Items->getTaxTypeCode($rowIndex),
                'mtoIgvItem'            => Operations::formatAmount($Items->getIgv($rowIndex)),
                'mtoBaseIgvItem'        => Operations::formatAmount($Items->getTaxableAmount($rowIndex)),
                'nomTributoIgvItem'     => $cat5Item['name'],
                'codTipTributoIgvItem'  => $cat5Item['UN_ECE_5153'],
                'tipAfeIGV'             => $Items->getIgvAffectationType($rowIndex),
                'porIgvItem'            => $porIgvItem,
                'codTriISC'             => '-',
                'mtoIscItem'            => '0.00',
                'mtoBaseIscItem'        => '0.00',
                'nomTributoIscItem'     => '0.00',
                'codTipTributoIscItem'  => '0.00',
                'tipSisISC'             => '0.00',
                'porIscItem'            => '0.00',
                'codTriOtroItem'        => '-',
                'mtoTriOtroItem'        => '0.00',
                'mtoBaseTriOtroItem'    => '0.00',
                'nomTributoIOtroItem'   => '0.00',
                'codTipTributoIOtroItem'        => '0.00',
                'porTriOtroItem'                => '0.00',
                'mtoPrecioVentaUnitario'        => $mtoPrecioVentaUnitario,
                'mtoValorVentaItem'             => Operations::formatAmount($Items->getItemBillableValue($rowIndex)),
                'mtoValorReferencialUnitario'   => $mtoValorReferencialUnitario,
            ];
            $json['detalle'][] = $item;
        }
        // Line jump
        $ENTER = chr(13) . chr(10);
        $cabContent = implode('|', $json['cabecera']);

        $detContent = '';
        for ($rowIndex = 0; $rowIndex < $ln; $rowIndex++) {
            $detContent .= implode('|', $json['detalle'][$rowIndex]) . $ENTER;
        }
        $invoiceId   = $Invoice->getInvoiceId();
        $invoiceType = $Invoice->getInvoiceType();
        self::writeFSFile("$companyRUC-$invoiceType-$invoiceId.CAB", $cabContent);
        self::writeFSFile("$companyRUC-$invoiceType-$invoiceId.DET", $detContent);
        //CABECERA VARIABLE
        if (!empty($json['variablesGlobales'])) {
            $glovalVars = $json['variablesGlobales'];
            $varGlobalContent = '';
            foreach ($glovalVars as $row) {
                $varGlobalContent .= implode('|', $row) . $ENTER;
            }
            self::writeFSFile("$companyRUC-$invoiceType-$invoiceId.ACV", $varGlobalContent);
        }
    }

    private static function writeFSFile($filename, $content) {
        $facturadorSUNATDataDir = 'F:\SUNAT/SFS_v1.2/sunat_archivos/sfs/DATA';
        file_put_contents("$facturadorSUNATDataDir/$filename", $content);
    }


    private static function getVariablesGlobales(InvoiceDocument $Invoice) {
        $data         = [];
        $currencyType = $Invoice->getCurrencyType();
        $ac           = $Invoice->getAllowancesAndCharges();
        $baseAmount   = $Invoice->getItems()->getTotalTaxableAmount();
        foreach ($ac as $item) {
            $k = $item['multiplierFactor'];
            $amount = $baseAmount * $k;
            $chargeIndicator = $item['isCharge'] ? 'true' : 'false';
            $item = [
                'tipVariableGlobal'      => $chargeIndicator,
                'codTipoVariableGlobal'  => $item['reasonCode'],
                'porVariableGlobal'      => $k,
                'monMontoVariableGlobal' => $currencyType,
                'mtoVariableGlobal'      => Operations::formatAmount($amount),
                'monBaseImponibleVariableGlobal' => $currencyType,
                'mtoBaseImpVariableGlobal'       => Operations::formatAmount($baseAmount)
            ];
            $data[] = $item;
        }
        return $data;
    }

}
