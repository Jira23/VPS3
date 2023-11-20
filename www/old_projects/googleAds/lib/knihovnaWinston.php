<?php

    /**
     * API WINSTON JSON
     * Čisté volání API WINSTON přes JSON pomocí nativních metod PHP
     */
    class JsonWinston {

        /**
         * $access_token - Po přihlášení je vyžadované u každého volání pro autentizaci
         * @var string 
         */
        protected $access_token = '';

        /**
         * URL pro volání dotazu
         * @var string 
         */
        protected $url = 'https://setup.bluewinston.com/api/';

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

            $data = curl_exec($ch);
            if (curl_errno($ch)) {
                return curl_error($ch);
            } else {
                curl_close($ch);
                return $data;
            }
        }

        /**
         * Přihlášení uživatele pomocí uživatelského jména a hesla
         */
        public function login() {
            $request = json_encode(array('api_client_id' => '8496abdc-26f4-4985-a68c-4278049c0073', 'refresh_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1laWQiOiI4NDk2YWJkYy0yNmY0LTQ5ODUtYTY4Yy00Mjc4MDQ5YzAwNzMiLCJwcmltYXJ5Z3JvdXBzaWQiOiIxIiwibmJmIjoxNjAxODExNDAzLCJleHAiOjQ3NTc0ODUwMDMsImlhdCI6MTYwMTgxMTQwM30.3mjfZX0qzM4E_rW8PJIjwfrJYyNXkf8-Sjj5tX65yHI'));
            $response = $this->call($this->url.'/getaccesstoken', $request);        
            $response = json_decode($response);
            //var_dump($response);
            if(isset($response->data->access_token)) {
                $this->access_token = $response->data->access_token;            
            }
        }

        
        public function GetAdWordsCustomerInfo() {

            $method = 'getadwordscustomerinfo';
            $request = json_encode(array('adwords_customer_id' => '589-936-0334', 'access_token' => $this->access_token));
            //var_dump($request);
            
            $response = $this->call($this->url.'/'.$method, $request);
            //return $response;
            return json_decode($response);
        }                
        
        
        
        public function VerifyAdWordsCustomerSecretKey() {

            $method = 'verifyadwordscustomersecretkey';
            $request = json_encode(array('adwords_customer_id' => '559-679-1676', 'secrect_key' => '6AbaxKpVsCA9yVUyARD8DlG6jhEfowU4Ol2BSVEX8U8Nko0v', 'access_token' => $this->access_token));
            //var_dump($request);
            
            $response = $this->call($this->url.'/'.$method, $request);
            //return $response;
            return json_decode($response);
        }        
        
        

        public function getCampaignList($adwords_customer_id) {

            $method = 'getcampaignlist';
            $request = json_encode(array('access_token' => $this->access_token, 'adwords_customer_id' => $adwords_customer_id));
            //var_dump($request);
            
            $response = $this->call($this->url.'/'.$method, $request);
            //return $response;
            return json_decode($response);
        }
    }

?>