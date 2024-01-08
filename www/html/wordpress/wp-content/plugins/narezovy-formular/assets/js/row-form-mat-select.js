jQuery(document).ready(function($) {    

    $('#material_deska, #material_hrana').on("click", function(){               // Open the modal when the button is clicked
        var jsonString = $(this).closest('tr').attr('deska-params');
        var productData = JSON.parse(jsonString);
        poulateModalDeskaParams(productData);
        setEdgeType(productData['edgeType']);
        displayEdgeType();
        
        $("#mod_material_desky").css("display", "block");
    });

    $(".close").on("click", function () {                                       // Close the modal when the close button is clicked
        $("#mod_material_desky").css("display", "none");
    });

    $("input[name='modal-edge-type']").on("change", function(){
        displayEdgeType();
    });        


    $("#mat-select-button").on("click", function () {
        console.log($('#modal-deska-mat-id').val());
    });

    window.poulateModalDeskaParams = function(obj){                             // global function, inserts values for deska
console.log(obj);        
        $('#modal-deska-mat-sku').html(obj.sku);
        $('#modal-deska-mat-nazev').html(obj.name);
        $('#modal-deska-mat-delka').html(obj.delka);
        $('#modal-deska-mat-sirka').html(obj.sirka);
        $('#modal-deska-mat-sila').html(obj.sila);
        $('#icon-deska > img').attr('src', obj.imgUrl);
        $('#modal-deska-mat-id').val(obj.id);

        $('#modal-hrana-mat-nazev-same').html(obj.edgeName);
        $('#icon-hrana-same > img').attr('src', obj.edgeImgUrl);
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
    };
    
    function setEdgeType(edgeType){
        $('input[name="modal-edge-type"][value="' + edgeType + '"]').prop('checked', true);
    }

});