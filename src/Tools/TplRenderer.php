<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 *
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Tools;

class TplRenderer
{
    public static function getRenderer($path)
    {
        $loader = new \Twig\Loader\FilesystemLoader();
        // templates path
        $loader->addPath($path);
        return new \Twig\Environment($loader, ['cache' => false]);
    }

}
