<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
exit;    
    ini_set('memory_limit','500M');
    
    include_once '/var/www/core/lib/knihovnaLogging.php';
    include_once '/var/www/core/lib/Sklik/JSON_clean.php';    
    include_once '/var/www/core/lib/knihovnaCore.php';
    include_once '/var/www/core/lib/knihovnaWinston.php';
    include_once '/var/www/core/config.php';
    


    $dups = array(532616117, 532616116, 532618867, 532618866, 532619009, 532619008, 532619210, 532619209, 532619348, 532619347, 532619308, 532620304, 532620303, 532621352, 532621512, 532621511, 532622038, 532622037, 532622566);
/*    
1-    
    Neúspech neexistuje existujú
    Neúspech neexistuje… existujú
    
2- Audiokniha Val’s Diary    
   Audiokniha Val s Diary
*/
    
/*
    $file = file_get_contents('http://static.audioteka.com/cz/integration/sklik.xml');
    file_put_contents('/var/www/core/logs/audioteka.xml', $file);
    
    exit;

    $bw = new JsonWinston();
    $bw->login();                                                               // zaloguju se na sklik
    var_dump($bw->VerifyAdWordsCustomerSecretKey());
    //var_dump($bw->GetAdWordsCustomerInfo());
    echo '<br><br>';

    $a = $bw->getCampaignList('559-679-1676');
    //var_dump($a);
    
    foreach ($a->data as $key => $value) {
        var_dump($value);
        echo '<br><br>';
    }
    
    
    //http://static.audioteka.com/cz/integration/sklik.xml

    
    //https://www.knihcentrum.cz/XmlFeed/GenerateFeed/UTKNQEYX54D2GKT4ZUZ0WETQ1IMHQAEWD
    //https://www.knihcentrum.cz/xmlfeed/generatefeed/utknqeyx54d2gkt4zuz0wetq1imhqaewd
    //https://www.knihcentrum.cz/XmlFeed/GenerateFeed/3HZH43XGMJBQV34PYMWZTNIEGO32JRPPM
    //http://asdata.sk/service/unzipper/?url=https://www.knihcentrum.cz/XmlFeed/GenerateFeed/3HZH43XGMJBQV34PYMWZTNIEGO32JRPPM
    //https://www.knihcentrum.cz/XmlFeed/GenerateFeed/UTKNQEYX54D2GKT4ZUZ0WETQ1IMHQAEWD
    //https://www.knihcentrum.cz/XmlFeed/GenerateFeed/UTKNQEYX54D2GKT4ZUZ0WETQ1IMHQAEWD
    
    exit;
*/    
    session_start();

    $campaignId = 2417106;                                                      // Id kampane sklik
    
    // spustim logovani
    $lg = new Logging;
    $lg->setLogging();

    // nactu xml data z winstona
    $lg->doLog('Loading Winston data...');
    $data = BW_data_load('/var/www/core/feed_example.xml');

    // zkontoluju xml data z winstona
    $lg->doLog('Checking Winston data...');
    BW_data_check($data);
    
    // nactu data z skliku
    $cc = new JsonSklik();
    $cc->login();                                                               // zaloguju se na sklik

    $lg->doLog('Loading sKlik Groups...');    

    $sklikGroups = $cc->getCampaignGroups($campaignId);                         // nactu seznam skupin na skliku do pole
    $lg->doLog('Loading sKlik Ads...');    
    $aa = new Ads();
    $aa->sklikCampaignAds = $cc->modifyCampaignAds($campaignId);                // nactu seznam reklam na skliku a upravim jej

    $lg->doLog('Loading sKlik Keywords...');    
    $KW = new KW();
    $KW->sklikCampaignKWs = $cc->modifyCampaignKWs($campaignId);                // nactu seznam klicovych slov na skliku a upravim jej

    //$cc->generalRequest('campaigns.create', array(array('name' => 'test5', 'dayBudget' => 10000, 'type' => 'fulltext')));                           // vytvori kampan
    

    //var_dump($sklikGroupsKWs);
    //var_dump($data);
    //var_dump(array_keys($sklikGroups));

    $lg->doLog('Starting loop...');
    $i = 0;
    foreach ($data as $key => $group) {                                                                                             // projizdim postupne kazdou skupinu v xml
        
        $toChange = array();
        
        if ($i >= 5000) break;
        $i++;
    
//        $lg->doLog('Group: ' .(string)$group['GroupName']);

        $sklikGroupId = findSklikId($sklikGroups, (string)$group['GroupName']);                                                                                         // hledam, zda je jmeno skupiny z xml i na skliku
        $sklikGroupMaxCpc = str_replace(',', '.', $group->maxCPA[0]) * 100;                                                                                             // prevedu CPA z winstona na CPC skliku
        
        if($sklikGroupId){                                                                                                                                              // pokud je jmeno skupiny z xml i na skliku, pak edituji skupinu

            if($sklikGroups[$sklikGroupId]['deleted']) $groups_to_restore[] = $sklikGroupId;                                                                            // pokud je group smazana, obnovim ji
            if($sklikGroups[$sklikGroupId]['maxCpc'] !== (int)$sklikGroupMaxCpc) $groups_to_update[] = array('id' => $sklikGroupId, 'cpc' => (int)$sklikGroupMaxCpc);                                                   // pokud se lisi CPA winston od CPC sklik, zmenim CPC na skliku

            $aa->adsToChange($group, $sklikGroupId);                            // pripravim reklamy
            $KW->compareKW($group->BW_KEYWORDS->KW, $sklikGroupId);             // pripravi klicova slova
            
        } else {                                                                                                                                                        // jinak vytvorim novou skupinu
            // pripravim data pro vytvoreni skupiny
            $group_params = array(
                    'campaignId' => $campaignId,
                    'name' => (string)$group['GroupName'],
                    'cpc' => (int)$sklikGroupMaxCpc
                );
            
            $groups_to_create[] = $group_params;                                // zapisu data do seznamu skupin k vytvoreni
            $groups_to_create_data[] = $group;                                  // zapisu data z winstona do promenne, abych k nim pak mohl pristupovat pri vytvareni reklamy
            
            // pripravim KW k zapisu
            $KW_to_create_data[] = $KW->convertBWKW($group->BW_KEYWORDS->KW);                                                              // prevedu KW z Winstona do podoby jako jsou na skliku            
        }
        
        $groups_names[] = (string)$group['GroupName'];

    }
    
    
    // mazani groups
    foreach ($sklikGroups as $key4 => $sklikGroup) {                                        // projizdim vsechny nazvy skupin na skliku
        if(!in_array($sklikGroup['name'], $groups_names) && !$sklikGroup['deleted']){       // pokud neni nazev v seznamu z winstona a zaroven neni mezi smazana na skliku, pak...
            $groups_to_remove[] = $key4;                                                    // vlozim ji do seznamu skupin ke smazani
        }

    }
 
    // objekty + hromadne
    
    
    // create sklik goups
    if(!empty($groups_to_create)) {
        $lg->doLog('Creating Groups...');
        $cc->generalRequest('groups.create', $groups_to_create, $lg);                                               // vytvorim nove skupiny
        
        // projizdim postupne nove vytvorene skupiny a pripravuji reklamy a KW pro vlozeni do nich
        foreach ($cc->newGroupsIds as $key5 => $new_group_id) {
            $aa->sklikGroupsAds[$new_group_id] = array();                                                           // pridam id skupiny do dat nactenych z skliku, protoze jsem je na skliku prave vytvoril a nebudu to nacitat znova
            $aa->adsToChange($groups_to_create_data[$key5], $new_group_id);                                         // pripravim reklamy k vlozeni

            foreach ($KW_to_create_data[$key5] as $key6 => $new_KW) {                                               // pripravim KW k vlozeni
                $KW->KWAdd[] = array_merge($new_KW, array('groupId' => $new_group_id));                             // ke jmenu a typu KW pridam jeste id prave vytvorene groupy
            }
        }
        
    }

    // remove sklik groups
    if(!empty($groups_to_remove)) {
        $lg->doLog('Removing Groups...');
        $cc->generalRequest('groups.remove', array_values($groups_to_remove));
    }   
   
    // restore sklik goups
    if(!empty($groups_to_restore)) {
        $lg->doLog('Restoring Groups...');
        $cc->generalRequest('groups.restore', $groups_to_restore, $lg);
    }

    // update sklik goups
    if(!empty($groups_to_update)) {
        $lg->doLog('Updating Groups...');
        $cc->generalRequest('groups.update', $groups_to_update, $lg);
    }

    // smazu reklamy
    if(!empty($aa->adsDelete)) {
        $lg->doLog('Removing Ads...');
        $cc->generalRequest('ads.remove', array_values($aa->adsDelete), $lg);
    } 

    // obnovim reklamy
    if(!empty($aa->adsRestore)){
        $lg->doLog('Restoring Ads...');
        $cc->generalRequest('ads.restore', array_values($aa->adsRestore), $lg);
    }
    
    // vytvorim nove reklamy
    if(!empty($aa->adsAdd)){
        $lg->doLog('Creating Ads...');
        $rek = $cc->generalRequest('ads.create', array_values($aa->adsAdd), $lg);
        /*
        echo '------<br><br>';
        //var_dump($rek->diagnostics);
        foreach ($rek->diagnostics as $key => $value) {
            echo $value->dbAdId .'<br>';
        }
         * 
         */

    }

    // vymazu klicova slova
    if(!empty($KW->KWDelete)){
        $lg->doLog('Deleting Keywords...');
        $cc->generalRequest('keywords.remove', $KW->KWDelete, $lg);
    }    
    
    // obnovim klicova slova
    if(!empty($KW->KWRestore)){
        $lg->doLog('Restoring Keywords...');
        $cc->generalRequest('keywords.restore', $KW->KWRestore, $lg);
    }    
    
    // vlozim klicova slova
    if(!empty($KW->KWAdd)){
        $lg->doLog('Creating Keywords...');
        $cc->generalRequest('keywords.create', $KW->KWAdd, $lg);
    }    

    $lg->doLog('Done!');

    //$data = NULL;


?>


