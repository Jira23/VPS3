
jQuery(document).ready(function($) {    

    /**
    *  ---- event fuctions ----
    */
   
    jQuery('#lamino_id').on("change", function(){                                                   // deska input
        if(!($('#hrana').val() === '1' && $('#hrana_id').val() !== '')) getHrany(this.value);
        actionIsPDK($('#lamino_id').attr('ispdk'));
        resetLepidlo();
        toggleHranaInputAvailability(false);
    });

    jQuery('#hrana_id').on("change", function(){                                                    // hrana input
        if(($('#hrana').val() === '1' && $('#hrana_id').val() !== '')) getHrany($('#lamino_id').val(), $('#input-hrana').val());
    });

    jQuery('#hrana').on("change", function(){                                                       // hrana selectbox
        toggleHranaInput(this.value);
        toggleHranySelectboxes(this.value === '-1' ? true : false);
        toggleLepidlo(this.value === '-1' ? true : false);

        if(this.value === '0' ) getHrany($('#lamino_id').val());
        if(this.value === '1' ) getHrany($('#lamino_id').val(), $('#input-hrana').val());
    });
    
    jQuery('#tupl').on("change", function(){                                                       // tupl selectbox
        if(($('#hrana').val() === '0')) getHrany($('#lamino_id').val());
        if(($('#hrana').val() === '1' && $('#hrana_id').val() !== '')) getHrany($('#lamino_id').val(), $('#input-hrana').val());
    });    
 
    /**
    *  ---- helper functions ----
    */
    
    var hranyRozmerSelectboxes = ['hrana-leva', 'hrana-horni', 'hrana-prava', 'hrana-dolni'];  // define hrany rozmery selectboxes ids
    
    // AJAX dotaz, nacte info o produktech spojenych s produktem vybranym jako lamino, (nacte hrany k laminu)
    function getHrany(product_id, dekor = ''){
        showLoadingIconHranySelectboxes(true);
        var data = {'action': 'get_hrany_dimensions','product_id': product_id,'tupl' : jQuery('#tupl').val(), 'dekor' : dekor};         // get data
        jQuery.post(NF_ajaxUrl, data, function(response) {
            fillHranySelectboxes (response);                                                                                            // fill selecboxes with data
            showLoadingIconHranySelectboxes(false);
        });
    };
    
    function showLoadingIconHranySelectboxes(state){
        hranyRozmerSelectboxes.forEach(function(element) {
            jQuery('#' + element + '-block-sb').toggle(!state);
            jQuery('#' + element + '-block-icon').toggle(state);
        });
    }
    
    // fill hrany selecboxes with data
    function fillHranySelectboxes($items){
        hranyRozmerSelectboxes.forEach(function(element) {
            jQuery('#select-' + element).html($items);
        });        
    }
    
    function toggleHranySelectboxes(state){
        hranyRozmerSelectboxes.forEach(function(element) {
            jQuery('#select-' + element).prop('disabled', state);
        });                
    }    
    
    // vypinam/zapinam selectbox tupl a odstanuji/pridavam mmoznost "Odlisna" do selectboxu hrana, podle toho zda je lamino v kategorii "pracovní desky kuchynske"
    function actionIsPDK(isPDK){
        if (isPDK === 'true') {                                                             // pokud je lamino v kategorii "pracovní desky kuchynske"
            jQuery('#tupl').prop("disabled", true);
            jQuery('#hrana').find('[value="1"]').remove();                                  // ... odstranim ze selectboxu hrana moznost "Odlisna"
        } else {
            jQuery('#tupl').prop("disabled", false);
            if(jQuery('#hrana option[value="1"]').length == 0){
                jQuery('#hrana').append(jQuery('<option>', {value: 1,text: 'Odlišná'}));
            }
        }        
    }
    
    // returns select box lepidlo to init state
    function resetLepidlo(){
        jQuery("#lepidlo option:first").prop("disabled", false);
        jQuery("#lepidlo option:not(:first)").prop("selected", false);
        jQuery("#lepidlo option:first").prop("disabled", true);         
    }
    
    function toggleLepidlo(state){
        jQuery('#lepidlo').prop("disabled", state);
    }
    
    // show or hide hrana input and icon
    function toggleHranaInput(sbValue){
        if (sbValue == 1) {
            jQuery('#input-hrana').show();
            jQuery('#icon-hrana').show();
            jQuery('#input-hrana').prop('required',true);
        } else {
            jQuery('#input-hrana').hide();
            jQuery('#icon-hrana').hide();
            jQuery('#input-hrana').prop('required',false);
        }        
    }
    
    function toggleHranaInputAvailability(state){
        $('#input-hrana').prop('disabled', state);
    }
    
    function setSelectHranySelectboxesOnEdit(){
        var dbValues = JSON.parse($('#hrany-selectboxes-selected-values').html());                              // load values from database from hidden div

        for (var key in dbValues) {
            if (dbValues.hasOwnProperty(key)) {
                if($("#" + key + " option[value='" + dbValues[key] + "']").length > 0){                         // if there is option with value wich corresponds with db value, then add "selected" attr to it
                    $("#" + key + " option[value='" + dbValues[key] + "']").attr("selected", "selected");
                }
            }
        }
    }
    
    /**
    *  ---- do after page loaded ----
    */
   
    if (window.location.href.indexOf("narezovy-formular-editor") !== -1) {
        $('#hrana').trigger("change");                                                              // load data for hrany

        $(document).ajaxComplete(function(event, xhr, settings) {                                   // call function after AJAX call is complete
            if (settings.data && settings.data.indexOf('action=get_hrany_dimensions') !== -1) {
                setSelectHranySelectboxesOnEdit();
            }
        });    
    }
    
    
});    