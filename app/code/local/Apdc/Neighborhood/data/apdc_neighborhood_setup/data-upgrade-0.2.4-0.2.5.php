<?php

// Upgrade data only if no Neighborhoods exists
$neighborhoods = Mage::getModel('apdc_neighborhood/neighborhood')->getCollection();

if ($neighborhoods->count() == 0) {
    $mediaDir = 'apdc/apdc_neighborhood';
    $destDir = Mage::getBaseDir('media') . DS . $mediaDir;
    $origDir = dirname(__FILE__) .DS . 'banner';

    $images = array('paris3e.jpg', 'paris7e.jpg', 'paris9e.jpg', 'paris10e.jpg', 'paris11e.jpg', 'paris13e.jpg', 'paris14e.jpg', 'paris15e.jpg', 'paris16e.jpg', 'paris17e.jpg', 'paris18e.jpg');
    if (!file_exists($destDir)) {
        mkdir($destDir, 0777, true);
    }

    foreach ($images as $image) {
        copy($origDir . DS . $image, $destDir . DS . $image);
    }

    //create missing neighborhoods
    $neighborhoodData = array(
        array(
            'is_active' => 0,
            'name' => 'Paris 1ᵉ',
            'website_id' => 16,
            'image' => $mediaDir . DS . 'paris1er.jpg',
            'image_banner' => $mediaDir . DS . 'paris1er_banner.jpg',
            'postcodes' => '75001',
            'code_do' => 'APDC5595',
            'mistral_guid' => 'CA68B97417EA4FF68013C60346720418',
            'sort_order' => 10
        ),
        array(
            'is_active' => 0,
            'name' => 'Paris 2ᵉ',
            'website_id' => 10,
            'image' => $mediaDir . DS . 'paris2e.jpg',
            'image_banner' => $mediaDir . DS . 'paris2e_banner.jpg',
            'postcodes' => '75002',
            'code_do' => 'APDC5589',
            'mistral_guid' => '5D0F578BA07A4D65AE49C07E8D97D16C',
            'sort_order' => 10
        ),
        array(
            'is_active' => 0,
            'name' => 'Paris 8ᵉ',
            'website_id' => 1,
            'image' => $mediaDir . DS . 'paris8e.jpg',
            'image_banner' => $mediaDir . DS . 'paris8e_banner.jpg',
            'postcodes' => '75008',
            'code_do' => 'APDC5614',
            'mistral_guid' => '38CAFB65A89A4B99AC06EC2A550F0AD5',
            'sort_order' => 10
        ),
        array(
            'is_active' => 0,
            'name' => 'Paris 19ᵉ',
            'website_id' => 4,
            'image' => $mediaDir . DS . 'paris19e.jpg',
            'image_banner' => $mediaDir . DS . 'paris19e_banner.jpg',
            'postcodes' => '75019',
            'code_do' => 'APDC5624',
            'mistral_guid' => 'A8F99FEDAB55438D97FA37EF097A7E06',
            'sort_order' => 10
        ),
        array(
            'is_active' => 0,
            'name' => 'Paris 20ᵉ',
            'website_id' => 11,
            'image' => $mediaDir . DS . 'paris20e.jpg',
            'image_banner' => $mediaDir . DS . 'paris20e_banner.jpg',
            'postcodes' => '75020',
            'code_do' => 'APDC5620',
            'mistral_guid' => '51BAF1CF683B4EF1A132515181416944',
            'sort_order' => 10
        ),
    );
    
    foreach ($neighborhoodData as $neighborhood) {
        $model = Mage::getModel('apdc_neighborhood/neighborhood')
            ->setData($neighborhood)
            ->save();
    }

    $neighborhoodDataupdate = array(
        array(
            'entity_id' => 1,
            'image_banner' => $mediaDir . DS . 'paris10e_banner.jpg',
            'code_do' => 'APDC5541',
            'mistral_guid' => '345EA4F0DF904D368DD102A3A4ED33B6',
        ),
        array(
            'entity_id' => 2,
            'image_banner' => $mediaDir . DS . 'paris7e_banner.jpg',
            'code_do' => 'APDC5585',
            'mistral_guid' => 'AB5227EED1CE4E2D81EC68A47BCDE7BD',
        ),
        array(
            'entity_id' => 3,
            'image_banner' => $mediaDir . DS . 'paris9e_banner.jpg',
            'code_do' => 'APDC5586',
            'mistral_guid' => '1C9AB3F2C53A4B83BC909257998AB4AA',
        ),
        array(
            'entity_id' => 4,
            'image_banner' => $mediaDir . DS . 'paris3e_banner.jpg',
            'code_do' => 'APDC5590',
            'mistral_guid' => 'D8FBA548ED6C410EA704D5CA22C85F09',
        ),
        array(
            'entity_id' => 5,
            'image_banner' => $mediaDir . DS . 'paris11e_banner.jpg',
            'code_do' => 'APDC5587',
            'mistral_guid' => 'E5A31990C34544C1916DC3303D885751',
        ),
        array(
            'entity_id' => 6,
            'image_banner' => $mediaDir . DS . 'paris13e_banner.jpg',
            'code_do' => 'APDC5543',
            'mistral_guid' => 'B745479D355D4C7C95A0393BF98713BD',
        ),
        array(
            'entity_id' => 7,
            'image_banner' => $mediaDir . DS . 'paris14e_banner.jpg',
            'code_do' => 'APDC5594',
            'mistral_guid' => 'E77FE3549F5946D781123BCC17DBF6FD',
        ),
        array(
            'entity_id' => 8,
            'image_banner' => $mediaDir . DS . 'paris15e_banner.jpg',
            'code_do' => 'APDC5540',
            'mistral_guid' => '71A57BE67A054E66B88A9CCD4CD897A8',
        ),
        array(
            'entity_id' => 9,
            'image_banner' => $mediaDir . DS . 'paris16enord_banner.jpg',
            'code_do' => 'APDC5542',
            'mistral_guid' => 'E1CE138D6DA44CE99DA2FB9A47EAE353',
        ),
        array(
            'entity_id' => 10,
            'image_banner' => $mediaDir . DS . 'paris17e_banner.jpg',
            'code_do' => 'APDC5535',
            'mistral_guid' => '6AA0A660A6B647C39F4CE6CED09621A2',
        ),
        array(
            'entity_id' => 11,
            'image_banner' => $mediaDir . DS . 'paris18e_banner.jpg',
            'code_do' => 'APDC5651',
            'mistral_guid' => '77BEF1ECE6A8431A9799BE2E88EAC393',
        ),
        array(
            'entity_id' => 12,
            'image_banner' => $mediaDir . DS . 'paris5e_banner.jpg',
            'code_do' => 'APDC5592',
            'mistral_guid' => 'FD36EB065AA54135AAE4D412A19F238F',
        ),
        array(
            'entity_id' => 13,
            'image_banner' => $mediaDir . DS . 'paris16esud_banner.jpg',
            'code_do' => 'APDC5807',
            'mistral_guid' => '92AF07FC67864C3F96F0A5674BDD26C4',
        ),
        array(
            'entity_id' => 14,
            'image_banner' => $mediaDir . DS . 'paris12e_banner.jpg',
            'code_do' => 'APDC5615',
            'mistral_guid' => 'D29971DFA2544305AD5AC68585EA28B5',
        ),
        array(
            'entity_id' => 15,
            'image_banner' => $mediaDir . DS . 'paris6e_banner.jpg',
            'code_do' => 'APDC5613',
            'mistral_guid' => '6CC9BE0DD9F7407A9D77B00722BCEE24',
        ),
        array(
            'entity_id' => 16,
            'image_banner' => $mediaDir . DS . 'paris4e_banner.jpg',
            'code_do' => 'APDC5591',
            'mistral_guid' => '1D89047E4BC74554A718E8A540F955E3',
        ),
    );

    foreach ($neighborhoodDataupdate as $neighborhood) {
        $entity = Mage::getModel('apdc_neighborhood/neighborhood')->load($neighborhood['entity_id']);
        $entity->setImageBanner($neighborhood['image_banner'])->save();
    }
}
