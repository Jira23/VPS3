<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages;

use Inc\Base\BaseController;

class RenderRegisterUser extends BaseController{
    
    public function render_register_user(){
        // redirect if have needed cookies
        $user = new \Inc\Base\User();
        if($user->is_logged_with_cookies()) (new PagesController())->jQuery_redirect($this->editor_page .'?form_id=0&part_id=0#form_top');
        ?>
            <form method="post" class="register-form" id="register-user-form">
                <h3>Před vyplněním nářezového fomuláře zadejte prosím své kontaktní údaje.</h3>
                <h4>Tento formulář slouží pouze pro jednorázovou poptávku/objednávku bez registrace. Pro využití všech funkcí formuláře se prosím přihlašte (pouze pro velkoobchodní zákazníky, maloobchod je pouze pro jednorázový nákup).</h4>
                <div class="form-section">    
                    <label for="jmeno">*Jméno:</label><br>
                    <input type="text" name="nf_jmeno" style="width: 300px;" autocomplete="off" required></input>    
                </div>
                <div class="form-section">    
                    <label for="prijmeni">*Příjmení:</label><br>
                    <input type="text" name="nf_prijmeni" style="width: 300px;" autocomplete="off" required></input>    
                </div>
                <div class="form-section">    
                    <label for="email">*Email:</label><br>
                    <input type="email" name="nf_email" style="width: 300px;" autocomplete="off" required></input>    
                </div>
                <div class="form-section">    
                    <label for="telefon">*Telefon:</label><br>
                    <input type="text" name="nf_telefon" style="width: 300px;" autocomplete="off" required></input>    
                </div>
                <div class="form-section">
                    <label for="ulice">*Ulice, č.p.:</label><br>
                    <input type="text" name="nf_ulice" style="width: 300px;" autocomplete="off" required></input>    
                </div>
                <div class="form-section">    
                    <label for="mesto">*Město, PSC:</label><br>
                    <input type="text" name="nf_mesto" style="width: 300px;" autocomplete="off" required></input>    
                </div>
                <div class="form-section">    
                    <label for="ICO">IČO:</label><br>
                    <input type="text" name="nf_ICO" style="width: 300px;" autocomplete="off"></input>    
                </div>     
                <div class="form-section">    
                    <button class="button button-main" name="btn_odeslat_registraci" type="input">Odeslat</button>
                </div>    
            </form>
        <?php
    }
   
}
