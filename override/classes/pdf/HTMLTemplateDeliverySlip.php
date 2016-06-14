<?php
class HTMLTemplateDeliverySlip extends HTMLTemplateDeliverySlipCore
{
    /**
     * Returns the template's HTML content
     *
     * @return string HTML content
     */
    public function getContent()
    {
        $delivery_address = new Address((int)$this->order->id_address_delivery);
        $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
        $formatted_invoice_address = '';

        if ($this->order->id_address_delivery != $this->order->id_address_invoice)
        {
                $invoice_address = new Address((int)$this->order->id_address_invoice);
                $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
        }

        $carrier = new Carrier($this->order->id_carrier);
        $carrier->name = ($carrier->name == '0' ? Configuration::get('PS_SHOP_NAME') : $carrier->name);
        $listMarket = $this->getListMarket($this->order->id);
        $totalPrice = $listMarket['totalPrice'];
        unset($listMarket['totalPrice']);
        
        $this->smarty->assign(array(
                'order' => $this->order,
                'listMarket' => $listMarket,
                'totalPrice' => $totalPrice,
                'delivery_address' => $formatted_delivery_address,
                'invoice_address' => $formatted_invoice_address,
                'order_invoice' => $this->order_invoice,
                'carrier' => $carrier,
                'display_product_images' => Configuration::get('PS_PDF_IMG_DELIVERY')
        ));

        $tpls = array(
                'style_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.style-tab')),
                'product_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.customproduct-tab')),
                'addresses_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.addresses-tab')),
                'summary_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.summary-tab')),
                'payment_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.payment-tab')),
        );
        $this->smarty->assign($tpls);

        return $this->smarty->fetch($this->getTemplate('delivery-slip'));
    }
    
    private function getListMarket($idOrder) {
        $orderDetail = new OrderDetail();
        $listProducts = $orderDetail->getOrderDetailById($idOrder);
        $listMarket = array();
        $product = new Product();
        $totalPrice = 0;
        foreach($listProducts as $key=>$orderDto) {
            $productDto = $product->getById($orderDto->getProductId(), 0, 0);
            if(!empty($productDto)) {
                $listMarket[$key]['product'][$key]['name'] = $orderDto->getProductName();
                $listMarket[$key]['product'][$key]['description'] = $productDto->getDescription();
                $listMarket[$key]['product'][$key]['supplierName'] = $productDto->getSupplierName();
                $listMarket[$key]['product'][$key]['category'] = $productDto->getCategoryId();
                $listMarket[$key]['product'][$key]['category_panier'] = Category::CATEGORY_PANIER;
                $listMarket[$key]['product'][$key]['unitPrice'] = number_format($orderDto->getUnitPriceTaxIncl(), 2);
                $listMarket[$key]['product'][$key]['quantity'] = $orderDto->getQuantity();
                $listMarket[$key]['product'][$key]['total'] = number_format($orderDto->getTotalPriceTaxIncl(), 2);
                $totalPrice += $orderDto->getTotalPriceTaxIncl();
            }
        }
        $listMarket['totalPrice'] = number_format($totalPrice, 2);
        return $listMarket;
    }
}
