<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\UblComponent;

use DateTime;

class DebitNote extends BaseComponent {

    /** @var UBLExtensions */
    protected $UBLExtensions;
    protected $UBLVersionID;
    protected $CustomizationID;
    protected $ProfileID;
    protected $ID;

    /** @var DateTime */
    protected $IssueDate;

    /** @var Note[] */
    protected $Notes = [];
    protected $DocumentCurrencyCode;

    /** @var DiscrepancyResponse */
    protected $DiscrepancyResponse;

    /** @var BillingReference */
    protected $BillingReference;

    /** @var Signature */
    protected $Signature;

    /** @var AccountingSupplierParty */
    protected $AccountingSupplierParty;

    /** @var AccountingCustomerParty */
    protected $AccountingCustomerParty;

    /** @var DespatchDocumentReference */
    protected $DespatchDocumentReference;

    /** @var TaxTotal */
    protected $TaxTotal;

    /** @var RequestedMonetaryTotal */
    protected $RequestedMonetaryTotal;

    /** @var DebitNoteLine[] */
    protected $DebitNoteLines = [];

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

    public function getDiscrepancyResponse() {
        return $this->DiscrepancyResponse;
    }

    public function setDiscrepancyResponse(DiscrepancyResponse $DiscrepancyResponse) {
        $this->DiscrepancyResponse = $DiscrepancyResponse;
        return $this;
    }

    public function getBillingReference() {
        return $this->BillingReference;
    }

    public function setBillingReference(BillingReference $BillingReference) {
        $this->BillingReference = $BillingReference;
        return $this;
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

    public function getTaxTotal() {
        return $this->TaxTotal;
    }

    public function setTaxTotal(TaxTotal $TaxTotal) {
        $this->TaxTotal = $TaxTotal;
        return $this;
    }

    public function getRequestedMonetaryTotal() {
        return $this->RequestedMonetaryTotal;
    }

    public function setRequestedMonetaryTotal(RequestedMonetaryTotal $RequestedMonetaryTotal) {
        $this->RequestedMonetaryTotal = $RequestedMonetaryTotal;
        return $this;
    }

    public function getDebitNoteLines() {
        return $this->DebitNoteLines;
    }

    public function setDebitNoteLines($DebitNoteLines) {
        $this->DebitNoteLines = $DebitNoteLines;
        return $this;
    }

    public function addDebitNoteLine(DebitNoteLine $InvoiceLine) {
        $this->DebitNoteLines[] = $InvoiceLine;
        return $this;
    }

}
