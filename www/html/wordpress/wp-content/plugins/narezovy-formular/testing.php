<?php
exit;
    require_once('/home/drevoobchoddolezal.cz/public_html/wp-load.php');
    global $wpdb;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);       
    
    if (!isset($argv[1])) exit;
    
    $form_id = $argv[1];
    
    echo date("d.m.Y H:i") .PHP_EOL;
    echo 'Starting optimalization of form id ' .$form_id .PHP_EOL;
    
    $wpdb->delete(NF_OPT_RESULTS_TABLE, array('form_id' => $form_id), array('%d')); 
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://drevoobchoddolezal.cz/wp-admin/admin-ajax.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
        'action' => 'optimize',
        'form_id' => $form_id
    )));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        echo 'Response: ' . $response;
        echo PHP_EOL .'--------------------------------------------------------------------------------' .PHP_EOL .PHP_EOL .PHP_EOL .PHP_EOL;
    }

    curl_close($ch);

    
    