<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\UblComponent;

use Exception;
use InvalidArgumentException;
use Sabre\Xml\XmlSerializable;
use Sabre\Xml\Writer;

class BaseComponent implements XmlSerializable {

    protected $validations = [];

    public function xmlSerialize(Writer $writer) {
        
    }

    public function validate() {
        foreach ($this->validations as $field => $validation) {
            if (is_numeric($field)) {
                // Null or empty array
                if (is_null($this->{$validation}) || (is_array($this->{$validation}) && count($this->{$validation}) === 0)) {
                    throw new InvalidArgumentException(get_class($this) . " $validation is required");
                }
            }
        }
    }

    public function setElementAttribute($element, $attribute, $value) {
        $this->{$element}[$attribute] = $value;
    }

    public function setElementAttributes($element, $attributes) {
        $attProperty = $element . 'Attributes';
        if (property_exists($this, $attProperty)) {
            $this->{$attProperty} = $attributes;
        } else {
            throw new Exception("The property $attProperty, doesn't exist!");
        }
        return $this;
    }

    public function set($element, $value, $attribues = null) {
        if (property_exists($this, $element)) {
            $this->{$element} = $value;
            if (is_array($attribues)) {
                return $this->setElementAttributes($element, $attributes);
            }
        } else {
            throw new Exception("The element $element, doesn't exist!");
        }
        return $this;
    }

    public function get($element) {
        if (property_exists($this, $element)) {
            return $this->{$element};
        } else {
            throw new Exception("The element $element, doesn't exist!");
        }
    }

    public function writeLineJump(Writer $writer) {
        // Line jump
        $ENTER = chr(13) . chr(10);
        $writer->writeRaw($ENTER);
    }

}
