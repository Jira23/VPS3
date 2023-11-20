<?php

/**
 * API DRAK JSON
 * Čisté volání API DRAK přes JSON pomocí nativních metod PHP
 */
class JsonSklik extends Logging{

    /**
     * Session - Po přihlášení je vyžadované u každého volání pro autentizaci
     * @var string 
     */
    protected $session = '';
    
    /**
     * URL pro volání dotazu - API verze DRAK
     * @var string 
     */
    protected $url = 'https://api.sklik.cz/drak/json/';
    public $newGroupsIds;                                                       // sem se ukladaji odpovedi sKlik API, pokud volam groups.create
    
    /**
     * Zavolání XML dotazu
     * @param string $url - kompletní adresa
     * @param string $request - XML dotaz (ve formátu XML-RCP)
     * @return string
     */
    protected function call($url, $request) {
        $header[] = "Content-type: application/json";
        $header[] = "Content-length: " . strlen($request);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);                                   //timeout in seconds

        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        } else {
            curl_close($ch);
            return $data;
        }
    }
    
    
    //Přihlášení uživatele pomocí uživatelského jména a hesla
    public function login() {
        $ses = array();
        $request = json_encode(
            //array('0x7c5ea0200805756451ab45c3b80b89e62697a771f1563ffa642e7dcdc1058d308d603-JiriMisik@seznam.cz')
                array('0x95cd8f307b26df4bf8c6a64604d8cad742c6fc6cf8f22e5e8e80a2181874ea41f44a4-testsklikimp@seznam.cz')
        );
        $response = $this->call($this->url.'/client.loginByToken', $request);        
        $response = json_decode($response);
        if(isset($response->session)) {
            $this->session = $response->session;            
        }
    }
    
    
    // vrati Groupy kampane
    public function getCampaignGroups($campaignId) {
        $to_return = array();
        $offset = 0;
        do {                
            $method = 'groups.list';
            $request = json_encode(
                array(
                    array('session'=>$this->session),
                    array('campaign' => array('ids' =>array($campaignId))),
                    array(
                        'offset' => $offset,
                        'limit' => 5000,
                        'displayColumns' => array('id', 'name', 'maxCpc', 'deleted')
                    )                
                )
            );  

            $response = $this->call($this->url.'/'.$method, $request);
            $decdedResponse = json_decode($response);

            if($decdedResponse->status === 200) {
                foreach ($decdedResponse->groups as $key => $group) {
                    $to_return[$group->id]['name'] = $group->name;
                    $to_return[$group->id]['maxCpc'] = $group->maxCpc;
                    $to_return[$group->id]['deleted'] = $group->deleted;
                }
            } else {
                var_dump($response);
                exit;
            }
            $offset = $offset + 5000;
            
        } while ($offset == count($to_return));
        return ($to_return);            
    }

    public function getCampaignAds($campaignId) {
        $to_return = array();
        $offset = 0;
        do {        
            $method = 'ads.list';
            $request = json_encode(
                array(
                    array('session'=>$this->session),
                    //array('group' => array('ids' =>$groupsId)),
                    array('campaign' => array('ids' =>array($campaignId))),
                    array(
                        'offset' => $offset,
                        'limit' => 5000,
                        'displayColumns' => array('id', 'headline1', 'headline2', 'headline3', 'description', 'description2', 'finalUrl', 'deleted', 'group.id')
                    )                
                )
            );  

            $response = $this->call($this->url.'/'.$method, $request);
            $decdedResponse = json_decode($response);

            if($decdedResponse->status === 200) {
                $to_return = array_merge($to_return, $decdedResponse->ads);
                //var_dump($to_return);
            } else {
                var_dump($response);
                exit;
            }
            
            $offset = $offset + 5000;
        } while ($offset == count($to_return));
        return ($to_return);
    }
    
    public function getCampaignKWs($campaignId) {
        
        $to_return = array();
        $offset = 0;
        $error = 0;
        do {
            $method = 'keywords.list';
            $request = json_encode(
                array(
                    array('session'=>$this->session),
                    //array('group' => array('ids' =>$groupsIds)),
                    array('campaign' => array('ids' =>array($campaignId))),
                    array(
                        'offset' => $offset,
                        'limit' => 5000,
                        'displayColumns' => array('id', 'name', 'matchType', 'deleted', 'group.id')
                    )                
                )
            );  

            $response = $this->call($this->url.'/'.$method, $request);
            $decdedResponse = json_decode($response);

            if($decdedResponse->status === 200) {
                $to_return = array_merge($to_return, $decdedResponse->keywords);
                $error = 0;
            } else {
                echo 'error<br>';
                var_dump($response);                
                $error++;
                $offset = $offset - 5000;
                if($error >= 5) exit;
            }
            
            echo $offset = $offset + 5000;
            echo PHP_EOL;
        } while ($offset == count($to_return));
        return ($to_return);
    }     
    
    // vytahne z skliku seznam vsech reklam a upravi odpoved tak, aby v klici pole bylo id groupy a pod nim v poli reklamy
    public function modifyCampaignAds($campaignId){
        $campaign_ads = $this->getCampaignAds($campaignId);            // nactu seznam reklam z skliku
//        var_dump($campaign_ads);
        $sklikCampaignAds = array();
        foreach ($campaign_ads as $key => $ad) {                                  // projizdim postupne vsechny reklamy...
            $group_id = $ad->group->id;                                         // zapisu si id groupy do promenne
            unset($ad->group);                                                  // vyhodim id groupy z objektu
            $sklikCampaignAds[$group_id][] = $ad;                                 // zapisu data o reklame
            //echo $group_id .'<br>';
        }        

        return($sklikCampaignAds);
    }
    
    // vytahne z skliku seznam vsech reklam a upravi odpoved tak, aby v klici pole bylo id groupy a pon nim v poli reklamy
    public function modifyCampaignKWs($campaignId){
        $campaign_KWs = $this->getCampaignKWs($campaignId);            // nactu seznam reklam z skliku
        $sklikCampaignKWs = array();
        foreach ($campaign_KWs as $key => $KW) {                                // projizdim postupne vsechny reklamy...
            $group_id = $KW->group->id;                                         // zapisu si id groupy do promenne
            unset($KW->group);                                                  // vyhodim id groupy z objektu
            $sklikCampaignKWs[$group_id][] = $KW;                               // zapisu data o reklame
        }        

        return($sklikCampaignKWs);
    }
    
    // upravi odpoved z sKlik API
    public function filterResponse($response){
        // seznam hlasek, ktera nepotrebuji zobrazit do logu
        $no_show = array('missing_space_after_dot', 'missing_space_after_comma', 'consecutive_two_and_more_uppercase', 'missing_space_after_hyphen', 'missing_space_before_hyphen');
        
        if($response->status === 200) {                                         // odpoved OK
            return $response->statusMessage;
        } else if($response->status === 206) {                                  // odpoved partialy OK
            foreach ($response->diagnostics as $key => $value) {                // prevedu jednotliva chybova hlaseni do pole
                $to_return[] =  $value->id;
            }
            $to_return = implode('', $to_return);                               // udelam z pole string
            $to_return = str_replace($no_show, '', $to_return) .'OK';           // ze stringu odstranim hlaseni, ktera nepotrebuji zobrazit do logu
            return($to_return);
        } else {
            var_dump($response);
            //return $response .'<br><br>';
        }
    }    
    
    // obecny request, ktery funguje na metody s urcitou spolecnou strukturou. Fce umi poslat vice requestu, pokud pocet parametru prekroci max. limit pro jedno volani sKlik API
    public function generalRequest($method, $params, $lg = '') {
        $item_limit = 99;
        $this->newGroupsIds = array();                                          // pokud je request na vytvoreni novych groups, vrati jejich Id. Ta zapisuji do teto promenne
        
        $chunked = array_chunk($params, $item_limit);                           // rozseknu pole na kusy s max. povolenym poctem items pro request
        
        $page = 1;
        foreach ($chunked as $key => $chunk) {                                  // projizdim postupne vsechny kusy pole
            $request = json_encode(                                             // sestavim request
                array(
                    array('session'=>$this->session),
                    $chunk
                )
            );
            
            $response = json_decode($this->call($this->url.'/'.$method, $request));                                                     // dekoduji odpoved
            if(isset($response->groupIds)) $this->newGroupsIds = array_merge ($this->newGroupsIds, $response->groupIds);                // pokud jsou v odpovedi groupIds, pridam je do pole - jedna se o odpoved na request vytvoreni novych groups )
            $filtered_response = $this->filterResponse($response);                                                                      // odfiltruji odpoved
            if($lg != '') $lg->doLog('Page:' .$page .' - Items:' .count($chunk) .' - Response:' .$filtered_response);                        // zaloguji odpoved
            $page ++;
        }
        
        return $response;
    }             

}

?>