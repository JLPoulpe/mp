<?php
class Market extends ObjectModel
{
    public $id;
    
    /** @var integer Market ID */
    public $id_market;
    
    /** @var integer Shop ID */
    public $id_shop;
    
    /** @var integer Lang ID */
    public $id_lang;
    
    /** @var string Market address */
    public $address;
    
    /** @var string Market description */
    public $description;
    
    /** @var string name Market name */
    public $name;
    
    /** @var string postal_code Market postal code */
    public $postal_code;
    
    /** @var string city Market city */
    public $city;
    
    /** @var Date  creation date */
    public $date_add;

    /** @var Date  last modification date */
    public $date_upd;

    /** @var Bool is market open on monday */
    public $lundi;
    
    /** @var Bool is market open on tuesday */
    public $mardi;
    
    /** @var Bool is market open on wednesday */
    public $mercredi;
    
    /** @var Bool is market open on thursday */
    public $jeudi;
    
    /** @var Bool is market open on friday */
    public $vendredi;
    
    /** @var Bool is market open on saturday */
    public $samedi;
    
    /** @var Bool is market open on sunday */
    public $dimanche;

    /** @var string  market open hour */
    public $open_at;

    /** @var string  market close hour */
    public $close_at;

    /** @var integer default Market ID */
    public $id_market_default;

    /** @var string string used in rewrited URL */
    public $link_rewrite;

    /** @var string Meta title */
    public $meta_title;

    /** @var string Meta keywords */
    public $meta_keywords;

    /** @var string Meta description */
    public $meta_description;
    
    /** @var array Supplier List */
    public $list_supplier;
    
    /** @var bool Is Market active */
    public $active;
    
    public $latitude;
    
    public $longitude;
    
    public static $listeJour = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
    
    public $suppliers;
    
    public $id_supplier;
    /**
    * @see ObjectModel::$definition
    */
    public static $definition = array(
        'table' => 'market',
        'primary' => 'id_market',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'address'           =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 255),
            'postal_code'       =>  array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedId', 'size' => 5),
            'city'              =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 100),
            'active'            =>  array('type' => self::TYPE_BOOL, 'required' => true, 'validate' => 'isBool', 'required' => true),
            'lundi'             =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'mardi'             =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'mercredi'          =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'jeudi'             =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'vendredi'          =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'samedi'            =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'dimanche'          =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),

            // Lang fields
            'name'              =>  array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128),
            'description'       =>  array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'link_rewrite'      =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 128),
            'meta_title'        =>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128),
            'meta_description'  =>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords'     =>  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'open_at'           =>  array('type' => self::TYPE_STRING, 'required' => true),
            'close_at'          =>  array('type' => self::TYPE_STRING, 'required' => true),
        ),
    );

    public function __construct($id_market = null, $id_lang = null, $id_shop = null)
    {
        $id_lang = ($id_lang==null) ? Configuration::get('PS_LANG_DEFAULT') : $id_lang;
        if(Tools::getValue('controller')=='AdminMarketSupplier' && $id_market && !Tools::getValue('submitAddmarket_supplier')) {
            $id_market = $this->getIdMarketFromIdMarketSupplierForBO($id_market);
        }
        
        $this->initSuppliers($id_market);
        parent::__construct($id_market, $id_lang, $id_shop);
        $this->image_dir        = _PS_MAR_IMG_DIR_;
        $this->id_shop          = $id_shop;
        $this->id_lang          = $id_lang;
    }
    
    /** 
     * 
     * GESTION BO 
     * 
     **/
    private function initSuppliers($id_market) {
        $ret = Db::getInstance()->executeS('SELECT id_supplier FROM ' . _DB_PREFIX_ . 'market_supplier WHERE id_market=' . (int) $id_market);
        foreach($ret as $value) {
            $this->{'supplier_' . $value['id_supplier']} = 1;
            $this->suppliers[] = $value['id_supplier'];
        }
    }
    
    public function add($autodate = true, $null_values = false)
    {
        if(Tools::getValue('controller')=='AdminMarket') {
            $ret = $this->addMarket();
        }elseif(Tools::getValue('controller')=='AdminMarketSupplier') {
            $ret = $this->addMarketSupplier();
        }

        return $ret;
    }
    
    private function addMarket() {
        $date = date("Y-m-d H:i:s");
        $this->getGeocode();
        $ret = Db::getInstance()->insert('market', array(array(
            'address'       =>  addslashes($this->address),
            'postal_code'   =>  $this->postal_code,
            'city'          =>  $this->city,
            'date_add'      =>  $date,
            'date_upd'      =>  $date,
            'active'        =>  $this->active,
            'open_at'       =>  $this->open_at,
            'close_at'      =>  $this->close_at,
            'lundi'         =>  $this->lundi,
            'mardi'         =>  $this->mardi,
            'mercredi'      =>  $this->mercredi,
            'jeudi'         =>  $this->jeudi,
            'vendredi'      =>  $this->vendredi,
            'samedi'        =>  $this->samedi,
            'dimanche'      =>  $this->dimanche,
            'latitude'      =>  $this->latitude,
            'longitude'     =>  $this->longitude,
            )));
        $this->id_market = (int) Db::getInstance()->Insert_ID();
        $this->id = $this->id_market;
        
        $ret &= Db::getInstance()->insert('market_lang', array(array('name'=>addslashes($this->name), 'description'=>addslashes($this->description), 'link_rewrite'=>$this->link_rewrite, 'meta_title'=>$this->meta_title, 'meta_description'=>$this->meta_description, 'meta_keywords'=>$this->meta_keywords, 'id_shop'=>1, 'id_lang'=>(int)$this->id_lang)));

        return true;
    }

    public function update($null_values = false)
    {
        if(Tools::getValue('controller')=='AdminMarket') {
            $ret = $this->updateMarket();
        }elseif(Tools::getValue('controller')=='AdminMarketSupplier') {
            $ret = $this->addMarketSupplier();
        }

        return $ret;
    }

    private function updateMarket() {
        $date = date("Y-m-d H:i:s");
        $this->getGeocode();
        $ret = Db::getInstance()->update('market', array(
            'address'       =>  addslashes($this->address), 
            'postal_code'   =>  $this->postal_code, 
            'city'          =>  $this->city, 
            'date_upd'      =>  $date, 
            'active'        =>  $this->active, 
            'open_at'       =>  $this->open_at,
            'close_at'      =>  $this->close_at,
            'lundi'         =>  $this->lundi,
            'mardi'         =>  $this->mardi,
            'mercredi'      =>  $this->mercredi,
            'jeudi'         =>  $this->jeudi,
            'vendredi'      =>  $this->vendredi,
            'samedi'        =>  $this->samedi,
            'dimanche'      =>  $this->dimanche,
            'latitude'      =>  $this->latitude,
            'longitude'     =>  $this->longitude,
            ), '`id_market` = '.(int)$this->id_market);
        
        if(!empty($this->name)) {
            $ret &= Db::getInstance()->update('market_lang', array('name'=>addslashes($this->name), 'description'=>addslashes($this->description), 'link_rewrite'=>$this->link_rewrite, 'meta_title'=>$this->meta_title, 'meta_description'=>$this->meta_description, 'meta_keywords'=>$this->meta_keywords, 'id_shop'=>1, 'id_lang'=>(int)$this->id_lang), '`id_market` = '.(int)$this->id_market);
        }
        
        return $ret;
    }
    //calcul de la distance 3D conçue par partir-en-vtt.com
    function distance($lat1, $lon1, $lat2, $lon2, $alt1, $alt2) 
    {
            //rayon de la terre
            $r = 6366;
            $lat1 = deg2rad($lat1);
            $lat2 = deg2rad($lat2);
            $lon1 = deg2rad($lon1);
            $lon2 = deg2rad($lon2);

            //recuperation altitude en km
            $alt1 = $alt1/1000;
            $alt2 = $alt2/1000;

            //calcul précis
            $dp= 2 * asin(sqrt(pow (sin(($lat1-$lat2)/2) , 2) + cos($lat1)*cos($lat2)* pow( sin(($lon1-$lon2)/2) , 2)));

            //sortie en km
            $d = $dp * $r;

            //Pythagore a dit que :
             $h = sqrt(pow($d,2)+pow($alt2-$alt1,2));

            return $h;
    }
    
    private function getGeocode() {
        $geocoder = "http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false";
        
        $adresse = $this->address;
        $adresse .= ', '.$this->postal_code;
        $adresse .= ', '.$this->city;

        // Requête envoyée à l'API Geocoding
        $query = sprintf($geocoder, urlencode(utf8_encode($adresse)));

        $result = json_decode(file_get_contents($query));
        $json = $result->results[0];

        $this->latitude = (string) $json->geometry->location->lat;
        $this->longitude = (string) $json->geometry->location->lng;
    }

    private function addMarketSupplier() {
        
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
    
    public static function getListMarketForBO() {
        $sql = 'SELECT m.id_market, CONCAT(city, " - ", name) as name FROM '._DB_PREFIX_. 'market m'
                . ' INNER JOIN '._DB_PREFIX_. 'market_lang ml ON m.id_market=ml.id_market'
                . ' WHERE ml.id_lang=' . Context::getContext()->language->id . ''
                . ' ORDER BY city';
        return Db::getInstance()->executeS($sql);
    }
    
    private function getIdMarketFromIdMarketSupplierForBO($idMarketSupplier) {
        $res = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_  .'market_supplier WHERE id_market_supplier=' . (int) $idMarketSupplier);
        return $res[0]['id_market'];
    }
    
    /** 
     * 
     * GESTION FRONT
     * 
     **/
    
    /** V2 **/
    /**
     * Renvoie la liste des marchés présent dans un departement via $numDep
     * @param int $numDep
     * 
     * @return $marketDto[]
     */
    public function getMarket() {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'market m WHERE m.active=1'; 
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDtoV2($result, true);
    }

    /** V1 **/
    public function getAllMarkets($fields = 'DISTINCT m.city, m.postal_code', $orderBy = ' ORDER BY city', $addCats=true, $addProducts=true, $addSuppliers=true, $loc='aquitaine') {
        $sql = 'SELECT ' . $fields . ' FROM ' . _DB_PREFIX_ . 'market m '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_lang ml ON ml.id_market=m.id_market ';
        if($loc==DepartementLivraison::AQUITAINE) {
            $sql .= 'AND ml.meta_keywords IN (\'' . DepartementLivraison::AQUITAINE . '\', \'\') ';
        } else {
            $sql .= 'AND ml.meta_keywords=\'' . DepartementLivraison::IDF . '\' ';
        }
        $sql .= 'WHERE m.active=1 AND ml.id_lang='  . (int) Context::getContext()->language->id . ' ' . $orderBy;
        
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true, $addCats, $addProducts, $addSuppliers);
    }
    /**
     * 
     * @param int $idSupplier
     * @return MarketDto[]
     */
    public function getMarketFromIdSupplier($idSupplier) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'market m '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_lang ml ON ml.id_market=m.id_market '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_market=m.id_market '
                . 'WHERE ms.id_supplier=' . (int) $idSupplier . ' AND m.active=1 AND ml.id_lang=' . (int) Context::getContext()->language->id;
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true, false, false, false);
    }
    /**
     * 
     * @param array $listIdSupplier
     * @return MarketDto[]
     */
    public function getListMarketFromListSupplier($listIdSupplier) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'market m '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_lang ml ON ml.id_market=m.id_market '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_market=m.id_market '
                . 'WHERE ms.id_supplier IN (' . implode(',', $listIdSupplier) . ') AND m.active=1 AND ml.id_lang=' . (int) Context::getContext()->language->id;
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true, false, false, false);
    }
    /**
     * 
     * @param int $idMarket
     * @return MarketDto
     */
    public function getMarketById($idMarket) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'market m '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_lang ml ON ml.id_market=m.id_market '
                . 'WHERE m.id_market=' . (int) $idMarket . ' AND m.active=1';
        
        $result = Db::getInstance()->getRow($sql);

        return $this->prepareDtoV2($result);
    }
    /**
     * 
     * @param int $idMarket
     * @param bool $addCats
     * @param bool $addProducts
     * @param bool $addSuppliers
     * @return MarketDto
     * @throws Exception
     */
    public function getMarketFromId($idMarket, $addCats=true, $addProducts=true, $addSuppliers=true, $active=true, $loc=DepartementLivraison::AQUITAINE) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'market m '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_lang ml ON ml.id_market=m.id_market '
                . 'WHERE m.id_market=' . (int) $idMarket;
        if($active) {
            $sql .= ' AND m.active=1';
        }
        $result = Db::getInstance()->getRow($sql);

        return $this->prepareDto($result, false, $addCats, $addProducts, $addSuppliers, array(), array(), array(), $loc);
    }
    /**
     * 
     * @param int $idMarket
     * @param int $idSupplier
     * @param int $idCategory
     * @param bool $addCats
     * @param bool $addProducts
     * @param bool $addSuppliers
     * @return MarketDto
     * @throws Exception
     */
    public function getMarketFromIdAndIdSupplierAndIdCategory($idMarket, $listIdSupplier, $listIdCategory, $addCats=true, $addProducts=true, $addSuppliers=true) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'market m '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_lang ml ON ml.id_market=m.id_market '
                . 'WHERE m.id_market=' . (int) $idMarket . ' AND m.active=1';
        $result = Db::getInstance()->getRow($sql);
        return $this->prepareDto($result, false, $addCats, $addProducts, $addSuppliers, $listIdCategory, array(), $listIdSupplier);
    }
    
    /**
     * @param int $idMarket
     * @param array $optionsCat
     * @param array $optionsProd
     * @param array $optionsSup
     * @return array
     */
    private function getListIdProducts($idMarket, $optionsCat = array(), $optionsProd = array(), $optionsSup = array()) {
        $sql = 'SELECT p.id_product FROM ' . _DB_PREFIX_ . 'market m '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_market=m.id_market '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier s ON s.id_supplier=ms.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_supplier=ms.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs ON cs.id_category=p.id_category_default '
                . 'WHERE p.active=1 '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' ';
        if(!empty($optionsCat)) {
            $sql .= ' AND cs.id_category IN (' . implode(',', $optionsCat) . ') ';
        }
        if(!empty($optionsProd)) {
            $sql .= ' AND p.id_product IN (' . implode(',', $optionsProd) . ') ';
        }
        if(!empty($optionsSup)) {
            $sql .= ' AND s.id_supplier IN (' . implode(',', $optionsSup) . ') ';
        }
        $sql .= 'AND m.id_market=' . (int) $idMarket . ' ORDER BY cs.position, s.id_supplier, p.owner_product DESC, pl.name, p.id_product';
        $result = Db::getInstance()->executeS($sql);
        $list = array();
        foreach($result as $row) {
            $list[] = $row['id_product'];
        }
        
        return $list;
    }
    
    public function getNbMarketFromDayAndSupplier($day, $listSupplierCategory) {
        $sql = 'SELECT count(*) as nbMarket FROM ' . _DB_PREFIX_ . 'market m '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_lang ml ON ml.id_market=m.id_market '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_market=m.id_market '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier s ON s.id_supplier=ms.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_supplier=ms.id_supplier '
                . 'WHERE p.id_category_default IN (' . implode(',', $listSupplierCategory) . ') AND m.' . $day . '=1 '
                . 'AND m.active=1 AND s.active=1 AND ml.id_lang=' . (int) Context::getContext()->language->id .' '
                . 'GROUP BY m.id_market';
        $result = Db::getInstance()->executeS($sql);
        return $result[0]['nbMarket'];
    }
    
    /**
     * 
     * @param string $day
     * @param array $listSupplierCategory
     * 
     * @return MarketDto[]
     */
    public function getMarketFromDayAndSupplier($day, $listSupplierCategory) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'market m '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_lang ml ON ml.id_market=m.id_market '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_market=m.id_market '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier s ON s.id_supplier=ms.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_supplier=ms.id_supplier '
                . 'WHERE p.id_category_default IN (' . implode(',', $listSupplierCategory) . ') AND m.' . $day . '=1 '
                . 'AND m.active=1 AND s.active=1 AND ml.id_lang=' . (int) Context::getContext()->language->id .' '
                . 'GROUP BY m.id_market';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true, false, false, false);
    }
    
    /**
     * 
     * @param string $day
     * 
     * @return MarketDto[]
     */
    public function getListMarketFromDay($day, $loc='aquitaine') {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'market m '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_lang ml ON ml.id_market=m.id_market '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_market=m.id_market '
                . 'WHERE m.' . $day . '=1 '
                . 'AND m.active=1 AND ml.id_lang=' . (int) Context::getContext()->language->id .' ';
        if($loc==DepartementLivraison::AQUITAINE) {
            $sql .= 'AND ml.meta_keywords IN (\'' . DepartementLivraison::AQUITAINE . '\', \'\') ';
        } else {
            $sql .= 'AND ml.meta_keywords=\'' . DepartementLivraison::IDF . '\' ';
        }
        $sql .= 'GROUP BY m.id_market';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true, false, false, false);
    }
    
    private function prepareDtoV2($result, $multipleArray = false)
    {
        if($multipleArray) {
            $listObj = null;
            foreach($result as $key=>$row) {
                $obj = $this->createDtoV2($row);
                $listObj[$key] = $obj;
            }
            return $listObj;
        } else {
            return $this->createDtoV2($result);
        }
    }
    
    private function prepareDto($result, $multipleArray = false, $addCats=true, $addProducts=true, $addSuppliers=true, $optionsCat = array(), $optionsProd = array(), $optionsSup=array(), $loc=DepartementLivraison::AQUITAINE)
    {
        if($multipleArray) {
            $listObj = null;
            foreach($result as $key=>$row) {
                $obj = $this->createDto($row, $addCats, $addProducts, $addSuppliers, $optionsCat, $optionsProd, $optionsSup, $loc);
                $listObj[$key] = $obj;
            }
            return $listObj;
        } else {
            return $this->createDto($result, $addCats, $addProducts, $addSuppliers, $optionsCat, $optionsProd, $optionsSup, $loc);
        }
    }
    
    private function getIntervalForSupplier(DateTime $date) {
        $now = new DateTime();
        $interval = $now->diff($date);
        
        return $interval->format('%a');
    }
    
    private function addSupplierToDto(MarketDto $marketDto, $options = array(), $loc=DepartementLivraison::AQUITAINE) {
        $supplier = new Supplier();
        $listSupplier = $supplier->getAllByIdMarket($marketDto->getIdMarket(), $this->getIntervalForSupplier($marketDto->getNextDateOpen()), $options, $loc);
        foreach($listSupplier as $supplierDto) {
            $marketDto->addSupplier($supplierDto->getIdSupplier(), $supplierDto);
        }
    }
    
    private function addCatToDto(MarketDto $marketDto, $options = array()) {
        $category = new Category();
        $marketDto->setListCat($category->getCatFromIdMarket($marketDto->getIdMarket(), $options));
    }
    
    private function addProductToDto(MarketDto $marketDto, $optionsCat = array(), $optionsProd = array(), $optionsSup=array()) {
        $product = new Product();
        $listId = $this->getListIdProducts($marketDto->getIdMarket(), $optionsCat, $optionsProd, $optionsSup);
            
        foreach($listId as $idProduct) {
            $productDto = $product->getById($idProduct);
            if($productDto instanceof ProductDto) {
                $marketDto->addProduct($productDto);
            }
        }
    }

    private function createDto($result, $addCats=true, $addProducts=true, $addSuppliers=true, $optionsCat = array(), $optionsProd = array(), $optionsSup=array(), $loc=DepartementLivraison::AQUITAINE)
    {
        if($result) {
            $market = new MarketDto();
            $market->setActive(isset($result['active']) ? $result['active'] : 0);
            $market->setAddress(isset($result['address']) ? $result['address'] : 0);
            $market->setCity(isset($result['city']) ? $result['city'] : 0);
            $market->setCloseAt(isset($result['close_at']) ? $result['close_at'] : 0);
            $market->setDateAdd(isset($result['date_add']) ? $result['date_add'] : 0);
            $market->setDateUpd(isset($result['date_upd']) ? $result['date_upd'] : 0);
            $market->setDescription(isset($result['description']) ? $result['description'] : 0);
            $market->setDimanche(isset($result['dimanche']) ? $result['dimanche'] : 0);
            $market->setIdLang(isset($result['id_lang']) ? $result['id_lang'] : 0);
            $market->setIdMarket(isset($result['id_market']) ? $result['id_market'] : 0);
            $market->setIdShop(isset($result['id_shop']) ? $result['id_shop'] : 0);
            $market->setJeudi(isset($result['jeudi']) ? $result['jeudi'] : 0);
            $market->setLatitude(isset($result['latitude']) ? $result['latitude'] : 0);
            $market->setLinkRewrite(isset($result['link_rewrite']) ? $result['link_rewrite'] : 0);
            $market->setLongitude(isset($result['longitude']) ? $result['longitude'] : 0);
            $market->setLundi(isset($result['lundi']) ? $result['lundi'] : 0);
            $market->setMardi(isset($result['mardi']) ? $result['mardi'] : 0);
            $market->setMercredi(isset($result['mercredi']) ? $result['mercredi'] : 0);
            $market->setMetaDescription(isset($result['meta_description']) ? $result['meta_description'] : 0);
            $market->setMetaKeywords(isset($result['meta_keywords']) ? $result['meta_keywords'] : 0);
            $market->setMetaTitle(isset($result['meta_title']) ? $result['meta_title'] : 0);
            $market->setName(isset($result['name']) ? $result['name'] : 0);
            $market->setOpenAt(isset($result['open_at']) ? $result['open_at'] : 0);
            $market->setPostalCode(isset($result['postal_code']) ? $result['postal_code'] : 0);
            $market->setSamedi(isset($result['samedi']) ? $result['samedi'] : 0);
            $market->setVendredi(isset($result['vendredi']) ? $result['vendredi'] : 0);
            if($addCats) {
                $this->addCatToDto($market, $optionsCat);
            }
            if($addProducts) {
                $this->addProductToDto($market, $optionsCat, $optionsProd, $optionsSup);
            }
            if($addSuppliers) {
                $this->addSupplierToDto($market, $optionsSup, $loc);
            }
            return $market;
        }
        return null;
    }
    
    private function createDtoV2($result)
    {
        if($result) {
            $market = new MarketDto();
            $market->setActive(isset($result['active']) ? $result['active'] : 0);
            $market->setAddress(isset($result['address']) ? $result['address'] : 0);
            $market->setCity(isset($result['city']) ? $result['city'] : 0);
            $market->setCloseAt(isset($result['close_at']) ? $result['close_at'] : 0);
            $market->setDateAdd(isset($result['date_add']) ? $result['date_add'] : 0);
            $market->setDateUpd(isset($result['date_upd']) ? $result['date_upd'] : 0);
            $market->setDescription(isset($result['description']) ? $result['description'] : 0);
            $market->setDimanche(isset($result['dimanche']) ? $result['dimanche'] : 0);
            $market->setIdLang(isset($result['id_lang']) ? $result['id_lang'] : 0);
            $market->setIdMarket(isset($result['id_market']) ? $result['id_market'] : 0);
            $market->setIdShop(isset($result['id_shop']) ? $result['id_shop'] : 0);
            $market->setJeudi(isset($result['jeudi']) ? $result['jeudi'] : 0);
            $market->setLatitude(isset($result['latitude']) ? $result['latitude'] : 0);
            $market->setLinkRewrite(isset($result['link_rewrite']) ? $result['link_rewrite'] : 0);
            $market->setLongitude(isset($result['longitude']) ? $result['longitude'] : 0);
            $market->setLundi(isset($result['lundi']) ? $result['lundi'] : 0);
            $market->setMardi(isset($result['mardi']) ? $result['mardi'] : 0);
            $market->setMercredi(isset($result['mercredi']) ? $result['mercredi'] : 0);
            $market->setMetaDescription(isset($result['meta_description']) ? $result['meta_description'] : 0);
            $market->setMetaKeywords(isset($result['meta_keywords']) ? $result['meta_keywords'] : 0);
            $market->setMetaTitle(isset($result['meta_title']) ? $result['meta_title'] : 0);
            $market->setName(isset($result['name']) ? $result['name'] : 0);
            $market->setOpenAt(isset($result['open_at']) ? $result['open_at'] : 0);
            $market->setPostalCode(isset($result['postal_code']) ? $result['postal_code'] : 0);
            $market->setSamedi(isset($result['samedi']) ? $result['samedi'] : 0);
            $market->setVendredi(isset($result['vendredi']) ? $result['vendredi'] : 0);
            return $market;
        }
        return null;
    }
    
}
