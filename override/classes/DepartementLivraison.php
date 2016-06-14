<?php
class DepartementLivraison extends ObjectModel {
    const GIRONDE = 33;
    const VALDOISE = 95;
    const AQUITAINE = 'aquitaine';
    const IDF = 'idf';
    /**
    * @see ObjectModel::$definition
    */
    public static $definition = array(
        'table' => 'ville_livraison',
        'primary' => 'code_postal',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'code_postal'       =>  array('type' => self::TYPE_INT, 'required' => true, 'size' => 10),
            'name'              =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 30),
            'code_departement'  =>  array('type' => self::TYPE_INT, 'required' => true, 'size' => 10),
        ),
    );

    public function __construct($code_postal = null)
    {
        parent::__construct($code_postal);
    }
    
    /**
     * @param int $cp
     * @return DepartementLivraisonDto
     */
    function getVilleByCp($cp) {
        $sql = 'SELECT dl.name as ville, dl.code_postal, dl.code_departement FROM ' . _DB_PREFIX_ . 'ville_livraison dl '
                . 'WHERE dl.code_postal=' . (int) $cp;
        $result = Db::getInstance()->getRow($sql);
        return $this->prepareDto($result, false);
    }

    /**
     * @return DepartementLivraisonDto[]
     */
    function getAll() {
        $sql = 'SELECT dl.name as ville, dl.code_postal, dl.code_departement, r.name as departement, dl.longitude, dl.latitude FROM ' . _DB_PREFIX_ . 'ville_livraison dl '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'departement r ON r.id_departement=dl.code_departement '
                . 'ORDER BY code_postal';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }

    /**
     * @param int $codeDepartement
     * @return DepartementLivraisonDto[]
     */
    function getVillesByDepartement($codeDepartement) {
        $sql = 'SELECT dl.name as ville, dl.code_postal, dl.code_departement, r.name as departement FROM ' . _DB_PREFIX_ . 'ville_livraison dl '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'departement r ON r.id_departement=dl.code_departement '
                . 'WHERE dl.code_departement=' . (int) $codeDepartement . ' '
                . 'ORDER BY code_postal';
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
        $departement = null;
        if($result) {
            $departement = new DepartementLivraisonDto();
            $departement->setCodePostal(isset($result['code_postal']) ? $result['code_postal'] : 0);
            $departement->setVille(isset($result['ville']) ? $result['ville'] : '');
            $departement->setCodeDepartement(isset($result['code_departement']) ? $result['code_departement'] : 0);
            $departement->setDepartement(isset($result['departement']) ? $result['departement'] : '');
            $departement->setLatitude(isset($result['latitude']) ? $result['latitude'] : 0);
            $departement->setLongitude(isset($result['longitude']) ? $result['longitude'] : 0);
        }
        return $departement;
    }
}