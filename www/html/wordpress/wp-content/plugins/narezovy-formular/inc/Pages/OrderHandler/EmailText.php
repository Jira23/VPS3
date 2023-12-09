<?php

namespace Inc\Pages\OrderHandler;

class EmailText {
    
    public function customer_email(){
        $email_text = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Email Confirmation</title>
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
}

