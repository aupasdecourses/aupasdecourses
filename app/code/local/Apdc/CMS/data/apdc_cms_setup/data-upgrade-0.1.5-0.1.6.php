<?php

//update des static blocks des commerçants pour qu'ils soient disponibles dans toutes les boutiques

$comBlocks = Mage::getModel('cms/block')
    ->getCollection()
    ->addFieldToFilter('identifier', array( 'like' => 'main%') );

// Loop through each navigation block
foreach( $comBlocks as $block ) {

    $staticBlock = Mage::getModel('cms/block')
       ->load( $block->getIdentifier(), 'identifier');

    if( $staticBlock->getId() ) {
        $staticBlock
            ->setStores(array(0))
            ->save();
    }
}