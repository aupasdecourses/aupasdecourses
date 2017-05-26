<?php


$mediaDir = 'apdc/apdc_neighborhood';
$destDir = Mage::getBaseDir('media') . DS . $mediaDir;
$origDir = dirname(__FILE__) .DS . 'images';

$images = array('paris3e.jpg', 'paris7e.jpg', 'paris9e.jpg', 'paris10e.jpg', 'paris11e.jpg', 'paris13e.jpg', 'paris14e.jpg', 'paris15e.jpg', 'paris16e.jpg', 'paris17e.jpg', 'paris18e.jpg');
if (!file_exists($destDir)) {
    mkdir($destDir, 0777, true);
}

foreach ($images as $image) {
    copy($origDir . DS . $image, $destDir . DS . $image);
}

$neighborhoodData = array(
    array(
        'is_active' => 1,
        'name' => 'Paris 3ᵉ',
        'website_id' => 4,
        'image' => $mediaDir . DS . 'paris3e.jpg',
        'postcodes' => '75003',
        'sort_order' => 10
    ),
    array(
        'is_active' => 1,
        'name' => 'Paris 7ᵉ',
        'website_id' => 9,
        'image' => $mediaDir . DS . 'paris7e.jpg',
        'postcodes' => '75007',
        'sort_order' => 20
    ),
    array(
        'is_active' => 0,
        'name' => 'Paris 9ᵉ',
        'image' => $mediaDir . DS . 'paris9e.jpg',
        'postcodes' => '75009',
        'sort_order' => 30
    ),
    array(
        'is_active' => 1,
        'name' => 'Paris 10ᵉ',
        'website_id' => 4,
        'postcodes' => '75010',
        'image' => $mediaDir . DS . 'paris10e.jpg',
        'sort_order' => 40
    ),
    array(
        'is_active' => 0,
        'name' => 'Paris 11ᵉ',
        'postcodes' => '75011',
        'image' => $mediaDir . DS . 'paris11e.jpg',
        'sort_order' => 50
    ),
    array(
        'is_active' => 1,
        'name' => 'Paris 13ᵉ',
        'website_id' => 6,
        'image' => $mediaDir . DS . 'paris13e.jpg',
        'postcodes' => '75013',
        'sort_order' => 60
    ),
    array(
        'is_active' => 1,
        'name' => 'Paris 14ᵉ',
        'website_id' => 8,
        'image' => $mediaDir . DS . 'paris14e.jpg',
        'postcodes' => '75014',
        'sort_order' => 70
    ),
    array(
        'is_active' => 1,
        'name' => 'Paris 15ᵉ',
        'website_id' => 5,
        'image' => $mediaDir . DS . 'paris15e.jpg',
        'postcodes' => '75015',
        'sort_order' => 80
    ),
    array(
        'is_active' => 1,
        'name' => 'Paris 16ᵉ',
        'website_id' => 7,
        'image' => $mediaDir . DS . 'paris16e.jpg',
        'postcodes' => '75016',
        'sort_order' => 90
    ),
    array(
        'is_active' => 1,
        'name' => 'Paris 17ᵉ',
        'website_id' => 1,
        'image' => $mediaDir . DS . 'paris17e.jpg',
        'postcodes' => '75017',
        'sort_order' => 100
    ),
    array(
        'is_active' => 1,
        'name' => 'Paris 18ᵉ',
        'website_id' => 1,
        'image' => $mediaDir . DS . 'paris18e.jpg',
        'postcodes' => '75018',
        'sort_order' => 110
    )
);

foreach ($neighborhoodData as $neighborhood) {
    $model = Mage::getModel('apdc_neighborhood/neighborhood')
        ->setData($neighborhood)
        ->save();
}
