<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Tools;

use InvalidArgumentException;

class XMatrix {

    private $_MATRIX = [];
    private $_ROW = [];

    /**
     * @var int|string[]
     */
    protected $columns = 0;
    private $totalColumns = 0;

    /**
     * 
     * @param int|string[] $columns
     */
    public function __construct($columns = 0) {
        $this->columns = $columns ? $columns : $this->columns;
        $this->totalColumns = is_numeric($this->columns) ? $this->columns : count($this->columns);
        for ($i = 0; $i < $this->totalColumns; $i++) {
            $this->_ROW[$i] = null;
        }
    }

    public function set($columnIndex, $rowIndex, $value) {
        if (!isset($this->_MATRIX[$rowIndex])) {
            $this->completeMatrix($rowIndex);
        }
        if ($columnIndex >= $this->totalColumns) {
            throw new InvalidArgumentException(printf('The index %s exceeds the number of defined for this matrix!', $columnIndex));
        }
        $this->_MATRIX[$rowIndex][$columnIndex] = $value;
    }

    public function get($columnIndex, $rowIndex) {
        return $this->_MATRIX[$rowIndex][$columnIndex];
    }

    public function getRow($rowIndex) {
        return $this->_MATRIX[$rowIndex];
    }

    private function completeMatrix($y) {
        for ($i = 0; $i <= $y; $i++) {
            if (!isset($this->_MATRIX[$i])) {
                $this->_MATRIX[$i] = $this->_ROW;
            }
        }
    }

    public function getMatrix() {
        return $this->_MATRIX;
    }

    public function countRows() {
        return count($this->_MATRIX);
    }

    public function each(callable $fn) {
        foreach ($this->_MATRIX as &$row) {
            $fn($row);
        }
    }

    public function getHtml($matrix = null, $mainMatrix = true) {
        $html = '';
        if ($mainMatrix) {
            $html = '<style>
            .xm-table{border:#a7a7a7 dashed 1px; border-collapse: collapse; margin: 5px;}
            .xm-table td, .xm-table th{border: dashed 1px #1976D2;padding: 2px 4px;}
            .xm-table th{border-bottom: solid 2px #1976D2;}
            .align-r{text-align: right;}
        </style>';
        }
        $html .= '<table class="xm-table">';

        if (!$matrix) {
            $matrix = $this->_MATRIX;
        }
        if ($mainMatrix && is_array($this->columns)) {
            $html .= '<tr><th>' . implode('</th><th>', $this->columns) . '</th></tr>';
        }
        if ($matrix) {
            $header = '';
            foreach ($matrix[0] as $key => $value) {
                if (is_string($key)) {
                    $header .= '<th>' . $key . '</th>';
                } else {
                    break;
                }
            }
            if ($header) {
                $html .= "<tr>$header</tr>";
            }
        }
        foreach ($matrix as $row) {
            $html .= '<tr>';
            foreach ($row as $value) {
                $value = !$value && is_array($value) ? '' : $value;
                // if array recursive
                if (is_array($value)) {
                    $html .= '<td>' . $this->getHtml($value, false) . '</td>';
                } else {
                    $class = is_float($value) || is_int($value) ? 'class="align-r"' : '';
                    $html .= "<td $class >$value</td>";
                }
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }

    public function sum($columnIndex) {
        $column = array_column($this->_MATRIX, $columnIndex);
        return array_sum($column);
    }

}
