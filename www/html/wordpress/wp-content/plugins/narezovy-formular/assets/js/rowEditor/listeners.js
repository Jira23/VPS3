
/** EVENT LISTENERS **/

jQuery(document).ready(function($) {   
 
    // add row when input changes in empty row
    $(document).on('change', '[name^="parts[empty]"]', function() {
        addRow($(this).closest('tr'));
    });     
  
    // hrana dokola
    $(document).on('change', '#hrana-dokola', function() {
        var thisRow = $(this).closest('tr');
        var selectedOption = $(this).val();
        var selectBoxes = thisRow.find('.parts-table-selectbox-edge');
        selectBoxes.each(function () {
          $(this).val(selectedOption);
        });        
    });   
  
    // tupl
    $(document).on('change', '.parts-table-selectbox-tupl', function() {
        var rowId = $(this).closest('tr').attr('row-id');
        populateEdgeSelectboxes(rowId);
    });      
    
    // clone row
    $(document).on('click', 'button[name="btn_duplikovat_radek"]', function() {
        var clonedRow = $(this).closest('tr').clone(true);
        
        // clone selectboxes selected values (these are not cloned by clone());
        $(this).closest('tr').find('select').each(function(index, element) {
            var selectedValue = $(element).val();
            clonedRow.find('select:eq(' + index + ')').val(selectedValue);
        });        
        
        var rowCount = findLastRowId() + 1;                                                     // get global last part row
        setRowCount(clonedRow, rowCount);                                                       // set hidden global index to row

        var matRow = $(this).closest('tr').prevAll('.NF-edit-group-material').first();          // find material group row
        var lastGroupRow = $('tr[row-id="' + findLastGroupRowId(matRow) + '"]') ;               // find last row in material group
        var lastGroupRowGroupNumber = parseInt(lastGroupRow.find('#group-number').val());       // get the last row local index
        
        clonedRow.find('td:first').html(lastGroupRowGroupNumber + 1);                           // set local index to <td>
        clonedRow.find('#group-number').val(lastGroupRowGroupNumber + 1);                       // set local index to hidden input
        clonedRow.insertAfter(lastGroupRow);

    });    
    
    // delete row
    $(document).on('click', 'button[name="btn_smazat_radek"]', function() {
        $(this).closest('tr').remove();
    });    
    
    // figure formula
    $(document).on('change', '.parts-table-input-figure', function() {
        applyFormula($(this).closest('tr'));
    });  
    
    
    
    
    
    
    /** TESTOVACI ULOZENI FORMULARE PRES AJAX **/
    
    //$(document).on('click', 'button[name="btn_save"]', function() {
    $("button[name='btn_optimalizovat']").click(function() {
        var formData = $('#mainForm').serialize(); // Serialize form data
        var formId = new URLSearchParams(window.location.search).get('form_id');                            // get form id value from page URL        

        formData += '&action=handle_editor_form&btn_ulozit_zadani&form_id=' + formId;

        $.ajax({
          type: "POST",
          url: NF_ajaxUrl,
          data: formData,
          success: function(response) {

            $('#optimized-block').show();
            $('#optimized-results-table').html('');
            showWaitingIcon($('#optimized-results-table'), true);
            $('#optimized-results-table').append('<h5>Probíhá optimalizace. Při velkém počtu dílů může trvat i několik minut.</h5>');

            if($("button[name='btn_optimalizovat']").index(this) === 0) $(window).scrollTop($('#optimized-results-table').offset().top);       // on top button click, scroll down
            
            var request = {'action': 'optimize', 'form_id' : formId};                                           // pripravim parametry pro AJAX volani
            ajaxRequest(request, $("#optimized-results-table"));                                                // zavolam AJAX a vykreslim vysledek

          },
          error: function(error) {
            alert('Nepodarilo se ulozit formular');
            console.error("Error:", error);
          }
        });
    });


    function showWaitingIcon(target) {
        target.html('<h3>Probíhá optimalizace...</h3>');
        target.append('<img width="200" id="loadingIcon" src="' + NF_wpUrl + '/wp-content/plugins/narezovy-formular/assets/img/Loading_icon.gif" />');        
    };
    
    function ajaxRequest (request, target = false) {

        latestRequest = request;                                                // used for strategy to show last request if there are multiple
        latestTarget = target;

        $.ajax({
            url: NF_ajaxUrl,
            type: 'POST',
            data: request,
            success: function (response) {
                if (request === latestRequest && target === latestTarget) {     // Check if this is the response for the latest request
                    if(target) target.html(response);
                }
            }
        });
    }   

    
});