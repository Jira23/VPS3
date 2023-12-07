jQuery(document).ready(function($) {    
/*    
    // odstrani atribut required pokud je zmacknuto jine tlacitko nez "ulozit dil"
    function submit_form(button){
        jQuery('#input-deska').removeAttr('required');
        jQuery('#input-hrana').removeAttr('required');
        jQuery('#delka_dilu').removeAttr('required');
        jQuery('#sirka_dilu').removeAttr('required');
        
        if(button == 'button_odeslat') {                                        // pro tlacitko "odeslat" je nutne zaskrtnout souhhlas s obch. podm.
            jQuery('#obchodni_podminky').attr('required', true);
        } else {                                                                // pro ostatni tlacitka ne
            jQuery('#obchodni_podminky').attr('required', false);
        }
        
        jQuery('#'+ button).click();                                            // kliknu na dane tlacitko
    };

    
*/    
    // manage buttons with alert
    $("button[type='button']").click(function() {
        var inputField = $("<input>")                                           // Create an input element with the name and value of clicked button
            .attr("type", "hidden")
            .attr("name", $(this).attr("name"))
            .val($(this).val());

        if($(this).attr("name") === 'btn_smazat_formular') {                    // confirmation when deleting form
            if(!confirm("Opravdu smazat?")) return;
            $("#parts-list-form").append(inputField);
            $("#parts-list-form").submit();            
        }

        if($(this).attr("name") === 'btn_smazat_dil') {                         // confirmation when deleting part
            if(!confirm("Opravdu smazat?")) return;
            $("#mainForm").append(inputField);
            $("#mainForm").submit();
        }

        if($(this).attr("name") === 'btn_delete_opt') {                         // confirmation when deleting optimalization
            if(!confirm("Opravdu odemknout? Budou odebrány výsledky optimalizace.")) return;
            $("#mainForm").append(inputField);
            $("#mainForm").submit();            
        }         
        
        if($(this).attr("name") === 'btn_duplikovat_dil') {    
            $("#mainForm").append(inputField);
            $("#mainForm").submit();      
        }
            
        if($(this).attr("name") === 'btn_duplikovat_formular') {    
            $("#parts-list-form").append(inputField);
            $("#parts-list-form").submit();             
        }
    });

    $('#optimized-results-table').on('DOMSubtreeModified', function() {         // lock parts table
        if ($('#optimized-results-table').find('tr').length) {
            $('.parts-table-overlay').show();
        }
    });
 
    // odstrani praram. required z selectboxu lepidlo, pokud uzivatel nevybere zadnou hranu 
    jQuery('#btn_ulozit_dil').on("click", function() {
        onSubmitLepidlo();
    });    
    
    jQuery("[name='btn_ulozit_zadani']").on("click", function() {
        onSubmitLepidlo();        
        $('#input-deska').removeAttr('required');                               // remove param. required from form inputs
        $('#hrana_id').removeAttr('required');
        $('#delka_dilu').removeAttr('required');
        $('#sirka_dilu').removeAttr('required');
     }); 
    
    function onSubmitLepidlo(){
        if($('#select-hrana-horni').val() == 0 && $('#select-hrana-dolni').val() == 0 && $('#select-hrana-prava').val() == 0 && $('#select-hrana-leva').val() == 0 ){
            $("#lepidlo").prop("required", false);
        } else {
            $("#lepidlo").prop("required", true);
        }        
    }
    
    
    // zobrazi alert o neulozenem dilu, pokud se zmeni nektery z inputu pro dil
    var partFormElements = $('#dil-horni-cast, #dil-spodni-cast').find('input, select');
    partFormElements.on("change", function() {
        jQuery('#save-alert').show();
    });    

    // shows alert when hrana rozmery are different
    jQuery('#select-hrana-leva, #select-hrana-horni, #select-hrana-prava, #select-hrana-dolni' ).on("change", function(){
        var hranyRozmerSelectboxes = ['select-hrana-leva', 'select-hrana-horni', 'select-hrana-prava', 'select-hrana-dolni'];
        var selectedOptions = [];
        
        for (var i = 0; i < hranyRozmerSelectboxes.length; i++) {
            var selectedValue = $('#' + hranyRozmerSelectboxes[i] + ' option:selected').val();        
            selectedOptions[i] = selectedValue !== '0' ? selectedValue : '';
        }
        
        var uniqueArray = selectedOptions.filter((value, index, self) => self.indexOf(value) === index);                // remove duplicities from array
        var filteredArray = uniqueArray.filter(function(value) { return value !== "";});                                // remove "" from array
        
        jQuery('#hrana-alert').toggle(filteredArray.length >= 2);
    });
    
});
