jQuery(document).ready(function($) {    
    
    window.poulateModalDeskaParams = function(obj){                             // inserts values for deska

        var delka = obj.delka;
        var sirka = obj.sirka;
        if($.inArray(2264, obj.categoryIds) !== -1){                            // increase values for mat. info in modal. Max. values for form remain unchanged (reduced by filter in ajax method). If product is in categry 2264, its dimensions are reduceb by 30 mm
            delka += 30;
            sirka += 30;
        }

        $('#modal-deska-mat-sku').html(obj.sku);
        $('#modal-deska-mat-nazev').html(obj.name);
        $('#modal-deska-mat-delka').html(delka);
        $('#modal-deska-mat-sirka').html(sirka);
        $('#modal-deska-mat-sila').html(obj.sila);
        $('#icon-deska > img').attr('src', obj.imgUrl);

        // modify hidden data
        var jsonString = $('#modal-deska-mat-data').val();
        var productData = JSON.parse(jsonString);        
        var toSave = $.extend(productData, obj);
        $('#modal-deska-mat-data').val(JSON.stringify(toSave));
    };
    
    window.poulateModalHranaParams = function(obj){                             // inserts values for hrana

        if(obj.hasOwnProperty('id')) $('#modal-hrana-mat-id-same').val(obj.id);
        if(obj.hasOwnProperty('imgUrl')) $('#icon-hrana > img').attr('src', obj.imgUrl);

        if(obj.edgeName === false) {
            $('#modal-hrana-mat-nazev-same').html('Pro tuto desku přivzorovaná hrana neexistuje.<br>Zvolte "Žádná" nebo "Odlišná"');
            $('#same-edge-valid-edge').hide();
            $('#same-edge-no-edge').show();
            $('#modal-hrana-mat-has-same-edge').val('false');
            
        } else {
            $('#modal-hrana-mat-nazev-same').html(obj.edgeName);
            $('#icon-hrana-same > img').attr('src', obj.edgeImgUrl);
            $('#same-edge-valid-edge').show();
            $('#same-edge-no-edge').hide();            
            $('#modal-hrana-mat-has-same-edge').val('true');
        }

        if(obj.hasOwnProperty('diffEdgeId')) $('#modal-hrana-mat-id-different').val(obj.diffEdgeId);
        if(obj.hasOwnProperty('diffEdgeName')) $('#modal-hrana-mat-nazev-different').html(obj.diffEdgeName);
        if(obj.hasOwnProperty('diffEdgeImgUrl')) $('#icon-hrana-different > img').attr('src', obj.diffEdgeImgUrl);        
        
    };
    
    // manage change on edge type radio selector
    window.displayEdgeType = function(){
        edgeType = $("input[name='modal-edge-type']:checked").val();
        
        if(edgeType === '-1') {
            $('#no-edge').show();
            $('#same-edge').hide();
            $('#different-edge').hide();
            $('#modal-input-hrana-wrapper').hide();
        }

        if(edgeType === '0') {
            $('#no-edge').hide();
            $('#same-edge').show();
            $('#different-edge').hide();
            $('#modal-input-hrana-wrapper').hide();
        }

        if(edgeType === '1') {
            $('#no-edge').hide();
            $('#same-edge').hide();
            $('#different-edge').show();            
            $('#modal-input-hrana-wrapper').show();
        }

        // disable save button when "privzorovana" is selected but doesnt exist
        if($('#modal-hrana-mat-has-same-edge').val() == 'false' && edgeType === '0'){
            $("#edge-select-button").prop('disabled', true);
        } else {
            $("#edge-select-button").prop('disabled', false);
        }

        var jsonString = $('#modal-deska-mat-data').val();                      // modify hidden mat props
        var edgeData = JSON.parse(jsonString);                
        
        
        
        edgeData.edgeType = edgeType;
        $('#modal-deska-mat-data').val(JSON.stringify(edgeData));        
        
    };    
    
    window.closeMatModal = function(){
        $("#mod_material_desky").css("display", "none");
        $("#mod_material_hrany").css("display", "none");
        $('#modal-deska-products-list').html('');
        $("#modal-deska-mat-info").show();
        $('#modal-hrana-products-list').html('');
        $("#modal-hrana-mat-info").show();
        colapseTree();
    };
    
    // colapse treemenu if its expanded
    function colapseTree(){
        var mainCatId = $('#div_tree').find('ul > li:first').attr('id').match(/\d+/g);
        if(!tree.getNode(mainCatId).foldedStatus) tree.getNode(mainCatId).toggleNode();       
    }    
});