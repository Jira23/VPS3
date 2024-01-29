
/** MODAL EVENT LISTENERS **/

jQuery(document).ready(function($) {   
    
    // click on "add new deska material" button
    $(document).on("click", "#add-group-material", function() {
        poulateModalDeskaParams({sku: "", name: "", delka: "", sirka: "", sila: "", imgUrl: ""});   // reset modal
        $("#mod_material_desky").css("display", "block");
        $("#mat-select-button").prop('disabled', 'true');
        $("#mat-select-button").attr("row-id", "add");
    });

    // click on "edit deska material" button
    $(document).on("click", ".NF-edit-group-material td:first-child", function() {
        var matParams = JSON.parse($(this).find('#mat-group-data').val());
        poulateModalDeskaParams(matParams);
        $("#mod_material_desky").css("display", "block");
        $("#mat-select-button").attr("row-id", matParams.id);
    });    
    
    // click on "save" button in deska material modal
    $("#mat-select-button").on("click", function () {    
        if($("#mat-select-button").attr("row-id") === 'add') {
            createNewDeskaMat();
        } else {
            editDeskaMat($(this));
        }
        //$("#mat-select-button").attr("row-id", "");
        $("#mod_material_desky").css("display", "none");
    });
    
    // click on "edit edge material" button
    $(document).on("click", ".mat-selector", function() {
        var jsonString = $(this).closest('tr').find('#params').val();
        var edgeParams = JSON.parse(jsonString);
        poulateModalHranaParams(edgeParams);
        setEdgeType($(this).closest('tr').find('#hrana_type').val());
        displayEdgeType();

        // disable "odlisna" when is pdk
       if(edgeParams.isPDK) {
           $('div.modal-edge-type-wrapper li:eq(1)').addClass('disabled-style'); 
       } else {
           $('div.modal-edge-type-wrapper li:eq(1)').removeClass('disabled-style');
       }
       
        $("#edge-select-button").attr("row-id", $(this).closest('tr').attr('row-id'));      // add row id to save button, so it can be identified when saving

        $("#mod_material_hrany").css("display", "block"); 
    });   
    
    // click on "save" button in deska material modal
    $("#edge-select-button").on("click", function () {    
        var rowId = $(this).attr('row-id');
        var partRow = $("tr[row-id='" + rowId + "']");
        var edgeType = $("input[name='modal-edge-type']:checked").val();
        var edgeId = '0';
        
        if(edgeType == '0') edgeId = $('#modal-hrana-mat-id-same').val();    
        if(edgeType == '1') {
            var newMatParams = {};
            newMatParams.diffEdgeId = $('#modal-hrana-mat-id-different').val();
            newMatParams.diffEdgeName = $('#modal-hrana-mat-nazev-different').html();
            newMatParams.diffEdgeImgUrl = $('#icon-hrana-different > img').attr('src');             
            
            rowParams = JSON.parse(partRow.find('#params').val());              // add/modify new different edge props
            Object.assign(rowParams, newMatParams);
            partRow.find('#params').val(JSON.stringify(rowParams));

            populateEdgeSelectboxes(rowId);
            
            edgeId = $('#modal-hrana-mat-id-different').val();
        }
        
        partRow.find('#hrana_type').val(edgeType);
        partRow.find('#hrana_id').val(edgeId);
        
        manageEdgeSelectors(rowId);
        
        $("#mod_material_hrany").css("display", "none");
    }); 
    
    // on edge modal selector change
    $("input[name='modal-edge-type']").on("change", function(){
        displayEdgeType();
    }); 

    // Close the modal when the close button is clicked
    $(".close").on("click", function () {                                       
        closeMatModal();
    });        
    
    function setEdgeType(edgeType){
        $('input[name="modal-edge-type"][value="' + edgeType + '"]').prop('checked', true);
    }    
    
});