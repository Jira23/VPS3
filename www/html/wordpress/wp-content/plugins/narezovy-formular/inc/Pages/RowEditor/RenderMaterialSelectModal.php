<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages\RowEditor;

use Inc\Pages\PagesController;

class RenderMaterialSelectModal extends RenderEditor{
    
    public function render(){
    ?>
        <div class="modal-container" id="mod_material_desky">
            <div class="modal-content wide-modal">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h1>Výběr materiálu desky a hrany</h1>
                </div>
                <div class="modal-body">
                    <?php $this->render_part_upper_section(); ?>
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
    
    private function render_part_upper_section(){
    ?>    
        <h2>Materiál desky</h2>
        <div class="modal-section">                
            <div class="modal-half">
                <div style="display: flex; align-items: center; margin-bottom: 50px;">
                    <h5 style="margin: 0; padding: 5px;">Výběr podle názvu nebo čísla dekoru: &nbsp;</h5>
                    <input type="text" style="max-width: 400px; flex: 1; padding: 5px;" id="modal-input-deska" autocomplete="off" value="">
                </div>
                <h5 style="text-align: left;">Výběr podle kategorie:</h5>
                <div id="div_tree" class="ptree"><div id="1702327778338_div_pickletree"><ul id="1702327778338_tree_picklemain"><li id="div_treenode_2255" data-order="order_0"><div id="div_g_div_treenode_2255"><a id="a_toggle_div_treenode_2255" href="javascript:;"><i id="i_div_treenode_2255" style="color: black;" class="fa fa-plus"></i> Deskový materiál</a></div><ul id="c_div_treenode_2255" class="not-active"><li id="div_treenode_4009" data-order="order_0"><div id="div_g_div_treenode_4009"><a id="a_toggle_div_treenode_4009" href="javascript:;"><i id="i_div_treenode_4009" style="color: black;" class="fa fa-plus"></i> Doprodej Kronospan 2022</a></div><ul id="c_div_treenode_4009" class="not-active"><li id="div_treenode_4011" data-order="order_0"><div id="div_g_div_treenode_4011"><a id="a_toggle_div_treenode_4011" href="javascript:;"><i id="i_div_treenode_4011" style="color: black; display: none;" class="fa fa-plus"></i> DTD Lamino</a></div><ul id="c_div_treenode_4011" class="not-active"></ul></li><li id="div_treenode_4010" data-order="order_0"><div id="div_g_div_treenode_4010"><a id="a_toggle_div_treenode_4010" href="javascript:;"><i id="i_div_treenode_4010" style="color: black; display: none;" class="fa fa-plus"></i> Pracovní desky + lišty</a></div><ul id="c_div_treenode_4010" class="not-active"></ul></li></ul></li><li id="div_treenode_3995" data-order="order_0"><div id="div_g_div_treenode_3995"><a id="a_toggle_div_treenode_3995" href="javascript:;"><i id="i_div_treenode_3995" style="color: black;" class="fa fa-plus"></i> Obkladové panely ROCKO TILES</a></div><ul id="c_div_treenode_3995" class="not-active"><li id="div_treenode_4001" data-order="order_0"><div id="div_g_div_treenode_4001"><a id="a_toggle_div_treenode_4001" href="javascript:;"><i id="i_div_treenode_4001" style="color: black; display: none;" class="fa fa-plus"></i> Obkladové panely Rocko Tiles</a></div><ul id="c_div_treenode_4001" class="not-active"></ul></li><li id="div_treenode_3998" data-order="order_0"><div id="div_g_div_treenode_3998"><a id="a_toggle_div_treenode_3998" href="javascript:;"><i id="i_div_treenode_3998" style="color: black; display: none;" class="fa fa-plus"></i> Příslušenstvý k Rocko Tiles</a></div><ul id="c_div_treenode_3998" class="not-active"></ul></li></ul></li><li id="div_treenode_4003" data-order="order_0"><div id="div_g_div_treenode_4003"><a id="a_toggle_div_treenode_4003" href="javascript:;"><i id="i_div_treenode_4003" style="color: black; display: none;" class="fa fa-plus"></i> Parapety vnitřní DTD</a></div><ul id="c_div_treenode_4003" class="not-active"></ul></li><li id="div_treenode_2264" data-order="order_0"><div id="div_g_div_treenode_2264"><a id="a_toggle_div_treenode_2264" href="javascript:;"><i id="i_div_treenode_2264" style="color: black;" class="fa fa-plus"></i> Dřevotřískové desky</a></div><ul id="c_div_treenode_2264" class="not-active"><li id="div_treenode_2389" data-order="order_0"><div id="div_g_div_treenode_2389"><a id="a_toggle_div_treenode_2389" href="javascript:;"><i id="i_div_treenode_2389" style="color: black; display: none;" class="fa fa-plus"></i> DTD dýhované</a></div><ul id="c_div_treenode_2389" class="not-active"></ul></li><li id="div_treenode_2265" data-order="order_0"><div id="div_g_div_treenode_2265"><a id="a_toggle_div_treenode_2265" href="javascript:;"><i id="i_div_treenode_2265" style="color: black;" class="fa fa-plus"></i> DTD laminované</a></div><ul id="c_div_treenode_2265" class="not-active"><li id="div_treenode_2266" data-order="order_0"><div id="div_g_div_treenode_2266"><a id="a_toggle_div_treenode_2266" href="javascript:;"><i id="i_div_treenode_2266" style="color: black; display: none;" class="fa fa-plus"></i> DTD Egger</a></div><ul id="c_div_treenode_2266" class="not-active"></ul></li><li id="div_treenode_2387" data-order="order_0"><div id="div_g_div_treenode_2387"><a id="a_toggle_div_treenode_2387" href="javascript:;"><i id="i_div_treenode_2387" style="color: black; display: none;" class="fa fa-plus"></i> DTD Egger PerfectSense</a></div><ul id="c_div_treenode_2387" class="not-active"></ul></li><li id="div_treenode_2305" data-order="order_0"><div id="div_g_div_treenode_2305"><a id="a_toggle_div_treenode_2305" href="javascript:;"><i id="i_div_treenode_2305" style="color: black; display: none;" class="fa fa-plus"></i> DTD Kronospan</a></div><ul id="c_div_treenode_2305" class="not-active"></ul></li></ul></li><li id="div_treenode_2364" data-order="order_0"><div id="div_g_div_treenode_2364"><a id="a_toggle_div_treenode_2364" href="javascript:;"><i id="i_div_treenode_2364" style="color: black; display: none;" class="fa fa-plus"></i> DTD surové</a></div><ul id="c_div_treenode_2364" class="not-active"></ul></li></ul></li><li id="div_treenode_2347" data-order="order_0"><div id="div_g_div_treenode_2347"><a id="a_toggle_div_treenode_2347" href="javascript:;"><i id="i_div_treenode_2347" style="color: black; display: none;" class="fa fa-plus"></i> Hobra, sololit, akulit</a></div><ul id="c_div_treenode_2347" class="not-active"></ul></li><li id="div_treenode_2325" data-order="order_0"><div id="div_g_div_treenode_2325"><a id="a_toggle_div_treenode_2325" href="javascript:;"><i id="i_div_treenode_2325" style="color: black;" class="fa fa-plus"></i> Laťovky</a></div><ul id="c_div_treenode_2325" class="not-active"><li id="div_treenode_2445" data-order="order_0"><div id="div_g_div_treenode_2445"><a id="a_toggle_div_treenode_2445" href="javascript:;"><i id="i_div_treenode_2445" style="color: black; display: none;" class="fa fa-plus"></i> Dveřové</a></div><ul id="c_div_treenode_2445" class="not-active"></ul></li><li id="div_treenode_2326" data-order="order_0"><div id="div_g_div_treenode_2326"><a id="a_toggle_div_treenode_2326" href="javascript:;"><i id="i_div_treenode_2326" style="color: black; display: none;" class="fa fa-plus"></i> Dýhované</a></div><ul id="c_div_treenode_2326" class="not-active"></ul></li><li id="div_treenode_2444" data-order="order_0"><div id="div_g_div_treenode_2444"><a id="a_toggle_div_treenode_2444" href="javascript:;"><i id="i_div_treenode_2444" style="color: black; display: none;" class="fa fa-plus"></i> Konstrukční</a></div><ul id="c_div_treenode_2444" class="not-active"></ul></li></ul></li><li id="div_treenode_2398" data-order="order_0"><div id="div_g_div_treenode_2398"><a id="a_toggle_div_treenode_2398" href="javascript:;"><i id="i_div_treenode_2398" style="color: black;" class="fa fa-plus"></i> MDF desky</a></div><ul id="c_div_treenode_2398" class="not-active"><li id="div_treenode_4039" data-order="order_0"><div id="div_g_div_treenode_4039"><a id="a_toggle_div_treenode_4039" href="javascript:;"><i id="i_div_treenode_4039" style="color: black; display: none;" class="fa fa-plus"></i> Acrylic Gloss a Matt Kronospan</a></div><ul id="c_div_treenode_4039" class="not-active"></ul></li><li id="div_treenode_4042" data-order="order_0"><div id="div_g_div_treenode_4042"><a id="a_toggle_div_treenode_4042" href="javascript:;"><i id="i_div_treenode_4042" style="color: black; display: none;" class="fa fa-plus"></i> FeelNess Kronospan</a></div><ul id="c_div_treenode_4042" class="not-active"></ul></li><li id="div_treenode_4051" data-order="order_0"><div id="div_g_div_treenode_4051"><a id="a_toggle_div_treenode_4051" href="javascript:;"><i id="i_div_treenode_4051" style="color: black; display: none;" class="fa fa-plus"></i> MDF desky METAL</a></div><ul id="c_div_treenode_4051" class="not-active"></ul></li><li id="div_treenode_4047" data-order="order_0"><div id="div_g_div_treenode_4047"><a id="a_toggle_div_treenode_4047" href="javascript:;"><i id="i_div_treenode_4047" style="color: black; display: none;" class="fa fa-plus"></i> MDF Egger PerfectSense</a></div><ul id="c_div_treenode_4047" class="not-active"></ul></li><li id="div_treenode_2405" data-order="order_0"><div id="div_g_div_treenode_2405"><a id="a_toggle_div_treenode_2405" href="javascript:;"><i id="i_div_treenode_2405" style="color: black; display: none;" class="fa fa-plus"></i> MDF lakované</a></div><ul id="c_div_treenode_2405" class="not-active"></ul></li><li id="div_treenode_2441" data-order="order_0"><div id="div_g_div_treenode_2441"><a id="a_toggle_div_treenode_2441" href="javascript:;"><i id="i_div_treenode_2441" style="color: black; display: none;" class="fa fa-plus"></i> MDF ohybatelné</a></div><ul id="c_div_treenode_2441" class="not-active"></ul></li><li id="div_treenode_2399" data-order="order_0"><div id="div_g_div_treenode_2399"><a id="a_toggle_div_treenode_2399" href="javascript:;"><i id="i_div_treenode_2399" style="color: black; display: none;" class="fa fa-plus"></i> MDF surové</a></div><ul id="c_div_treenode_2399" class="not-active"></ul></li></ul></li><li id="div_treenode_2410" data-order="order_0"><div id="div_g_div_treenode_2410"><a id="a_toggle_div_treenode_2410" href="javascript:;"><i id="i_div_treenode_2410" style="color: black;" class="fa fa-plus"></i> OSB desky</a></div><ul id="c_div_treenode_2410" class="not-active"><li id="div_treenode_2746" data-order="order_0"><div id="div_g_div_treenode_2746"><a id="a_toggle_div_treenode_2746" href="javascript:;"><i id="i_div_treenode_2746" style="color: black; display: none;" class="fa fa-plus"></i> OSB Kronospan</a></div><ul id="c_div_treenode_2746" class="not-active"></ul></li><li id="div_treenode_2411" data-order="order_0"><div id="div_g_div_treenode_2411"><a id="a_toggle_div_treenode_2411" href="javascript:;"><i id="i_div_treenode_2411" style="color: black; display: none;" class="fa fa-plus"></i> OSB nebroušené</a></div><ul id="c_div_treenode_2411" class="not-active"></ul></li></ul></li><li id="div_treenode_3512" data-order="order_0"><div id="div_g_div_treenode_3512"><a id="a_toggle_div_treenode_3512" href="javascript:;"><i id="i_div_treenode_3512" style="color: black; display: none;" class="fa fa-plus"></i> Plexisklo</a></div><ul id="c_div_treenode_3512" class="not-active"></ul></li><li id="div_treenode_2276" data-order="order_0"><div id="div_g_div_treenode_2276"><a id="a_toggle_div_treenode_2276" href="javascript:;"><i id="i_div_treenode_2276" style="color: black;" class="fa fa-plus"></i> Pracovní desky kuchyňské</a></div><ul id="c_div_treenode_2276" class="not-active"><li id="div_treenode_4040" data-order="order_0"><div id="div_g_div_treenode_4040"><a id="a_toggle_div_treenode_4040" href="javascript:;"><i id="i_div_treenode_4040" style="color: black; display: none;" class="fa fa-plus"></i> Pracovní desky Slim Line</a></div><ul id="c_div_treenode_4040" class="not-active"></ul></li><li id="div_treenode_2287" data-order="order_0"><div id="div_g_div_treenode_2287"><a id="a_toggle_div_treenode_2287" href="javascript:;"><i id="i_div_treenode_2287" style="color: black; display: none;" class="fa fa-plus"></i> Pracovní desky EGGER šíře 600 a 920mm</a></div><ul id="c_div_treenode_2287" class="not-active"></ul></li><li id="div_treenode_2309" data-order="order_0"><div id="div_g_div_treenode_2309"><a id="a_toggle_div_treenode_2309" href="javascript:;"><i id="i_div_treenode_2309" style="color: black; display: none;" class="fa fa-plus"></i> Pracovní desky KRONOSPAN šíře 600 a 900mm</a></div><ul id="c_div_treenode_2309" class="not-active"></ul></li><li id="div_treenode_2314" data-order="order_0"><div id="div_g_div_treenode_2314"><a id="a_toggle_div_treenode_2314" href="javascript:;"><i id="i_div_treenode_2314" style="color: black; display: none;" class="fa fa-plus"></i> Zástěny EGGER</a></div><ul id="c_div_treenode_2314" class="not-active"></ul></li></ul></li><li id="div_treenode_2332" data-order="order_0"><div id="div_g_div_treenode_2332"><a id="a_toggle_div_treenode_2332" href="javascript:;"><i id="i_div_treenode_2332" style="color: black;" class="fa fa-plus"></i> Překližky</a></div><ul id="c_div_treenode_2332" class="not-active"><li id="div_treenode_2394" data-order="order_0"><div id="div_g_div_treenode_2394"><a id="a_toggle_div_treenode_2394" href="javascript:;"><i id="i_div_treenode_2394" style="color: black; display: none;" class="fa fa-plus"></i> Překližky ohybatelné</a></div><ul id="c_div_treenode_2394" class="not-active"></ul></li><li id="div_treenode_2353" data-order="order_0"><div id="div_g_div_treenode_2353"><a id="a_toggle_div_treenode_2353" href="javascript:;"><i id="i_div_treenode_2353" style="color: black;" class="fa fa-plus"></i> Překližky truhlářské</a></div><ul id="c_div_treenode_2353" class="not-active"><li id="div_treenode_2751" data-order="order_0"><div id="div_g_div_treenode_2751"><a id="a_toggle_div_treenode_2751" href="javascript:;"><i id="i_div_treenode_2751" style="color: black; display: none;" class="fa fa-plus"></i> Multiplex bříza</a></div><ul id="c_div_treenode_2751" class="not-active"></ul></li><li id="div_treenode_2753" data-order="order_0"><div id="div_g_div_treenode_2753"><a id="a_toggle_div_treenode_2753" href="javascript:;"><i id="i_div_treenode_2753" style="color: black; display: none;" class="fa fa-plus"></i> Multiplex buk</a></div><ul id="c_div_treenode_2753" class="not-active"></ul></li><li id="div_treenode_2385" data-order="order_0"><div id="div_g_div_treenode_2385"><a id="a_toggle_div_treenode_2385" href="javascript:;"><i id="i_div_treenode_2385" style="color: black; display: none;" class="fa fa-plus"></i> Překližky borovice</a></div><ul id="c_div_treenode_2385" class="not-active"></ul></li><li id="div_treenode_2354" data-order="order_0"><div id="div_g_div_treenode_2354"><a id="a_toggle_div_treenode_2354" href="javascript:;"><i id="i_div_treenode_2354" style="color: black; display: none;" class="fa fa-plus"></i> Překližky buk</a></div><ul id="c_div_treenode_2354" class="not-active"></ul></li><li id="div_treenode_2440" data-order="order_0"><div id="div_g_div_treenode_2440"><a id="a_toggle_div_treenode_2440" href="javascript:;"><i id="i_div_treenode_2440" style="color: black; display: none;" class="fa fa-plus"></i> Překližky dub</a></div><ul id="c_div_treenode_2440" class="not-active"></ul></li></ul></li><li id="div_treenode_2333" data-order="order_0"><div id="div_g_div_treenode_2333"><a id="a_toggle_div_treenode_2333" href="javascript:;"><i id="i_div_treenode_2333" style="color: black;" class="fa fa-plus"></i> Překližky vodovzdorné</a></div><ul id="c_div_treenode_2333" class="not-active"><li id="div_treenode_2750" data-order="order_0"><div id="div_g_div_treenode_2750"><a id="a_toggle_div_treenode_2750" href="javascript:;"><i id="i_div_treenode_2750" style="color: black; display: none;" class="fa fa-plus"></i> Multiplex bříza</a></div><ul id="c_div_treenode_2750" class="not-active"></ul></li><li id="div_treenode_2334" data-order="order_0"><div id="div_g_div_treenode_2334"><a id="a_toggle_div_treenode_2334" href="javascript:;"><i id="i_div_treenode_2334" style="color: black; display: none;" class="fa fa-plus"></i> Foliované</a></div><ul id="c_div_treenode_2334" class="not-active"></ul></li><li id="div_treenode_2376" data-order="order_0"><div id="div_g_div_treenode_2376"><a id="a_toggle_div_treenode_2376" href="javascript:;"><i id="i_div_treenode_2376" style="color: black; display: none;" class="fa fa-plus"></i> Stavební obalové</a></div><ul id="c_div_treenode_2376" class="not-active"></ul></li></ul></li></ul></li><li id="div_treenode_2432" data-order="order_0"><div id="div_g_div_treenode_2432"><a id="a_toggle_div_treenode_2432" href="javascript:;"><i id="i_div_treenode_2432" style="color: black;" class="fa fa-plus"></i> Spárovky, Biodesky</a></div><ul id="c_div_treenode_2432" class="not-active"><li id="div_treenode_2446" data-order="order_0"><div id="div_g_div_treenode_2446"><a id="a_toggle_div_treenode_2446" href="javascript:;"><i id="i_div_treenode_2446" style="color: black; display: none;" class="fa fa-plus"></i> Biodesky</a></div><ul id="c_div_treenode_2446" class="not-active"></ul></li><li id="div_treenode_2435" data-order="order_0"><div id="div_g_div_treenode_2435"><a id="a_toggle_div_treenode_2435" href="javascript:;"><i id="i_div_treenode_2435" style="color: black; display: none;" class="fa fa-plus"></i> Spárovky borovice</a></div><ul id="c_div_treenode_2435" class="not-active"></ul></li><li id="div_treenode_2433" data-order="order_0"><div id="div_g_div_treenode_2433"><a id="a_toggle_div_treenode_2433" href="javascript:;"><i id="i_div_treenode_2433" style="color: black; display: none;" class="fa fa-plus"></i> Spárovky smrk</a></div><ul id="c_div_treenode_2433" class="not-active"></ul></li></ul></li></ul></li></ul></div></div>                
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
                        <input id="modal-deska-mat-id" value="" style="display: none;"/>
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <table id="modal-deska-products-list" class="lamino-list" style="margin-bottom: 0px;"></table>
             </div>
            <div style="clear: both;"></div>
        </div>

        <hr>    
         <h2>Materiál hrany</h2>
        <div class="modal-section">
            <div class="modal-half">
                <h5 style="margin: 0; padding: 5px;">Zvolte typ hrany: &nbsp;</h5>
                <div class="modal-edge-type-wrapper">
                    <?php $this->radio->render('modal edge type'); ?>    
                </div>
                <input type="text" style="max-width: 400px; flex: 1; padding: 5px; display: none;" id="modal-input-hrana" autocomplete="off" value="">
            </div>
        
            <div class="modal-half">
                <div id="modal-hrana-mat-info">
                    <div id="no-edge">
                        <h3>Deska nebude mít žádnou hranu.</h3>
                    </div>
                    <div id="same-edge">
                        <h3 id="modal-hrana-mat-nazev-same"></h3>
                        <div id="icon-hrana-same">
                            <img src="">
                        </div>
                    </div>
                    <div id="different-edge">
                        <h3 id="modal-hrana-mat-nazev-different"></h3>
                        <div id="icon-hrana-different">
                            <img src="">
                        </div>
                    </div>                    
                    <input id="modal-hrana-mat-id" value="" style="display: none;"/>
                </div>
                <table id="modal-hrana-products-list" class="lamino-list" style="margin-bottom: 0px;">
                </table>             
            </div>
            <div style="clear: both;"></div>    
        </div>    
        <?php
    }      
}    