
<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once '/var/www/core/google-api-php-client-2.4.1/vendor/autoload.php';

    session_start();
 
    //unset($_SESSION['access_token']);
    //var_dump($_SESSION['access_token']);
  
    //$a = serialize($_SESSION['access_token']);
    //var_dump(htmlspecialchars($a));
    //echo '<br><br>';
    $token_jiri_nikola = 'a:5:{s:12:"access_token";s:170:"ya29.a0Ae4lvC2RfNaR1Iw6s4fO066nfGCDmJdK7eiLY7BFEM1qyz3LqaY13xqynjFkVVBl-pgcEAvomWhh9RuuVrMzhDTcT_ieFLF_9p15Lg4-M1-iU7QeIEMncaV9f2tnb44kSw9_EGkiEsWnWAoQIpbfD9ofAYUD1GT6dQI";s:10:"expires_in";i:3599;s:5:"scope";s:50:"https://www.googleapis.com/auth/analytics.readonly";s:10:"token_type";s:6:"Bearer";s:7:"created";i:1585830019;}';
    $token_nabytek_safr = 'a:5:{s:12:"access_token";s:170:"ya29.a0Ae4lvC3ilPre3rOEtHJrPqQ5D8wIhd-FC6unyexdjHo9GWsSa3GQ7JFoJCckTtAHrfYFov9i7sKxSuGHRV03b2iGiAhx7x5Jw3Sxb1GyDXr0FHuYxx6i1ilybDAHWThC-5CAHAAZM8goBbchyzj1MqeLfJsi_-HFrQg";s:10:"expires_in";i:3599;s:5:"scope";s:50:"https://www.googleapis.com/auth/analytics.readonly";s:10:"token_type";s:6:"Bearer";s:7:"created";i:1586339187;}';
    $login = unserialize($token_nabytek_safr);

    
    $client = new Google_Client();
    $client->setAuthConfig('/var/www/core/google-api-php-client-2.4.1/client_secrets.json');
    $client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);
    
//    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

      //$client->setAccessToken($_SESSION['access_token']);
      $client->setAccessToken($login);
      $client->setAccessType('offline');
      $client->setApprovalPrompt("consent");
      $client->setIncludeGrantedScopes(true);   // incremental auth

      // Create an authorized analytics service object.
      $analytics = new Google_Service_AnalyticsReporting($client);
      $response = getReport($analytics);
      printResults($response);
/*
    } else {
      $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
      header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
      $client->setAccessType('offline');
      $client->setApprovalPrompt("consent");
      $client->setIncludeGrantedScopes(true);   // incremental auth
    }
*/
    function getReport($analytics) {

        // balkonika: 154718876
        // nabytek safr: 166007108
        $VIEW_ID = "166007108";
      
        // Create the DateRange object.
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate("2020-01-15");
        $dateRange->setEndDate("2020-03-30");

        // Create the Metrics object.
        $sessions = new Google_Service_AnalyticsReporting_Metric();
        $sessions->setExpression("ga:itemRevenue");
        $sessions->setAlias("itemRevenue");

        //Create the Dimensions object.
        $browser = new Google_Service_AnalyticsReporting_Dimension();
        $browser->setName("ga:productName");
        
        $browser2 = new Google_Service_AnalyticsReporting_Dimension();
        $browser2->setName("ga:productSku");
        
        $ordering = new Google_Service_AnalyticsReporting_OrderBy();
        $ordering->setFieldName("ga:itemRevenue");
        $ordering->setOrderType("VALUE");   
        $ordering->setSortOrder("DESCENDING");

        // Create the ReportRequest object.
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($VIEW_ID);
        $request->setDateRanges($dateRange);
        $request->setDimensions(array($browser, $browser2));
        $request->setMetrics(array($sessions));
        $request->setOrderBys($ordering); // note this one!

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $request) );
        return $analytics->reports->batchGet( $body );

      
    }


    function printResults($reports) {
        
      for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
        $report = $reports[ $reportIndex ];
//      var_dump(get_class_methods($report));
        $header = $report->getColumnHeader();
//        var_dump(get_class_methods($header));
        $dimensionHeaders = $header->getDimensions();
//        var_dump($dimensionHeaders);
        $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
        $rows = $report->getData()->getRows();

        for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
          $row = $rows[ $rowIndex ];
          $dimensions = $row->getDimensions();
          $metrics = $row->getMetrics();
          for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
            print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "<br>");
          }

          for ($j = 0; $j < count($metrics); $j++) {
            $values = $metrics[$j]->getValues();
            for ($k = 0; $k < count($values); $k++) {
              $entry = $metricHeaders[$k];
              print($entry->getName() . ": " . $values[$k] . "<br>");
            }
          }
        }
      }
    }



?>
