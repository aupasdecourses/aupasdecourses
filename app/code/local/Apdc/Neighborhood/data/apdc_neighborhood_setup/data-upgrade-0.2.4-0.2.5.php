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
            'code_do' => '',
            'sort_order' => 10
        ),
        array(
            'is_active' => 0,
            'name' => 'Paris 2ᵉ',
            'website_id' => 10,
            'image' => $mediaDir . DS . 'paris2e.jpg',
            'image_banner' => $mediaDir . DS . 'paris2e_banner.jpg',
            'postcodes' => '75002',
            'code_do' => '',
            'sort_order' => 10
        ),
        array(
            'is_active' => 0,
            'name' => 'Paris 19ᵉ',
            'website_id' => 4,
            'image' => $mediaDir . DS . 'paris19e.jpg',
            'image_banner' => $mediaDir . DS . 'paris19e_banner.jpg',
            'postcodes' => '75019',
            'code_do' => '',
            'sort_order' => 10
        ),
        array(
            'is_active' => 0,
            'name' => 'Paris 20ᵉ',
            'website_id' => 11,
            'image' => $mediaDir . DS . 'paris20e.jpg',
            'image_banner' => $mediaDir . DS . 'paris20e_banner.jpg',
            'postcodes' => '75020',
            'code_do' => '',
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
            'code_do' => '',
        ),
        array(
            'entity_id' => 2,
            'image_banner' => $mediaDir . DS . 'paris7e_banner.jpg',
            'code_do' => '',
        ),
        array(
            'entity_id' => 3,
            'image_banner' => $mediaDir . DS . 'paris9e_banner.jpg',
        ),
        array(
            'entity_id' => 4,
            'image_banner' => $mediaDir . DS . 'paris3e_banner.jpg',
        ),
        array(
            'entity_id' => 5,
            'image_banner' => $mediaDir . DS . 'paris11e_banner.jpg',
        ),
        array(
            'entity_id' => 6,
            'image_banner' => $mediaDir . DS . 'paris13e_banner.jpg',
        ),
        array(
            'entity_id' => 7,
            'image_banner' => $mediaDir . DS . 'paris14e_banner.jpg',
        ),
        array(
            'entity_id' => 8,
            'image_banner' => $mediaDir . DS . 'paris15e_banner.jpg',
        ),
        array(
            'entity_id' => 9,
            'image_banner' => $mediaDir . DS . 'paris16enord_banner.jpg',
        ),
        array(
            'entity_id' => 10,
            'image_banner' => $mediaDir . DS . 'paris17e_banner.jpg',
        ),
        array(
            'entity_id' => 11,
            'image_banner' => $mediaDir . DS . 'paris18e_banner.jpg',
        ),
        array(
            'entity_id' => 12,
            'image_banner' => $mediaDir . DS . 'paris5e_banner.jpg',
        ),
        array(
            'entity_id' => 13,
            'image_banner' => $mediaDir . DS . 'paris16esud_banner.jpg',
        ),
        array(
            'entity_id' => 14,
            'image_banner' => $mediaDir . DS . 'paris12e_banner.jpg',
        ),
        array(
            'entity_id' => 15,
            'image_banner' => $mediaDir . DS . 'paris6e_banner.jpg',
        ),
        array(
            'entity_id' => 16,
            'image_banner' => $mediaDir . DS . 'paris4e_banner.jpg',
        ),
    );

    foreach ($neighborhoodDataupdate as $neighborhood) {
        $entity = Mage::getModel('apdc_neighborhood/neighborhood')->load($neighborhood['entity_id']);
        $entity->setImageBanner($neighborhood['image_banner'])->save();
    }
}
