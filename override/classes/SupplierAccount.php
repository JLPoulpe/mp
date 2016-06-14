<?php
class SupplierAccount extends ObjectModel
{
    public $id_supplier_account;
    public $id_supplier;
    public $id_account;
    public $code_etablissement;
    public $code_guichet;
    public $numero_compte;
    public $cle_rib;
    public $iban;
    public $code_bic;
    
    /**
    * @see ObjectModel::$definition
    */
    public static $definition = array(
        'table' => 'supplier_account',
        'primary' => 'id_supplier_account',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'code_etablissement'    =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 6),
            'code_guichet'          =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 6),
            'numero_compte'         =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 11),
            'cle_rib'               =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 2),
            'iban'                  =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 34),
            'code_bic'              =>  array('type' => self::TYPE_STRING, 'required' => true, 'size' => 11),
        ),
    );

    public function __construct($id_supplier_account = null)
    {
        parent::__construct($id_supplier_account);
    }
    
    /** 
     * 
     * GESTION BO 
     * 
     **/
    public function add($autodate = true, $null_values = false)
    {
        $ret = Db::getInstance()->insert('supplier_account', array(array(
            'id_supplier'           =>  $this->id_supplier, 
            'id_account'            =>  $this->id_account, 
            'code_etablissement'    =>  $this->code_etablissement, 
            'code_guichet'          =>  $this->code_guichet, 
            'numero_compte'         =>  $this->numero_compte, 
            'cle_rib'               =>  $this->cle_rib, 
            'iban'                  =>  $this->iban,
            'code_bic'              =>  $this->code_bic,
            )));
        return true;
    }
    
    public function update($null_values = false)
    {
        $ret = Db::getInstance()->update('supplier_account', array(
            'id_supplier'           =>  $this->id_supplier, 
            'id_account'            =>  $this->id_account, 
            'code_etablissement'    =>  $this->code_etablissement, 
            'code_guichet'          =>  $this->code_guichet, 
            'numero_compte'         =>  $this->numero_compte, 
            'cle_rib'               =>  $this->cle_rib, 
            'iban'                  =>  $this->iban,
            'code_bic'              =>  $this->code_bic,
            ), '`id_supplier_account` = '.(int)$this->id_supplier_account);

        return $ret;
    }
    
    /** 
     * 
     * GESTION FRONT
     * 
     **/
    
    /**
     * 
     * @param int $idSupplier
     * @return SupplierAccountDto
     */
    public function getById($idSupplier) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'supplier_account WHERE id_supplier=' . (int) $idSupplier;
        $result = Db::getInstance()->getRow($sql);
        return $this->prepareDto($result);
    }
    
    public function updateBanque($idSupplier, $codeEtablissement, $codeGuichet, $numeroCompte, $clefRib, $iban, $codeBic) {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'supplier_account SET code_etablissement=' . $codeEtablissement . ', code_guichet=' . $codeGuichet
                . ', numero_compte=' . $numeroCompte . ', cle_rib=' . $clefRib . ', iban=' . $iban . ', code_bic=' . $codeBic . ' WHERE id_supplier=' . $idSupplier;
        Db::getInstance()->execute($sql);
    }
    
    private function prepareDto($result, $multipleArray = false)
    {
        if($multipleArray) {
            $listObj = array();
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
        $supplierAccount = new SupplierAccountDto();
        $supplierAccount->setClefRib(isset($result['cle_rib']) ? $result['cle_rib'] : '');
        $supplierAccount->setCodeBic(isset($result['code_bic']) ? $result['code_bic'] : '');
        $supplierAccount->setCodeEtablissement(isset($result['code_etablissement']) ? $result['code_etablissement'] : '');
        $supplierAccount->setCodeGuichet(isset($result['code_guichet']) ? $result['code_guichet'] : '');
        $supplierAccount->setIban(isset($result['iban']) ? $result['iban'] : '');
        $supplierAccount->setIdAccount(isset($result['id_account']) ? $result['id_account'] : '');
        $supplierAccount->setIdSupplier(isset($result['id_supplier']) ? $result['id_supplier'] : '');
        $supplierAccount->setIdSupplierAccount(isset($result['id_supplier_account']) ? $result['id_supplier_account'] : '');
        $supplierAccount->setNumeroCompte(isset($result['numero_compte']) ? $result['numero_compte'] : '');

        return $supplierAccount;
    }
}
