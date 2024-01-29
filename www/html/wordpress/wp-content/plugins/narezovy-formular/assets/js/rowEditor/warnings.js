
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

    // check if material already exists on page. If yes, disable save button    
    window.checkMatExists = function(matToCheck){
        
        // get all materials on page ids
        var matIds = [];
        $(".NF-edit-group-material").each(function() {
            matIds.push($(this).find('.group-material-info').attr('id'));
        });

        // check iff material exists on page
        if(matIds.includes(matToCheck.toString())){
            $('#alert-mat-in-form').show(500);
            $("#mat-select-button").prop('disabled', true);
        } else {
            $('#alert-mat-in-form').hide(500);
            $("#mat-select-button").prop('disabled', false);
        }
    }
    
});