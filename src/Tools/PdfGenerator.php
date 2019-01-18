<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Tools;

use F72X\F72X;
use F72X\Sunat\DataMap;
use F72X\Sunat\SunatVars;
use F72X\Sunat\Catalogo;
use F72X\Sunat\Operations;
use F72X\Company;
use F72X\Repository;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Extensions_Extension_Intl;
use Twig_Extension_Escaper;
use Dompdf\Dompdf;

class PdfGenerator {

    /**
     * Generates a PDF based on the invoice data
     * @param DataMap $Invoice
     * @param type $documentName
     */
    public static function generatePdf(DataMap $Invoice, $documentName) {
        Repository::savePDF($documentName, self::buildPdf($Invoice));
    }

    /**
     * Builds a PDF and returns the stream
     * @param DataMap $Invoice
     * @return string The PDF stream
     */
    public static function buildPdf(DataMap $Invoice) {
        $dompdf = new Dompdf();
        $docType = self::getTplFor($Invoice->getDocumentType());
        $html = self::getRenderedHtml($Invoice, $docType);
        // Render the HTML as PDF
        $dompdf->loadHtml($html);
        $dompdf->render();
        return $dompdf->output();
    }
    
    private static function getTplFor($docType) {
        if ($docType == Catalogo::DOCTYPE_FACTURA) {
            return 'factura.html';
        }
        if ($docType == Catalogo::DOCTYPE_BOLETA) {
            return 'boleta.html';
        }
        if ($docType == Catalogo::DOCTYPE_NOTA_CREDITO) {
            return 'nota-credito.html';
        }
        return 'nota-debito.html';
    }

    /**
     * Compiles the template with the input data
     * @param DataMap $Invoice The invoice data
     * @param string $tpl The template name
     * @return string
     */
    public static function getRenderedHtml(DataMap $Invoice, $tpl) {
        $invoiceData = self::getDocumentData($Invoice);
        $renderer = self::getRenderer();
        return $renderer->render($tpl, $invoiceData);
    }

    private static function getDocumentData(DataMap $inv) {
        $currency = Catalogo::getCurrencyPlural($inv->getCurrencyCode());
        $payableAmount = $inv->getPayableAmount();
        $payableInWords = Operations::getAmountInWords($payableAmount, $currency);
        // has dueDate =
        $dueDate = $inv->getDueDate();
        $data = [
            'companyRuc'           => Company::getRUC(),
            'companyAddress'       => Company::getAddress(),
            'companyCity'          => Company::getCity(),
            'companyContactInfo'   => Company::getContactInfo(),
            'documentSeries'       => $inv->getDocumentSeries(),
            'documentNumber'       => $inv->getDocumentNumber(),
            'officialDocumentName' => $inv->getOfficialDocumentName(),
            'currency'             => $currency,
            'customerRegName'      => $inv->getCustomerRegName(),
            'customerDocNumber'    => $inv->getCustomerDocNumber(),
            'customerAddress'      => $inv->getCustomerAddress(),
            'issueDate'            => $inv->getIssueDate()->format('d-m-Y'),
            'dueDate'              => $dueDate ? $dueDate->format('d-m-Y') : '',
            'igvPercent'           => SunatVars::IGV_PERCENT,
            'logo'                 => LogoMgr::getLogoString(),
            'qr'                   => QrGenerator::getQrString($inv), // QR Code
            'taxableOperations'    => $inv->getTotalTaxableOperations(),    // Total operaciones gravadas
            'freeOperations'       => $inv->getTotalFreeOperations(),       // Total operaciones gratuitas
            'unaffectedOperations' => $inv->getTotalUnaffectedOperations(), // Total operaciones inafectas
            'exemptedOperations'   => $inv->getTotalExemptedOperations(),   // Total operaciones exoneradas
            'totalAllowances'      => $inv->getTotalAllowances(),           // Total operaciones exoneradas
            'igvAmount'            => $inv->getIGV(),                       // Total a pagar
            'payableAmount'        => $payableAmount,                       // Total a pagar
            'payableInWords'       => $payableInWords,                      // Monto en palabras
            'items'                => self::getDocumentDataItems($inv)     // Items
        ];
        // For credit and debit notes
        if (in_array($inv->getDocumentType(), [Catalogo::DOCTYPE_NOTA_CREDITO, Catalogo::DOCTYPE_NOTA_DEBITO])) {
            $noteData = [
                'noteType'                    => $inv->getNoteType(),
                'discrepancyResponseReason'   => $inv->getDiscrepancyResponseReason(),
                'affectedDocumentId'          => $inv->getNoteAffectedDocId(),
                'affectedDocumentOficialName' => Catalogo::getOfficialDocumentName($inv->getNoteAffectedDocType()),
                'note'                        => $inv->getNoteDescription()
            ];
            return array_merge($data, $noteData);
        }

        return $data;
    }

    private static function getDocumentDataItems(DataMap $inv) {
        $Items = $inv->getItems();
        $ln = $Items->getCount();
        $items2 = [];
        for ($i = 0; $i < $ln; $i++) {
            $items2[]= [
                'productCode'       => $Items->getProductCode($i),
                'quantity'          => $Items->getQunatity($i),
                'unitName'          => Catalogo::getUnitName($Items->getUnitCode($i)),
                'unitBillableValue' => $Items->getUnitBillableValue($i),
                'itemPayableAmount' => $Items->getPayableAmount($i),
                'description'       => $Items->getDescription($i)
            ];
        }
        return $items2;
    }

    private static function getRenderer() {
        $loader = new Twig_Loader_Filesystem();
        // Custom
        $loader->addPath(Company::getPdfTemplatesPath());
        // Defaults
        $loader->addPath(F72X::getDefaultPdfTemplatesPath());
        $view = new Twig_Environment($loader, ['cache' => false]);
        // I18n ext
        $view->addExtension(new Twig_Extensions_Extension_Intl());
        // Scape html ext
        $view->addExtension(new Twig_Extension_Escaper('html'));
        return $view;
    }

}
