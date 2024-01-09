jQuery(document).ready(function($) {    

    $(document).on("click", ".mat-selector", function() {
        var jsonString = $(this).closest('tr').find('#params').val();
        var productData = JSON.parse(jsonString);
        poulateModalDeskaParams(productData);
        setEdgeType(productData['edgeType']);
        displayEdgeType();

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
        }        
        
        if(productData.edgeType === '0'){
            row.find('#material_hrana .mat-icon img').attr('src', productData.edgeImgUrl);
            row.find('#material_hrana .mat-title').html(productData.edgeName);
            row.find('#hrana_id').val(productData.edgeId);
        }

        if(productData.edgeType === '1'){
            row.find('#material_hrana .mat-icon img').attr('src', productData.diffEdgeImgUrl);
            row.find('#material_hrana .mat-title').html(productData.diffEdgeName);
            row.find('#hrana_id').val(productData.diffEdgeId);
        }        
        
        $("#mod_material_desky").css("display", "none");
    });

    window.poulateModalDeskaParams = function(obj){                             // global function, inserts values for deska
        $('#modal-deska-mat-sku').html(obj.sku);
        $('#modal-deska-mat-nazev').html(obj.name);
        $('#modal-deska-mat-delka').html(obj.delka);
        $('#modal-deska-mat-sirka').html(obj.sirka);
        $('#modal-deska-mat-sila').html(obj.sila);
        $('#icon-deska > img').attr('src', obj.imgUrl);
        
        $('#modal-hrana-mat-nazev-same').html(obj.edgeName);
        $('#icon-hrana-same > img').attr('src', obj.edgeImgUrl);

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
    
    
    
    //$('.parts-table').on('change', 'tbody tr:last-child input, tbody tr:last-child select', function() {
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
        var clonedRow = $(this).closest('tr').clone();
        rowCount = parseInt($('.parts-table tbody tr:last').attr('row-id'), 10);
        setRowCount(clonedRow, rowCount);
        clonedRow.insertBefore($('.parts-table tbody tr:last'));
        setRowCount($('.parts-table tbody tr:last') , rowCount + 1);
       
    });

    // delete row
    $(document).on('click', 'button[name="btn_smazat_radek"]', function() {
        $(this).closest('tr').remove();
    });
    
    
    function setRowCount(row, newCount){
        row.find('#params').val('{"row_id":' + newCount + '}');
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
    
    addRow();                                                                   // run on every page load

});