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

    //----------------------------------------------------------------------
    private static function image($frame, $pixelPerPoint = 4, $outerFrame = 4) {
        $h = count($frame);
        $w = strlen($frame[0]);

        $imgW = $w + 2 * $outerFrame;
        $imgH = $h + 2 * $outerFrame;

        $base_image = ImageCreate($imgW, $imgH);

        $col[0] = ImageColorAllocate($base_image, 255, 255, 255);
        $col[1] = ImageColorAllocate($base_image, 0, 0, 0);

        imagefill($base_image, 0, 0, $col[0]);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                if ($frame[$y][$x] == '1') {
                    ImageSetPixel($base_image, $x + $outerFrame, $y + $outerFrame, $col[1]);
                }
            }
        }

        $target_image = ImageCreate($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
        ImageCopyResized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);
        ImageDestroy($base_image);

        return $target_image;
    }

    private static function getQrContent(DataMap $inv) {
        $ruc = Company::getRUC();
        $invoiveType = $inv->getDocumentType();
        $documentSeries = $inv->getDocumentSeries();
        $seriesNumber = $inv->getDocumentNumber();
        $igv = Operations::formatAmount($inv->getIGV());
        $payableAmount = Operations::formatAmount($inv->getPayableAmount());
        $issueDate = $inv->getIssueDate()->format('Y-m-d');
        $customerDocType = $inv->getCustomerDocType();
        $customerDocNumber = $inv->getCustomerDocNumber();
        return "$ruc|$invoiveType|$documentSeries|$seriesNumber|$igv|$payableAmount|$issueDate|$customerDocType|$customerDocNumber";
    }

}
