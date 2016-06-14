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

class Cart extends CartCore
{
    const PRODUIT_EN_ATTENTE_VALIDATION = 0;
    const PRODUIT_EN_COURS_DE_PREPARATION = 1;
    const PRODUIT_ANNULE = 2;
    const PRODUIT_A_PAYER = 3;
    const ADD_SHIPPING_COAST_FOR_SEVERAL_DAYS = 3;
    
    protected $webserviceParameters = array(
            'fields' => array(
                'id_address_delivery' => array('xlink_resource' => 'addresses'),
                'id_address_invoice' => array('xlink_resource' => 'addresses'),
                'id_currency' => array('xlink_resource' => 'currencies'),
                'id_customer' => array('xlink_resource' => 'customers'),
                'id_guest' => array('xlink_resource' => 'guests'),
                'id_lang' => array('xlink_resource' => 'languages'),
            ),
            'associations' => array(
                'cart_rows' => array('resource' => 'cart_rows', 'virtual_entity' => true, 'fields' => array(
                    'id_product' => array('required' => true, 'xlink_resource' => 'products'),
                    'id_supplier' => array('required' => true),
                    'id_market' => array('required' => true),
                    'status' => array('required' => true),
                    'date_withdrawal' => array('required' => true),
                    'id_product_attribute' => array('required' => true, 'xlink_resource' => 'combinations'),
                    'id_address_delivery' => array('required' => true, 'xlink_resource' => 'addresses'),
                    'quantity' => array('required' => true),
                    )
                ),
            ),
	);
    
	/**
	 * Update product quantity
	 *
	 * @param integer $quantity Quantity to add (or substract)
	 * @param integer $id_product Product ID
	 * @param integer $id_product_attribute Attribute ID if needed
	 * @param string $operator Indicate if quantity must be increased or decreased
	 */
	public function updateQtyV2($quantity, $id_product, $id_supplier, $date_withdrawal, $id_product_attribute = null, $id_customization = false,
		$operator = 'up', $id_address_delivery = 0, Shop $shop = null, $auto_add_cart_rule = true)
	{
		if (!$shop)
			$shop = Context::getContext()->shop;

		if (Context::getContext()->customer->id)
		{
			if ($id_address_delivery == 0 && (int)$this->id_address_delivery) // The $id_address_delivery is null, use the cart delivery address
				$id_address_delivery = $this->id_address_delivery;
			elseif ($id_address_delivery == 0) // The $id_address_delivery is null, get the default customer address
				$id_address_delivery = (int)Address::getFirstCustomerAddressId((int)Context::getContext()->customer->id);
			elseif (!Customer::customerHasAddress(Context::getContext()->customer->id, $id_address_delivery)) // The $id_address_delivery must be linked with customer
				$id_address_delivery = 0;
		}

		$quantity = (int)$quantity;
		$id_product = (int)$id_product;
		$id_product_attribute = (int)$id_product_attribute;
		$product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'), $shop->id);

		if ($id_product_attribute)
		{
			$combination = new Combination((int)$id_product_attribute);
			if ($combination->id_product != $id_product)
				return false;
		}

		/* If we have a product combination, the minimal quantity is set with the one of this combination */
		if (!empty($id_product_attribute))
			$minimal_quantity = (int)Attribute::getAttributeMinimalQty($id_product_attribute);
		else
			$minimal_quantity = (int)$product->minimal_quantity;

		if (!Validate::isLoadedObject($product))
			die(Tools::displayError());

		if (isset(self::$_nbProducts[$this->id]))
			unset(self::$_nbProducts[$this->id]);

		if (isset(self::$_totalWeight[$this->id]))
			unset(self::$_totalWeight[$this->id]);

		if ((int)$quantity <= 0)
			return $this->deleteProduct($id_product, $id_product_attribute, (int)$id_customization);
		elseif (!$product->available_for_order || Configuration::get('PS_CATALOG_MODE'))
			return false;
		else
		{
			/* Check if the product is already in the cart */
			$result = $this->containsProduct($id_product, $id_product_attribute, (int)$id_customization, (int)$id_address_delivery);

			/* Update quantity if product already exist */
			if ($result)
			{
				if ($operator == 'up')
				{
					$sql = 'SELECT stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
							FROM '._DB_PREFIX_.'product p
							'.Product::sqlStock('p', $id_product_attribute, true, $shop).'
							WHERE p.id_product = '.$id_product;

					$result2 = Db::getInstance()->getRow($sql);
					$product_qty = (int)$result2['quantity'];
					// Quantity for product pack
					if (Pack::isPack($id_product))
						$product_qty = Pack::getQuantity($id_product, $id_product_attribute);
					$new_qty = (int)$result['quantity'] + (int)$quantity;
					$qty = '+ '.(int)$quantity;

					if (!Product::isAvailableWhenOutOfStock((int)$result2['out_of_stock']))
						if ($new_qty > $product_qty)
							return false;
				}
				else if ($operator == 'down')
				{
					$qty = '- '.(int)$quantity;
					$new_qty = (int)$result['quantity'] - (int)$quantity;
					if ($new_qty < $minimal_quantity && $minimal_quantity > 1)
						return -1;
				}
				else
					return false;

				/* Delete product from cart */
				if ($new_qty <= 0)
					return $this->deleteProduct((int)$id_product, (int)$id_product_attribute, (int)$id_customization);
				else if ($new_qty < $minimal_quantity)
					return -1;
				else
					Db::getInstance()->execute('
						UPDATE `'._DB_PREFIX_.'cart_product`
						SET `quantity` = `quantity` '.$qty.', `date_add` = NOW()
						WHERE `id_product` = '.(int)$id_product.
						(!empty($id_product_attribute) ? ' AND `id_product_attribute` = '.(int)$id_product_attribute : '').'
						AND `id_cart` = '.(int)$this->id.(Configuration::get('PS_ALLOW_MULTISHIPPING') && $this->isMultiAddressDelivery() ? ' AND `id_address_delivery` = '.(int)$id_address_delivery : '').'
						LIMIT 1'
					);
			}
			/* Add product to the cart */
			elseif ($operator == 'up')
			{
				$sql = 'SELECT stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity
						FROM '._DB_PREFIX_.'product p
						'.Product::sqlStock('p', $id_product_attribute, true, $shop).'
						WHERE p.id_product = '.$id_product;

				$result2 = Db::getInstance()->getRow($sql);

				// Quantity for product pack
				if (Pack::isPack($id_product))
					$result2['quantity'] = Pack::getQuantity($id_product, $id_product_attribute);

				if (!Product::isAvailableWhenOutOfStock((int)$result2['out_of_stock']))
					if ((int)$quantity > $result2['quantity'])
						return false;

				if ((int)$quantity < $minimal_quantity)
					return -1;

				$result_add = Db::getInstance()->insert('cart_product', array(
					'id_product'            => (int)$id_product,
					'id_supplier'           => (int)$id_supplier,
                                        'date_withdrawal'       => $date_withdrawal,
					'id_product_attribute'  => (int)$id_product_attribute,
					'id_cart'               => (int)$this->id,
					'id_address_delivery'   => (int)$id_address_delivery,
					'id_shop'               => $shop->id,
					'quantity'              => (int)$quantity,
					'date_add'              => date('Y-m-d H:i:s')
				));

				if (!$result_add)
					return false;
			}
		}

		// refresh cache of self::_products
		$this->_products = $this->getProducts(true);
		$this->update(true);
		$context = Context::getContext()->cloneContext();
		$context->cart = $this;
		Cache::clean('getContextualValue_*');
		if ($auto_add_cart_rule)
			CartRule::autoAddToCart($context);

		if ($product->customizable)
			return $this->_updateCustomizationQuantity((int)$quantity, (int)$id_customization, (int)$id_product, (int)$id_product_attribute, (int)$id_address_delivery, $operator);
		else
			return true;
	}
        
        /**
	 * Return package shipping cost
	 *
	 * @param int          $id_carrier      Carrier ID (default : current carrier)
	 * @param bool         $use_tax
	 * @param Country|null $default_country
	 * @param array|null   $product_list    List of product concerned by the shipping.
	 *                                      If null, all the product of the cart are used to calculate the shipping cost
	 * @param int|null $id_zone
	 *
	 * @return float Shipping total
	 */
	public function getPackageShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null, $id_zone = null)
	{
		if ($this->isVirtualCart())
			return 0;

		if (!$default_country)
			$default_country = Context::getContext()->country;

		if (!is_null($product_list))
			foreach ($product_list as $key => $value)
				if ($value['is_virtual'] == 1)
					unset($product_list[$key]);

		if (is_null($product_list))
			$products = $this->getProducts();
		else
			$products = $product_list;

		if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice')
			$address_id = (int)$this->id_address_invoice;
		elseif (count($product_list))
		{
			$prod = current($product_list);
			$address_id = (int)$prod['id_address_delivery'];
		}
		else
			$address_id = null;
		if (!Address::addressExists($address_id))
			$address_id = null;

		$cache_id = 'getPackageShippingCost_'.(int)$this->id.'_'.(int)$address_id.'_'.(int)$id_carrier.'_'.(int)$use_tax.'_'.(int)$default_country->id;
		if ($products)
			foreach ($products as $product)
				$cache_id .= '_'.(int)$product['id_product'].'_'.(int)$product['id_product_attribute'];

		if (Cache::isStored($cache_id))
			return Cache::retrieve($cache_id);

		// Order total in default currency without fees
		$order_total = $this->getOrderTotal(true, Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING, $product_list);

		// Start with shipping cost at 0
		$shipping_cost = 0;
		// If no product added, return 0
		if (!count($products))
		{
			Cache::store($cache_id, $shipping_cost);
			return $shipping_cost;
		}

		if (!isset($id_zone))
		{
			// Get id zone
			if (!$this->isMultiAddressDelivery()
				&& isset($this->id_address_delivery) // Be carefull, id_address_delivery is not usefull one 1.5
				&& $this->id_address_delivery
				&& Customer::customerHasAddress($this->id_customer, $this->id_address_delivery
			))
				$id_zone = Address::getZoneById((int)$this->id_address_delivery);
			else
			{
				if (!Validate::isLoadedObject($default_country))
					$default_country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), Configuration::get('PS_LANG_DEFAULT'));

				$id_zone = (int)$default_country->id_zone;
			}
		}

		if ($id_carrier && !$this->isCarrierInRange((int)$id_carrier, (int)$id_zone))
			$id_carrier = '';

		if (empty($id_carrier) && $this->isCarrierInRange((int)Configuration::get('PS_CARRIER_DEFAULT'), (int)$id_zone))
			$id_carrier = (int)Configuration::get('PS_CARRIER_DEFAULT');

		$total_package_without_shipping_tax_inc = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, $product_list);
		if (empty($id_carrier))
		{
			if ((int)$this->id_customer)
			{
				$customer = new Customer((int)$this->id_customer);
				$result = Carrier::getCarriers((int)Configuration::get('PS_LANG_DEFAULT'), true, false, (int)$id_zone, $customer->getGroups());
				unset($customer);
			}
			else
				$result = Carrier::getCarriers((int)Configuration::get('PS_LANG_DEFAULT'), true, false, (int)$id_zone);

			foreach ($result as $k => $row)
			{
				if ($row['id_carrier'] == Configuration::get('PS_CARRIER_DEFAULT'))
					continue;

				if (!isset(self::$_carriers[$row['id_carrier']]))
					self::$_carriers[$row['id_carrier']] = new Carrier((int)$row['id_carrier']);

				/** @var Carrier $carrier */
				$carrier = self::$_carriers[$row['id_carrier']];

				$shipping_method = $carrier->getShippingMethod();
				// Get only carriers that are compliant with shipping method
				if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $carrier->getMaxDeliveryPriceByWeight((int)$id_zone) === false)
				|| ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->getMaxDeliveryPriceByPrice((int)$id_zone) === false))
				{
					unset($result[$k]);
					continue;
				}

				// If out-of-range behavior carrier is set on "Desactivate carrier"
				if ($row['range_behavior'])
				{
					$check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $this->getTotalWeight(), (int)$id_zone);

					$total_order = $total_package_without_shipping_tax_inc;
					$check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $total_order, (int)$id_zone, (int)$this->id_currency);

					// Get only carriers that have a range compatible with cart
					if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && !$check_delivery_price_by_weight)
					|| ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && !$check_delivery_price_by_price))
					{
						unset($result[$k]);
						continue;
					}
				}

				if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT)
					$shipping = $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), (int)$id_zone);
				else
					$shipping = $carrier->getDeliveryPriceByPrice($order_total, (int)$id_zone, (int)$this->id_currency);

				if (!isset($min_shipping_price))
					$min_shipping_price = $shipping;

				if ($shipping <= $min_shipping_price)
				{
					$id_carrier = (int)$row['id_carrier'];
					$min_shipping_price = $shipping;
				}
			}
		}

		if (empty($id_carrier))
			$id_carrier = Configuration::get('PS_CARRIER_DEFAULT');

		if (!isset(self::$_carriers[$id_carrier]))
			self::$_carriers[$id_carrier] = new Carrier((int)$id_carrier, Configuration::get('PS_LANG_DEFAULT'));

		$carrier = self::$_carriers[$id_carrier];

		// No valid Carrier or $id_carrier <= 0 ?
		if (!Validate::isLoadedObject($carrier))
		{
			Cache::store($cache_id, 0);
			return 0;
		}
		$shipping_method = $carrier->getShippingMethod();

		if (!$carrier->active)
		{
			Cache::store($cache_id, $shipping_cost);
			return $shipping_cost;
		}

		// Free fees if free carrier
		if ($carrier->is_free == 1)
		{
			Cache::store($cache_id, 0);
			return 0;
		}

		// Select carrier tax
		if ($use_tax && !Tax::excludeTaxeOption())
		{
			$address = Address::initialize((int)$address_id);

			if (Configuration::get('PS_ATCP_SHIPWRAP'))
			{
				// With PS_ATCP_SHIPWRAP, pre-tax price is deduced
				// from post tax price, so no $carrier_tax here
				// even though it sounds weird.
				$carrier_tax = 0;
			}
			else
			{
				$carrier_tax = $carrier->getTaxesRate($address);
			}

		}

		$configuration = Configuration::getMultiple(array(
			'PS_SHIPPING_FREE_PRICE',
			'PS_SHIPPING_HANDLING',
			'PS_SHIPPING_METHOD',
			'PS_SHIPPING_FREE_WEIGHT'
		));

		// Free fees
		$free_fees_price = 0;
		if (isset($configuration['PS_SHIPPING_FREE_PRICE']))
			$free_fees_price = Tools::convertPrice((float)$configuration['PS_SHIPPING_FREE_PRICE'], Currency::getCurrencyInstance((int)$this->id_currency));
		$orderTotalwithDiscounts = $this->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, null, null, false);
		if ($orderTotalwithDiscounts >= (float)($free_fees_price) && (float)($free_fees_price) > 0)
		{
			Cache::store($cache_id, $shipping_cost);
			return $shipping_cost;
		}

		if (isset($configuration['PS_SHIPPING_FREE_WEIGHT'])
			&& $this->getTotalWeight() >= (float)$configuration['PS_SHIPPING_FREE_WEIGHT']
			&& (float)$configuration['PS_SHIPPING_FREE_WEIGHT'] > 0)
		{
			Cache::store($cache_id, $shipping_cost);
			return $shipping_cost;
		}

		// Get shipping cost using correct method
		if ($carrier->range_behavior)
		{
			if (!isset($id_zone))
			{
				// Get id zone
				if (isset($this->id_address_delivery)
					&& $this->id_address_delivery
					&& Customer::customerHasAddress($this->id_customer, $this->id_address_delivery))
					$id_zone = Address::getZoneById((int)$this->id_address_delivery);
				else
					$id_zone = (int)$default_country->id_zone;
			}

			if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && !Carrier::checkDeliveryPriceByWeight($carrier->id, $this->getTotalWeight(), (int)$id_zone))
			|| ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && !Carrier::checkDeliveryPriceByPrice($carrier->id, $total_package_without_shipping_tax_inc, $id_zone, (int)$this->id_currency)
			))
				$shipping_cost += 0;
			else
			{
				if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT)
					$shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), $id_zone);
				else // by price
					$shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int)$this->id_currency);
			}
		}
		else
		{
			if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT)
				$shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), $id_zone);
			else
				$shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int)$this->id_currency);

		}
		// Adding handling charges
		if (isset($configuration['PS_SHIPPING_HANDLING']) && $carrier->shipping_handling)
			$shipping_cost += (float)$configuration['PS_SHIPPING_HANDLING'];

		// Additional Shipping Cost per product
                $allreadyUseSC = 0;
		foreach ($products as $product) {
                    if (!$product['is_virtual'] && empty($allreadyUseSC)) {
                        $shipping_cost -= $product['additional_shipping_cost'] * $product['cart_quantity'];
                        $allreadyUseSC = 1;
                    }
                }
                
                // Additional Shipping Cost per date of delivery
                if ($products) {
                    $cart = new Cart();
                    $listCartDto = $cart->getCartProductById($this->id);
                    $oldWithdrawal = 0;
                    $addShippingCost = false;
                    foreach($listCartDto as $cartDto) {
                        $withDrawal = $cartDto->getDateWithdrawal();
                        if(empty($oldWithdrawal)) {
                            $oldWithdrawal = $withDrawal;
                        }
                        if($oldWithdrawal!=$withDrawal) {
                            $addShippingCost = true;
                            break;
                        }
                    }
                    if($addShippingCost) {
                        $shipping_cost += self::ADD_SHIPPING_COAST_FOR_SEVERAL_DAYS;
                    }
                }
		$shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance((int)$this->id_currency));

		//get external shipping cost from module
		if ($carrier->shipping_external)
		{
			$module_name = $carrier->external_module_name;

			/** @var CarrierModule $module */
			$module = Module::getInstanceByName($module_name);

			if (Validate::isLoadedObject($module))
			{
				if (array_key_exists('id_carrier', $module))
					$module->id_carrier = $carrier->id;
				if ($carrier->need_range)
					if (method_exists($module, 'getPackageShippingCost'))
						$shipping_cost = $module->getPackageShippingCost($this, $shipping_cost, $products);
					else
						$shipping_cost = $module->getOrderShippingCost($this, $shipping_cost);
				else
					$shipping_cost = $module->getOrderShippingCostExternal($this);

				// Check if carrier is available
				if ($shipping_cost === false)
				{
					Cache::store($cache_id, false);
					return false;
				}
			}
			else
			{
				Cache::store($cache_id, false);
				return false;
			}
		}

		if (Configuration::get('PS_ATCP_SHIPWRAP'))
		{
				if (!$use_tax)
				{
					// With PS_ATCP_SHIPWRAP, we deduce the pre-tax price from the post-tax
					// price. This is on purpose and required in Germany.
					$shipping_cost /= (1 + $this->getAverageProductsTaxRate());
				}
		}
		else
		{
			// Apply tax
			if ($use_tax && isset($carrier_tax))
				$shipping_cost *= 1 + ($carrier_tax / 100);
		}

		$shipping_cost = (float)Tools::ps_round((float)$shipping_cost, 2);
		Cache::store($cache_id, $shipping_cost);

		return $shipping_cost;
	}
        
    public function updateStatus($cartId, $productId, $status) {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'cart_product SET status=' . (int) $status . ' WHERE id_cart='. (int) $cartId . ' AND id_product=' . (int) $productId;
        Db::getInstance()->execute($sql);
        
        return true;
    }

    public function getCommandes($status, $supplierIds) {
        $sql = 'SELECT o.id_order, o.reference, cp.id_cart, cp.id_product, IF(cp.id_product_attribute=0, cp.quantity, al.name) as quantity , cp.id_market, cp.date_withdrawal, p.price as productPrice, pa.price as attributePrice, pl.name as productName, m.city, m.postal_code, ml.name as marketName FROM ' . _DB_PREFIX_ . 'orders o '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cart_product cp ON cp.id_cart=o.id_cart '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product=cp.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=cp.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market m ON m.id_market=cp.id_market '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_lang ml ON ml.id_market=cp.id_market '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON pa.id_product_attribute=cp.id_product_attribute '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pac.id_product_attribute=cp.id_product_attribute '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON al.id_attribute=pac.id_attribute '
                . 'WHERE o.current_state=' . (int) $status . ' AND cp.id_supplier IN (' . implode(',', $supplierIds) .') AND cp.status=' . (int) Cart::PRODUIT_EN_ATTENTE_VALIDATION . ' '
                . 'GROUP BY id_product '
                . 'ORDER BY reference';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * Retourne la liste des commandes depuis la date $dateFrom
     * @param int $status
     * @param array $supplierIds
     * @param string $dateFrom
     * @return CartDto
     * id_tax_rules_group
     */
    public function getCommandesByDate($status, $supplierIds, $dateFrom) {
        $sql = 'SELECT o.date_add as date_withdrawal, o.id_order, p.id_supplier, IF(od.product_attribute_id=0, od.product_quantity, al.name) as quantity, od.original_wholesale_price as productPrice, p.unity, pl.name as productName, od.total_price_tax_incl, t.rate, p.mp_com '
                . 'FROM ' . _DB_PREFIX_ . 'order_detail od '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_order = od.id_order '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = od.product_id '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON pa.id_product_attribute=od.product_attribute_id '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pac.id_product_attribute=od.product_attribute_id '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON al.id_attribute=pac.id_attribute AND al.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax_rule tr ON tr.id_tax_rules_group=od.id_tax_rules_group AND tr.id_country=' . (int)Context::getContext()->country->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax t ON t.id_tax=tr.id_tax '
                . 'WHERE o.current_state IN (' . Order::LIVRE . ',' . Order::PAIEMENT_ACCEPTE . ',' . Order::PREPARATION_EN_COURS . ') AND p.id_supplier IN (' . implode(',', $supplierIds) .') '
                . 'AND o.date_add>=\'' . $dateFrom . '\' '
                . 'ORDER BY o.date_add DESC, p.id_product';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * @param array $supplierIds
     * @param string $dateFrom
     * @return PackDto
     */
    public function getProductsFromPack($supplierIds, $dateFrom) {
        $sql = 'SELECT o.date_add as date_withdrawal, o.id_order, IF(pack.id_product_attribute_item=0, pack.quantity, al.name) as quantity, p.IF(pas.price,p.price+pas.price,p.price) as productPrice, p.unity, pl.name as productName, IF(pas.price,p.price+pas.price,p.price)*IF(t.rate,(1+t.rate/100),1)*pack.quantity as total_price_tax_incl, t.rate, p.mp_com '
                . 'FROM ' . _DB_PREFIX_ . 'orders o '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'order_detail od ON od.id_order = o.id_order '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'pack pack ON pack.id_product_pack = od.product_id '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = pack.id_product_item '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pac.id_product_attribute=pack.id_product_attribute_item '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_shop pas ON pas.id_product_attribute = pack.id_product_attribute_item AND pas.id_product=pack.id_product_item '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON al.id_attribute=pac.id_attribute AND al.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax_rule tr ON tr.id_tax_rules_group=od.id_tax_rules_group AND tr.id_country=' . (int)Context::getContext()->country->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax t ON t.id_tax=tr.id_tax '
                . 'WHERE p.id_supplier IN (' . implode(',', $supplierIds) .') '
                . 'AND o.date_add>=\'' . $dateFrom . '\' '
                . 'ORDER BY o.date_add DESC, p.id_product';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * Retourne la liste des commandes depuis la date $dateFrom
     * @param int $status
     * @param array $supplierIds
     * @param string $dateFrom
     * @param string $dateTo
     * @return SupplierDto
     * id_tax_rules_group
     */
    public function getCommandesByDateInDateOut($supplierIds, $dateFrom, $dateTo) {
        $sql = 'SELECT o.date_add as date_withdrawal, o.id_order, IF(od.product_attribute_id=0, od.product_quantity, al.name) as quantity, od.original_wholesale_price as productPrice, p.unity, pl.name as productName, od.total_price_tax_incl, t.rate, p.mp_com '
                . 'FROM ' . _DB_PREFIX_ . 'order_detail od '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_order = od.id_order '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = od.product_id '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON pa.id_product_attribute=od.product_attribute_id '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pac.id_product_attribute=od.product_attribute_id '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON al.id_attribute=pac.id_attribute AND al.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax_rule tr ON tr.id_tax_rules_group=od.id_tax_rules_group AND tr.id_country=' . (int)Context::getContext()->country->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax t ON t.id_tax=tr.id_tax '
                . 'WHERE o.current_state IN (' . Order::LIVRE . ',' . Order::PAIEMENT_ACCEPTE . ',' . Order::PREPARATION_EN_COURS . ') AND p.id_supplier IN (' . implode(',', $supplierIds) .') '
                . 'AND o.date_add>=\'' . $dateFrom . '\' '
                . 'AND o.date_add<\'' . $dateTo . '\' '
                . 'ORDER BY o.date_add DESC, p.id_product';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * @param int $idSupplier
     * @return CartDto[]
     */
    public function getReferenceToValidateByIdSupplier($supplierIds) {
        $sql = 'SELECT o.reference, cp.id_cart FROM ' . _DB_PREFIX_ . 'cart_product cp '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_cart=cp.id_cart '
                . 'WHERE cp.id_supplier IN (' . implode(',', $supplierIds) . ') AND cp.status=' . (int) Cart::PRODUIT_EN_COURS_DE_PREPARATION . ' GROUP BY o.reference';
        
        $result = Db::getInstance()->executeS($sql);
        
        return $this->prepareDto($result, true);
    }

    public function updateStatusFromReference($idCart, $ref, $supplierIds, $status) {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'cart_product cp INNER JOIN ' . _DB_PREFIX_  .'orders o ON o.id_cart=cp.id_cart SET status=' . (int) $status . ' '
                . 'WHERE cp.id_cart=' . (int) $idCart . ' AND cp.id_supplier IN (' . implode(',', $supplierIds) . ') AND o.reference=\'' . $ref . '\'';
        Db::getInstance()->execute($sql);
    }
    
    /**
     * @param int $idCustomer
     * @return array
     */
    public function getByIdCustomerForHistory($idCustomer) {
        $sql = 'SELECT o.reference as reference, pl.name as productName, p.price as unitPrice, pa.price as attrPrice, cp.quantity as quantity, cp.date_withdrawal as dateRetrait '
                . 'FROM ' . _DB_PREFIX_ . 'cart_product cp '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier_account sa ON sa.id_supplier=cp.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product=cp.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_cart=cp.id_cart '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON pa.id_product_attribute=cp.id_product_attribute '
                . 'WHERE sa.id_account=' . (int) $idCustomer . ' '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'AND o.current_state=' . (int) Order::LIVRE . ' '
                . 'ORDER BY dateRetrait DESC';
        return Db::getInstance()->executeS($sql);
    }

    /**
     * @param int $idCart
     * @return CartDto[]
     */
    public function getCartProductById($idCart, $orderBy = '') {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'cart_product WHERE id_cart=' . (int) $idCart;
        if(!empty($orderBy)) {
            $sql .= ' ' . $orderBy;
        }
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * @param int $idCart
     * @param int $idProduct
     * @return CartDto
     */
    public function getCartProductByIdCartAndIdProduct($idCart, $idProduct) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'cart_product WHERE id_cart=' . (int) $idCart . ' AND id_product=' . (int) $idProduct;
        $result = Db::getInstance()->getRow($sql);
        return $this->prepareDto($result);
    }

    /**
     * @param int $idOrder
     * @return CartDto
     */
    public function getByIdOrderAndIdProduct($idOrder, $idProduct) {
        $sql = 'SELECT cp.* FROM ' . _DB_PREFIX_ . 'cart_product cp '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'orders o ON cp.id_cart=o.id_cart '
                . 'WHERE o.id_order=' . (int) $idOrder  .' AND cp.id_product=' . (int) $idProduct . ' ORDER BY id_market, id_supplier';
        $result = Db::getInstance()->getRow($sql);
        
        return $this->prepareDto($result);
    }
    
    /**
     * @param int $idOrder
     * @return CartDto
     */
    public function getByIdOrder($idOrder) {
        $sql = 'SELECT cp.* FROM ' . _DB_PREFIX_ . 'cart_product cp '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'orders o ON cp.id_cart=o.id_cart '
                . 'WHERE o.id_order=' . (int) $idOrder  .' ORDER BY id_market, id_supplier';
        $result = Db::getInstance()->getRow($sql);
        
        return $this->prepareDto($result);
    }
    
    
    /**
     * @param int $status
     * @return CartDto[]
     */
    public function getByCartStatus($status, $dateStart, $dateEnd) {
        $sql = 'SELECT s.id_supplier, cp.id_market FROM ' . _DB_PREFIX_ . 'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cart_product cp ON cp.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_cart=cp.id_cart '
                . 'WHERE o.current_state=' . Order::PAIEMENT_ACCEPTE . ' AND cp.status=' . (int) $status . ' AND date_withdrawal>\'' . $dateStart . '\' AND date_withdrawal<=\'' . $dateEnd . '\' GROUP BY s.id_supplier';
        $result = Db::getInstance()->executeS($sql);
        
        return $this->prepareDto($result, true);
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
        $cart = null;
        if($result) {
            $cart = new CartDto();
            $cart->setDateWithdrawal(isset($result['date_withdrawal']) ? $result['date_withdrawal'] : '');
            $cart->setProductName(isset($result['productName']) ? $result['productName'] : '');
            $cart->setIdCart(isset($result['id_cart']) ? $result['id_cart'] : 0);
            $cart->setIdMarket(isset($result['id_market']) ? $result['id_market'] : 0);
            $cart->setIdOrder(isset($result['id_order']) ? $result['id_order'] : 0);
            $cart->setOrderReference(isset($result['reference']) ? $result['reference'] : 0);
            $cart->setIdProduct(isset($result['id_product']) ? $result['id_product'] : 0);
            $cart->setIdSupplier(isset($result['id_supplier']) ? $result['id_supplier'] : 0);
            $cart->setQuantity(isset($result['quantity']) ? $result['quantity'] : 0);
            $cart->setStatus(isset($result['status']) ? $result['status'] : 0);
            $cart->setIdProductAttribute(isset($result['id_product_attribute']) ? $result['id_product_attribute'] : 0);
            $cart->setPriceIncludeTax(isset($result['total_price_tax_incl']) ? $result['total_price_tax_incl'] : 0);
            $cart->setTax(isset($result['rate']) ? $result['rate'] : 0);
            $cart->setMpCom(isset($result['mp_com']) ? $result['mp_com'] : 0);
            $cart->setProductPrice(isset($result['productPrice']) ? $result['productPrice'] : 0);
            $cart->setUnity(isset($result['unity']) ? $result['unity'] : '');
            $cart->setDatePayment(isset($result['date_payment']) ? $result['date_payment'] : '');
        }
        return $cart;
    }
}
