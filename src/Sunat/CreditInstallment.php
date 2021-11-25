<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 *
 * Copyright 2021, Jaime Cruz
 */

namespace F72X\Sunat;

class CreditInstallment
{
    private $id;
    private $amount;
    private $paymentDueDate;
    // Getters
    function getId()
    {
        return $this->id;
    }
    function getAmmount()
    {
        return $this->amount;
    }
    function getPaymentDueDate()
    {
        return $this->paymentDueDate;
    }
    // Setters
    function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }
    function setPaymentDueDate($paymentDueDate)
    {
        $this->paymentDueDate = $paymentDueDate;
        return $this;
    }
}
