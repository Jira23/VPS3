
/** EVENT LISTENERS **/

jQuery(document).ready(function($) {   
 
    // add row when input changes in empty row
    $(document).on('change', '[name^="parts[empty]"]', function() {
        addRow($(this).closest('tr'));
    });     
  
    // hrana dokola
    $(document).on('mousedown click', '#hrana-dokola', function() {
        
        var thisRow = $(this).closest('tr');
        var selectedOptionValue = $(this).val();
        var selectedOptionText = $(this).find('option:selected').text();
        var dimension = extractDimension(selectedOptionText);

        var newOption = $('<option>', {value: selectedOptionValue,text: dimension});    

        var selectBoxesVisible = thisRow.find('.parts-table-selectbox-edge:not(#hrana-dokola)');
        selectBoxesVisible.each(function () {
            $(this).empty();
            $(this).append(newOption.clone());       
            $(this).trigger('change');
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
        disableSaveButtons();
    });    

    // delete material group
    $(document).on('click', 'button[name="btn_smazat_material"]', function() {
        var betweenTrs = $(this).closest('.NF-edit-group-material').nextUntil($('tr.NF-edit-group-material'));          // remove all part rows of material group 
        betweenTrs.each(function () {
            $(this).remove();
        });        
        $(this).closest('.NF-edit-group-material').remove();                    // remove material group row
        
        disableSaveButtons();
    });     
    
    // figure formula
    $(document).on('change', '.parts-table-input-figure', function() {
        applyFormula($(this).closest('tr'));
    });


    // edge selectbox handling
    $(document).on('mousedown', '.parts-table-selectbox-edge', function(event) {
        if (!$(event.target).is('option')) optionTextAsFullname($(this));       // fill edge selectbox with full text options        
    });

    // edge selectbox handling
    $(document).on('change', '.parts-table-selectbox-edge', function(event) {
        optionTextAsDim($(this));                                               // fill edge selecboxt with dimension only
    });

    // form not saved alert
    $(document).on('change', 'input, select, textarea, input[type="checkbox"]', function() {
        $('.NF-alert-not-saved').show();
    });
   
    $("button[name='btn_optimalizovat']").click(function() {
        checkQuantityInputs();
        checkDimInputs();
        if(disableSaveButtons()) return;        
   
        var buttonIndex = $(this).index("button[name='btn_optimalizovat']");
        var formData = $('#mainForm').serialize(); // Serialize form data
        var formId = new URLSearchParams(window.location.search).get('form_id');                            // get form id value from page URL        

        formData += '&action=handle_editor_form&btn_ulozit_zadani&form_id=' + formId;

        $.ajax({
          type: "POST",
          url: NF_ajaxUrl,
          data: formData,
          success: function(response) {

            $('.NF-alert-not-saved').hide();
            $('#optimized-block').show();
            $('#optimized-results-table').html('');
            showWaitingIcon($('#optimized-results-table'), true);
            $('#optimized-results-table').append('<h5>Probíhá optimalizace. Při velkém počtu dílů může trvat i několik minut.</h5>');
            if (buttonIndex === 0) $(window).scrollTop($('#optimized-results-table').offset().top);             // on top button click, scroll down
            
            var request = {'action': 'optimize', 'form_id' : formId};                                           // pripravim parametry pro AJAX volani
            ajaxRequest(request, $("#optimized-results-table"));                                                // zavolam AJAX a vykreslim vysledek

          },
          error: function(error) {
            alert('Optimalizace se nezdařila. Kontaktujte nás prosím na emailu pavel.zitka@drevoobchoddolezal.cz');
            console.error("Error:", error);
          }
        });
    });
    
    // AJAX dotaz, nacte seznam desek z kategorie nebo tagu vybrane ve stome
    $('[id*=div_g_div_tree_product_catnode_], [id*=div_g_div_tree_product_tagnode_]').click(function(e) {  
        
        var isVisible = jQuery(this).find('i').css('display');
        if(isVisible !== 'none') return;                                                                    // show list for last category only

        $("#mat-select-button").prop('disabled', true);
        $("#modal-deska-mat-info").hide();        
        
        jQuery('.ptree-selected').removeClass('ptree-selected');                                            // vymazu oznaceni vybrane kategorie (predchozi)
        jQuery(this).addClass('ptree-selected');                                                            // oznacim vybranou kategorii
        showWaitingIcon(jQuery('#modal-deska-products-list'));
        
        var originTree = jQuery(this).attr('id').replace(/div_g_div_tree_|node_\d+$/g, "");                                    // identify source tree
        var thisId = jQuery(this).attr('id').replace('div_g_div_tree_' + originTree + 'node_', '');                            // id of node where user clicked

        if(originTree == 'product_cat') var request = {'action': 'get_desky','keyword': tree_category.getNode(thisId).addional, 'source':'ptree'};
        if(originTree == 'product_tag') var request = {'action': 'get_desky','keyword': tree_tag.getNode(thisId).addional, 'source':'ptree-tag'};

        ajaxRequest(request, jQuery("#modal-deska-products-list"));

    });      

    function showWaitingIcon(target) {
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