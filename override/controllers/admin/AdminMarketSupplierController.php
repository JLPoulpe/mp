<?php
class AdminMarketSupplierControllerCore extends AdminController
{
	public $bootstrap = true ;

	public function __construct()
	{
            $this->table = 'market_supplier';
            $this->className = 'Market';

            $this->addRowAction('edit');
            $this->addRowAction('delete');
            $this->allow_export = true;

            $this->_defaultOrderBy = 'city';
            $this->_defaultOrderWay = 'ASC';

            $this->bulk_actions = array(
                'delete' => array(
                    'text' => $this->l('Delete selected'),
                    'icon' => 'icon-trash',
                    'confirm' => $this->l('Delete selected items?')
                )
            );

            $this->_select = 'm.*, ml.name, COUNT(DISTINCT a.`id_supplier`) AS suppliers';
            $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'market` m ON (a.`id_market` = m.`id_market`) '
            . 'LEFT JOIN `'._DB_PREFIX_.'market_lang` ml ON (a.`id_market` = ml.`id_market`)';
            $this->_group = 'GROUP BY a.`id_market`';

            $this->fieldImageSettings = array('name' => 'logo', 'dir' => 'su');

            $this->fields_list = array(
                'id_market_supplier' => array(
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs'
                ),
                'city' => array(
                    'title' => $this->l('Ville'),
                    'orderby' => true
                ),
                'name' => array(
                    'title' => $this->l('Nom')
                ),
                'active' => array(
                    'title' => $this->l('Activé'),
                    'active' => 'status',
                    'type' => 'bool',
                    'class' => 'fixed-width-xs',
                    'align' => 'center',
                    'orderby' => false
                ),
                'suppliers' => array(
                    'title' => $this->l('Nombre de producteurs'),
                    'orderby' => false
                )
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
				'title' => $this->l('Liaison entre un marché et un producteur'),
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
        $list = array();
        if($market->suppliers) {
            foreach($market->suppliers as $value)
            {
                $list[] = $value;
            }
        }
        
        $ret = array(
				array(
					'type' => 'hidden',
					'name' => 'id_market',
				),
				array(
					'type'      => 'select',
					'label'     => $this->l('Marché'),
					'name'      => 'id_market',
					'required'  => true,
                    'options'   => array(
						'query'     => Market::getListMarketForBO(),
						'id'        => 'id_market',
						'name'      => 'name',
					),
				),
                array(
					'type'          => 'select',
					'label'         => $this->l('Producteur à ajouter'),
					'name'          => 'id_supplier',
                	//'multiple'  => true,
					'options'       => array(
						'query'         => Supplier::getSuppliersForForm($list),
                        'id'            => 'id_supplier',
						'name'          => 'name',
					),
				),
            );
        
        $condition = '';
        if(count($list)) {
            $condition =  ' WHERE id_supplier IN (' . implode(',', $list) . ')';
            $listSupplier = Db::getInstance()->executeS('SELECT id_supplier, name FROM ' . _DB_PREFIX_ .'supplier' . $condition);
        
            foreach($listSupplier as $supplier)
            {
                $tab = array(
                    'type' => 'switch',
                    'label' => $this->l($supplier['name']),
                    'name' => 'supplier_' . $supplier['id_supplier'],
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                );
                array_push($ret, $tab);
            }
        }
        
        return $ret;
    }
    
    public function initPageHeaderToolbar()
	{
		if (empty($this->display))
			$this->page_header_toolbar_btn['new_market_supplier_link'] = array(
				'href' => self::$currentIndex.'&addmarket_supplier&token='.$this->token,
				'desc' => $this->l('Add new link', null, null, false),
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
