<?php
class mpmarketmarketModuleFrontController extends ModuleFrontController
{
    public function __construct()
	{
		parent::__construct();
		$this->context  = Context::getContext();
        $this->action   = Tools::getValue('action');
    }

    public function initContent()
    {
        parent::initContent();
        $this->{$this->action}();
    }
}
