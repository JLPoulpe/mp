<?php
class SupplierAccountDto {
    private $idSupplierAccount;
    private $idSupplier;
    private $idAccount;
    private $codeEtablissement;
    private $codeGuichet;
    private $numeroCompte;
    private $clefRib;
    private $iban;
    private $codeBic;
    
    function getIdSupplierAccount() {
        return $this->idSupplierAccount;
    }

    function getIdSupplier() {
        return $this->idSupplier;
    }

    function getIdAccount() {
        return $this->idAccount;
    }

    function getCodeEtablissement() {
        return $this->codeEtablissement;
    }

    function getCodeGuichet() {
        return $this->codeGuichet;
    }

    function getNumeroCompte() {
        return $this->numeroCompte;
    }

    function getClefRib() {
        return $this->clefRib;
    }

    function getIban() {
        return $this->iban;
    }

    function getCodeBic() {
        return $this->codeBic;
    }

    function setIdSupplierAccount($idSupplierAccount) {
        $this->idSupplierAccount = $idSupplierAccount;
    }

    function setIdSupplier($idSupplier) {
        $this->idSupplier = $idSupplier;
    }

    function setIdAccount($idAccount) {
        $this->idAccount = $idAccount;
    }

    function setCodeEtablissement($codeEtablissement) {
        $this->codeEtablissement = $codeEtablissement;
    }

    function setCodeGuichet($codeGuichet) {
        $this->codeGuichet = $codeGuichet;
    }

    function setNumeroCompte($numeroCompte) {
        $this->numeroCompte = $numeroCompte;
    }

    function setClefRib($clefRib) {
        $this->clefRib = $clefRib;
    }

    function setIban($iban) {
        $this->iban = $iban;
    }

    function setCodeBic($codeBic) {
        $this->codeBic = $codeBic;
    }


}
