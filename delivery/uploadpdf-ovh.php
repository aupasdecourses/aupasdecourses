<?php
//init Magento
  error_reporting(E_ALL);
  setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
  ini_set('display_errors', 1);
  include 'global/init.php';
  include CHEMIN_MODELE.'magento.php';
  connect_magento();
if(!empty($_POST['pdf'])){
	
	//Save file to server
	$data = base64_decode($_POST['pdf']);
    $filename="attachments/".$_POST['filename'];
	file_put_contents( $filename, $data );
	//echo($_POST['filename']." saved");

	//Define Return-Path
    $tr = new Zend_Mail_Transport_Sendmail('-contact@aupasdecourses.com');
    Zend_Mail::setDefaultTransport($tr);

	$mail = new Zend_Mail('utf-8');

    //For test purpose only
    //$recipients=array('mainguetpierre@gmail.com');

     $recipients = array(
        // 'pierre@aupasdecourses.com',
        commercant($_POST['id_commercant'])->getData('mail_contact'),
        commercant($_POST['id_commercant'])->getData('mail_pro'),
        commercant($_POST['id_commercant'])->getData('mail_3')
    );

    $mailBody   = "<br/>Commande du jour<br/>
    <br/>
    Bonjour,<br/>
    <br/>
    Vous avez ".$_POST['nbecommande']." commande(s) aujourd'hui. Vous retrouverez le détail en pièce jointe.<br/>
    <br/>
    Cordialement,<br/>
    <br/>
    L'équipe de AU PAS DE COURSES";
    $date=strftime("%A %d %B"); 
    $mail->setBodyHtml($mailBody)
        ->setSubject('Au Pas De Courses - Commande du '.$date)
        ->addTo($recipients)
        ->addCc(Mage::getStoreConfig('trans_email/ident_general/email'))
        ->setFrom(Mage::getStoreConfig('trans_email/ident_general/email'), "L'équipe d'Au Pas De Courses")
        ->setDefaultTransport($tr);

    //file content is attached
    $content = file_get_contents($filename);
    $attachment = new Zend_Mime_Part($content);
    $attachment->type = 'application/pdf';
    $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
    $attachment->encoding = Zend_Mime::ENCODING_BASE64;
    $attachment->filename = $_POST['filename'];

    $mail->addAttachment($attachment);                  

    try {
        $mail->send();
        echo "Mail envoyé à ".commercant($_POST['id_commercant'])->getName()." !";
    } catch (Exception $e) {
    	echo "Erreur lors de l'envoi";
        Mage::logException($e);
    }
} else {
	echo "No Data Sent";
}
exit();