<?php

// Upgrade data only if no Neighborhoods exists
$shops = Mage::getModel('apdc_commercant/shop')->getCollection();

foreach ($shops as $shop) {
    $shop->setFlagMagmi(1)->save();
}
