jQuery(document).ready(function($) {    

    $(document).on("click", ".mat-selector", function() {
        
        var jsonString = $(this).closest('tr').find('#params').val();
        var productData = JSON.parse(jsonString);
        poulateModalDeskaParams(productData);
        setEdgeType(productData['edgeType']);
        displayEdgeType();

        // disable "odlisna" when is pdk
        $('.modal-edge-type-wrapper input[type="radio"][value="1"]').prop('disabled', productData.isPDK);
        $('#modal-input-hrana').hide(productData.isPDK);

        $("#mod_material_desky").css("display", "block");
    });

    $(".close").on("click", function () {                                       // Close the modal when the close button is clicked
        $("#mod_material_desky").css("display", "none");
        $('#modal-deska-products-list').html('');
        $("#modal-deska-mat-info").show();
        $('#modal-hrana-products-list').html('');
        $("#modal-hrana-mat-info").show();
        colapseTree();
    });

    $("input[name='modal-edge-type']").on("change", function(){                 // oc mat type selector change
        displayEdgeType();
    });        


    $("#mat-select-button").on("click", function () {
        var jsonString = $('#modal-deska-mat-data').val();
        var productData = JSON.parse(jsonString);
        
        var row = $('tr[row-id="' + productData.row_id  + '"]');
        row.find('#params').val(JSON.stringify(productData));                          // save mat params

        row.find('#material_deska .mat-icon img').attr('src', productData.imgUrl);      // modify data in row
        row.find('#material_deska .mat-title').html(productData.name);
        row.find('#lamino_id').val(productData.id);
        row.find('#hrana_type').val(productData.edgeType);

        if(productData.edgeType === '-1'){
            row.find('#material_hrana .mat-icon img').attr('src', '/wp-content/plugins/narezovy-formular2/assets/img/icon_plus.png');
            row.find('#material_hrana .mat-title').html('');
            row.find('#hrana_id').val(0);
            toggleEdgeSelectboxesReadOnly(productData.row_id, true);
            populateEdgeSelectboxes(productData);
        }        
        
        if(productData.edgeType === '0'){
            row.find('#material_hrana .mat-icon img').attr('src', productData.edgeImgUrl);
            row.find('#material_hrana .mat-title').html(productData.edgeName);
            row.find('#hrana_id').val(productData.edgeId);
            toggleEdgeSelectboxesReadOnly(productData.row_id, false);
            populateEdgeSelectboxes(productData);
        }

        if(productData.edgeType === '1'){
            row.find('#material_hrana .mat-icon img').attr('src', productData.diffEdgeImgUrl);
            row.find('#material_hrana .mat-title').html(productData.diffEdgeName);
            row.find('#hrana_id').val(productData.diffEdgeId);
            toggleEdgeSelectboxesReadOnly(productData.row_id, false);
            populateEdgeSelectboxes(productData);
        }        
        
        row.find('#params').trigger('change');                                  // trigger change to add new row under
        $("#mod_material_desky").css("display", "none");
    });

    window.poulateModalDeskaParams = function(obj){                             // global function, inserts values for deska
        $('#modal-deska-mat-sku').html(obj.sku);
        $('#modal-deska-mat-nazev').html(obj.name);
        $('#modal-deska-mat-delka').html(obj.delka);
        $('#modal-deska-mat-sirka').html(obj.sirka);
        $('#modal-deska-mat-sila').html(obj.sila);
        $('#icon-deska > img').attr('src', obj.imgUrl);

        if(obj.edgeName === false) {
            $('#modal-hrana-mat-nazev-same').html('Pro tuto desku přivzorovaná hrana neexistuje.<br>Zvolte "Žádná" nebo "Odlišná"');
            $('#same-edge-valid-edge').hide();
            $('#same-edge-no-edge').show();
        } else {
            $('#modal-hrana-mat-nazev-same').html(obj.edgeName);
            $('#icon-hrana-same > img').attr('src', obj.edgeImgUrl);
            $('#same-edge-valid-edge').show();
            $('#same-edge-no-edge').hide();            
        }

        $('#modal-hrana-mat-nazev-different').html(obj.diffEdgeName);
        $('#icon-hrana-different > img').attr('src', obj.diffEdgeImgUrl);

        // modify hidden data
        var jsonString = $('#modal-deska-mat-data').val();
        var productData = JSON.parse(jsonString);        
        var toSave = $.extend(productData, obj);
        $('#modal-deska-mat-data').val(JSON.stringify(toSave));
    };

    window.poulateModalHranaParams = function(obj){                             // global function, inserts values for hrana
        $('#modal-hrana-mat-nazev-different').html(obj.name);
        $('#icon-hrana-different > img').attr('src', obj.imgUrl);
        $('#modal-hrana-mat-id').val(obj.id);
    };

    window.displayEdgeType = function(){

        edgeType = $("input[name='modal-edge-type']:checked").val();
        
        if(edgeType === '-1') {
            $('#no-edge').show();
            $('#same-edge').hide();
            $('#different-edge').hide();
            $('#modal-input-hrana').hide();
        }

        if(edgeType === '0') {
            $('#no-edge').hide();
            $('#same-edge').show();
            $('#different-edge').hide();
            $('#modal-input-hrana').hide();
        }

        if(edgeType === '1') {
            $('#no-edge').hide();
            $('#same-edge').hide();
            $('#different-edge').show();            
            $('#modal-input-hrana').show();

        }

        var jsonString = $('#modal-deska-mat-data').val();                      // modify hidden mat props
        var productData = JSON.parse(jsonString);                
        productData.edgeType = edgeType;
        $('#modal-deska-mat-data').val(JSON.stringify(productData));        
        
    };
    
    function setEdgeType(edgeType){
        $('input[name="modal-edge-type"][value="' + edgeType + '"]').prop('checked', true);
    }
    
    // colapse treemenu if its expanded
    function colapseTree(){
        var mainCatId = $('#div_tree').find('ul > li:first').attr('id').match(/\d+/g);
        if(!tree.getNode(mainCatId).foldedStatus) tree.getNode(mainCatId).toggleNode();       
    }   
    
    $('.parts-table').on('change', 'tbody tr:last-child', function() {
        addRow();
    });
    

    
    function addRow(){
        var clonedRow = $('#empty-row').clone();
        clonedRow.removeAttr('id');

        clonedRow.show();
        
        if($('.parts-table tbody tr').length === 0){                                    // if there is no tr in table, set beginning row to 0
            rowCount = 0;
        } else {
            rowCount = parseInt($('.parts-table tbody tr:last').attr('row-id'), 10);
        }

        clonedRow.find('button').hide();
        $('tr[row-id="' + rowCount + '"]').find('button').show();                

        setRowCount(clonedRow, rowCount + 1);

        $('.parts-table tbody').append(clonedRow);        

    }
    
    // clone row
    $(document).on('click', 'button[name="btn_duplikovat_radek"]', function() {
        var clonedRow = $(this).closest('tr').clone(true);
        
        // clone selectboxes selected values (these are not cloned by clone());
        $(this).closest('tr').find('select').each(function(index, element) {
            var selectedValue = $(element).val();
            clonedRow.find('select:eq(' + index + ')').val(selectedValue);
        });        
        
        rowCount = parseInt($('.parts-table tbody tr:last').attr('row-id'), 10);
        setRowCount(clonedRow, rowCount);

        clonedRow.insertBefore($('.parts-table tbody tr:last'));
        setRowCount($('.parts-table tbody tr:last') , rowCount + 1);
    });

    // delete row
    $(document).on('click', 'button[name="btn_smazat_radek"]', function() {
        $(this).closest('tr').remove();
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
        var thisRow = $(this).closest('tr');
        var productData = JSON.parse(thisRow.find('#params').val());
        populateEdgeSelectboxes(productData);
    });    
    
    function setRowCount(row, newCount){

        if(row.find('#params').val() == ''){                                    //  when add empty row
            row.find('#params').val('{"row_id":' + newCount + '}');
        } else {                                                                // when clone row
            var paramsObject = JSON.parse(row.find('#params').val());
            paramsObject.row_id = newCount;
            row.find('#params').val(JSON.stringify(paramsObject));
        }

        
        row.attr('row-id', newCount);        
        row.find('td:first-child').html(newCount);
        
        //  the row index is in the name attribute of the first input/select in the row
        var firstInputName = row.find('input, select').first().attr('name');    
        var toReplace = firstInputName.match(/parts\[(.*?)\]/)[1];        
        
        // Update the name attributes of inputs and selects in the cloned row
        row.find('input, select').each(function() {
            if($(this).attr('name') !== undefined) {
                var newName = $(this).attr('name').replace('parts[' + toReplace + ']', 'parts[' + newCount + ']');
                $(this).attr('name', newName);
            }
        });
    }
    
    function toggleEdgeSelectboxesReadOnly(rowId, state){
        var row = $('tr[row-id="' + rowId  + '"]');
        var selectBoxes = row.find('.parts-table-selectbox-edge');
        selectBoxes.each(function () {
          state ? $(this).attr('readonly', 'readonly') : $(this).removeAttr('readonly');
        });        
    }

    function populateEdgeSelectboxes(productData){
        var row = $('tr[row-id="' + productData.row_id  + '"]');
        var selectBoxes = row.find('.parts-table-selectbox-edge');
        var tupl = row.find('.parts-table-selectbox-tupl').val();

        var request = {'action': 'get_hrany_props', 'product_id': productData.id, 'dekor': '', 'tupl' : tupl, 'dims' : true};                                            // pro zadnou a privzorovanou hranu
        if(productData.edgeType === '1') var request = {'action': 'get_hrany_props', 'product_id': productData.id, 'dekor': productData.diffEdgeName ,'tupl' : tupl, 'dims' : true};          // pro pro odlisnou hranu
//console.log(request);        
        latestRequest = request;                                                                                            // used for strategy to show last request if there are multiple
        $.ajax({
            url: NF_ajaxUrl,
            type: 'POST',
            data: request,
            beforeSend: function() {
                loadingSelectboxes(selectBoxes, true);
            },            
            success: function (response) {
                console.log(response);
                var selectOptions = JSON.parse(response);
                
                selectBoxes.each(function () {
                    var currentSB = $(this);
                    currentSB.empty();
                    selectOptions[0] = "";

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
                if(productData.edgeType === '1') var toSave = $.extend(productData, {diffEdgeDims: selectOptions});
                row.find('#params').val(JSON.stringify(toSave));                
                loadingSelectboxes(selectBoxes, false);
            }            
        });
        
        function loadingSelectboxes(selectBoxes, state){
            selectBoxes.each(function () {
                $(this).closest('.selectbox-with-loading').find('.linear-loading-icon').toggle(state);
                $(this).parent().toggle(!state);                
            });
        }

    }    
    
    addRow();                                                                   // run on every page load

});