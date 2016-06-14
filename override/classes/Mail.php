<?php
class Mail extends MailCore {

    public function sendMailToSupplierForMarket($idSupplier, $marketname, $test=false) {
        $supplier = new Supplier();
        $info = $supplier->getById($idSupplier);
        $templateVars = array(
            'firstname'     => $info->getFirstname(),
            'lastname'      => $info->getLastname(),
            'marketname'    => $marketname,
        );
        $emailTo = $info->getEmail();
        if($test) {
            $emailTo = 'julien.ledieu@gmail.com';
        }
        $this->mpSendMail($info->getEmail(), $info->getFirstname() . ' ' . $info->getLastname(), 'Des commandes vous attendent', 'recap_producteur', $templateVars);
    }
    
    /*
     * firstname
     * lastname
     * market_name
     * order_ref
     * products
     */
    
    /**
     * @param string $to
     * @param string $toName
     * @param string $subject
     * @param string $template
     * @param array $templateVars
     * @param string $cc
     * @param string $bcc
     * @param string $from
     * @param string $fromName
     */
    public function mpSendMail($to, $toName, $subject, $template, $templateVars, $cc='', $bcc='', $from = 'mespaysans.com', $fromName= 'contact@mespaysans.com') {
        try {
            $themePath = _PS_ROOT_DIR_.'/themes/mespaysans/mail';
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= 'To: ' . $toName . ' <' . $to . '>' . "\r\n";
            $headers .= 'From: ' . $from . ' <' . $fromName  .'>' . "\r\n";
            if($cc) {
                $headers .= 'Cc: ' . $cc . "\r\n";
            }
            if($bcc) {
                $headers .= 'Bcc: ' . $bcc . "\r\n";
            }
            
            $content = file_get_contents($themePath . DIRECTORY_SEPARATOR . $template);
            
            foreach($templateVars as $key=>$var) {
                $content = str_replace('{' . $key . '}', $var, $content);
            }
            mail($to, $subject, $content, $headers);
        } catch(Exception $e) {
            d($e);
        }
    }
    
    /**
     * @param string $to
     * @param string $toName
     * @param string $subject
     * @param string $template
     * @param array $templateVars
     * @param string $cc
     * @param string $bcc
     * @param string $from
     * @param string $fromName
     * @param string $file
     */
    public function mpSendMailFile($to, $toName, $subject, $template, $templateVars, $file, $cc='', $bcc='', $from = 'contact@mespaysans.com', $fromName = 'mespaysans.com') {
        try {
            echo 'Envoi de mail à ' . $to . "\n";
            $themePath = _PS_ROOT_DIR_.'/themes/mespaysans/mail';
            $content = file_get_contents($themePath . DIRECTORY_SEPARATOR . $template);
            
            foreach($templateVars as $key=>$var) {
                $content = str_replace('{' . $key . '}', $var, $content);
            }
            $email = new PHPMailer();
            $email->isHTML(true);
            $email->From = $from;
            $email->FromName = $fromName;
            $email->Subject = $subject;
            $email->Body = $content;
            $email->addAddress($to, $toName);
            if(is_array($file)) {
                foreach($file as $fichier) {
                    $res = $email->addAttachment('./fichiers/' . $fichier, $fichier);
                }
            } else {
                $email->addAttachment('./' . $file, $file);
            }
            $email->send();
            
        } catch(Exception $e) {
            d($e);
        }
    }
}
