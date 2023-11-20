


<?php

/*
[GOOGLE_ADS]
developerToken = "iogmixWUo5SCzQt0gmvPFQ"                                                                                           -- 
loginCustomerId = "3936849593"                                                                                                      -- 

[OAUTH2]
clientId = "505022117156-n1875no0oi5jfuu8vaaj1b4hh9iadb89.apps.googleusercontent.com"                                               -- jiri.nikola
 95706671030-i2bnadgjf9mfa55jvp5s44a5lkoovt7f.apps.googleusercontent.com
clientSecret = "XNoPDsYqFEHXwrnkCKkUyFeK"                                                                                           -- jiri.nikola
 u1jkcJ8hd936ZeFJu2PZRf5W
refreshToken = "1//03SCVhSp5HI5iCgYIARAAGAMSNwF-L9IrBUGKmGoHlOMwKoZL7dFYYJsfBeZDSINdoBMl9V7rY1Zqe7x-cLjsBwo3m_1rIQVPqk4"            -- z fce
 1//03ap5bb9Ta0NqCgYIARAAGAMSNwF-L9IrCX7nB8rst-4r-PEpzxLYH4jRiGi-FjzisJKEgb8SO5UOFH22X6S3dVagZVUc_vy0dXk
 */


    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require '/var/www/core/lib/googleAds/customOps.php';
    require '/var/www/core/lib/googleAds/authentications.php';
    
    session_start();

    //$oauth2 = AuthenticateStandalone::getURL('95706671030-i2bnadgjf9mfa55jvp5s44a5lkoovt7f.apps.googleusercontent.com', 'u1jkcJ8hd936ZeFJu2PZRf5W');
    //$_SESSION['oauth2'] = serialize($oauth2);
  
    //AuthenticateStandalone::getRefreshToken(unserialize($_SESSION['oauth2']), '4/yQFEr3WyPBXT8vGU4NsAvI-Ek9LeJhAIkpYtZsC90MseO2cUs_Gh71M');
    //exit;

    
    
    // !!! upravit hodnotu podle cisla uctu, ktery chci spravovat
    define('CUSTOMER_ID', 6812948011);          //test.jira.freelancer@gamil.com - testovaci vedlejsi
    //define('CUSTOMER_ID', 9264703993);        //jiri.nikola@gmail.com - produkcni hlavni 

    $krajeId = array('20218', '20219', '21494', '21495', '21496', '21497', '21498', '21499', '21500', '21501', '21502', '21503', '21504', '21505');     // id vsech kraju

    $userCampaigns = (CustomOps::getCampaigns(CUSTOMER_ID));
    var_dump($userCampaigns);
    
    //CustomOps::createLocation(CUSTOMER_ID, 9739090241, array(21496, 21496));

    //$locationId = CustomOps::getCampaignLocations(CUSTOMER_ID, 9739090241);
    //var_dump($locationId);    
    
    //CustomOps::removeCampaignLocation(CUSTOMER_ID, '9739090241', $locationId);

    //$locationInfo = CustomOps::getGeoTargetConstantById($locationId);
    //var_dump($locationInfo);    
    
    
    
/*
    20218-South Bohemian Region
    20219-South Moravian Region
    21494-Central Bohemian Region
    21495-Hradec Kralove Region
    21496-Karlovy Vary Region
    21497-Liberec Region
    21498-Moravian-Silesian Region
    21499-Olomouc Region
    21500-Pardubice Region
    21501-Plzen Region
    21502-Prague
    21503-Usti nad Labem Region
    21504-Vysocina Region
    21505-Zlin Region
*/

    
?> 
