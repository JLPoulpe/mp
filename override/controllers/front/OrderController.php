<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderController extends OrderControllerCore
{
    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
            parent::initContent();

            if (Tools::isSubmit('ajax') && Tools::getValue('method') == 'updateExtraCarrier')
            {
                    // Change virtualy the currents delivery options
                    $delivery_option = $this->context->cart->getDeliveryOption();
                    $delivery_option[(int)Tools::getValue('id_address')] = Tools::getValue('id_delivery_option');
                    $this->context->cart->setDeliveryOption($delivery_option);
                    $this->context->cart->save();
                    $return = array(
                        'content' => Hook::exec(
                                'displayCarrierList',
                                array(
                                        'address' => new Address((int)Tools::getValue('id_address'))
                                )
                        )
                    );
                    die(Tools::jsonEncode($return));
            }

            if ($this->nbProducts)
                    $this->context->smarty->assign('virtual_cart', $this->context->cart->isVirtualCart());

            if (!Tools::getValue('multi-shipping'))
                    $this->context->cart->setNoMultishipping();

            // 4 steps to the order
            switch ((int)$this->step)
            {
                    case -1;
                            $this->context->smarty->assign('empty', 1);
                            $this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
                    break;

                    case 1:
                            $this->_assignAddress();
                            $this->processAddressFormat();
                            if (Tools::getValue('multi-shipping') == 1)
                            {
                                $this->_assignSummaryInformations();
                                $this->context->smarty->assign('product_list', $this->context->cart->getProducts());
                                $this->setTemplate(_PS_THEME_DIR_.'order-address-multishipping.tpl');
                            }
                            else
                                    $this->setTemplate(_PS_THEME_DIR_.'order-address.tpl');
                    break;

                    case 2:
                            if (Tools::isSubmit('processAddress'))
                                    $this->processAddress();
                            $this->autoStep();
                            $this->_assignCarrier();
                            $this->_isGoodAddressForDelivery();
                            $this->_applyTypeOrderRule();
                            $this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl');
                    break;

                    case 3:
                            // Check that the conditions (so active) were accepted by the customer
                            $cgv = Tools::getValue('cgv') || $this->context->cookie->check_cgv;
                            if (Configuration::get('PS_CONDITIONS') && (!Validate::isBool($cgv) || $cgv == false))
                                    Tools::redirect('index.php?controller=order&step=2');
                            Context::getContext()->cookie->check_cgv = true;

                            // Check the delivery option is set
                            if (!$this->context->cart->isVirtualCart())
                            {
                                    if (!Tools::getValue('delivery_option') && !Tools::getValue('id_carrier') && !$this->context->cart->delivery_option && !$this->context->cart->id_carrier)
                                            Tools::redirect('index.php?controller=order&step=2');
                                    elseif (!Tools::getValue('id_carrier') && !$this->context->cart->id_carrier)
                                    {
                                            $deliveries_options = Tools::getValue('delivery_option');
                                            if (!$deliveries_options)
                                                    $deliveries_options = $this->context->cart->delivery_option;

                                            foreach ($deliveries_options as $delivery_option)
                                                    if (empty($delivery_option))
                                                            Tools::redirect('index.php?controller=order&step=2');
                                    }
                            }

                            $this->autoStep();

                            // Bypass payment step if total is 0
                            if (($id_order = $this->_checkFreeOrder()) && $id_order)
                            {
                                    if ($this->context->customer->is_guest)
                                    {
                                            $order = new Order((int)$id_order);
                                            $email = $this->context->customer->email;
                                            $this->context->customer->mylogout(); // If guest we clear the cookie for security reason
                                            Tools::redirect('index.php?controller=guest-tracking&id_order='.urlencode($order->reference).'&email='.urlencode($email));
                                    }
                                    else
                                            Tools::redirect('index.php?controller=history');
                            }
                            $this->_assignPayment();
                            // assign some informations to display cart
                            $this->_assignSummaryInformations();
                            $this->setTemplate(_PS_THEME_DIR_.'order-payment.tpl');
                    break;

                    default:
                            $this->_assignSummaryInformations();
                            $this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
                    break;
            }

            $this->context->smarty->assign(array(
                'currencySign' => $this->context->currency->sign,
                'currencyRate' => $this->context->currency->conversion_rate,
                'currencyFormat' => $this->context->currency->format,
                'currencyBlank' => $this->context->currency->blank,
                'noRightColumn' => true,
            ));
    }
    
    protected function _applyTypeOrderRule() {
        /*$cart = new Cart();
        $listProducts = $cart->getCartProductById($this->context->cart->id);
        $produit = false;
        $product = new Product();
        $date = '';
        $produitJour = array();
        $cmptJour = array();
        foreach($listProducts as $cartDto) {
            if(empty($date)) {
                $date = $cartDto->getDateWithdrawal();
            }
            $productDto = $product->getById($cartDto->getIdProduct());
            $attribute = $product->getAttributeForProductDto($productDto);
            $listAttribute = $attribute->getListAttribute();
            $priceTTC = $productDto->getTTCPrice();
            if(!empty($listAttribute)) {
                $priceTTC += $listAttribute[$cartDto->getIdProductAttribute()]['price'];
            }
            $price = $productDto->getTTCPrice()*$cartDto->getQuantity();
            if(isset($cmptJour[$cartDto->getDateWithdrawal('Ymd')])) {
                $cmptJour[$cartDto->getDateWithdrawal('Ymd')]+=$price;
            } else {
                $cmptJour[$cartDto->getDateWithdrawal('Ymd')]=$price;
            }
            $produitJour[$cartDto->getDateWithdrawal('Ymd')] = $cartDto->getDateWithdrawal('', true);
        }
        ksort($produitJour);
        $this->context->smarty->assign(
            array(
                'produitJour'   => $produitJour,
                'oneDate'       => array_shift($produitJour),
                'produit'       => $produit,
            )
        );*/
    }

    protected function _assignLocalisationProducts()
    {
        $listProduct = $this->context->cart->getProducts();
        $cart = new Cart();
        $listCartDto = $cart->getCartProductById($this->context->cart->id, 'ORDER BY id_supplier');
        $supplier = new Supplier();
        $market = new Market();
        $cacheSupplier = array();
        $cacheMarket = array();
        $listRecap = array();
        foreach($listCartDto as $cartDto) {
            $date = new DateTime($cartDto->getDateWithdrawal(''));
            $dateRef = $date->format('Ymd');

            $idSupplier = $cartDto->getIdSupplier();
            if(!in_array($idSupplier, array_keys($cacheSupplier))) {
                $cacheSupplier[$idSupplier] = $supplier->getById($idSupplier);
                $listRecap[$dateRef]['supplierDto'][] = $cacheSupplier[$idSupplier];
            }

            $idMarket = $cartDto->getIdMarket();
            if(!in_array($idMarket, array_keys($cacheMarket))) {
                $cacheMarket[$idMarket] = $market->getMarketFromId($idMarket, false, false, false);
            }
            $listRecap[$dateRef]['marketDto'] = $cacheMarket[$idMarket];

            $date = new DateTime($cartDto->getDateWithdrawal(''));
            $listRecap[$dateRef]['date'] = MPTools::$listJour[$date->format('w')] . $date->format(' d') . ' ' . MPTools::$listMois[$date->format('n')-1] . $date->format(' Y');

            $listProductForSupplier = array();
            $total = 0;
            foreach($listProduct as $product) {
                $total += $product['total'];
                if($product['id_supplier']==$idSupplier) {
                    $listProductForSupplier[] = $product;
                }
            }

            if(!empty($listProductForSupplier)) {
                $listRecap[$dateRef]['products'][$idSupplier] = $listProductForSupplier;
            }
        }

        $isGoodCp = $this->_isGoodAddressForDelivery();
        $service = new Service();
        $return = $service->checkAddressVoisin($this->context->customer->id);
        $this->context->smarty->assign(
            array(
                'listRecap'     => $listRecap,
                'total'         => $total,
                'isGoodCp'      => $isGoodCp,
                'customerId'    => $this->context->customer->id,
                'needAddress'   => $return,
        ));
    }
        
    public function _isGoodAddressForDelivery() {
        $idAddressDelivery = $this->context->cart->id_address_delivery;
        $address = new Address($idAddressDelivery);
        $postCode = $address->postcode;
        $dep = substr($postCode, 0,2);
        $isGoodCp = false;
        if($dep==33) {
            $isGoodCp = true;
        }
        /*$villeDelivery = new DepartementLivraison();
        $ville = $villeDelivery->getVilleByCp($postCode);
        if($ville) {
            return true;
        }*/
        $this->context->smarty->assign(
            array(
                'isGoodCp'      => $isGoodCp,
        ));
    }
}
