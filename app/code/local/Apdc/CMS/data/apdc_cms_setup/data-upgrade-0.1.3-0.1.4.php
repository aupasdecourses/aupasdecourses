<?php

$page=Mage::getModel('cms/page')->load('no-route','identifier');
$page->setCustomTheme('boilerplate/default');
$page->save();