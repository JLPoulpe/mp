<?php
class AdminSupplierAccountControllerCore extends AdminController
{
	public $bootstrap = true ;

	public function __construct()
	{
		$this->table = 'supplier_account';
		$this->className = 'SupplierAccount';
        
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->allow_export = true;

		$this->_defaultOrderBy = 'name';
		$this->_defaultOrderWay = 'ASC';
		
		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'icon' => 'icon-trash',
				'confirm' => $this->l('Delete selected items?')
			)
		);
        
        $this->_select = 's.name, c.email';
		$this->_join = 'INNER JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = a.`id_supplier`) '
                . ' INNER JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_account`)';
		$this->_group = '';

		$this->fields_list = array(
			'id_supplier_account' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'name' => array(
				'title' => $this->l('Nom du producteur')
			),
            'email' => array(
				'title' => $this->l('Email')
			),
		);

		parent::__construct();
	}
    
    public function initProcess()
	{
            if (Tools::isSubmit('add'.$this->table.'root'))
            {
                if ($this->tabAccess['add'])
                {
                    $this->action = 'add'.$this->table.'root';
                    $obj = $this->loadObject(true);
                    if (Validate::isLoadedObject($obj))
                        $this->display = 'edit';
                    else
                        $this->display = 'add';
                }
                else
                    $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }

            parent::initProcess();
	}
    
    public function renderForm()
	{
        // loads current warehouse
		if (!($obj = $this->loadObject(true)))
			return;
        
        $this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Lier un compte client à un producteur'),
				'icon' => 'icon-truck'
			),
			'input' => $this->initInput($obj),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return parent::renderForm();
    }

    private function initInput($market)
    {
        $ret = array(
				array(
					'type' => 'hidden',
					'name' => 'id_supplier_account',
				),
				array(
					'type'      => 'select',
					'label'     => $this->l('Producteurs'),
					'name'      => 'id_supplier',
					'required'  => true,
                    'options'   => array(
						'query'     => Supplier::getListSupplierForBO(),
						'id'        => 'id_supplier',
						'name'      => 'name',
					),
				),
                array(
					'type'          => 'select',
					'label'         => $this->l('Compte client à lier'),
					'name'          => 'id_account',
					'options'       => array(
						'query'         => Customer::getCustomers(),
                        'id'            => 'id_customer',
						'name'          => 'email',
					),
				),
                array(
					'type'          => 'text',
					'label'         => $this->l('Code établissement'),
					'name'          => 'code_etablissement',
                    'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
                array(
					'type'          => 'text',
					'label'         => $this->l('Code guichet'),
					'name'          => 'code_guichet',
                    'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
                array(
					'type'          => 'text',
					'label'         => $this->l('Numéro compte'),
					'name'          => 'numero_compte',
                    'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
                array(
					'type'          => 'text',
					'label'         => $this->l('Clef RIB'),
					'name'          => 'cle_rib',
                    'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
                array(
					'type'          => 'text',
					'label'         => $this->l('IBAN'),
					'name'          => 'iban',
                    'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
                array(
					'type'          => 'text',
					'label'         => $this->l('Code BIC'),
					'name'          => 'code_bic',
                    'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
            );
        
        
        
        return $ret;
    }
    
    public function initPageHeaderToolbar()
	{
		if (empty($this->display))
			$this->page_header_toolbar_btn['new_supplier_account_link'] = array(
				'href' => self::$currentIndex.'&addsupplier_account&token='.$this->token,
				'desc' => 'Créer un compte Producteur',
				'icon' => 'process-icon-new'
			);

		parent::initPageHeaderToolbar();
	}
    
    public function renderView()
	{
            $this->initToolbar();
            return $this->renderList();
	}
    
    public function renderList()
	{
		return parent::renderList();
	}
}
