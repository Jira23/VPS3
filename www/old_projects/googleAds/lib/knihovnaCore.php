<?php

    //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // alternativa pro xmlParser, ktery zere pamet - rucni rozparsovani xml z Winstona - DODELAT
    //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

class test {
    
    public function loadBW(){
        
        $source = file_get_contents('/var/www/core/short_example.xml');
        
        $radky = explode(PHP_EOL, $source);
        
        var_dump(count($radky));
        
        $x = 0;
        foreach ($radky as $key => $radek) {
            if(strpos($radek, '<BW_SHOPITEM')){
                $groups[$x]['GroupName'] = str_replace(array('<BW_SHOPITEM GroupName="', ''), '">', $radek);
                
                $i = 0;
                do{
                    if(strpos($radky[$key + $i], '<Name>')) $groups[$x]['Name'] = $radky[$key + $i];
                    if(strpos($radky[$key + $i], '<URL>')) $groups[$x]['URL'] = $radky[$key + $i];
                    if(strpos($radky[$key + $i], '<Price>')) $groups[$x]['Price'] = $radky[$key + $i];
                    if(strpos($radky[$key + $i], '<maxCPA>')) $groups[$x]['maxCPA'] = $radky[$key + $i];
                    
                    if(strpos($radky[$key + $i], '<BW_KEYWORDS')){
                        $j = 0;
                        do{
                            $groups[$x]['KW'][$j]['MatchType'] = 'dodelat';
                            $groups[$x]['KW'][$j]['Value'] = 'dodelat';
                            $j++;
                        } while (!strpos($radky[$key + $j], '</BW_KEYWORDS'));
                    }
                    
                    if(strpos($radky[$key + $i], '<EXPANDED_AD')){
                        $j = 0;
                        do{
                            if(strpos($radky[$key + $i + $j], '<Headline1>')) $groups[$x]['EXPANDED_AD']['Headline1'] = $radky[$key + $i + $j];
                            if(strpos($radky[$key + $i + $j], '<Headline2>')) $groups[$x]['EXPANDED_AD']['Headline2'] = $radky[$key + $i + $j];
                            if(strpos($radky[$key + $i + $j], '<Desc1>')) $groups[$x]['EXPANDED_AD']['Desc1'] = $radky[$key + $i + $j];
                            $j++;
                        } while (!strpos($radky[$key + $i + $j], '</EXPANDED_AD'));
                    }                            
                    echo htmlspecialchars($radky[$key + $i]) .'<br>';    
                    
                    $i++;
                    if($i > 1000) break;
                } while (!strpos($radky[$key + $i], '</BW_SHOPITEM'));
                $x++;
            }
        }
        
        var_dump($groups);
        
    }
    
    public function parse($name){
        
    }

}


    // hleda jmeno skupiny z winston ve skupinach z skliku. Pokud najde, vrati sklikId skupiny, jinak vrati false
    function findSklikId($sklikGroups, $winstonGroupName) {
        foreach ($sklikGroups as $key => $sklikGroup) {
            if($sklikGroup['name'] === $winstonGroupName) return $key;
        }
        return (false);
    }

    // nacte data z feedu z Blue Winston
    function BW_data_load($url){
        $reader = new XMLReader;
        $doc = new DOMDocument;
        
        $reader->open($url);
        
        while($reader->read()) {
          if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'BW_SHOPITEM') {
              $data[] =  simplexml_import_dom($doc->importNode($reader->expand(), true));
          }
        }
        $reader->close();
        
        return $data;
    }
    
    // kontroluje spravnost xml feedu z Blue Winstona
    function BW_data_check($data){
        
        // kontrola duplicitnich nazvu skupin
        foreach ($data as $key => $group) {                                     // projizdim postupne kazdou skupinu v xml
            $group_names[] = $group['GroupName'] .'<br>';                       // a jeji nazev ulozim do pole
        }

        if(count($group_names) !== count(array_unique($group_names))){          // pokud je pocet nazvu v poli jiny nez pocet nazvu v poli po odstraneni duplicit, pak...
            echo 'Duplicate group names found!';                                //vyhodim chybu a ukoncim proces
            exit;
        }
        
    }

    class KW {
        
        public $KWSet = array();                                                                                    // promenna, ze ktere se budou brat KW pro vlozeni na sklik
        public $KWAdd = array();                                                                                 
        public $KWRestore = array();                                                                                 
        public $KWDelete = array();                                                                                 
        public $sklikCampaignKWs;
        
        // porovna klicova slova z winstona a skliku, pokud najde rozdil, zapise do promennych
        public function compareKW($groupKW, $sklikGroupId){
            $winston_KW = $this->convertBWKW($groupKW);                                                             // prevedu KW z Winstona do podoby jako jsou na skliku

            if(!isset($this->sklikCampaignKWs[$sklikGroupId])) $this->sklikCampaignKWs[$sklikGroupId] = array();    // pokud index neexistuje, pak nejsou na skliku zadna KW (sKlik API nevrati id skupiny, kde nejsou KW), vytvorim index a vyplnim ho prazdnym polem

            $to_compare = array();            
            $i=0;
            foreach ($this->sklikCampaignKWs[$sklikGroupId] as $key => $value) {                                    // projizdim postupne vsechny radky KW z skliku
                $to_compare[$i] = array(                                                                            // prevedu jej do pole pro porovnani
                    'name' => (string)$value->name,
                    'matchType' => (string)$value->matchType
                );                
                if($value->deleted){                                                                                // pokud KW je smazane, pak...
                    if(in_array($to_compare[$i], $winston_KW)) $this->KWRestore[] = $value->id;                     // ... pokud je KW na Winstonovi, zaradim ho do Restore
                } else {                                                                                            // jinak...
                    if(!in_array($to_compare[$i], $winston_KW)) $this->KWDelete[] = $value->id;                     // ...pokud neni KW na Winstonovi, zaradim ho do Delete
                }
                $i++;
            }      
            
            // projedu KW na Winstonovi, pokud nektere z nich neni na sKliku, zaradim ho do Add
            foreach ($winston_KW as $key => $value) {
                if(!in_array($value, $to_compare)) $this->KWAdd[] = array_merge($value, array('groupId' => $sklikGroupId));
            }
             
        }        
        
        // prevede KW z Winstona do podoby jako jsou na skliku
        public function convertBWKW($groupKWs){
            if(empty($groupKWs)) return array();                                                                            // pokud je vstupni pole prazdne, nevratim nic
            $kw_params = array();
            foreach ($groupKWs as $key => $keyWords) {                                                              // projizdim postupne data a upravuji je
                $kw_params[] = array(
                    'name' => (string)$keyWords,
                    'matchType' => strtolower((string)$keyWords['MatchType'])
                );                
            }
            return $kw_params;
        }
    }
    
    class Ads {
        
        public $sklikCampaignAds;
        public $adsAdd = array();
        public $adsRestore = array();
        public $adsDelete = array();
        
        public function adsToChange($group, $sklikGroupId) {
            
            // mazani ads, pokud je groupa v Winstonovi bez reklam
            if(empty($group->BW_EXPANDED_ADS->EXPANDED_AD)){                                        // pokud je groupa ve Winstonovi bez reklam, pak...
                if(!empty($this->sklikCampaignAds[$sklikGroupId])){                                   // ...pokud jsou nejake reklamy na skliku, pak...
                    foreach ($this->sklikCampaignAds[$sklikGroupId] as $key7 => $value) {             // ...projizdim postupne vsechny ads z skliku, pro danou groupu
                        if(!$value->deleted) $this->adsDelete[] = $value->id;                       // ...pokud neni ads vedena jako vymazana, zapisu ji do pole. Vsechny ads z tohoto pole budou vymazany
                    }
                    return;
                }
                return;                                                                             // neudelam nic
            }
            
            // upravim reklamy z winstona do stejne podoby jako jsou na skliku, abych je mohl porovnat
            $i = 0;
            foreach ($group->BW_EXPANDED_ADS->EXPANDED_AD as $key4 => $gr_ad) {
                $modifAds[$i]['groupId'] = $sklikGroupId;
                $modifAds[$i]['headline1'] = (string)$gr_ad->Headline1;
                $modifAds[$i]['headline2'] = (string)$gr_ad->Headline2;
                $modifAds[$i]['headline3'] = (string)$gr_ad->Headline3;
                $modifAds[$i]['description'] = (string)$gr_ad->Desc1;
                $modifAds[$i]['description2'] = (string)$gr_ad->Desc2;
                $modifAds[$i]['finalUrl'] = (string)$group->URL;
                $i++;
            }
            
            // vytahnu reklamy z skliku pro dannou groupu
            if(!isset($this->sklikCampaignAds[$sklikGroupId])) $this->sklikCampaignAds[$sklikGroupId] = array();    // pokud index neexistuje, pak nejsou na skliku zadne Ads (sKlik API nevrati id skupiny, kde nejsou ads), vytvorim index a vyplnim ho prazdnym polem            
            $modifSklikAds = json_decode(json_encode($this->sklikCampaignAds[$sklikGroupId]), true);

            $toDelete = array();
            $toKeep = array();
            $toRestore = array();            
            
            foreach ($modifSklikAds as $key5 => $value5) {                      // projizdim postupne reklamy z skliku
                $id = $value5['id'];                                            // ulozim si id reklamy do promenne
                $deleted = $value5['deleted'];                                  // ulozim si udaj o smazanosti(viditelnosti) reklamy do promenne
                unset($value5['id']);                                           // udaj o id smazu z pole, aby se mi tam v dalsim kroku neplet
                unset($value5['deleted']);                                      // udaj o smazani (viditelnosti) smazu z pole, aby se mi tam v dalsim kroku neplet
                foreach ($modifAds as $key6 => $value6) {                       // projizdim postupne reklamy z winstona
                    if(empty(array_diff($value5, $value6))) {                   // porovnam reklamy, pokud array_diff vrati prazdne pole, reklamy se shoduji
                        if($deleted){                                           // pokud je reklama smazana...
                            $toRestore[$key6] = $id;                            // zapisu ji do pole toRestore
                        }else{
                            $toKeep[$key6] = $id;                               // zapisu id reklamy do pole toKeep, v klici je klic (poradi) reklamy ve winstonovi
                        }
                    } else {                                                    // pokud ne, reklamy se neshoduji
                        if(!$deleted) $toDelete[] = $id;                        // neni reklama smazana, zapisu id reklamy do pole. V podstate vytvorim pole, ve kterem budou vsechna id nesmazanych reklam a nektera duplicitne. Po dokonceni smycky upravim tak, aby zde byla jen id k smazani
                    }
                }
            }
            
            $toDelete = array_unique($toDelete);                                // vymazu duplicity
            $toDelete = array_diff($toDelete, $toKeep);                         // vymazu id, ktere nechci smazat
            $toDelete = array_diff($toDelete, $toRestore);                      // vymazu id, ktere nechci smazat

            $toAdd = array_diff_key($modifAds, array_flip(array_merge(array_keys($toKeep), array_keys($toRestore))));   // najdu reklamy, ktere na skliku nejsou.

            
            //return(array('toDelete' => $toDelete, 'toRestore' => $toRestore, 'toAdd' => $toAdd));    
            
            //$this->adsAdd[$sklikGroupId] = $toAdd;
            $this->adsAdd = array_merge($this->adsAdd, $toAdd);
            $this->adsRestore = array_merge($this->adsRestore, $toRestore);
            $this->adsDelete = array_merge($this->adsDelete, $toDelete);

            
        }
    }
    
    

?>

