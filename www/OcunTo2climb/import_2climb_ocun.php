<?php

    ini_set('max_execution_time', '3000');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once '/var/www/OcunTo2climb/OcunTo2climb.php';

    echo 'Starting covert...';
    
    $tc = new OcunTo2climb();
    $tc->createXMLShoptet();

    
    echo 'Done.' .PHP_EOL;
    //$tc->createCSVShoptet();
    //$tc->getImages(1600);
    