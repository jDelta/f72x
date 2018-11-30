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
use F72X\Sunat\Operations;
use F72X\Company;
use Codelint\QRCode\QRCode;

class QrGenerator {
    public static function getQrString(DataMap $inv) {
        $documentName = $inv->getDocumentName();
        $qr = new QRCode();
        $qrContent = self::getQrContent($inv);
        $qrTempPath = F72X::getTempDir() . "/QR-$documentName.png";
        $qr->png($qrContent, $qrTempPath, 'Q', 8, 2);
        $qrs = base64_encode(file_get_contents($qrTempPath));
        unlink($qrTempPath);
        return $qrs;
    }

    private static function getQrContent(DataMap $inv) {
        $ruc               = Company::getRUC();
        $invoiveType       = $inv->getDocumentType();
        $documentSeries    = $inv->getDocumentSeries();
        $seriesNumber      = $inv->getDocumentNumber();
        $igv               = Operations::formatAmount($inv->getIGV());
        $payableAmount     = Operations::formatAmount($inv->getPayableAmount());
        $issueDate         = $inv->getIssueDate()->format('Y-m-d');
        $customerDocType   = $inv->getCustomerDocType();
        $customerDocNumber = $inv->getCustomerDocNumber();
        return "$ruc|$invoiveType|$documentSeries|$seriesNumber|$igv|$payableAmount|$issueDate|$customerDocType|$customerDocNumber";
    }

}
