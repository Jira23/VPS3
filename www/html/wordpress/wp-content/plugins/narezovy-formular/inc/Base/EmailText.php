<?php

namespace Inc\Base;

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
                <p>Budeme Vás kontaktovat ohledně platby zálohy.</p>
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
    
    public function admin_email(){
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
            </body>
            </html>
            ';
        
        return $email_text;
    }    
    
    public function hash_email($hash_url){
        $email_text = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Nářezový formulář</title>
            </head>
            <body>
                <p>Dobrý den,</p>
                <p>děkujeme za registraci do nářezového fomuláře na Dřevoobchod Doležal.</p>
                <h3>K rozpracovanému formuláři se můžete vrátit pomocí tohoto odkazu: <a href="' .$hash_url .'">' .$hash_url .'</a></h3>
            </body>
            </html>
            ';
        
        return $email_text;
    }     
}

