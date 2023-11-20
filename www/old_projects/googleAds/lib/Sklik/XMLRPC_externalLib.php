<?php

/**
 * API DRAK XML-RPC
 * API DRAK přes XML-RPC pomocí s použitím externí knihovny na práci s XML
 * !!Řetězec požadavku, musí začínat <?xml version=\"1.0\" ?> a nesmí tomu předcházet mezery 
 */
class XmlRpcSklik {

    /**
     * Session - Po přihlášení je vyžadované u každého volání pro autentizaci
     * @var string 
     */
    protected $session = '';
    
    /**
     * XMLRPC client (external lib)
     * @var xmlrpc_client 
     */   
    protected $client;
    
    /**
     * Setup XMLRPC client
     */
    function __construct() {
        $GLOBALS["xmlrpc_null_extension"] = true; // We need to work with NULL
        $GLOBALS["xmlrpc_internalencoding"] = "UTF-8"; // We work with UTF-8
        $this->client = new xmlrpc_client('/drak/RPC2', 'api.sklik.cz', 443, 'https');
        $this->client->setSSLVerifyHost(0);
        $this->client->setSSLVerifyPeer(false);
    }    
    
    /**
     * Přihlášení uživatele pomocí tokenu
     */
    public function login() {
        $args = array('0x7c5ea0200805756451ab45c3b80b89e62697a771f1563ffa642e7dcdc1058d308d603-JiriMisik@seznam.cz');
        $method = 'client.loginByToken';
        $encoded = array_map("php_xmlrpc_encode", $args);

        $msg = new xmlrpcmsg($method, $encoded);
        $response = php_xmlrpc_decode($this->client->send($msg)->value());
        if(isset($response['session'])) {
            $this->session = $response['session'];
        }
    }
    /**
     * Volání metody. 
     * @return string
     */
    public function request() {
        $this->login();
        $method = 'client.stats';
        $date = new DateTime();
        $args = array(
            array('session'=>$this->session),
            array(
                'dateFrom' => $date->setTimestamp(mktime(0, 0, 0, 1, 1, 2012)),
                'dateTo' => $date->setTimestamp(mktime(0, 0, 0, 1, 1, 2012)),
                'granularity' => 'total'
            )
        );        
        $encoded = array_map("php_xmlrpc_encode", $args);
        $msg = new xmlrpcmsg($method, $encoded);
        $response = php_xmlrpc_decode($this->client->send($msg)->value());        
        return $response;
    }
}

?>