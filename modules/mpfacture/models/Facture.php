<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Facture
 *
 * @author Poulpe
 */
class Facture extends ObjectModel {

    public $idFacture;
    public $idOrder;
    public $status;
    public $datePayment;

    public static $definition = array(
        'table' => 'opinion',
        'primary' => 'id_opinion',
        'multilang' => false,
        'fields' => array(
            'id_facture' => array(
                'type' => ObjectModel :: TYPE_INT
            ),
            'id_order' => array(
                'type' => ObjectModel :: TYPE_INT,
                'required' => true
            ),
            'status' => array(
                'type' => ObjectModel :: TYPE_INT,
                'required' => true
            ),
            'date_payment' => array(
                'type' => ObjectModel :: TYPE_DATE,
                'required' => true
            )
        )
    );
    
    public function getListOrders($status) {
        $orderDetail = new OrderDetail();
        $listOrderDetail = $orderDetail->getDetailsCommandsForBo($status);

        $listOrders = array();
        if(!empty($listOrderDetail)) {
            foreach($listOrderDetail as $orderDetailDto) {
                $listOrders[$orderDetailDto->getIdOrder()][] = $orderDetailDto;
            }
        }

        return $listOrders;
    }
    
    public function getListOrdersBySupplier($status) {
        $orderDetail = new OrderDetail();
        $listOrderDetail = $orderDetail->getDetailsCommandsForBo($status);
        
        $listOrders = array();
        if(!empty($listOrderDetail)) {
            foreach ($listOrderDetail as $orderDetailDto) {
                $result = $this->isAlreadyPaid($orderDetailDto->getIdSupplier(), $orderDetailDto->getIdOrder());
                if (!$result) {
                    $listOrders[$orderDetailDto->getIdSupplier()][$orderDetailDto->getIdOrder()][] = $orderDetailDto;
                }
            }
        }

        return $listOrders;
    }
    
    public function getListIdSupplier() {
        $sql = 'SELECT f.id_supplier, s.name FROM ' . _DB_PREFIX_ . 'facture f '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier s ON s.id_supplier=f.id_supplier '
                . 'GROUP BY f.id_supplier';
        return Db::getInstance()->executeS($sql);
    }
    
    public function isAlreadyPaid($idSupplier, $idOrder) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'facture WHERE id_supplier=' . (int) $idSupplier . ' AND id_order=' . (int) $idOrder;
        $result = Db::getInstance()->getRow($sql);
        return ($result);
    }
    
    public function ordersToPay($params) {
        list($idSupplier, $listOrders) = explode('-', $params);
        $listOrder = explode('|', $listOrders);
        $dateTime = new DateTime();
        foreach($listOrder as $idOrder) {
            if(!empty($idOrder)) {
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'facture (id_order, id_supplier, date_payment) VALUES (' . (int) $idOrder . ',' . (int) $idSupplier . ',\'' . $dateTime->format('Y-m-d H:i:s') . '\')';
                Db::getInstance()->execute($sql);
            }
        }
        return $idSupplier;
    }
    
    private function prepareDto($result, $multipleArray = false)
    {
        if($multipleArray) {
            $listObj = null;
            foreach($result as $key=>$row) {
                $obj = $this->createDto($row);
                $listObj[$key] = $obj;
            }
            return $listObj;
        } else {
            return $this->createDto($result);
        }
    }
    
    private function createDto($result)
    {
        $factureDto = null;
        if($result) {
            $factureDto = new FactureDto();
            $factureDto->setIdFacture(isset($result['id_facture']) ? $result['id_facture'] : 0);
            $factureDto->setIdOrder(isset($result['id_order']) ? $result['id_order'] : 0);
            $factureDto->setStatus(isset($result['status']) ? $result['status'] : 0);
            $factureDto->setDatePayment(isset($result['date_payment']) ? $result['date_payment'] : '');
        }
        return $factureDto;
    }
}