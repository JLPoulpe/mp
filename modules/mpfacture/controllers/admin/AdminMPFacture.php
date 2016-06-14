<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminMPFacture
 *
 * @author Poulpe
 */
include_once(_PS_MODULE_DIR_.'mpfacture/models/Facture.php');
include_once(_PS_MODULE_DIR_.'mpfacture/models/FactureDto.php');
class AdminMPFactureController extends ModuleAdminController {
    protected $action;
    protected $idSupplier;
    protected $idOrder;
    
    public function __construct() {
        $this->bootstrap    = true;
        $this->table        = 'facture';
        $this->className    = 'Facture';
        $this->lang = false;
                
        parent :: __construct();
        
        $this->action = Tools::getIsset('method') ? Tools::getValue('method') : '';
        $this->idSupplier = Tools::getIsset('idSupplier') ? Tools::getValue('idSupplier') : 0;
        $this->idOrder = Tools::getIsset('idorder') ? Tools::getValue('idorder') : 0;
    }
    
    public function display() {
        parent::display();
    }
    
    public function renderList() {
        if(!empty($this->action)) {
            $return = $this->{$this->action}();
        } else {
            $return = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'mpfacture/views/templates/front/listcommande.tpl');
        }
        return $return;
    }
    
    public function neworders() {
        $facture = new Facture();
        $listOrders = $facture->getListOrders(Order::PAIEMENT_ACCEPTE);
        $this->context->smarty->assign(
            array(
                'listOrders'    => $listOrders,
                'title'         => 'Nouvelles commandes',
            )
        );
        $return = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'mpfacture/views/templates/front/orders.tpl');
        return $return;
    }
    
    public function preparedorders() {
        $facture = new Facture();
        $listOrders = $facture->getListOrders(Order::PREPARATION_EN_COURS);
        $this->context->smarty->assign(
            array(
                'listOrders'    => $listOrders,
                'title'         => 'Commandes en cours de préparation',
            )
        );
        $return = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'mpfacture/views/templates/front/orders.tpl');
        return $return;
    }
    
    public function orderToPay() {
        $facture = new Facture();
        $listOrders = $facture->getListOrdersBySupplier(Order::LIVRE);
        $this->context->smarty->assign(
            array(
                'listOrders'    => $listOrders,
                'title'         => 'Commandes livrées à payer',
            )
        );
        $return = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'mpfacture/views/templates/front/ordersToPay.tpl');
        return $return;
    }
    
    public function alreadyPaid() {
        $facture = new Facture();
        $listSupplier = $facture->getListIdSupplier();
        $listOrders = array();
        $idSupplier = 0;
        if(!empty($this->idSupplier)) {
            $idSupplier = $this->idSupplier;
            $orderDetail = new OrderDetail();
            $listOrders = $orderDetail->getDetailsCommandsForBoByIdSupplier(Order::LIVRE, $this->idSupplier);
        }
        $this->context->smarty->assign(
            array(
                'listSupplier'  => $listSupplier,
                'listOrders'    => $listOrders,
                'idSupplier'    => $idSupplier,
                'title'         => 'Commandes payées',
            )
        );
        $return = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'mpfacture/views/templates/front/ordersPaid.tpl');
        return $return;
    }
    
    public function reInitProducts() {
        $result = false;
        if(!empty($this->idOrder)) {
            $order = new Order();
            $result = $order->reInitProducts($this->idOrder);
        }
        $this->context->smarty->assign(
            array(
                'title'         => 'Récupération des commandes',
                'idOrder'       => $this->idOrder,
                'linkToOrder'   => $result,
            )
        );
        $return = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'mpfacture/views/templates/front/reInitProduct.tpl');
        return $return;
    }
}
