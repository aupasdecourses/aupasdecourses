<?php

class Pmainguet_Customhome_IndexController extends Mage_Core_Controller_Front_Action
{
    public function redirectAction()
    {
        $baseurl=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        if ($data = $this->getRequest()->getPost()) {
            $zipcode = $data['zipcode'];
            $refererUrl = Mage::helper('core/http')->getHttpReferer(true);
            $url = Mage::getBaseUrl();
            if (isset($zipcode)) {
                switch ($zipcode) {
                    case 'Paris 17e':
                        $url .= '../batignolles';
                        break;
                    case 'Paris 18e':
                        $url .= '../batignolles';
                        break;
                    case 'Paris 9e':
                        $url .= '../batignolles';
                        break;
                    case 'Paris 8e':
                        $url .= '../batignolles';
                        break;
                    case 'Paris 2e':
                        $url .= '../saintmartin';
                        break;
                    case 'Paris 3e':
                        $url .= '../saintmartin';
                        break;
                    case 'Paris 10e':
                        $url .= '../saintmartin';
                        break;
                    case 'Paris 11e':
                        $url .= '../saintmartin';
                        break;
                    case 'Paris 1er':
                        $url .= '../quartiers/Paris_1er';
                        break;
                    case 'Paris 4e':
                        $url .= '../quartiers/Paris_4e';
                        break;
                    case 'Paris 5e':
                        $url .= '../quartiers/Paris_5e';
                        break;
                    case 'Paris 6e':
                        $url .= '../quartiers/Paris_6e';
                        break;
                    case 'Paris 7e':
                        $url .= '../quartiers/Paris_7e';
                        break;
                    case 'Paris 12e':
                        $url .= '../quartiers/Paris_12e';
                        break;
                    case 'Paris 13e':
                        $url .= '../quartiers/Paris_13e';
                        break;
                    case 'Paris 14e':
                        $url .= '../quartiers/Paris_14e';
                        break;
                    case 'Paris 15e':
                        $url .= '../quartiers/Paris_15e';
                        break;
                    case 'Paris 16e':
                        $url .= '../quartiers/Paris_16e';
                        break;
                    case 'Paris 19e':
                        $url .= '../quartiers/Paris_19e';
                        break;
                    case 'Paris 20e':
                        $url .= '../quartiers/Paris_20e';
                        break;
                    case 'Boulogne':
                        $url .= '../quartiers/Boulogne';
                        break;
                    case 'Issy Les Moulineaux':
                        $url .= '../quartiers/Issy-Les-Moulineaux';
                        break;
                    case 'Levallois Perret':
                        $url .= '../quartiers/Levallois-Perret';
                        break;
                    case 'Montrouge':
                        $url .= '../quartiers/Montrouge';
                        break;
                    case 'Vincennes':
                        $url .= '../quartiers/Vincennes';
                        break;
                    default:
                        break;
                }
				Mage::app()->getResponse()->setRedirect($url);
            } else {
                Mage::app()->getResponse()->setRedirect($refererUrl);
            }
        } else {
            Mage::app()->getResponse()->setRedirect($baseurl);
        }
    }
}
