<?php
$content = '<div class="sitelogo">LOGO</div>
<h2>This Website requires you to be 18 years or older to enter.</h2>';
$content2 ='<div class="sitelogo">LOGO</div>
<h2>Sorry Adults Only</h2>';

//if you want one general block for all the store viwes, uncomment the line below
$stores = array(0);
foreach ($stores as $store){
    $block = Mage::getModel('cms/block');
    $block->setTitle('Age Gate');
    $block->setIdentifier('vsourz-age-gate');
    $block->setStores(array($store));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
	$block = Mage::getModel('cms/block');
    $block->setTitle('Age Gate Disagree');
    $block->setIdentifier('vsourz-age-gate-disagree');
    $block->setStores(array($store));
    $block->setIsActive(1);
    $block->setContent($content2);
    $block->save();
}