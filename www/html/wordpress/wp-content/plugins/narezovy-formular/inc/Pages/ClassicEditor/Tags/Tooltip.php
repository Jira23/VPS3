<?php 
/**
 *  @package  narezovy-formular
 */
namespace Inc\Pages\ClassicEditor\Tags;

use Inc\Base\BaseController;

class Tooltip extends BaseController {
    
    public function render_tooltip($tooltip_id){
        
        $props = $this->define_tooltip_props($tooltip_id);
        
        echo '<div class="tooltip">' .PHP_EOL;
        echo '  <img src="' .$this->plugin_url .'assets/img/info_icon.png"/>';
        echo '  <span class="tooltiptext"'; 
        if(isset($props['attrs'] )){
            foreach ($props['attrs'] as $attr_name => $attr_value) {
                echo $attr_name .'="' .$attr_value .'" ';
            }
        }
        echo '>';
        echo $props['text'];
        echo '  </span>' .PHP_EOL;
        echo '</div>'.PHP_EOL;
    }
    
    private function define_tooltip_props($tooltip_id){
        $tooltip = [
            'nazev' => [
                'text'   => 'Povinné pole. Zvolte si libovolný název formuláře.',
                'attrs' => ['style' => 'width:250px;']
            ],
            'stitky' => [
                'text'   => 'Na každý dílec je lepen štítek, máte na výběr možnosti:<br>- NE - štítky budou odstraněny<br>- ANO - štítky budou ponechány<br>(Pokud budou dílce olepovány ABS hranou, štítky jsou vždy,  pokud vyberete možnost NE štítky budou odstraněny).',
                'attrs' => ['style' => 'margin-right: 50px;']
            ],            
            'doprava' => [
                'text'   => 'Vyberte požadovaný způsob dopravy nebo osobního vyzvednutí.',
                'attrs' => ['style' => 'width: 200px;']
            ],
            'deska' => [
                'text'   => 'Povinné pole. Zvolte plošný materiál, který chcete naformátovat<br>-Je možno zadat přímo číslo produktu nebo pro filtrování stačí zadat část názvu, číslo dekoru apod. a systém Vám bude nabízet položky, které vyhovují zadanému filtru<br>-Dle vybrané položky se zobrazí obrázek zvoleného dekoru vpravo.',
                'attrs' => ['style' => 'width: 350px;']
            ],
            'ulozit_dil' => [
                'text'   => 'Pokud máte vyplněné všechny položky, stisknete tlačítko „Uložit díl“ a vybrané údaje se vyplní dotabulky „Výpis zadaných dílů“<br>Následně se data okolo obrázku dílu smažou a stav je jako před zadáváním<br>Zůstane předvyplněno pouze pole v výběrem materiálu a pole s výběrem hrany podle výběru posledního uloženého dílu.',
                'attrs' => ['style' => 'width: 500px;']
            ],
            'hrana_select' => [
                'text'   => 'Zvolte jednu z nabízených variant v rozbalovacím menu:<br>-Přivzorovaná (tj. následně se bude k vybranému LTD nabízet pouze dekor hrany, která je k LTD přivzorovaná v rozměrech, které jsou skladové)<br>-Odlišná (tj. následně se budou k vybranému LTD nabízet všechny skladové hrany)<br>-Žádná (tj. následně se k vybranému materiálu nebude nabízet žádná hrana).'
            ],
            'lepidlo' => [
                'text'   => 'Zvolte jednu z nabízených variant v rozbalovacím menu.'
            ],
            'nazev_dilce' => [
                'text'   => 'Můžete si zvolit svůj název dílce.',
                'attrs' => ['style' => 'width: 250px;']
            ],
            'tupl' => [
                'text'   => 'V případě, že chcete mít silnější výslednou desku, je možno využít funkce tupl, kde se na plocho slepí dvě desky.<br>Možnost zadání tuplu:<br>-Ne - zůstane síla zadaného materiálu<br>-30mm - podlepení deskou síly 12mm v dekoru bílá<br>-36mm - podlepení deskou síly 18mm ve stejném dekoru jako zadaný materiál<br>-Tupl se standardně na dílech nedělá, tj. základní hodnota je nastavena jako „Ne“.',
                'attrs' => ['style' => 'top: -5px; right: 105%; left: auto;']
            ],
            'delka_dilu' => [
                'text'   => 'Pokud má materiál léta (kresbu dřeva, např.: dřevodekory, spárovky, překližky, dýhy) délka je vždy rozměr po létech.'
            ],
            'hrana_horni' => [
                'text'   => 'V jednotlivých políčcích okolo obrázku si vyberete rozměry hran, které chcete olepit na jednotlivé strany<br>Systém nabízí dle výše zvoleného menu (přivzorovaná/odlišná/žádná) hrany.'
            ],
            'sirka_dilu' => [
                'text'   => 'Pokud má materiál léta (kresbu dřeva, např.: dřevodekory, spárovky, překližky, dýhy) šířka je vždy rozměr přes léta.',
                'attrs' => ['style' => 'width:250px;']
            ],
            'vypis_dilu' => [
                'text'   => 'V tabulce „Výpis zadaných dílů“ se postupně vyplňují jednotlivé řádky, jak jednotlivé díly zadáváte<br>Každý řádek = jeden zadaný díl<br>U každého dílu je možnost:<br>-X = Smazat<br>-D = Duplikovat<br>- Opravit lze po kliknutí na název dílu nebo lamina<br><br>Smazat<br>Po stisknutí tlačítka „Smazat“ smažete aktuální řádek bez náhrady (pro potvrzení této volby se zobrazí ještě dialogové okno, kde je možný krok zpět)<br><br>Duplikovat<br>Tato funkce slouží pro usnadnění zadávání podobných dílů, jako jsou ty, které jsou už vyplněné<br>Po stisknutí tlačítka „Duplikovat“ se aktuální řádek nahraje do zadávacího formuláře „Parametry dílu“<br>Pak můžete opravit požadované údaje po kliknutí na název dílu nebo lamina <br><br>Opravit<br>Po kliknutí na název dílu nebo lamina se aktuální řádek nahraje do zadávacího formuláře „Parametry dílu“<br>Můžete opravit požadované údaje<br>Stisknutím tlačítka „Uložit díl“ změníte údaje u tohoto dílu a data se opraví v daném řádku v tabulce „Výpis zadaných dílů“',
                'attrs' => ['style' => 'width:500px;']
            ]            
        ];
        return $tooltip[$tooltip_id];
    }
}
