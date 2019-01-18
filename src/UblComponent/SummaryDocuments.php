<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\UblComponent;

use DateTime;

abstract class SummaryDocuments extends BaseComponent {

    /** @var UBLExtensions */
    protected $UBLExtensions;
    protected $ProfileID;
    protected $ID;

    /** @var DateTime */
    protected $IssueDate;

    /** @var DateTime */
    protected $DueDate;
    protected $InvoiceTypeCode;

    /** @var Note[] */
    protected $Notes = [];
    protected $DocumentCurrencyCode;
    protected $languageLocaleID;
    protected $LineCountNumeric;

    /** @var OrderReference */
    protected $OrderReference;

    /** @var Signature */
    protected $Signature;

    /** @var AccountingSupplierParty */
    protected $AccountingSupplierParty;

    /** @var AccountingCustomerParty */
    protected $AccountingCustomerParty;

    /** @var DespatchDocumentReference */
    protected $DespatchDocumentReference;

    /** @var AllowanceCharge[] */
    protected $AllowanceCharges = [];

    /** @var TaxTotal */
    protected $TaxTotal;

    /** @var LegalMonetaryTotal */
    protected $LegalMonetaryTotal;

    /** @var InvoiceLine[] */
    protected $InvoiceLines = [];

    public function getUBLExtensions() {
        return $this->UBLExtensions;
    }

    public function setUBLExtensions($UBLExtensions) {
        $this->UBLExtensions = $UBLExtensions;
        return $this;
    }

    public function getProfileID() {
        return $this->ProfileID;
    }

    public function setProfileID($ProfileID) {
        $this->ProfileID = $ProfileID;
        return $this;
    }

    public function getID() {
        return $this->ID;
    }

    public function setID($ID) {
        $this->ID = $ID;
        return $this;
    }

    public function getIssueDate() {
        return $this->IssueDate;
    }

    public function setIssueDate(DateTime $IssueDate) {
        $this->IssueDate = $IssueDate;
    }

    public function getDueDate() {
        return $this->DueDate;
    }

    public function setDueDate(DateTime $DueDate) {
        $this->DueDate = $DueDate;
    }

    public function getInvoiceTypeCode() {
        return $this->InvoiceTypeCode;
    }

    public function setInvoiceTypeCode($InvoiceTypeCode) {
        $this->InvoiceTypeCode = $InvoiceTypeCode;
        return $this;
    }

    public function getNotes() {
        return $this->Notes;
    }

    public function setNotes($Notes) {
        $this->Notes = $Notes;
        return $this;
    }

    /**
     * 
     * @param Note $Note
     * @return $this
     */
    public function addNote(Note $Note) {
        $this->Notes[] = $Note;
        return $this;
    }

    public function getDocumentCurrencyCode() {
        return $this->DocumentCurrencyCode;
    }

    public function setDocumentCurrencyCode($DocumentCurrencyCode) {
        $this->DocumentCurrencyCode = $DocumentCurrencyCode;
        return $this;
    }

    public function getLineCountNumeric() {
        return $this->LineCountNumeric;
    }

    public function setLineCountNumeric($LineCountNumeric) {
        $this->LineCountNumeric = $LineCountNumeric;
    }

    public function getOrderReference() {
        return $this->OrderReference;
    }

    public function setOrderReference(OrderReference $OrderReference) {
        $this->OrderReference = $OrderReference;
    }

    public function getSignature() {
        return $this->Signature;
    }

    public function setSignature(Signature $Signature) {
        $this->Signature = $Signature;
        return $this;
    }

    public function getAccountingSupplierParty() {
        return $this->AccountingSupplierParty;
    }

    public function setAccountingSupplierParty(AccountingSupplierParty $AccountingSupplierParty) {
        $this->AccountingSupplierParty = $AccountingSupplierParty;
        return $this;
    }

    public function getAccountingCustomerParty() {
        return $this->AccountingCustomerParty;
    }

    public function setAccountingCustomerParty(AccountingCustomerParty $AccountingCustomerParty) {
        $this->AccountingCustomerParty = $AccountingCustomerParty;
        return $this;
    }

    public function getDespatchDocumentReference() {
        return $this->DespatchDocumentReference;
    }

    public function setDespatchDocumentReference(DespatchDocumentReference $DespatchDocumentReference) {
        $this->DespatchDocumentReference = $DespatchDocumentReference;
    }

    public function getAllowanceCharges() {
        return $this->AllowanceCharges;
    }

    public function setAllowanceCharges(array $AllowanceCharges) {
        $this->AllowanceCharges = $AllowanceCharges;
        return $this;
    }

    /**
     * 
     * @param AllowanceCharge $AllowanceCharge
     * @return $this
     */
    public function addAllowanceCharge(AllowanceCharge $AllowanceCharge) {
        $this->AllowanceCharges[] = $AllowanceCharge;
        return $this;
    }

    public function getTaxTotal() {
        return $this->TaxTotal;
    }

    public function setTaxTotal(TaxTotal $TaxTotal) {
        $this->TaxTotal = $TaxTotal;
        return $this;
    }

    public function getLegalMonetaryTotal() {
        return $this->LegalMonetaryTotal;
    }

    public function setLegalMonetaryTotal(LegalMonetaryTotal $LegalMonetaryTotal) {
        $this->LegalMonetaryTotal = $LegalMonetaryTotal;
        return $this;
    }

    public function getInvoiceLines() {
        return $this->InvoiceLines;
    }

    public function setInvoiceLines($InvoiceLines) {
        $this->InvoiceLines = $InvoiceLines;
        return $this;
    }

    public function addInvoiceLine(InvoiceLine $InvoiceLine) {
        $this->InvoiceLines[] = $InvoiceLine;
        return $this;
    }

}
