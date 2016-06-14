<?php
class Basket extends ObjectModel
{
    public $name;
    public $price;
    public $day_of_week;
    public $active;
    public $id_basket;
    public $id_shop;
    public $id_lang;
    
    /**
    * @see ObjectModel::$definition
    */
    public static $definition = array(
        'table' => 'basket',
        'primary' => 'id_basket',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'name'              =>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
            'price'             =>  array('type' => self::TYPE_FLOAT, 'required' => true),
            'day_of_week'       =>  array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedId', 'size' => 5),
            'active'            =>  array('type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool', 'required' => true),
        ),
    );

    public function __construct($idBasket = null, $idLang = null, $idShop = null)
    {
        $idLang = ($idLang==null) ? Configuration::get('PS_LANG_DEFAULT') : $idLang;
        if(Tools::getValue('controller')=='AdminMPBasket' && $idBasket && !Tools::getValue('submitAddBasket')) {
            $idBasket = $this->getIdBasketForBO($idBasket);
        }

        $this->initProducts($idBasket);
        parent::__construct($idBasket, $idLang, $idShop);
        $this->image_dir    = _PS_MAR_IMG_DIR_;
        $this->id_shop      = $idShop;
        $this->id_lang      = $idLang;
    }

    /** 
     * 
     * GESTION BO 
     * 
     **/
    private function initProducts($idBasket) {
        $ret = Db::getInstance()->executeS('SELECT id_product, id_product_attribute, id_supplier FROM ' . _DB_PREFIX_ . 'basket_product WHERE id_basket=' . (int) $idBasket);
        foreach($ret as $value) {
            $this->{'product_' . $value['id_product']} = 1;
            $this->{'product_attribute_' . $value['id_product_attribute']} = 1;
            $this->{'supplier_' . $value['id_supplier']} = 1;
            $this->listProducts[$value['id_supplier']][] = array('product'=>$value['id_product'], 'product_attribute'=>$value['id_product_attribute']);
        }
    }
    
    public function add($autodate = true, $null_values = false)
    {
        if(Tools::getValue('controller')=='AdminMPBasket') {
            $ret = $this->addPanier();
        }

        return $ret;
    }
    
    private function addPanier() {
        $ret = Db::getInstance()->insert('basket', 
                array(
                    array(
                        'name'          =>  addslashes($this->name),
                        'price'         =>  $this->price,
                        'day_of_week'   =>  $this->day_of_week,
                        'active'        =>  $this->active,
                    )
                )
            );
        $this->id_basket = (int) Db::getInstance()->Insert_ID();
        $this->id = $this->id_basket;

        $ret &= Db::getInstance()->insert('basket_lang', array(array('id_lang'=>(int)$this->id_lang)));

        return $ret;
    }

    public function update($null_values = false)
    {
        if(Tools::getValue('controller')=='AdminMPBasket') {
            $ret = $this->updateBasket();
        }

        return $ret;
    }

    private function updateBasket() {
        $ret = Db::getInstance()->update('basket', 
                array(
                    'name'          =>  addslashes($this->name),
                    'price'         =>  $this->price,
                    'day_of_week'   =>  $this->day_of_week,
                    'active'        =>  $this->active,
            ), '`id_basket` = ' . (int)$this->id_basket);
        
        $ret &= Db::getInstance()->update('basket_lang', array('id_lang'=>(int)$this->id_lang), '`id_basket` = '.(int)$this->id_basket);
        
        return $ret;
    }

    private function addBasketProducts() {
        
        $values = array();
        $vars = get_object_vars($this);
        foreach($vars as $key=>$value) {
            if(strstr($key, 'supplier_') && $value==='1') {
                $values[] .= substr($key, strlen('supplier_'));
            }
        }
        
        $ret = Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'market_supplier WHERE id_market=' . $this->id_market);
        if(isset($this->id_supplier)) {
            $ret &= Db::getInstance()->insert('market_supplier', array('id_market'=>$this->id_market, 'id_supplier'=>$this->id_supplier));
        }
        foreach($values as $value) {
            $ret &= Db::getInstance()->insert('market_supplier', array('id_market'=>$this->id_market, 'id_supplier'=>$value));
        }
        
        return $ret;
    }

    public static function getListBasketForBO() {
        $sql = 'SELECT * FROM '._DB_PREFIX_. 'basket ORDER BY name';
        return Db::getInstance()->executeS($sql);
    }

    private function getIdBasketForBO($idBasket) {
        $res = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_  .'basket WHERE id_basket=' . (int) $idBasket);
        return $res[0]['id_basket'];
    }

    /** 
     * 
     * GESTION FRONT
     * 
     **/
    
    /** V2 **/
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
        $basket = null;
        if($result) {
            $basket = new MarketDto();
            $basket->setActive(isset($result['active']) ? $result['active'] : 0);
            $basket->setAddress(isset($result['address']) ? $result['address'] : 0);
            $basket->setCity(isset($result['city']) ? $result['city'] : 0);
            $basket->setCloseAt(isset($result['close_at']) ? $result['close_at'] : 0);
            $basket->setDateAdd(isset($result['date_add']) ? $result['date_add'] : 0);
        }
        return $basket;
    }
    
}
