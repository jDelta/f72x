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
use F72X\Company;

class LogoMgr {

    public static function getLogoString() {
        $customLogoPath = Company::getPdfTemplatesPath() . '/company-logo.png';
        if (file_exists($customLogoPath)) {
            return base64_encode(file_get_contents($customLogoPath));
        }
        $defaultLogoPath = F72X::getDefaultPdfTemplatesPath() . '/company-logo.png';
        return base64_encode(file_get_contents($defaultLogoPath));
    }

}
