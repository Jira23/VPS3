<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Pages;

use Inc\Base\User;

class RenderImport extends PagesContoller{
    public function __construct() {
        parent::__construct();
    }
    
    public function render_import(){
    ?>
        <a href="<?php echo $this->forms_list_page ?>">
            <?php $this->button->render_button('zpet_na_seznam'); ?>
        </a>

        <div id="file-info-block" hidden>
            <div class="button button-alert" id="file-info"></div>
        </div> 
        <div id="file-drop-area" class="file-drop">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Sem přetáhněte CSV soubor nebo klikem otevřete okno.</p>
        </div>
        <form id="file-upload-form" method="post" enctype="multipart/form-data">
            <input name="file" type="file" id="file-input" accept=".csv" style="display: none;" />
        </form>
<button type="button" id="test">Click</button>
        <div id="results" hidden></div>
        
    <?php
    }
}

