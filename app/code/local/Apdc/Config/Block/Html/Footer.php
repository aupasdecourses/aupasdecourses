<?php
/**
 * @author      Pierre Mainguet
 * Remove footer from cache
 */
class Apdc_Config_Block_Html_Footer extends Mage_Page_Block_Html_Footer
{

    public function getCacheLifetime()
    {
        return;
    }

}
