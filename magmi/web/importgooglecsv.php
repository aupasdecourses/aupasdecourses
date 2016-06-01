<?php

//Global var

    //To get rid of loading time on Google Sheets side (if file less than 1Ko, retry)
    $size_file_limit = 1000;

    define('CHEMIN_MAGE', '../../');
    include CHEMIN_MAGE.'app/Mage.php';
    umask(0);
    Mage::app();
    $url_base = Mage::getBaseDir('var').'/import/';
    $arrContextOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
        ),
    );

    if (isset($_GET['action'])) {
        $commercant = explode('|', $_GET['action']);
        foreach ($commercant as $code) {
            $google_csv = Mage::helper('pmainguet_delivery')->getgooglecsv($code);
            $name = $google_csv['name'];
            $key = $google_csv['key'];
            $gid = $google_csv['gid'];
            if (!is_null($key) && !is_null($gid)) {
                try {
                    $filepath = $url_base.date('ymd_Hi').'_'.$code.'.csv';
                    $filesize = 0;
                    //while($filesize<$size_file_limit){
                        file_put_contents($filepath, file_get_contents('https://docs.google.com/spreadsheets/d/'.$key.'/export?gid='.$gid.'&format=csv&id='.$key, false, stream_context_create($arrContextOptions)));
                    $filesize = filesize($filepath);
                    //}
                    echo 'Fichier '.$name.' synchronisé! (taille='.round(floatval($filesize) / 1000, 0).'Ko)';
                } catch (Exception $e) {
                    echo 'Erreur!';
                }
            } else {
                echo 'Key & gid vides! Vérifier les informations Google Sheets dans la catégorie correspondante.';
            }
        }
    }
