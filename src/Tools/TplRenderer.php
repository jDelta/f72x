<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Tools;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Extensions_Extension_Intl;
use Twig_Extension_Escaper;

class TplRenderer {

    public static function getRenderer($path) {
        $loader = new Twig_Loader_Filesystem();
        // templates path
        $loader->addPath($path);
        $renderer = new Twig_Environment($loader, ['cache' => false]);
        // I18n ext
        $renderer->addExtension(new Twig_Extensions_Extension_Intl());
        // Scape html ext
        $renderer->addExtension(new Twig_Extension_Escaper('html'));
        return $renderer;
    }

}
