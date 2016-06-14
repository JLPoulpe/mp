<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FactureDto
 *
 * @author Poulpe
 */
class FactureDto {
    private $idFacture;
    private $idOrder;
    private $status;
    private $datePayment;
    
    function getIdFacture() {
        return $this->idFacture;
    }

    function getIdOrder() {
        return $this->idOrder;
    }

    function getStatus() {
        return $this->status;
    }

    function getDatePayment() {
        return $this->datePayment;
    }

    function setIdFacture($idFacture) {
        $this->idFacture = $idFacture;
    }

    function setIdOrder($idOrder) {
        $this->idOrder = $idOrder;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setDatePayment($datePayment) {
        $this->datePayment = $datePayment;
    }
}
