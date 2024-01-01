<?php

namespace Inc\Pages\OrderHandler;

class EmailText {
    
    public function customer_email(){
        $email_text = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Nářezový formulář</title>
            </head>
            <body>
                <p>Dobrý den,</p>
                <p>Potvrzujeme přijetí nářezového formuláře.</p>
                <p>Pokud máte dotazy ohledně řezání, kontaktujte nás zde:</p>
                <p>Telefon: 602 233 371<br>
                Email: <a href="mailto:rezaninamiru@drevoobchoddolezal.cz">rezaninamiru@drevoobchoddolezal.cz</a></p>
                <br>
                <p>Dřevoobchod Doležal</p>
            </body>
            </html>
            ';
        
        return $email_text;
    }
    
    public function admin_email($saw_file_url){
        $email_text = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Nářezový formulář</title>
            </head>
            <body>
                <p>Dobrý den,</p>
                <p>byl přijat nový nářezový formulář.</p>
                <p><a href="' .$saw_file_url .'">Soubor pro pilu je ke stažení zde</a></p>
            </body>
            </html>
            ';
        
        return $email_text;
    }    
}

