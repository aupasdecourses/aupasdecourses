<?php
function rrmdir($src) {
    $dir = opendir($src);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            $full = $src . '/' . $file;
            if ( is_dir($full) ) {
                rrmdir($full);
            }
            else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    rmdir($src);
}

// Upgrade data only if no Neighborhoods exists
$neighborhoods = Mage::getModel('apdc_neighborhood/neighborhood')->getCollection();

//banners
$origDir = Mage::getBaseDir('media') . DS . 'apdc/apdc_neighborhood';
$destDir = Mage::getBaseDir('media') . DS . 'wysiwyg/neighborhood';
if (!file_exists($destDir)) {
    mkdir($destDir, 0777, true);
}

$banners = array(
    'paris1e.jpg' => 'paris1er_banner.jpg',
    'paris2e.jpg' => 'paris2e_banner.jpg',
    'paris3e.jpg' => 'banners/paris3e_banner.jpb',
    'paris4e.jpg' => 'banners/paris4e_banner.jpg',
    'paris5e.jpg' => 'banners/paris5e_banner.jpg',
    'paris6e.jpg' => 'banners/paris6e_banner.jpg',
    'paris7e.jpg' => 'banners/paris7e_banner.jpg',
    'paris8e.jpg' => 'paris8e_banner.jpg',
    'paris9e.jpg' => 'banners/paris9e_banner.jpg',
    'paris10e.jpg' => 'banners/paris10e_banner.jpg',
    'paris11e.jpg' => 'banners/paris11e_banner.jpg',
    'paris12e.jpg' => 'banners/paris12e_banner.jpg',
    'paris13e.jpg' => 'banners/paris13e_banner.jpg',
    'paris14e.jpg' => 'banners/paris14e_banner.jpg',
    'paris15e.jpg' => 'banners/paris15e_banner.jpg',
    'paris16e.jpg' => 'banners/paris16enord_banner.jpg',
    'paris16esud.jpg' => 'banners/paris16esud_banner.jpg',
    'paris17e.jpg' => 'banners/paris17e_banner.jpg',
    'paris18e.jpg' => 'banners/paris18e_banner.jpg',
    'paris19e.jpg' => 'paris19e_full.jpg',
    'paris20e.jpg' => 'paris20e_full.jpg',
    'levalloisperret.jpg' => 'levallois_full.jpg'
);


foreach ($banners as $newBanner => $oldBanner) {
    @rename($origDir . DS . $oldBanner, $destDir . DS . $newBanner);
}

rrmdir($origDir);

