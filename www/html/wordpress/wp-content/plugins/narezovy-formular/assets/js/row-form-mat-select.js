jQuery(document).ready(function($) {    

    $('#material_deska, #material_hrana').on("click", function(){          // Open the modal when the button is clicked
        var jsonString = $(this).closest('tr').attr('deska-params');
        var productData = JSON.parse(jsonString);
        poulateModalDeskaParams(productData);
        $("#mod_material_desky").css("display", "block");
    });

    $(".close").on("click", function () {                                   // Close the modal when the close button is clicked
        $("#mod_material_desky").css("display", "none");
    });

    $("input[name='modal-edge-type']").on("change", function(){                                                   // deska input
        poulateEdgeMatModal();
    });        

    window.poulateModalDeskaParams = function(obj){                             // global function, inserts values for deska
        $('#modal-deska-mat-sku').html(obj.sku);
        $('#modal-deska-mat-nazev').html(obj.name);
        $('#modal-deska-mat-delka').html(obj.delka);
        $('#modal-deska-mat-sirka').html(obj.sirka);
        $('#modal-deska-mat-sila').html(obj.sila);
        $('#icon-deska > img').attr('src', obj.imgUrl);
        $('#modal-deska-mat-id').val(obj.id);
    };

    window.poulateModalHranaParams = function(obj){                             // global function, inserts values for hrana
        $('#modal-hrana-mat-nazev').html(obj.name);
        $('#icon-hrana > img').attr('src', obj.imgUrl);
        $('#modal-hrana-mat-id').val(obj.id);
    };

    window.poulateEdgeMatModal = function(){

        var edgeType = $("input[name='modal-edge-type']:checked").val();
        if(edgeType === '-1') {
            $('#modal-hrana-mat-nazev').html('Deska nebude mít žádnou hranu.');
            $('#modal-input-hrana').hide();
            $('#modal-hrana-products-list').html('');
        }

        if(edgeType === '0') {
            $('#modal-hrana-mat-nazev').html('');
            $('#modal-input-hrana').hide();
            $('#modal-hrana-products-list').html('');
        }

        if(edgeType === '1') {
            $('#modal-hrana-mat-nazev').html('');
            $('#modal-input-hrana').show();
        }
    };        

});