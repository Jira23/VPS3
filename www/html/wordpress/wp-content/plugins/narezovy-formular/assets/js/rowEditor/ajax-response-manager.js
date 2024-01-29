jQuery(document).ready(function($) {    
    
    $('#modal-deska-products-list').on("click", "tr", function(){               // click on product from list

        $('#mat-modal-overlay').css('display', 'flex');
        var product_data = $(this).find("#selected_product_param").html();      // najde data o produktu v nakliknutem radku tabulky 
        var deskaProps = JSON.parse(product_data);
        
        poulateModalDeskaParams(deskaProps);

        // AJAX request to get props for privzorovana hrana
        var jsonString = $('#modal-deska-mat-data').val();
        var deskaProps = JSON.parse(jsonString);

        latestRequest = request;                                                                            // used for strategy to show last request if there are multiple
        var request = {'action': 'get_hrany_props', 'product_id': deskaProps.id, 'dekor': '' ,'tupl' : 'NE', 'dims' : false};
console.log('arm');        
console.log(request);
        $.ajax({
            url: NF_ajaxUrl,
            type: 'POST',
            data: request,
            beforeSend: function() {
                $('#mat-modal-overlay').css('display', 'flex');
            },            
            success: function (response) {
                var hranaProps = JSON.parse(response);
                poulateModalDeskaParams(hranaProps);
                $('#mat-modal-overlay').hide();
            }            
        });

        var partRowId = $('#mat-select-button').attr('row-id');
        $('tr[row-id="' + partRowId + '"]').find('.parts-table-selectbox-tupl').attr('readonly', deskaProps.isPDK);

        $("#mat-select-button").prop('disabled', false);                        // enable button "ulozit"
        $('#modal-deska-products-list').html('');
        $("#modal-deska-mat-info").show();                  
        checkMatExists(deskaProps.id);
        closeTree();
    });
    
    // pri kliknuti na jednu hranu ze seznamu
    jQuery('#modal-hrana-products-list').on("click", "tr", function(){          // click on product from list
        var product_data = $(this).find("#selected_product_param").html();
        var obj = JSON.parse(product_data);
        
        var toDeskaParams = {};
        toDeskaParams.diffEdgeId = obj.id;
        toDeskaParams.diffEdgeName = obj.name;
        toDeskaParams.diffEdgeImgUrl = obj.imgUrl;
        
        poulateModalHranaParams(toDeskaParams);
        
        $("#mat-select-button").prop('disabled', false);                        // enable button "ulozit"
        $('#modal-hrana-products-list').html('');
        $("#modal-hrana-mat-info").show();     
        closeTree();
    });       
    
    // zavre tree menu, pokud je otevrene
    function closeTree(){
        var mainCatId = $('#div_tree').find('ul > li:first').attr('id').match(/\d+/g);
        if(!tree.getNode(mainCatId).foldedStatus) tree.getNode(mainCatId).toggleNode();       
    }    
    
});    