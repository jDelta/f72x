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
use Twig_Extension_Escaper;
use Dompdf\Dompdf;
use Codelint\QRCode\QRCode;

class PdfGenerator {

    public static function generatePdf(DataMap $Invoice, $billName) {
        $dompdf = new Dompdf();
        $docType = self::getTplFor($Invoice->getDocumentType());
        $html = self::getRenderedHtml($Invoice,$docType);
        // Render the HTML as PDF
        $dompdf->loadHtml($html);
        $dompdf->render();
        $pdf = $dompdf->output();
        Repository::savePDF($billName, $pdf);
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

    public static function getRenderedHtml(DataMap $Invoice, $tpl) {
        $invoiceData = self::getDocumentData($Invoice);
        $renderer = self::getRenderer();
        return $renderer->render($tpl, $invoiceData);
    }

    private static function getDocumentData(DataMap $inv) {
        $currency = Catalogo::getCurrencyPlural($inv->getCurrencyCode());
        $payableAmount = $inv->getPayableAmount();
        $payableInWords = Operations::getAmountInWords($payableAmount, $currency);
        return [
            'companyRuc'           => Company::getRUC(),
            'companyAddress'       => Company::getAddress(),
            'companyCity'          => Company::getCity(),
            'companyContactInfo'   => Company::getContactInfo(),
            'documentSeries'       => $inv->getDocumentSeries(),
            'documentNumber'       => $inv->getDocumentNumber(),
            'documentName'         => $inv->getDocumentName(),
            'currency'             => $currency,
            'customerRegName'      => $inv->getCustomerRegName(),
            'customerDocNumber'    => $inv->getCustomerDocNumber(),
            'customerAddress'      => $inv->getCustomerAddress(),
            'issueDate'            => $inv->getIssueDate()->format('d-m-Y'),
            'igvPercent'           => SunatVars::IGV_PERCENT,
            'logo'                 => self::getLogoString(),
            'qr'                   => self::getQrString($inv), // QR Code
            'taxableOperations'    => $inv->getTotalTaxableOperations(),    // Total operaciones gravadas
            'freeOperations'       => $inv->getTotalFreeOperations(),       // Total operaciones gratuitas
            'unaffectedOperations' => $inv->getTotalUnaffectedOperations(), // Total operaciones inafectas
            'exemptedOperations'   => $inv->getTotalExemptedOperations(),   // Total operaciones exoneradas
            'totalAllowances'      => $inv->getTotalAllowances(),           // Total operaciones exoneradas
            'igvAmount'            => $inv->getIGV(),                       // Total a pagar
            'payableAmount'        => $payableAmount,                       // Total a pagar
            'payableInWords'       => $payableInWords,                      // Monto en palabras
            'items'                => self::getDocumentDataItems($inv)       // Items
                
        ];
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

    private static function getLogoString() {
        $customLogoPath = Company::getPdfTemplatesPath() . '/company-logo.png';
        if (file_exists($customLogoPath)) {
            return base64_encode(file_get_contents($customLogoPath));
        }
        $defaultLogoPath = F72X::getDefaultPdfTemplatesPath() . '/company-logo.png';
        return base64_encode(file_get_contents($defaultLogoPath));
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
