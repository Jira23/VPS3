<?php


// experimentalni kod

exit;    
    // majitele bezkempu
    for ($i = 5001; $i <= 6000; $i++) {
        $headers = get_headers('https://www.bezkempu.cz/' .$i);
        if($headers[0] != 'HTTP/1.1 200 OK') continue;        
        $page = file_get_contents('https://www.bezkempu.cz/' .$i);
        //echo htmlspecialchars($page);
        //var_dump(strpos($page, "style='color:#c93751'></i>&nbsp;"));
        //exit;
        if(strpos($page, "style='color:#c93751'></i>&nbsp;") === false) continue;
        $score = 'N/A';
        $score = explode("style='color:#c93751'></i>&nbsp;", $page)[1];
        $score = explode("&nbsp;%&nbsp;<a", $score)[0];
        
        echo $i .': ' .$score .'<br>';
        
        $toLog = $i .': ' .$score .PHP_EOL;
        file_put_contents('/var/www/core/logs/bezkempu.log', $toLog, FILE_APPEND);        
    }    
    
exit;    
    
    //uzivatele bezkempu
    for ($i = 151; $i <= 500; $i++) {
        $headers = get_headers('https://www.bezkempu.cz/profil/' .$i);
        if($headers[0] != 'HTTP/1.1 200 OK') continue;        
        $page = file_get_contents('https://www.bezkempu.cz/profil/' .$i);
        //var_dump(strpos($page, 'Host nemá zatím žádné hodnocení'));
        if(strpos($page, '<div val=\'') === false) continue;
        if(strpos($page, 'Host nemá zatím žádné hodnocení') !== false) continue;
        
        $score = 'N/A';
        $score = explode('<div val=\'', $page)[1];
        $score = explode('\' class=', $score)[0];
        
        echo $i .': ' .$score .'<br>';
        
        $toLog = $i .': ' .$score .PHP_EOL;
        file_put_contents('/var/www/core/logs/bezkempu.log', $toLog, FILE_APPEND);        
    }

