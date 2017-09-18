<?php
//init Magento
  error_reporting(E_ALL);
  setlocale(LC_TIME, 'fr_FR.utf8', 'fra'); 
  ini_set('display_errors', 1);
  include 'global/init.php';
  include CHEMIN_MODELE.'magento.php';
  connect_magento();
if(!empty($_POST['pdf'])){
	
	//Save file to server
	$data = base64_decode($_POST['pdf']);
    $filename="attachments/".$_POST['filename'];
	file_put_contents( $filename, $data );
	echo($_POST['filename']." sauvegardé: OK!");

	//Setup Mandrill Usage through Zend Mail
    $config = array('auth' => 'login',
                'username' => 'pierre@aupasdecourses.com',
                'password' => 'suQMuVOzZHE5kc-wmH3oUA',
                'port' => 2525,
				'return-path' => 'contact@aupasdecourses.com'
                );
    $transport = new Zend_Mail_Transport_Smtp('smtp.mandrillapp.com', $config);
    
    //Gmail setup
    // $config = array('auth' => 'login',
    //      'username' => 'mainguetpierre@gmail.com',
    //      'password' => '$VYwEDoxo1710$',
    //      'ssl'=>'ssl',
    //       'port'=>465);
    //$transport = new Zend_Mail_Transport_Smtp("smtp.gmail.com", $config);

	$mail = new Zend_Mail('utf-8');

    //For test purpose only
   //$recipients=array('mainguetpierre@gmail.com','pierre@aupasdecourses.com');

    $shop=getShops($_POST['id_commercant']);

    //For production only
     $recipients=array();
     $mail_array=array('mail_contact','mail_pro','mail_3');
     foreach($mail_array as $m){
         if(!in_array($shop[$m],array('',' ',null))){
             array_push($recipients, $shop[$m]);
         }
     }

    //Load custom template and process variables to personnalize it
    $emailTemplate  = Mage::getModel('core/email_template')->loadByCode('APDC::Mail envoi commande commerçants');
    $variables=array(
        'commercant'=>$_POST['commercant'],
        'nbecommande'=>$_POST['nbecommande'],
        );
    $processedTemplate = $emailTemplate->getProcessedTemplate($variables);

    //Setup mail options
    $mailBody   = $processedTemplate;
    $date=strftime("%A %d %B"); 
    $mail->setBodyHtml($mailBody)
        ->setSubject('Au Pas De Courses - Commande du '.$date)
        ->addTo($recipients)
        ->addCc(Mage::getStoreConfig('trans_email/ident_general/email'))
        ->setFrom(Mage::getStoreConfig('trans_email/ident_general/email'), "L'équipe d'Au Pas De Courses");

    //file content is attached
    $content = file_get_contents($filename);
    $attachment = new Zend_Mime_Part($content);
    $attachment->type = 'application/pdf';
    $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
    $attachment->encoding = Zend_Mime::ENCODING_BASE64;
    $attachment->filename = $_POST['filename'];
    $mail->addAttachment($attachment);                  
    try {
        $fin=$mail->send($transport);
        echo "<br/>Mail envoyé à ".$shop['name'].": OK!";
    } catch (Exception $e) {
    	echo "Erreur lors de l'envoi";
        Mage::log($e,null,'email.log');
    }
} else {
	echo "No Data Sent";
}
exit();
