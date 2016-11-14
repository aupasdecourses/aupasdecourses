<?php

$colors = array(
    'primeur' => '#3cad27',
    'boucher' => '#e42f43',
    'fromager' => '#f09646',
    'poissonnier' => '#4880d4',
    'caviste' => '#da2321',
    'boulanger' => '#eb9900',
    'epicerie fine' => '#24349e',
    'traiteur' => '#e85100'
);
$rayons = Mage::getModel('catalog/category')->getCollection()
    ->addFieldToFilter('level', 2)
    ->addFieldToFilter('is_active', 1)
    ->addAttributeToSelect('name');
$rayons->load();

foreach ($rayons as $rayon) {
    $name = $rayon->getName();
    if (isset($colors[strtolower($name)])) {
        $rayon->setMenuBgColor($colors[strtolower($name)])
            ->setMenuTextColor('#ffffff')
            ->save();

    }
}

$commercants = Mage::getModel('catalog/category')->getCollection()
    ->addFieldToFilter('level', 3)
    ->addFieldToFilter('is_active', 1)
    ->addAttributeToSelect('name');
$commercants->load();
foreach ($commercants as $commercant) {
    if ($commercant->getChildrenCount() > 4) {
        $commercant->setMenuTemplate('template3')
            ->save();
    } else if ($commercant->getChildrenCount() == 4) {
        $commercant->setMenuTemplate('template2')
            ->save();
    } else {
        $commercant->setMenuTemplate('template1')
            ->save();
    }
}
$shellDir = Mage::getBaseDir() . DS .'shell' . DS;
shell_exec('php ' . $shellDir . 'indexer.php --reindex catalog_category_flat,catalog_category_product');
