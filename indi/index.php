<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);

include '../app/Mage.php';

Mage::app();

include 'public/index.php';
