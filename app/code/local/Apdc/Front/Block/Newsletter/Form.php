<?php


class Apdc_Front_Block_Newsletter_Form extends Mage_Core_Block_Template
{

       private $_listcustom_id = [
            // 'Paris 1er' => 'e2c0e6e524',
            // 'Paris 2e' => 'd9ccbf4ec7',
            // 'Paris 3e' => '85bc548e15',
            // 'Paris 4e' => 'ad80c26648',
            // 'Paris 5e' => '751a437c84',
            // 'Paris 6e' => '343bf9fdc1',
            // 'Paris 7e' => '29f484f97b',
            // 'Paris 8e' => '1e484362a1',
            // 'Paris 9e' => 'bbd4b33de5',
            // 'Paris 10e' => '2b680440e9',
            // 'Paris 11e' => '1448c7ec3f',
            // 'Paris 12e' => 'd53a74be1e',
            // 'Paris 13e' => 'b452464ce5',
            // 'Paris 14e' => '1eed79f344',
            // 'Paris 15e' => 'ccfb1e0d57',
            // 'Paris 16e' => 'f8bb26d742',
            // 'Paris 17e' => '8ad1c76cc9',
            // 'Paris 18e' => '85ba477555',
            // 'Paris 19e' => 'a89fa056eb',
            // 'Paris 20e' => '74c1dc5ef5',
            'Boulogne Billancourt' => '7c78a77fa2',
            'Clichy' => 'f8fd8db2a6',
            'Courbevoie' => '53fb7e50b5',
            'Issy-les-Moulineaux' => 'f6f69ddb53',
            'Levallois Perret' => '9153610c96',
            'Montrouge' => '26b29599c4',
            'Neuilly sur Seine' => 'e9d7b11fe8',
            'Puteaux' => '145c212032',
            'Vincennes' => '8350561dc8',  
            'Autres villes' => '6e87af31ed',
        ];

    public function getNewsletterlist()
    {
        /*$mclists = $this->getLists();
        $mclists_size = count($mclists);
        $arr = array();

        foreach ($this->_listcustom as $q) {
            foreach ($mclists as $key => $ql) {
                if ($ql['name'] == $q) {
                    $arr[$q] = $ql['id'];
                    unset($mclists[$key]);
                    break;
                }
            }
        }

        if ($mclists_size != count($arr)) {
            Mage::log('Newsletter entry missing on landing page newsletter form', null, 'Ebizmart_Newsletter_Form.log');
        }

        return $arr;*/

        return $this->_listcustom_id;
    }
}
