<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages\RowEditor;

use Inc\Pages\PagesController;

class RenderMaterialSelectModal extends RenderEditor{
    
    public function render_deska(){
    ?>
        <div class="modal-container" id="mod_material_desky">
            <div class="modal-content wide-modal">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h1>Výběr materiálu desky</h1>
                </div>
                <div class="modal-body">
                    <?php $this->render_deska_section(); ?>
                </div>
                <div class="modal-footer">
                    <div style="text-align: center;">
                        <?php $this->button->render_button('mat_select'); ?>
                    </div>               
                </div>
            </div>

        </div>
    <?php
    }

    public function render_hrana(){
    ?>
        <div class="modal-container" id="mod_material_hrany">
            <div class="modal-content wide-modal">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h1>Výběr materiálu hrany</h1>
                </div>
                <div class="modal-body">
                    <?php $this->render_hrana_section(); ?>
                </div>
                <div class="modal-footer">
                    <div style="text-align: center;">
                        <?php $this->button->render_button('edge_select'); ?>
                    </div>               
                </div>
            </div>

        </div>
    <?php
    }
    
    private function render_deska_section(){
    ?>    
        <h2>Materiál desky</h2>
        <div class="modal-section">                
            <div class="modal-half">
                <div class="modal-input-container">
                    <h5 style="margin: 0; padding: 5px;">1 - Výběr podle názvu nebo čísla dekoru: &nbsp;</h5>
                    <input type="text" style="width: 460px; padding: 5px;" id="modal-input-deska" autocomplete="off" value="">
                </div>    
                <div class="modal-input-container">
                    <h5 style="text-align: left;">2 - Výběr podle kategorie:</h5>
                    <table>
                        <tr>
                            <td style="width: 50px;">Formátování</td>
                            <td> - odběr na celé desky</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td> - prodej je realizován pouze na celé desky</td>
                        </tr>                        
                        <tr>
                            <td></td>
                            <td> - nařežeme Vám vše, co naše pila pojme</td>
                        </tr>                                                
                    </table>
                    <div id="div_tree_product_cat" class="ptree"><div id="1702327778338_div_pickletree"><ul id="1702327778338_tree_picklemain"></ul></div></div>
                </div>
                <div class="modal-input-container">
                    <h5 style="text-align: left;">3 - Výběr podle kolekce:</h5>
                    <table>
                        <tr>
                            <td style="width: 50px;">Formátování</td>
                            <td> - odběr jen to co chci (bez zbytků)</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td> - pouze vybrané dekory</td>
                        </tr>                        
                        <tr>
                            <td></td>
                            <td> - prodej je realizován jen z těch kusů, které máte zadány ve</td>
                        </tr>                                                
                        <tr>
                            <td></td>
                            <td>Vámi vyplněném objednávkovém formuláři</td>
                        </tr>                         
                    </table>                    
                    <div id="div_tree_product_tag" class="ptree"><div id="1702327778339_div_pickletree"><ul id="1702327778339_tree_picklemain"></ul></div></div>
                </div>
            </div>
            <div class="modal-half">
                <div id="modal-deska-mat-info">
                    <h3 id="modal-deska-mat-nazev"></h3>
                    <div class="modal-half">
                        <div id="icon-deska">
                            <img src="">
                        </div>
                    </div>
                    <div class="modal-half">
                        <table class="modal-deska-mat-params-table">
                            <tr>
                                <td>Sku:</td>
                                <td id="modal-deska-mat-sku"></td>
                            </tr>                                
                            <tr>
                                <td>Délka:</td>
                                <td id="modal-deska-mat-delka"></td>
                            </tr>
                            <tr>
                                <td>Šířka:</td>
                                <td id="modal-deska-mat-sirka"></td>
                            </tr>
                            <tr>
                                <td>Síla:</td>
                                <td id="modal-deska-mat-sila"></td>
                            </tr>
                        </table>
                        <input id="modal-deska-mat-data" value="{}" style="display: none;"/>
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <div id="modal-deska-products-list" class="lamino-list" style="margin-bottom: 0px;"></div>
             </div>
            <div style="clear: both;"></div>
            <div style="display: flex; justify-content: center;">
                <?php $this->alert->render_alert('Materiál je již ve formuláři!', 'error', true, 'alert-mat-in-form'); ?>
            </div>
        </div>
        <div id="mat-modal-overlay" class="NF-mat-modal-overlay">
          <div class="centered-content">
            <img width="200" id="loadingIcon" src="/wp-content/plugins/narezovy-formular/assets/img/Loading_icon.gif" style="display: block;margin: 0 auto;display: block;margin: 0 auto;">
          </div>
        </div>
         
        <?php
    }
    
    private function render_hrana_section(){
    ?>    
        <hr>    
         <h2>Materiál hrany</h2>
        <div class="modal-section">
            <div class="modal-half">
                <h5 style="margin: 0; padding: 5px;">Zvolte typ hrany: &nbsp;</h5>
                <div class="modal-edge-type-wrapper custom-radio-wrapper">
                    <ul>
                        <li>
                            <input type="radio" name="modal-edge-type" id="cb1" value="0" checked=""/>
                            <label for="cb1"><img src="https://drevoobchoddolezal.cz/wp-content/plugins/narezovy-formular2/assets/img/privzorovana.png" /><p>Přivzorovaná</p></label>
                        </li>
                        <li>
                            <input type="radio" name="modal-edge-type" id="cb2" value="1" />
                            <label for="cb2"><img src="https://drevoobchoddolezal.cz/wp-content/plugins/narezovy-formular2/assets/img/odlisna.png" /><p>Odlišná</p></label>
                           
                        </li>
                        <li>
                            <input type="radio" name="modal-edge-type" id="cb3" value="-1" />
                            <label for="cb3"><img src="https://drevoobchoddolezal.cz/wp-content/plugins/narezovy-formular2/assets/img/zadna.png" /><p>Žádná</p></label>
                        </li>
                    </ul>
                    <div id="modal-input-hrana-wrapper" >
                        <h6>Vyhledávejte nejlépe podle čísla dekoru:</h6>
                        <input type="text" style="width: 30%; padding: 5px;" autocomplete="off" value="" id="modal-input-hrana">
                    </div>     
                </div>
            </div>
        
            <div class="modal-half">
                <div id="modal-hrana-mat-info">
                    <div id="no-edge">
                        <h3>Deska nebude mít žádnou hranu.</h3>
                    </div>
                    <div id="same-edge">
                        <div id="same-edge-valid-edge">
                            <h5>Hrana bude mít stejný dekor jako deska.<br><small>Obrázek dekoru hrany a dekoru desky de může lišit.</small></h5>
                            <h3 id="modal-hrana-mat-nazev-same"></h3>
                            <div id="icon-hrana-same">
                                <img src="">
                            </div>                            
                        </div>
                        <div id="same-edge-no-edge">
                            <h3>Pro tuto desku přivzorovaná hrana neexistuje.<br>Zvolte "Žádná" nebo "Odlišná"</h3>
                        </div>
                    </div>
                    <div id="different-edge">
                        <h3 id="modal-hrana-mat-nazev-different"></h3>
                        <div id="icon-hrana-different">
                            <img src="">
                        </div>
                    </div>                    
                    <input id="modal-hrana-mat-id-same" value="" style="display: none;"/>
                    <input id="modal-hrana-mat-id-different" value="" style="display: none;"/>
                    <input id="modal-hrana-mat-has-same-edge" value="" style="display: none;"/>
                </div>
                <table id="modal-hrana-products-list" class="lamino-list" style="margin-bottom: 0px;">
                </table>             
            </div>
            <div style="clear: both;"></div>    
        </div>
        <div id="mat-modal-overlay" class="NF-mat-modal-overlay">
          <div class="centered-content">
            <img width="200" id="loadingIcon" src="/wp-content/plugins/narezovy-formular/assets/img/Loading_icon.gif" style="display: block;margin: 0 auto;display: block;margin: 0 auto;">
          </div>
        </div>
         
        <?php
    }      
}    