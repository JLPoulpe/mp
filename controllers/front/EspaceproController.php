<?php
class EspaceproController extends FrontController {
    public $php_self = 'espace-pro';
    private $method;
    public $supplierIds;
    
    public function init()
    {
        parent::init();
        if (!$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&back=order-follow');
        }
        $this->idUser = $this->context->customer->id;
        $supplier = new Supplier();
        $this->supplierIds = $supplier->getSupplierIdsByIdUser($this->idUser);

        if (empty($this->supplierIds)) {
            header('Location:/mon-compte');
        }
        
        $mod = Tools::getValue('mod');
        $this->method = !empty($mod) ? $mod : 'commande';       
        parent::initContent();
    }

    public function initContent() {
        parent::initContent();
        $assign = array(
                'menu'      => _PS_MODULE_DIR_ . 'mppaysans/views/templates/front/menu.tpl',
                'method'    => $this->method,
            );
        $infos = $this->{$this->method}();
        $tpl = $infos['tpl'];
        unset($infos['tpl']);
        $assigned = array_merge($assign, $infos);
        $this->context->smarty->assign(
            $assigned
        );
        return $this->setTemplate(_PS_MODULE_DIR_ . 'mppaysans/views/templates/front/' . $tpl . '.tpl');
    }
    
    /**
    * Set default medias for this controller
    * @see FrontController::setMedia()
    */
    public function setMedia()
    {
         parent::setMedia();
         $this->addCSS(_THEME_CSS_DIR_.'espacepro.css');
         $this->addJS(_THEME_JS_DIR_.'espacepro.js');
    }
    
    private function commande() {
        $cart = new Cart();
        $commandes = $cart->getCommandes(Order::PAIEMENT_ACCEPTE, $this->supplierIds);

        return array('commandes'=>$commandes, 'tpl'=>'commande');
    }

    private function validate() {
        $listProduitsok = Tools::getValue('produitok');
        $listProduitsko = Tools::getValue('produitko');
        $idCart = Tools::getValue('id_cart');
        $cart = new Cart();
        if(!empty($listProduitsko)) {
            $this->sendMail($listProduitsko, $idCart);
            $aProduits = explode(',', $listProduitsko[0]);
            foreach($aProduits as $idProduit) {
                if(!empty($idProduit)) {
                    $cart->updateStatus($idCart, $idProduit, Cart::PRODUIT_ANNULE);
                }
            }
        }
        if(!empty($listProduitsok)) {
            $aProduits = explode(',', $listProduitsok[0]);
            foreach($aProduits as $idProduit) {
                if(!empty($idProduit)) {
                    $cart->updateStatus($idCart, $idProduit, Cart::PRODUIT_EN_COURS_DE_PREPARATION);
                }
            }
        }
        
        return $this->commande();
    }
    
    private function sendMail($listProduitsko, $idCart) {
        $aProduits = explode(',', $listProduitsko[0]);
        if(!empty($aProduits)) {
            $product = new Product();
            $email = $this->context->customer->email;
            $lastname = $this->context->customer->lastname;
            $firstname = $this->context->customer->firstname;
            $content = '<table style="width:100%;">';
            foreach ($aProduits as $idProduit) {
                if(!empty($idProduit)) {
                    $productDto = $product->getById($idProduit);
                    $content .= '<tr><td>' . $productDto->getProductName() . '</td></tr>';
                }
            }
            $content .= '</table>';
            if(!empty($productDto)) {
                $customer = new Customer();
                $customerDto = $customer->getByIdCart($idCart);
                $templateVars = array(
                    'customerLN'    => $customerDto->getLastname(),
                    'customerFN'    => $customerDto->getFirstname(),
                    'supplierFN'    => $firstname,
                    'supplierLN'    => $lastname,
                    'content'       => $content,
                );
                $mail = new Mail();
                $mail->mpSendMailFile(Mail::EMAIL_CONTACT_MP, Mail::EMAILNAME_CONTACT_MP, 'Des produits sont manquants pour la commande de ' . $customerDto->getFirstname() . ' ' . strtoupper($customerDto->getLastname()), 'missingProduct.html', $templateVars, '');
            }
        }
        return null;
    }
    
    private function retrait() {
        $cart = new Cart();
        $listRef = $cart->getReferenceToValidateByIdSupplier($this->supplierIds);
        return array('listRef'=>$listRef, 'tpl'=>'retrait');
    }
    
    private function valideref() {
        $listRefToValidate = Tools::getValue('reference');
        $idCart = Tools::getValue('idCart');
        $cart = new Cart();
        $order = new Order();
        $listRef = $cart->getReferenceToValidateByIdSupplier($this->supplierIds);
        $tmp = array();
        foreach($listRef as $cartDto) {
            $tmp[] = $cartDto->getOrderReference();
        }
        $listReferenceOk = array_map(function($n, $m) {
            if(strtoupper($n)==substr($m,4)) {
                return $m;
            }
        }, $listRefToValidate, $tmp);
        
        foreach($listReferenceOk as $ref) {
            $cart->updateStatusFromReference($idCart, $ref, $this->supplierIds, Cart::PRODUIT_A_PAYER);
        }
        //$order->changeStatusByIdCart($idCart, Order::LIVRE);
        return $this->retrait();
    }

    private function banque() {
        $supplierAccount = new SupplierAccount();
        $supplierAccountDto = $supplierAccount->getById($this->supplierIds[0]);
        return array('supplierAccount'=>$supplierAccountDto, 'tpl'=>'banque', 'err'=>array());
    }
    
    private function validebanque() {
        $err = array();
        $supplierAccountDto = new SupplierAccountDto();
        $codeEtablissement = Tools::getValue('codeEtablissement');
        if(empty($codeEtablissement)) {
            $err['codeEtablissement'] = 'codeEtablissement';
        } else {
            $supplierAccountDto->setCodeEtablissement($codeEtablissement);
        }
        $codeGuichet = Tools::getValue('codeGuichet');
        if(empty($codeGuichet)) {
            $err['codeGuichet'] = 'codeGuichet';
        } else {
            $supplierAccountDto->setCodeGuichet($codeGuichet);
        }
        $numeroCompte = Tools::getValue('numeroCompte');
        if(empty($numeroCompte)) {
            $err['numeroCompte'] = 'numeroCompte';
        } else {
            $supplierAccountDto->setNumeroCompte($numeroCompte);
        }
        $clefRib = Tools::getValue('clefRib');
        if(empty($clefRib)) {
            $err['clefRib'] = 'clefRib';
        } else {
            $supplierAccountDto->setClefRib($clefRib);
        }
        $iban = Tools::getValue('iban');
        if(empty($iban)) {
            $err['iban'] = 'iban';
        } else {
            $supplierAccountDto->setIban($iban);
        }
        $codeBic = Tools::getValue('codeBic');
        if(empty($codeBic)) {
            $err['codeBic'] = 'codeBic';
        } else {
            $supplierAccountDto->setCodeBic($codeBic);
        }
        
        if(empty($err)) {
            $supplierAccount = new SupplierAccount();
            $supplierAccount->updateBanque($this->supplierIds[0], $codeEtablissement, $codeGuichet, $numeroCompte, $clefRib, $iban, $codeBic);
            $retour = array('valide'=>'ok', 'tpl'=>'banque', 'supplierAccount'=>$supplierAccountDto);
        } else {
            $retour = array('tpl'=>'banque', 'supplierAccount'=>$supplierAccountDto, 'err'=>$err);
        }
        
        return $retour;
    }
    
    public function history() {
        $idCustomer = $this->context->customer->id;
        $cart = new Cart();
        $listCart = $cart->getByIdCustomerForHistory($idCustomer);
        $detail = array();
        foreach($listCart as $key=>$cart) {
            $detail[$key]['productName'] = $cart['productName'];
            $detail[$key]['order'] = $cart['reference'];
            $unitPrice = $cart['unitPrice'];
            $attrPrice = $cart['attrPrice'];
            $priceToFormat = $unitPrice+$attrPrice;
            $price = $priceToFormat;
            $detail[$key]['price'] = number_format($price, 2, ',', ' ');
            $detail[$key]['totalPrice'] = number_format($price*(int)$cart['quantity'], 2, ',', ' ');
            $detail[$key]['quantity'] = $cart['quantity'];
            $date = new DateTime($cart['dateRetrait']);
            $detail[$key]['dateRetrait'] = $date->format('d/m/Y');
        }
        
        return array('tpl'=>'history', 'detail'=>$detail);
    }
}