<?php
class mpformformModuleFrontController extends ModuleFrontController
{
    private $action;
    
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

    public function nousrejoindre() {
        $err = array();
        if(Tools::getIsset('form') && Tools::getValue('form')=='sent') {
            if(Tools::getIsset('nom')) {
                $nom = Tools::getValue('nom');
                if(!MPTools::isName($nom)) {
                    $nom='';
                    $err['nom'] = 'invalide';
                }
            } else {
                $err['nom'] = 'needed';
            }
            if(Tools::getIsset('prenom')) {
                $prenom = Tools::getValue('prenom');
                if(!MPTools::isName($prenom)) {
                    $prenom='';
                    $err['prenom'] = 'invalide';
                }
            } else {
                $err['prenom'] = 'needed';
            }
            if(Tools::getIsset('tel')) {
                $tel = Tools::getValue('tel');
                if(!MPTools::isTel($tel)) {
                    $tel = '';
                    $err['tel'] = 'invalide';
                }
            } else {
                $err['tel'] = 'needed';
            }
            if(Tools::getIsset('email')) {
                $email = Tools::getValue('email');
                if(!MPTools::isEmail($email)) {
                    $email='';
                    $err['email'] = 'invalide';
                }
            } else {
                $err['email'] = 'needed';
            }
            
            $ok = '';
            if(empty($err)) {
                $newSupplier = new NewSupplier();
                $ok = $newSupplier->add($email, $prenom, $nom, $tel);
            }
            
            $this->context->smarty->assign(
               array(
                   'err' => $err,
                   'nom'    => $nom,
                   'prenom' => $prenom,
                   'tel'    => $tel,
                   'email'  => $email,
                   'ok'     => $ok,
               )
            );
        }
        $this->setTemplate('nousrejoindre.tpl');
    }
}
