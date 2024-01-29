jQuery(document).ready(function($) {   
    
    // reset edge selectors according to params in part row
    window.manageEdgeSelectors = function(rowId){
        var row = $('tr[row-id="' + rowId + '"]');
        var edgeType = row.find('#hrana_type').val();
        var rowParams = JSON.parse(row.find('#params').val());
        
        if(edgeType === '-1'){
            row.find('#material_hrana .mat-icon img').attr('src', '/wp-content/plugins/narezovy-formular2/assets/img/icon_plus.png');
            row.find('#material_hrana .mat-title').html('Klikněte pro přidání/editaci');
            toggleEdgeSelectboxesReadOnly(rowId, true);
        }        
        
        if(edgeType === '0'){
            row.find('#material_hrana .mat-icon img').attr('src', rowParams.edgeImgUrl);
            row.find('#material_hrana .mat-title').html(rowParams.edgeName);
            toggleEdgeSelectboxesReadOnly(rowId, false);
            populateEdgeSelectboxes(rowId);
        }

        if(edgeType === '1'){
            row.find('#material_hrana .mat-icon img').attr('src', rowParams.diffEdgeImgUrl);
            row.find('#material_hrana .mat-title').html(rowParams.diffEdgeName);
            toggleEdgeSelectboxesReadOnly(rowId, false);
            populateEdgeSelectboxes(rowId);
        }        
        
    };
    
    window.toggleEdgeSelectboxesReadOnly = function(rowId, state){
        var row = $('tr[row-id="' + rowId  + '"]');
        var selectBoxes = row.find('.parts-table-selectbox-edge');
        selectBoxes.each(function () {
            $(this).val('0');
            state ? $(this).attr('readonly', 'readonly') : $(this).removeAttr('readonly');
        });        
    };
    
    // requests edge params via API and populates selectboxes
    window.populateEdgeSelectboxes = function(rowId){

        // get part row params
        var row = $('tr[row-id="' + rowId + '"]');
        var edgeType = $("input[name='modal-edge-type']:checked").val();
        var rowParams = JSON.parse(row.find('#params').val());
        var tupl = row.find('.parts-table-selectbox-tupl').val();
        var selectBoxes = row.find('.parts-table-selectbox-edge');

        // request edge selectboxes content
        var request = {'action': 'get_hrany_props', 'product_id': rowParams.id, 'dekor': '', 'tupl' : tupl, 'dims' : true};                                            // pro zadnou a privzorovanou hranu
        if(edgeType === '1') var request = {'action': 'get_hrany_props', 'product_id': rowParams.id, 'dekor': rowParams.diffEdgeName ,'tupl' : tupl, 'dims' : true};          // pro pro odlisnou hranu
console.log('i-u');                
console.log(request);
        latestRequest = request;                                                                                            // used for strategy to show last request if there are multiple
        $.ajax({
            url: NF_ajaxUrl,
            type: 'POST',
            data: request,
            beforeSend: function() {
                loadingSelectboxes(selectBoxes, true);
            },            
            success: function (response) {
                var selectOptions = JSON.parse(response);
                
                selectBoxes.each(function () {                                  // populate selectboxes
                    var currentSB = $(this);
                    currentSB.empty();
                    $.extend({0 : ''}, selectOptions); 
//                    selectOptions[0] = "";

                    $.each(selectOptions, function(key, value) {
                      var option = $("<option></option>");
                      option.val(key);
                      option.text(value);
                      currentSB.append(option);
                    });          

                }); 
                
                // modify hidden data
                var jsonString = row.find('#params').val();
                var productData = JSON.parse(jsonString);        
                var toSave = $.extend(productData, {edgeDims: selectOptions});
                if(edgeType == '1') var toSave = $.extend(productData, {diffEdgeDims: selectOptions});
                row.find('#params').val(JSON.stringify(toSave));                

                loadingSelectboxes(selectBoxes, false);
            }            
        });
        
        function loadingSelectboxes(selectBoxes, state){                        // loadin icon for selectboxes
            selectBoxes.each(function () {
                $(this).closest('.selectbox-with-loading').find('.linear-loading-icon').toggle(state);
                $(this).parent().toggle(!state);                
            });
        }
    };
    
});