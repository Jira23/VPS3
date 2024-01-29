jQuery(document).ready(function($) {

    // create new deska material row
    window.createNewDeskaMat = function(){  
        var clonedRow = $('.NF-edit-group-material-empty').clone();                                     // clone row from hidden row
        clonedRow.removeClass('NF-edit-group-material-empty').addClass('NF-edit-group-material');
        clonedRow.show();
        
        var newMatParams = $('#modal-deska-mat-data').val();                    // get params from modal
        clonedRow.find('#mat-group-data').val(newMatParams);                    // set params to material group row
        $(".parts-table tbody").append(clonedRow);
        var newMatRow = $(".parts-table tbody tr:last");

        populateEditMatRow(newMatRow);                                          // change row look according to mat params

        var clonedRow = $('#empty-row').clone();                                // clone hidden part row
        clonedRow.removeAttr('id');
        clonedRow.show();  
        clonedRow.find('button').hide();
        clonedRow.find('.mat-title').html('');
        clonedRow.find('.mat-icon img').attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');      // transparent image                
        clonedRow.insertAfter(newMatRow);
    };

    // edit params in material group row
    window.editDeskaMat = function(button){  
        var newMatParams = $('#modal-deska-mat-data').val();                    // get params from modal
        var rowId = button.attr('row-id');                                      // get id of row is being edited
        var matRowInfo = $('#' + rowId);
        matRowInfo.find('#mat-group-data').val(newMatParams);

        populateEditMatRow(matRowInfo);
        resetPartRows(matRowInfo.closest('.NF-edit-group-material'));
    };
    
    // used when deska materal was changed - resets all necesary inputs in all material group part rows
    window.resetPartRows = function(matRow){                                        
        var betweenTrs = matRow.nextUntil($('tr.NF-edit-group-material'));          // get all part rows of material group 
        betweenTrs.not(':last').each(function () {
            var matGroupProps = JSON.parse(matRow.find('#mat-group-data').val());            
            $(this).find('#params').val(JSON.stringify(matGroupProps));
            $(this).find('.parts-table-selectbox-tupl').val('NE');                              // reset tupl
            $(this).find('.parts-table-selectbox-tupl').attr('readonly', matGroupProps.isPDK);  // disable when is PDK                       
            modifyRowData($(this), matGroupProps);
        });
    };

    populateEditMatRow = function(row){  
        var matParams = JSON.parse(row.find('#mat-group-data').val());
        row.attr('id', matParams.id);
        row.find('#group-material-icon img').attr('src', matParams.imgUrl);
        row.find('#group-material-nazev').html(matParams.name);
    };

    // find last part row in global context (counts all part rows in form)
    window.findLastRowId = function(){
        var highestRowId = -1;
        var $targetRow = null;
        
        $('table tbody tr').each(function() {
           var rowId = parseInt($(this).attr('row-id'));
           if (!isNaN(rowId) && rowId > highestRowId) highestRowId = rowId;
         });        
         
         return highestRowId;
    };
    
    // find last part row in group (counts all part rows in group)
    window.findLastGroupRowId = function(matRow){
        var betweenTrs = matRow.nextUntil($('tr.NF-edit-group-material'));          // get all part rows of material group     
        var lastGroupRow = betweenTrs.eq(betweenTrs.length - 2);
        var lastGroupRowId = lastGroupRow.attr("row-id");
        
        return lastGroupRowId;
    };    
    
    // add part row at the end of material group
    window.addRow = function(lastRow, onPageLoad = false){

        // clone hidden row
        var clonedRow = $('#empty-row').clone();
        clonedRow.removeAttr('id');
        clonedRow.show();  
        clonedRow.find('button').hide();
        clonedRow.find('.mat-title').html('');
        clonedRow.find('.mat-icon img').attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');      // transparent image
        clonedRow.insertAfter(lastRow);

        // modify new row props
        var rowCount = findLastRowId() + 1;                                     // set global index of row to be modified
        
        $('tr[row-id="' + rowCount + '"]').find('button').show();
        if(!onPageLoad) {
            setRowCount(lastRow, rowCount );

            var matGrouptRow = lastRow.prevAll('tr.NF-edit-group-material').first();
            var matGroupProps = JSON.parse(matGrouptRow.find('#mat-group-data').val());
            modifyRowData(lastRow, matGroupProps);
            lastRow.find('button').show();
        }
        

    };
    
    // modify props in part row acording to data in material row
    function modifyRowData(partRow, matGroupProps){
            partRow.find('#lamino_id').val(matGroupProps['id']);
            partRow.find('#hrana_id').val(matGroupProps['edgeId']);
            
            partRow.find('input[name*="delka_dilu"]').attr('max', matGroupProps.delka);
            partRow.find('input[name*="sirka_dilu"]').attr('max', matGroupProps.sirka);

            if(matGroupProps['edgeId'] == false) {
                partRow.find('#hrana_type').val('-1');
            } else {
                partRow.find('#hrana_type').val('0');
            }
            manageEdgeSelectors(partRow.attr('row-id'));            
    }
    
    // change id of part row (used when new row is cloned)
    window.setRowCount = function(row, newCount){

        if(typeof row.find('#params').val() == 'undefined' || row.find('#params').val() == ''){                                    //  when add empty row
            var matParams = row.prevAll('.NF-edit-group-material:first').find('#mat-group-data').val();
            row.find('#params').val(matParams);
        } else {                                                                // when clone row
            var paramsObject = JSON.parse(row.find('#params').val());
            paramsObject.row_id = newCount;
            row.find('#params').val(JSON.stringify(paramsObject));
        }

        var groupRowIndex = parseInt(row.prev('tr').find('#group-number').val()) + 1;
        if(isNaN(groupRowIndex)) groupRowIndex = 1;                                             // when there is no row above
        row.find('#group-number').val(groupRowIndex);

        row.find('td:first-child').html(groupRowIndex);

        row.attr('row-id', newCount);

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
    };
    
    // add empty part rows at the end of all material groups (used on page load)
    function addRows(){
        $('tr.NF-edit-group-material').each(function() {
            var nextGroup = $(this).nextAll('tr.NF-edit-group-material').first();  // Find the next 'NF-edit-group-material' or the end of the table
            var lastRow;

            if (nextGroup.length) {
                lastRow = nextGroup.prev('tr');                                 // If another 'NF-edit-group-material' is found, get the previous row
            } else {
                lastRow = $(this).siblings('tr').last();                        // If no more 'NF-edit-group-material', get the last row in the table
            }
            addRow(lastRow, true);
        });        
    }
    
    
    /*** run on every page load ***/
    addRows();
    colorRows();
    
     // Prevent the Enter key submitting the form and make it behive like Tab instead (jump to next input field)
    $(document).ready(function() {
        $('input').keydown(function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                var inputs = $(this).closest('form').find(':input');                      // Get all input elements within the same form
                var nextInput = inputs.eq(inputs.index(this) + 1);                        // Get the next input element
                if (nextInput.length) nextInput.focus();                                  // Focus on the next input element
            }
        });
    });
    
    $(document).on("keydown", function(e) {                                     // close mat modaln on hit Esc
        if (e.key === "Escape" || e.key === "Esc") {
            closeMatModal();
        }
    });    
    
});