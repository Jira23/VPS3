<?php

    class OcunTo2climb{
        
        const OCUN_CSV_LISTING = '/var/www/core/ocunTo2climb/Listing_table_2023.csv';                           // soubor od obchodaka - seznam vsech produktu vcetne variant
        const OCUN_XML = 'https://sales.ocun.com/feeds/stockQty15-CZ598cc424f1e1bc5e7838f627710c69a0.xml';      // xml soubor s aktualnimi daty o skladovosti a cene
//        const OCUN_XML = '/var/www/core/ocunTo2climb/stockQty15-CZ598cc424f1e1bc5e7838f627710c69a0.xml';        
        const OCUN_CSV_TO_IMPORT = '/var/www/html/2climb/ocun_import.csv';                                      // csv soubor v heureka formatu pro jednorazovy import na shoptet
        const OCUN_XML_TO_UPDATE_IMPORT = '/var/www/html/2climb/ocun_update_import.xml';                        // xml soubor v heureka formatu pro opakovany import na shoptet (aktualizace skladu)
        const FAILED_IMAGES_PATH = 'http://194.182.64.183/2climb/images/failedImages/';                         // cesta k nahradnim obrazkum

        
        // vytvori xml soubor pro pravidelnou aktualizaci skladovych zasob. Taha data z ocun feedu
        public function createXMLShoptet(){
           
            $csvFile = file_get_contents(self::OCUN_CSV_TO_IMPORT);
            
            $xml_reader = new XMLReader;
            $xml_reader->xml(file_get_contents(self::OCUN_XML));
            while ($xml_reader->read() && $xml_reader->name != 'item');         // move the pointer to the first product

            $i=0;
            while ($xml_reader->name == 'item'){
                $xml = simplexml_load_string($xml_reader->readOuterXML());
                $ean = (string)$xml->ean;
                if($ean == '' || !strpos($csvFile, $ean)) {
                    $xml_reader->next('item');
                    continue;
                }
                $toUpdateFeed[$i]['EAN'] = $ean;
                $toUpdateFeed[$i]['STOCK']['AMOUNT'] = (int)$xml->inStock;
                $xml_reader->next('item');
                $i++;
            }

            $xml_reader->close();    

///            echo 'celkem: ' .count($toUpdateFeed) .'<br>';


            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><SHOP></SHOP>');  // vytvorim novz simpleCML objekt
            $this->arrayToXml($toUpdateFeed,$xml);                              // naplnim ho daty z pole

//            print htmlspecialchars($xml->asXML());
            $xml->asXML(self::OCUN_XML_TO_UPDATE_IMPORT);
                        
/*            
            foreach ($toUpdateFeed as $key => $value) {
                echo $value['STOCK']['AMOUNT'] .'<br>';
                //var_dump($value);
                //var_dump (strpos($csvFile, $value['EAN']));
                echo '<br>';
            }            
*/            
        }

        // naplni simpleXML objekt daty z pole
        private function arrayToXml( $scrappedData, &$xml ) {
            foreach( $scrappedData as $key => $value ) {                        // projizdim postupne pole
                if( is_array($value) ) {                                        // pokud se jedna o pole, pak...
                    if($key == 'STOCK') {                                       // ...pokud se jedna o dalsi obrazky, pak...
                        if(isset($value['AMOUNT'])){
                            $a = $xml->addChild('STOCK');
                            $a->addChild("AMOUNT", $value['AMOUNT']);               // ... vkladam je za sebe
                        }
                        continue;                                               // ... preskocim na dalsi bod
                    }
                    $key = 'SHOPITEM';                     // ... pokud to neni pole s parametry, je to nova polozka -> vytvorim nazev uzlu jako "SHOPITEM"
                    $subnode = $xml->addChild($key);                            // ...vytvorim novy poduzel (child node)
                    $this->arrayToXml($value, $subnode);                        // ...znovu zavolam tuto fci
                } else {
                    $xml->addChild("$key",htmlspecialchars("$value"));          // ...vytvorim novy poduzel (child node)
                }
            }
        } 
        
        // vytahne data z excelivskeho souboru "zalistovaci tabulka" a ulozi je do xml. SLouzi k vytvoreni jednorazoveho importniho csv.
        public function createCSVShoptet(){
            $source = file_get_contents(self::OCUN_CSV_LISTING);
            $ocunXMLfile = file_get_contents(self::OCUN_XML);
            
            $radky = explode(PHP_EOL, $source);
            $i = 0;
            foreach ($radky as $radek) {
                $params = explode(';', $radek);
                
                $item[$i]['code'] = $params[12];                                        // EAN skladovy
                //$item[$i]['pairCode'] = '';// $params[3];
                $item[$i]['pairCode'] = (substr_count($source, $params[3]) > 2) ? $params[3] : '';
                $item[$i]['name'] = $params[4];
                $item[$i]['description'] = (string)$this->getOcunXMLItem($ocunXMLfile, $params[12])['longDescription'];
                $item[$i]['manufacturer'] = 'Ocún';
                $item[$i]['ean'] = $params[13];
                $item[$i]['defaultImage'] = $item[$i]['image'] = $this->replace404Images($params[29]);
                $item[$i]['categoryText'] = $this->translateCategory($params[0]);
                $item[$i]['price'] = str_replace(array(' ', ',00Kč'), '', $params[16]);
                $item[$i]['variant:Barva'] = $params[8];
                $item[$i]['variant:Velikost'] = $params[6];
                $item[$i]['variant:pohlavi'] = $params[5];
                $item[$i]['stock'] = (string)$this->getOcunXMLItem($ocunXMLfile, $params[12])['inStock'];
                $item[$i]['unit'] = $params[19];
                $item[$i]['weight'] = (float)$params[20];
                $item[$i]['height'] = (float)$params[23];
                $item[$i]['depth'] = (float)$params[21];
                $item[$i]['width'] = (float)$params[22];
                
                // plus parametry hmotnost vyska, sirka .....
                $i++;
//                if($i >= 10) break;  
            }
            
            $fp = fopen(self::OCUN_CSV_TO_IMPORT, 'w');

            fputcsv($fp, array_keys($item[0]), ';');
            foreach ($item as $fields) {
                fputcsv($fp, $fields, ';');
            }
            fclose($fp);
        }
        
        // najde v xml souboru potrebnou radku
        private function getOcunXMLItem($ocunXMLfile, $id){
            $ocunXML = $this->xmlToArray($ocunXMLfile, 'item');
            foreach ($ocunXML as $item) {
                if((string)$item['ean'] == $id) {
                    //echo $id .' - ' .(string)$item['inStock'] .'<br>';
                    return($item);
                }
            }
        }
        
        // prevede xml subor do pole
        private function xmlToArray($xmlFile, $itemName = 'SHOPITEM'){
            $xml_reader = new XMLReader;
            $xml_reader->xml($xmlFile);
            
            while ($xml_reader->read() && $xml_reader->name != $itemName);     // move the pointer to the first product

            $i = 1;
            while ($xml_reader->name === $itemName){
                $xml = simplexml_load_string($xml_reader->readOuterXML());
                foreach ($xml as $key => $item) {
                    if($key == 'PARAM'){
                        $toFeed[$i]['PARAM'] = array(array('PARAM_NAME' => $xml->$key->PARAM_NAME, 'VAL' => $xml->$key->VAL));
                        continue;
                    }
                    $toFeed[$i][$key] = $xml->$key;
                }
                    $i++;
//                    if($i > 1) break;
                $xml_reader->next($itemName);
            }
            $xml_reader->close();    
            return($toFeed);            
        }        
        
        // vrati cesky ekvivalent nazvu kategorie
        private function translateCategory($en){
            
            $enToCz = array(    
            "Accessories"           =>  "Doplňky a příslušenství",
            "Belay devices"         =>  "Jistící pomůcky",
            "Carabiners"            =>  "Karabiny",
            "Climbing harnesses"    =>  "Úvazky",
            "Climbing holds"        =>  "Chyty",
            "Climbing shoes"        =>  "Lezečky",
            "Crash pads"            =>  "Bouldermatky",
            "Gloves"                =>  "Rukavice",
            "Headwear"              =>  "Pokrývky hlavy",
            "Helmets"               =>  "Helmy",
            "Chalk and tape"        =>  "Magnézium",
            "Chalk bags"            =>  "Pytlíky na magnézium",
            "Jackets Men"           =>  "Pánské bundy",
            "Jackets Women"         =>  "Dámské bundy",
            "Komponenty"            =>  "Komponenty",
            "Pants Men"             =>  "Pánské kalhoty",
            "Pants Women"           =>  "Dámské kalhoty",
            "Quickdraw sets"        =>  "Expreskové sety",
            "Ropes"                 =>  "Lana",
            "Slings"                =>  "Smyčky",
            "Special packs"         =>  "Speciální balíčky",
            "T-shirts Men"          =>  "Pánská trička",
            "T-shirts Women"        =>  "Dámská trička",
            "Via ferrata"           =>  "Via ferrata");
            
            return ('Ocun > ' .$enToCz[$en]);
                    
        }
        
        // nahradi obrazky, ktere jsou v listingu spatne. Rucne jsem dohledal spravne url k nim
        private function replace404Images($imgUrl){
            $images = array(    
            "https://www.dropbox.com/s/gra7d6momv6d9rl/Guru%2010%2C2%20mm_green.jpg?dl=1"                                   => "Guru_Blue.jpg",
            "https://www.dropbox.com/s/qag777j1hzjvfbn/Guru%2010%2C2%20mm_Violet.jpg?dl=1"                                  => "Guru_Violet.jpg",
            "https://www.dropbox.com/s/opm9hi2hiwmti4f/04082_Kestrel_QD_DYN%208_60%20cm_orange.jpg?dl=1"                    => "04082_Kestrel_o-sling_DYN_8_60_cm_orange.jpg",
            "https://www.dropbox.com/s/c4mdskf4atkle8y/On-sight%208%2C8%20mm_green.jpg?dl=1"                                => "On-SIght_Green-Yellow.jpg",
            "https://www.dropbox.com/s/97y0b0fsg0vqgz8/On-sight%208%2C8%20mm_yellow.jpg?dl=1"                               => "On-SIght_Orange-Yellow.jpg",
            "https://www.dropbox.com/s/7oynp903x668prb/Spirit%209%2C5%20mm_blue.jpg?dl=1"                                   => "Spirit_Blue-White.jpg",
            "https://www.dropbox.com/s/ldyjfxjb3r62hw3/Spirit%209%2C5%20mm_red.jpg?dl=1"                                    => "Spirit_Red-White.jpg",
            "https://www.dropbox.com/s/01zlwc309w35vyb/04395_Via_Ferrata_Captur.jpg?dl=1"                                   => "04395_Via_Ferrata_Captur_2022.jpg",
            "https://www.dropbox.com/s/ie4v9olo7et0jyg/VISION_WR%209%2C1mm_blue.jpg?dl=1"                                   => "Vision_WR_Blue-Purple.jpg",
            "https://www.dropbox.com/s/6tfz2judsp2klpp/VISION_WR%209%2C1mm_purple.jpg?dl=1"                                 => "Vision_WR_Purple-Yellow.jpg",
            "https://www.dropbox.com/s/l5n9sxdysdo483t/05005_Mania%20Eco%20Pants_Anthracite%20Dark%20Navy_1.jpg?dl=1"       => "05005_MANIA_ECO_Pants_Anthracite_Dark_Navy_1.jpg",
            "https://www.dropbox.com/s/kbfgwae8aakfqcp/05005_Mania%20Eco%20Pants_Blue%20Opal_1.jpg?dl=1"                    => "05005_MANIA_ECO_Pants_Blue_Opal_1.jpg",
            "https://www.dropbox.com/s/w01sm2klhbs2as7/05005_Mania%20Eco%20Pants_Turquoise%20Deep%20Lagon_1.jpg?dl=1"       => "05005_MANIA_ECO_Pants_Turquoise_Deep_Lagon_1.jpg",
            "https://www.dropbox.com/s/b8ztbw9qc7lqgj2/05006_Mania%20Eco%20Shorts_Anthracite%20Dark%20Navy_1.jpg?dl=1"      => "05006_MANIA_ECO_Shorts_Anthracite_Dark_Navy_1.jpg",
            "https://www.dropbox.com/s/1m1u6pysssa9wrv/05006_Mania%20Eco%20Shorts_Blue%20Opal_1.jpg?dl=1"                   => "05006_MANIA_ECO_Shorts_Blue_Opal_1.jpg",
            "https://www.dropbox.com/s/4r8mifs3xtz6akq/05006_Mania%20Eco%20Shorts_Turqouise%20Deep%20Lagon_1.jpg?dl=1"      => "05006_MANIA_ECO_Shorts_Turqouise_Deep_Lagon_1.jpg");
            
            if(!array_key_exists($imgUrl, $images)) return ($imgUrl);           // pokud url neni v poli, vrati ji zpet
            return(self::FAILED_IMAGES_PATH .$images[$imgUrl]);                                           // pokud tam je, vrati opravenou hodnotu
            
        }
        
        // stahne obrazky sem na server. Pouzivam pouze pokud je nejaky problem se stazenim obrazku pri importu na shoptet
        public function getImages($maxPer){
            $source = file_get_contents(self::OCUN_CSV_TO_IMPORT);
            
            $radky = explode(PHP_EOL, $source);
            unset($radky[0]);
            
            $i = 0;
            $errorCount = 0;
            foreach ($radky as $radek) {
                $items = explode(';', $radek);
                echo $items[0] .' - ';
                
                if(!file_exists('/var/www/html/2climb/images/' .$items[0] .'.jpg')){
                    $file = @file_get_contents($items[6]);    
                    if($file != '') {
                        file_put_contents('/var/www/html/2climb/images/' .$items[0] .'.jpg', $file); 
                        echo 'ukladam<br>';
                    } else {
                        $errorCount++;
                        echo $items[6] .' - error<br>';
                    }
                } else {
                    echo 'ulozeno<br>'; 
                }
                
                $i++;
                if($i >= $maxPer) return;
            }
            
            echo 'Pocet chyb: ' .$errorCount;
        }
        
    }