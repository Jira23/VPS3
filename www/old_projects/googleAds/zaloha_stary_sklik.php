<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once '/var/www/core/lib/Sklik/JSON_clean.php';
    include_once '/var/www/core/lib/knihovnaCore.php';
    include_once '/var/www/core/config.php';
    
    session_start();

    $campaignId = 1975665;                                                      // Id kampane sklik
    
    $lg = new Logging;
    $lg->setLogging();
    
    // nactu xml data z winstona
    $source = file_get_contents('/var/www/core/short_example.xml');
    $data = new SimpleXMLElement($source);
    
    // nactu data z skliku
    $cc = new JsonSklik();
    $cc->login();                                                               // zaloguju se na sklik
    $sklikGroups = $cc->getCampaignGroups($campaignId);                         // nactu seznam skupin na skliku do pole
    $lg->doLog('Spoustim prevod...<br>');

    
    $i = 0;
    $sum_time = 0;
    foreach ($data as $key => $group) {                                                                                             // projizdim postupne kazdou skupinu v xml
        $start_time = microtime(true);
        $toChange = array();
        
        if ($i >= 3) break;
        $i++;
    
        $lg->doLog('Group: ' .(string)$group['GroupName']);

        $sklikGroupId = findSklikId($sklikGroups, (string)$group['GroupName']);                                                     // hledam, zda je jmeno skupiny z xml i na skliku
        $sklikGroupMaxCpc = str_replace(',', '.', $group->maxCPA[0]) * 100;                                                         // prevedu CPA z winstona na CPC skliku
        
        if($sklikGroupId){                                                                                                          // pokud je jmeno skupiny z xml i na skliku, pak edituji skupinu

            if($sklikGroups[$sklikGroupId]['deleted']){                                                                             // pokud je group smazana, obnovim ji
                $response = $cc->generalRequest('groups.restore', array($sklikGroupId));
                $lg->doLog('-->Restoring Group...' .$cc->filterResponse($response));
            }
            
            if($sklikGroups[$sklikGroupId]['maxCpc'] !== (int)$sklikGroupMaxCpc){                                                   // pokud se lisi CPA winston od CPC sklik, zmenim CPC na skliku
                $response = $cc->generalRequest('groups.update', array(array('id' => $sklikGroupId, 'cpc' => (int)$sklikGroupMaxCpc)));
                $lg->doLog('-->Changing maxCpc...' .$cc->filterResponse($response));
            }
            
            $toChange = adsToChange($group, $cc->getGroupAds($sklikGroupId));                                                       // pripravim 

            // smazu reklamy
            if(!empty($toChange['toDelete'])) {
                $response = $cc->generalRequest('ads.remove', array_values($toChange['toDelete']));
                $lg->doLog('-->Removing Ads...' .$cc->filterResponse($response));                
            }
            
            // obnovim smazane reklamy
            if(!empty($toChange['toRestore'])) {
                $response = $cc->generalRequest('ads.restore', array_values($toChange['toRestore']));
                $lg->doLog('-->Restoring Ads...' .$cc->filterResponse($response));                                
            }
            
        } else {                                                                                                                    // jinak vytvorim novou skupinu a vlozim do ni reklamy
            
            $group_params = array(
                    'campaignId' => $campaignId,
                    'name' => (string)$group['GroupName'],
                    'cpc' => (int)$sklikGroupMaxCpc
                );
            
            
            if(!empty($group_params)){
                $sklikGroupId = $cc->createGroup($group_params)[0];
                $lg->doLog('-->Creating Group Id:' .$sklikGroupId);
            }            
            
            $toChange = adsToChange($group, $cc->getGroupAds($sklikGroupId));                                                       // pripravim reklamy k vlozeni
            
        }
        
        
        // vlozi klicova slova
        $groupKW = $group->BW_KEYWORDS->KW;
        //var_dump($groupKW);
        
        if(!empty($groupKW)){

            $kw_params = array();
            foreach ($groupKW as $key2 => $keyWords) {
                $kw_params[] = array(
                    'name' => (string)$keyWords,
                    'matchType' => strtolower((string)$keyWords['MatchType'])
                );                
            }
            $response = $cc->setKeywords($kw_params, $sklikGroupId);            
            $lg->doLog('->Setting keywords...' .$cc->filterResponse($response));
        }
        
        // vlozi reklamy
        $j = 0;
        $ad_params = array();
        foreach ($group->BW_EXPANDED_ADS->EXPANDED_AD as $key3 => $ads) {
            if(in_array($j, $toChange['toAdd'])){
                $ad_params[] = array(
                    'groupId' => $sklikGroupId,
                    'adType' => 'eta',
                    'headline1' => (string)$ads->Headline1,
                    'headline2' => (string)$ads->Headline2,
                    'headline3' => (string)$ads->Headline3,
                    'description' => (string)$ads->Desc1,
                    'description2' => (string)$ads->Desc2,
                    'finalUrl' => (string)$group->URL
                );
            }
            $j++;
        }

        if(!empty($ad_params)){
            //echo '***<br><br>';
            //var_dump($ad_params);
            //echo '***<br><br>';
            $response = $cc->createExpandedAds($ad_params);
            $lg->doLog('-->Creating ads...' .$cc->filterResponse($response));
        }

        
        $groups_names[] = (string)$group['GroupName'];
        
        $end_time = round((microtime(true) - $start_time) * 1000);
        $sum_time += $end_time;
        
        $lg->doLog(' time:' .$end_time .'ms<br>');
    }

    $lg->doLog('Celkovy cas:' .$sum_time .'ms<br>');
    
    
    //var_dump($groups_names);
    //echo '<br>';
    
    foreach ($sklikGroups as $key4 => $sklikGroup) {
        if(!in_array($sklikGroup['name'], $groups_names) && !$sklikGroup['deleted']){
            //echo '*' .$sklikGroup['name'] .'<br>';
            $to_remove[] = $key4;
        }
        //echo '<br>';
    }
        
    
    if(!empty($to_remove)) {
        $response = $cc->generalRequest('groups.remove', array_values($to_remove));
        $lg->doLog('-->Removing groups...' .$cc->filterResponse($response));                                
    }
    
    
    
    /// !!!!!!!!!!!!!!!!!!!!!!!! LZE UDELAT HROMADNY REQUEST NA SMAZANI A VLOZENI VSECH REKLAM, KLICOVYCH SLOV, SKUPIT ATD? NEBUDE PROBLEM MAX. DELKA REQUESTU?
    /// !!!!!!!!!!!!!!!!!!!!!!!! TO SAME S NACITANIM. MUZU NACIST NA JEDEN REQUEST VSECHNY REKLAMY A TY PAK ZPRACOVAT?
    /// dalsi zrychleni: nedavat promenne ve fci getCampaignGroups do pole a nechat v objektu
    
    //*********************************
    //***** DODELAT MAZANI GROUPS *****
    //*********************************


    
    
exit;  
 

/*    
$JSON = $response;
    
$jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($JSON, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);

foreach ($jsonIterator as $key => $val) {
    if(!is_array($val)) {
        if($key == "status") {
            print "<br/>";
        }
    print $key."    : ".$val . "<br/>";
    }
}    
*/

?>


