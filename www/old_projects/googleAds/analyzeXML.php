<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    ini_set('memory_limit','500M');
    

    
    $xmlFile = 'https://dvere-erkado.cz/google-all-feeds/heureka-freelancer.xml';
    //$xmlFile = 'https://www.drevar.cz/data-feed/3db4bac4-38f1-4cde-992e-e2d4316985f9';        
    
    $xml_reader = new XMLReader;
    $xml_reader->xml(file_get_contents($xmlFile));

    // move the pointer to the first product
    while ($xml_reader->read() && $xml_reader->name != 'SHOPITEM');

    $i=0;
    while ($xml_reader->name == 'SHOPITEM'){
        $xml = simplexml_load_string($xml_reader->readOuterXML());
        if(strpos($xml->PRODUCTNAME, 'Interiérové dveře ALTAMURA 1')) {
            echo $xml->PRODUCTNAME .' -*- ' .$xml->URL .'<br>';
        }
        //$toUnoque[] = $xml->PARAM->PARAM_NAME;
        //if($xml->CATEGORYTEXT == 'Heureka.cz | Stavebniny | Dveře a zárubně | Dveře | Interiérové dveře') $toUnoque[] = $xml->PRODUCTNAME;
        //if($xml->PARAM->PARAM_NAME == 'Šířka') $toUnoque[] = $xml_reader->next('PARAM');
        //if($xml->PARAM->PARAM_NAME == 'Šířka') $toUnoque[] = $xml->PRODUCTNAME;
        /*
        if(strpos($xml->PRODUCTNAME, 'odlaha laminátová Kronotex')) {
            echo $xml->PRODUCTNAME . ' --- ';
            //if($xml->PARAM->PARAM_NAME->asXml() == '<PARAM_NAME>Šířka</PARAM_NAME>') $toUnoque[] = $xml->PARAM->PARAM_NAME;
            //if($xml->PARAM->PARAM_NAME->asXml() == '<PARAM_NAME>Šířka</PARAM_NAME>') echo $xml->PARAM->PARAM_NAME;
            foreach ($xml->PARAM as $key => $value) {
                //var_dump($value->PARAM_NAME->asXml());
                if($value->PARAM_NAME->asXml() == '<PARAM_NAME>Šířka</PARAM_NAME>') var_dump ($value->VAL->asXml());
                //var_dump ($value->PARAM_VAL->asXml());
            }

            echo '<br>';
        }
         * 
         */
//var_dump($a);
        
        $xml_reader->next('SHOPITEM');
        $i++;
    }

    $xml_reader->close();    
    
//    $c = array_unique($toUnoque);
    
    foreach ($toUnoque as $key => $value) {
        //echo $value .'<br>';
        var_dump($value);
        echo '<br>';
    }

exit;


    $xmlFile = '/var/www/core/google.xml';
   
        
    $xml_reader = new XMLReader;
    $xml_reader->xml(str_replace(array('g:', '/g:'),'', file_get_contents($xmlFile)));    

    // move the pointer to the first product
    while ($xml_reader->read() && $xml_reader->name != 'item');

    while ($xml_reader->name == 'item'){
        $xml = simplexml_load_string($xml_reader->readOuterXML());
        echo $xml->product_type .'<br>';
        $xml_reader->next('item');
        //break;
    }

    $xml_reader->close();


    
    
?>


