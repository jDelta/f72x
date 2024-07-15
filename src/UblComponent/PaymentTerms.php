<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 *
 * Copyright 2021, Jaime Cruz
 */

namespace F72X\UblComponent;

use Sabre\Xml\Writer;

class PaymentTerms extends BaseComponent
{

    protected $ID;
    protected $PaymentMeansID;
    /** @var Amount */
    protected $Amount;
    protected $PaymentDueDate;
    public function __construct($ID, $PaymentMeansID)
    {
        $this->ID = $ID;
        $this->PaymentMeansID = $PaymentMeansID;
    }
    function xmlSerialize(Writer $writer): void
    {
        $writer->write([
            SchemaNS::CBC . 'ID' => $this->ID,
            SchemaNS::CBC . 'PaymentMeansID' => $this->PaymentMeansID
        ]);
        if ($this->Amount) {
            $writer->write([
                $this->Amount
            ]);
        }
        if ($this->PaymentDueDate) {
            $writer->write([
                SchemaNS::CBC . 'PaymentDueDate' => $this->PaymentDueDate->format('Y-m-d')
            ]);
        }
    }

    public function setAmount($Amount)
    {
        $this->Amount = $Amount;
        return $this;
    }

    public function setPaymentDueDate($PaymentDueDate)
    {
        $this->PaymentDueDate = $PaymentDueDate;
        return $this;
    }
}
