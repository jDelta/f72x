<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
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
use Dompdf\Dompdf;
use Codelint\QRCode\QRCode;

class PdfGenerator {

    public static function generateFactura(DataMap $Invoice, $billName) {
        $dompdf = new Dompdf();
        $html = self::getRenderedHtml($Invoice, 'factura.html');
        // Render the HTML as PDF
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdf = $dompdf->output();
        Repository::savePDF($billName, $pdf);
    }

    public static function getRenderedHtml(DataMap $Invoice, $tpl) {
        $invoiceData = self::getInvoiceData($Invoice);
        $renderer = self::getRenderer();
        return $renderer->render($tpl, $invoiceData);
    }
    private static function getInvoiceData(DataMap $inv) {
        
        $currency = Catalogo::getCurrencyPlural($inv->getCurrencyCode());
        $payableAmount = $inv->getPayableAmount();
        $payableInWords = Operations::getAmountInWords($payableAmount, $currency);
        return [
            'companyRuc'           => Company::getRUC(),
            'documentSeries'       => $inv->getDocumentSeries(),
            'documentNumber'       => $inv->getDocumentNumber(),
            'documentName'         => $inv->getDocumentName(),
            'currency'             => $currency,
            'customerRegName'      => $inv->getCustomerRegName(),
            'customerDocNumber'    => $inv->getCustomerDocNumber(),
            'customerAddress'      => $inv->getCustomerAddress(),
            'issueDate'            => $inv->getIssueDate()->format('d-m-Y'),
            'igvPercent'           => SunatVars::IGV_PERCENT,
            'qr'                   => self::getQrString($inv), // QR Code
            'taxableOperations'    => $inv->getTotalTaxableOperations(),    // Total operaciones gravadas
            'freeOperations'       => $inv->getTotalFreeOperations(),       // Total operaciones gratuitas
            'unaffectedOperations' => $inv->getTotalUnaffectedOperations(), // Total operaciones inafectas
            'exemptedOperations'   => $inv->getTotalExemptedOperations(),   // Total operaciones exoneradas
            'totalAllowances'      => $inv->getTotalAllowances(),           // Total operaciones exoneradas
            'igvAmount'            => $inv->getIGV(),                       // Total a pagar
            'payableAmount'        => $payableAmount,                       // Total a pagar
            'payableInWords'       => $payableInWords,                      // Monto en palabras
            'items'                => self::getInvoiceDataItems($inv)       // Items
                
        ];
    }

    private static function getInvoiceDataItems(DataMap $inv) {
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

    private static function getQrString(DataMap $inv) {
        $billName = $inv->getBillName();
        $qr = new QRCode();
        $qrContent = self::getQrContent($inv);
        $qrTempPath = F72X::getTempDir() . "/QR-$billName.png";
        $qr->png($qrContent, $qrTempPath, 'Q', 8, 2);
        $qrs = base64_encode(file_get_contents($qrTempPath));
        unlink($qrTempPath);
        return $qrs;
    }

    private static function getQrContent(DataMap $inv) {
        $ruc               = Company::getRUC();
        $invoiveType       = $inv->getDocumentType();
        $documentSeries     = $inv->getDocumentSeries();
        $seriesNumber      = $inv->getDocumentNumber();
        $igv               = Operations::formatAmount($inv->getIGV());
        $payableAmount     = Operations::formatAmount($inv->getPayableAmount());
        $issueDate         = $inv->getIssueDate()->format('Y-m-d');
        $customerDocType   = $inv->getCustomerDocType();
        $customerDocNumber = $inv->getCustomerDocNumber();
        return "$ruc|$invoiveType|$documentSeries|$seriesNumber|$igv|$payableAmount|$issueDate|$customerDocType|$customerDocNumber";
    }
    private static function getRenderer() {
        $loader = new Twig_Loader_Filesystem();
        $loader->addPath(Company::getTplsPath());
        $view = new Twig_Environment($loader, ['cache' => false]);
        $view->addExtension(new Twig_Extensions_Extension_Intl());
        return $view;
    }

}
