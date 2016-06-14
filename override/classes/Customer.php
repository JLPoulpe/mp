<?php
class Customer extends CustomerCore
{
    public function idSupplier()
	{
		$sql = 'SELECT id_supplier FROM ' . _DB_PREFIX_ . 'supplier_account'
                . ' WHERE id_account=' . (int) $this->id;
        $res = Db::getInstance()->getRow($sql);
        
        return $res['id_supplier'];
	}
    
    /**
     * @param int $idCustomer
     * @return customerDto
     */
    public function getById($idCustomer) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'customer c WHERE id_customer=' . (int) $idCustomer;
        $result = Db::getInstance()->getRow($sql);
        
        return $this->prepareDto($result);
    }
    
    /**
     * @param int $idCart
     * @return customerDto
     */
    public function getByIdCart($idCart) {
        $sql = 'SELECT c.* FROM ' . _DB_PREFIX_ . 'customer c '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cart ca ON ca.id_customer=c.id_customer '
                . 'WHERE ca.id_cart=' . (int) $idCart;
        $result = Db::getInstance()->getRow($sql);
        return $this->prepareDto($result);
    }

    /**
	 * Return customers list
	 *
	 * @return customerDto
	 */
	public function getCustomersForNewsletter()
	{
            $sql = 'SELECT `id_customer`, `email`, `firstname`, `lastname`
                    FROM `'._DB_PREFIX_.'customer` c
                    WHERE c.newsletter=1 
                    ORDER BY `id_customer` ASC';

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
        $customer = null;
        if($result) {
            $customer = new CustomerDto();
            $customer->setIdCustomer(isset($result['id_customer']) ? $result['id_customer'] : 0);
            $customer->setEmail(isset($result['email']) ? $result['email'] : '');
            $customer->setFirstname(isset($result['firstname']) ? $result['firstname'] : '');
            $customer->setLastname(isset($result['lastname']) ? $result['lastname'] : '');
        }
        
        return $customer;
    }
}
