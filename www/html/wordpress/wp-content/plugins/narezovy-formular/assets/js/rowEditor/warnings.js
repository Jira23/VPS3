
jQuery(document).ready(function($) {

    $(document).on('change', '.parts-table-selectbox-edge', function() {
        var thisRow = $(this).closest('tr');
        var selectBoxes = thisRow.find('.parts-table-selectbox-edge:not(#hrana-dokola)');
        var selectBoxesValues = [];
        
        selectBoxes.each(function () {
          if($(this).val() !== '0') selectBoxesValues.push($(this).val());
        });        
        
        var uniqueArray = [];
        var uniqueArray = selectBoxesValues.filter(function(item, index, arr) {
          return arr.indexOf(item) === index;
        });                

        if(uniqueArray.length >= 2) {
            thisRow.find('span.edge-warning').show();
        } else {
            thisRow.find('span.edge-warning').hide(); 
        }
    });

    // restrict user input to numbers only for ks and dimensions inputs
    $(document).on('input', '.parts-table-input-pocet, .parts-table-input-dimension', function() {
        var numericValue = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(numericValue);
    });

    $(document).on('input', '.parts-table-input-pocet, .parts-table-input-dimension', function() {
        checkQuantityInputs();
        checkDimInputs();
        disableSaveButtons();
    });


    $(document).on('click', 'button[name="btn_ulozit_zadani"]', function() {
        checkQuantityInputs();
        checkDimInputs();
        if(!disableSaveButtons()) {
            var hiddenInput = $('<input>').attr('type', 'hidden').attr('name', 'btn_ulozit_zadani');
            $('#mainForm').append(hiddenInput);                    
            $('#mainForm').submit();
        }
    });
  
    window.checkQuantityInputs = function(){    
        $('.parts-table-input-pocet').each(function() {
            if($(this).closest('tr').attr('row-id') == '') return;
     
            if($(this).val() == ''){
                $(this).closest('.input-with-warning').find('.dim-warning').show();
            } else {
                $(this).closest('.input-with-warning').find('.dim-warning').hide();
            }
        });        
    };
    
    window.checkDimInputs = function(){
        $('.parts-table-input-dimension').each(function() {
            if($(this).closest('tr').attr('row-id') == '') return;
            
            if($(this).val() == ''){
                $(this).closest('.input-with-warning').find('.dim-warning').show();
                return true;
            } else {
                $(this).closest('.input-with-warning').find('.dim-warning').hide();
            }

            if(parseInt($(this).val()) > parseInt($(this).attr('max')) || parseInt($(this).val()) < 10){
                $(this).closest('.input-with-warning').find('.dim-warning').show();
            } else {
                $(this).closest('.input-with-warning').find('.dim-warning').hide();
            }
        });        
    };

    // disable "save" and "optimize" buttons when there is a warning on page
    window.disableSaveButtons = function(){
        $('button[name="btn_ulozit_zadani"], button[name="btn_optimalizovat"]').each(function() {
            if($('.dim-warning:visible').length > 0){
                $(this).attr('disabled', 'true');
            } else {
                $(this).removeAttr('disabled');
            }
        });
        
        return $('.dim-warning:visible').length > 0 ? true : false;
    };
    
    // check if material already exists on page. If yes, disable save button    
    window.checkMatExists = function(matToCheck){

        // get all materials on page ids
        var matIds = [];
        $(".NF-edit-group-material").each(function() {
            matIds.push($(this).find('.group-material-info').attr('id'));
        });

        // check if material exists on page
        if(matIds.includes(matToCheck.toString())){
            $('#alert-mat-in-form').show(500);
            $("#mat-select-button").prop('disabled', 'true');
        } else {
            $('#alert-mat-in-form').hide(500);
            $("#mat-select-button").removeAttr('disabled');
        }
    }
    
});