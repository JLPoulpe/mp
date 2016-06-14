<?php
class AdminMarketControllerCore extends AdminController
{
	/**
	 *  @var object Market() instance for navigation
	 */
	protected $_market = null;
	protected $position_identifier = 'id_market';

	private $original_filter = '';

	public function __construct()
	{
		$this->bootstrap = true;
		$this->table = 'market';
		$this->className = 'Market';
		$this->lang = true;
		$this->deleted = false;
		$this->explicitSelect = true;
		$this->_defaultOrderBy = 'id_market';
		$this->allow_export = true;

		$this->context = Context::getContext();
        $this->fieldImageSettings = array(
 			'name' => 'image',
 			'dir' => 'ma'
 		);

		$this->fields_list = array(
			'id_market' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'city' => array(
				'title' => $this->l('City'),
				'orderby' => true
			),
			'name' => array(
				'title' => $this->l('Name')
			),
			'active' => array(
				'title' => $this->l('Displayed'),
				'active' => 'status',
				'type' => 'bool',
				'class' => 'fixed-width-xs',
				'align' => 'center',
				'orderby' => false
			)
		);

		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'icon' => 'icon-trash',
				'confirm' => $this->l('Delete selected items?')
			)
		);
		$this->specificConfirmDelete = false;

		parent::__construct();
	}

	public function init()
	{
		parent::init();

		// context->shop is set in the init() function, so we move the _category instanciation after that
		if (($id_market = Tools::getvalue('id_market')) && $this->action != 'select_delete')
                    $this->_market = new Market($id_market, $this->lang);
	}

	public function renderList()
	{
		if (isset($this->_filter) && trim($this->_filter) == '')
			$this->_filter = $this->original_filter;

		$this->addRowAction('add');
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		return parent::renderList();
	}

	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
        parent::getList($id_lang, $order_by, $order_way, $start, $limit);
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
    
    public function initInput($image_url, $image_size) {
        $ret = array(
				array(
					'type' => 'text',
					'label' => $this->l('Name'),
					'name' => 'name',
					'required' => true,
					'class' => 'copy2friendlyUrl',
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Short Name'),
					'name' => 'link_rewrite',
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Displayed'),
					'name' => 'active',
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
				),
				array(
					'type' => 'text',
					'label' => $this->l('Address'),
					'name' => 'address',
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Postal Code'),
					'name' => 'postal_code',
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
				array(
					'type' => 'text',
					'label' => $this->l('City'),
					'name' => 'city',
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description'),
					'name' => 'description',
					'autoload_rte' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'file',
					'label' => $this->l('Image'),
					'name' => 'image',
					'display_image' => true,
					'required' => true,
					'image' => $image_url ? $image_url : false,
					'size' => $image_size,
					'hint' => $this->l('Upload a market logo from your computer.'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta title'),
					'name' => 'meta_title',
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta description'),
					'name' => 'meta_description',
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'tags',
					'label' => $this->l('Meta keywords'),
					'name' => 'meta_keywords',
					'hint' => $this->l('To add "tags," click in the field, write something, and then press "Enter."').'&nbsp;'.$this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'select',
					'label' => $this->l('Open at :'),
					'name' => 'open_at',
					'required' => true,
                    'options' => array(
                        'query' => array(
                                    array('id'=>'0600', 'name'=>'6h00'), 
                                    array('id'=>'0630', 'name'=>'6h30'), 
                                    array('id'=>'0700', 'name'=>'7h00'), 
                                    array('id'=>'0730', 'name'=>'7h30'), 
                                    array('id'=>'0800', 'name'=>'8h00'),
                                    array('id'=>'0830', 'name'=>'8h30'),
                                    array('id'=>'0900', 'name'=>'9h00'),
                                    array('id'=>'1700', 'name'=>'17h00'),
                                    ),
                        'id'    => 'id',
                        'name'  => 'name'
                    ),
					'hint' => $this->l('Choose the hour when market is open.')
				),
                array(
					'type' => 'select',
					'label' => $this->l('Close at :'),
					'name' => 'close_at',
					'required' => true,
                    'options' => array(
                        'query' => array(
                                    array('id'=>'1100', 'name'=>'11h00'), 
                                    array('id'=>'1130', 'name'=>'11h30'), 
                                    array('id'=>'1200', 'name'=>'12h00'), 
                                    array('id'=>'1230', 'name'=>'12h30'), 
                                    array('id'=>'1300', 'name'=>'13h00'),
                                    array('id'=>'1330', 'name'=>'13h30'),
                                    array('id'=>'1400', 'name'=>'14h00'),
                                    array('id'=>'2100', 'name'=>'21h00'),
                                    ),
                        'id'    => 'id',
                        'name'  => 'name'
                    ),
					'hint' => $this->l('Choose the hour when market is close.')
				)
			);
        $listeJour = array('Monday'=>'lundi', 'Tuesday'=>'mardi', 'Wednesday'=>'mercredi', 'Thursday'=>'jeudi', 'Friday'=>'vendredi', 'Saturday'=>'samedi', 'Sunday'=>'dimanche');
        foreach($listeJour as $day=>$jour) {
            $tab = array(
					'type' => 'switch',
					'label' => $this->l($day),
					'name' => $jour,
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
        
        return $ret;
    }
    
	public function renderForm()
	{
		$this->initToolbar();

		if (!($obj = $this->loadObject(true)))
			return;
        
        $image = _PS_MAR_IMG_DIR_.$obj->id.'.jpg';
		$image_url = ImageManager::thumbnail($image, $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350,
			$this->imageType, true, true);
		$image_size = file_exists($image) ? filesize($image) / 1000 : false;
        
        $input = $this->initInput($image_url, $image_size);
        
		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Market'),
				'icon' => 'icon-tags'
			),
			'input' => $input,
			'submit' => array(
				'title' => $this->l('Save'),
				'name' => 'submitAdd'.$this->table.(!Tools::isSubmit('add'.$this->table) && !Tools::isSubmit('add'.$this->table.'root') ? '': 'AndBackToParent')
			)
		);
        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
		
		$image = ImageManager::thumbnail(_PS_MAR_IMG_DIR_.'/'.$obj->id.'.jpg', $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350, $this->imageType, true);

		$this->fields_value = array(
			'image' => $image ? $image : false,
			'size' => $image ? filesize(_PS_MAR_IMG_DIR_.'/'.$obj->id.'.jpg') / 1000 : false
		);

		return parent::renderForm();
	}

	public function postProcess()
	{
        if (!in_array($this->display, array('edit', 'add')))
            $this->multishop_context_group = false;
        if (Tools::isSubmit('forcedeleteImage') || (isset($_FILES['image']) && $_FILES['image']['size'] > 0) || Tools::getValue('deleteImage'))
        {
            $this->processForceDeleteImage();
            if (Tools::isSubmit('forcedeleteImage'))
                Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminCategories').'&conf=7');
        }

        return parent::postProcess();
	}

	public function processAdd()
	{
        // test si le nom du marché existe déjà
        $name = Tools::getValue('name');
        $object = parent::processAdd();
        if(is_object($object)) {
            $_POST['id_market'] = $object->id_market;
        }
        return $object;
	}

	public function processForceDeleteImage()
	{
		$market = $this->loadObject(true);
		if (Validate::isLoadedObject($market))
			$market->deleteImage(true);
	}

    public function initPageHeaderToolbar()
	{
		parent::initPageHeaderToolbar();

		if ($this->display != 'edit' && $this->display != 'add')
		{
			$this->page_header_toolbar_btn['new_market'] = array(
				'href' => self::$currentIndex.'&addmarket&token='.$this->token,
				'desc' => $this->l('Add new market', null, null, false),
				'icon' => 'process-icon-new'
			);
		}
	}

	public function initContent()
	{
		if ($this->action == 'select_delete')
			$this->context->smarty->assign(array(
				'delete_form' => true,
				'url_delete' => htmlentities($_SERVER['REQUEST_URI']),
				'boxes' => $this->boxes,
			));

		parent::initContent();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
	}

	public function renderView()
	{
            $this->initToolbar();
            return $this->renderList();
	}

    protected function postImage($id)
    {
        $ret = parent::postImage($id);
        if (($id_market = (int)Tools::getValue('id_market')) &&
            isset($_FILES) && count($_FILES) && $_FILES['image']['name'] != null &&
            file_exists(_PS_MAR_IMG_DIR_.$id_market.'.jpg'))
        {
            $images_types = ImageType::getImagesTypes('categories');
            foreach ($images_types as $k => $image_type)
            {
                ImageManager::resize(
                    _PS_MAR_IMG_DIR_.$id_market.'.jpg',
                    _PS_MAR_IMG_DIR_.$id_market.'-'.stripslashes($image_type['name']).'.jpg',
                    (int)$image_type['width'], (int)$image_type['height']
                );
            }
        }

        return $ret;
	}

	public static function getDescriptionClean($description)
	{
		return Tools::getDescriptionClean($description);
	}
}
