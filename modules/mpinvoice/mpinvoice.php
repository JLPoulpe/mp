<?php
if (!defined('_PS_VERSION_'))
  exit;

class Mpinvoice extends Module
{
    public function __construct()
    {
        $this->name = 'mpinvoice';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'Julien Ledieu';
        
        parent::__construct();

        $this->displayName = $this->l('Invoice - Mes Paysans');
        $this->description = $this->l('Ajout d\'information dans le pdf de facture');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayPDFInvoice') && $this->registerHook('displayPDForderslip');
    }

    public function hookDisplayPDFInvoice($params)
    {
        $idOrder = Tools::getValue('id_order');
        $order = new Order();
        if(!$idOrder) {
            if(isset($this->context->controller->order->id)) {
                $idOrder = $this->context->controller->order->id;
            } else {
                $idOrder = $order->getOrderByCartId($this->context->cart->id);
            }
        }
        $listProduct = $this->getListProducts($idOrder);
        $totalPrice = $listProduct['totalPrice'];
        $totalTax = $listProduct['totalCostByTax'];
        $listSupplier = $listProduct['suppliers'];
        $widthdrawal = $listProduct['datewithdrawal'];
        $shipping = '5 €';
        if(count($widthdrawal)>1) {
            $shipping = '8 €';
        }
        unset($listProduct['totalPrice'], $listProduct['suppliers'], $listProduct['totalCostByTax'], $listProduct['datewithdrawal']);
        //d(print_r($listProduct, true));
        $this->context->smarty->assign(
            array(
                'listProduct'       => $listProduct,
                'totalPrice'        => $totalPrice,
                'totalTax'          => $totalTax,
                'shipping'          => $shipping,
                'listSupplier'      => $listSupplier,
            )
        );
        return $this->display(__FILE__, 'pdf.tpl');
    }
    
    private function getListProducts($idOrder) {
        $orderDetail = new OrderDetail();
        $listOrderDetail = $orderDetail->getOrderDetailById($idOrder);
        $supplier = new Supplier();
        $attribute = new Attribute();
        $listProducts = array();
        $totalPrice = 0;
        foreach($listOrderDetail as $key=>$orderDetail) {
            $supplierDto = $supplier->getFromIdProduct($orderDetail->getProductId());
            $listProducts['suppliers'][$supplierDto->getIdSupplier()] = $supplierDto;
            $listProducts[$key]['supplierName'] = $supplierDto->getName();
            $dateTWithDrawal = new DateTime($orderDetail->getDateWithdrawal(false));
            $listProducts['datewithdrawal'][$dateTWithDrawal->format('Ymd')] = 1;
            $listProducts[$key]['withdrawal'] = MPTools::$listJour[$dateTWithDrawal->format('w')] . $dateTWithDrawal->format(' d') . ' ' . MPTools::$listMois[$dateTWithDrawal->format('n')-1] . $dateTWithDrawal->format(' Y');
            $listProducts[$key]['name'] = $orderDetail->getProductName();
            $listProducts[$key]['unitPrice'] = $orderDetail->getUnitPriceTaxIncl();
            $listProducts[$key]['panier'] = $orderDetail->getIdCategoryDefault()==Category::CATEGORY_PANIER ? 1 : 0;
            $listProducts[$key]['description'] = $orderDetail->getProductDescription();
            $listProducts[$key]['quantity'] = $orderDetail->getQuantity();
            $listProducts[$key]['tax'] = number_format($orderDetail->getTax(), 1);
            $listProducts[$key]['total'] = $orderDetail->getTotalPriceTaxIncl();
            $totalPriceTax = $orderDetail->getTotalPriceTaxIncl()-$orderDetail->getTotalPriceTaxExcl();
            $listProducts[$key]['totalTax'] = $totalPriceTax;
            $attributeID = $orderDetail->getProductAttributeId();
            if(!empty($attributeID)) {
                $attributeDto = $attribute->getAttributeFromId($attributeID);
                if($attributeDto instanceof AttributeDto) {
                    $listProducts[$key]['attribute'] = $attributeDto->getName();
                } else {
                    $listProducts[$key]['attribute'] = 0;
                }
            } else {
                $listProducts[$key]['attribute'] = 0;
            }
            $listProducts[$key]['total'] = $orderDetail->getTotalPriceTaxIncl();
            $totalCostByTax[number_format($orderDetail->getTax(), 1)] += number_format($totalPriceTax, 2);
            $totalPrice += $orderDetail->getTotalPriceTaxIncl();
        }
        $listProducts['totalCostByTax'] = $totalCostByTax;
        if(count($listProducts['datewithdrawal'])>1) {
            $listProducts['totalPrice'] = $totalPrice+8;
        } else {
            $listProducts['totalPrice'] = $totalPrice+5;
        }
        return $listProducts;
    }
    
    private function getListMarket($idOrder, $idCart, $idCustomer) {
        $orderDetail = new OrderDetail();
        $listProducts = $orderDetail->getOrderDetailById($idOrder);
        $listMarket = array();
        $product = new Product();
        $attribute = new Attribute();
        $cart = new Cart();
        $totalPrice = 0;
        $totalCostByTax = array();
        $listKey = 0;
        foreach($listProducts as $key=>$orderDto) {
            $cartDto = $cart->getCartProductByIdCartAndIdProduct($idCart, $orderDto->getProductId());
            $productDto = $product->getByIdV2($orderDto->getProductId(), 0, 0);
            $idMarket = $cartDto->getIdMarket();
            $dateWithdrawal = '';
            $listMarket[$listKey]['product'][$key]['idMarket'] = $idMarket;
            if(!empty($cartDto)) {
                $dateWithdrawal = $cartDto->getDateWithdrawal('', true);
                $listKey = $cartDto->getDateWithdrawal('Ymd');
            }
            if($orderDto->getProductAttributeId()!=0) {
                $attributeDto = $attribute->getAttributeFromId($orderDto->getProductAttributeId());
                if(!empty($attributeDto)) {
                    $pos = strpos($attributeDto->getName(), 'unit');
                    if(empty($pos)) {
                        $listMarket[$listKey]['product'][$key]['attribute'] = $attributeDto->getName();
                    }
                }
            }

            $listMarket[$listKey]['deliveryDay'] = $dateWithdrawal;
            if(!empty($productDto)) {
                $listMarket[$listKey]['product'][$key]['name'] = $productDto->getProductName();
                $listMarket[$listKey]['product'][$key]['description'] = $productDto->getDescription();
                $listMarket[$listKey]['product'][$key]['category'] = $productDto->getCategoryId();
                $listMarket[$listKey]['product'][$key]['category_panier'] = Category::CATEGORY_PANIER;
                $listMarket[$listKey]['product'][$key]['supplierName'] = $productDto->getSupplierName();
                $listMarket[$listKey]['product'][$key]['unitPrice'] = number_format($orderDto->getUnitPriceTaxIncl(), 2);
                $listMarket[$listKey]['product'][$key]['quantity'] = $orderDto->getQuantity();
                $listMarket[$listKey]['product'][$key]['tax'] = number_format($productDto->getTax(), 2);
                $listMarket[$listKey]['product'][$key]['total'] = $orderDto->getTotalPriceTaxIncl();
                $listMarket[$listKey]['product'][$key]['totalTax'] = $orderDto->getTotalPriceTaxExcl();
                $totalCostByTax[$productDto->getTax()] += $orderDto->getTotalPriceTaxExcl();
                $totalPrice += $orderDto->getTotalPriceTaxIncl();
            }
        }
        $order = new Order();
        $nbOrder = $order->getNbOrderFromIdCustomer($idCustomer, $idOrder);
        $shipping = '5 €';
        if($nbOrder==0 && $totalPrice>=Order::MIN_PRICE_FOR_FREE_SHIPPING) {
            $shipping = 'Gratuit';
        } else {
            $totalPrice += 5;
        }
        $listMarket['totalCostByTax'] = number_format($totalCostByTax, 2);
        $listMarket['totalPrice'] = number_format($totalPrice, 2);
        $listMarket['shipping'] = $shipping;
        ksort($listMarket);
        return $listMarket;
    }
}
