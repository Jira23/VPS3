
jQuery(document).ready(function($) {    
    
    // pri kliknuti na jednu desku ze seznamu
    jQuery('#deska-products-list').on("click", "tr", function(){

        var product_data = jQuery(this).find("#selected_product_param").html();     // najde data o produktu v nakliknutem radku tabulky 
        var obj = JSON.parse(product_data);                                         // rozparsuje JSON

        jQuery('#input-deska').val(obj.name);                                       // zapise nazev produktu do inputu
        jQuery('#lamino_id').val(obj.id);                                           // zapise id produktu do skryteho inputu
        jQuery('#lamino_id').attr('isPDK',obj.isPDK);                               // zapise atribut do skryteho inputu

        var img_url = jQuery(this).find("img").attr('src');                         // najdu url obrazku produktu
        jQuery('#icon-lamino > img').attr('src', img_url);                          // vlozi ho vedle inputu
        
        jQuery('#delka_dilu').attr('max', obj.delka);                               // nastavim do inputu max. hodnotu
        jQuery('#sirka_dilu').attr('max', obj.sirka);                               // nastavim do inputu max. hodnotu

        jQuery('#deska-products-list').html('');                                          // vymaze tabulku s produkty        
        jQuery('html, body').animate({scrollTop: jQuery("#input-deska").offset().top - 200}, 5); // nascroluju nahoru
        
        jQuery("button[name='btn_ulozit']").prop('disabled', false);                // enabluju talcitko ulozit dil

        closeTree();

        $('#lamino_id').trigger("change");
    });

    // pri kliknuti na jednu hranu ze seznamu
    jQuery('#hrana-products-list').on("click", "tr", function(){

        var product_data = jQuery(this).find("#selected_product_param").html();     // najde data o produktu v nakliknutem radku tabulky 
        var obj = JSON.parse(product_data);                                         // rozparsuje JSON

        jQuery('#input-hrana').val(obj.name);                                       // zapise nazev produktu do inputu
        jQuery('#hrana_id').val(obj.id);                                           // zapise id produktu do skryteho inputu

        var img_url = jQuery(this).find("img").attr('src');                         // najdu url obrazku produktu
        jQuery('#icon-hrana > img').attr('src', img_url);                          // vlozi ho vedle inputu

        jQuery('#hrana-products-list').html('');                                          // vymaze tabulku s produkty        
        jQuery('html, body').animate({scrollTop: jQuery("#input-hrana").offset().top - 200}, 5); // nascroluju nahoru
        
        jQuery("button[name='btn_ulozit']").prop('disabled', false);                // enabluju talcitko ulozit dil

        closeTree();

        $('#hrana_id').trigger("change");
    });    
    
// ---- AJAJX for row form start ----        

    $('#modal-deska-products-list').on("click", "tr", function(){               // click on product from list
        var product_data = $(this).find("#selected_product_param").html();      // najde data o produktu v nakliknutem radku tabulky 
        var obj = JSON.parse(product_data);

        poulateModalDeskaParams(obj);
        
        $("#mat-select-button").prop('disabled', false);                        // enable button "ulozit"
        $('#modal-deska-products-list').html('');
        $("#modal-deska-mat-info").show();

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
        
        poulateModalDeskaParams(toDeskaParams);
        poulateModalHranaParams(obj);
        

        $("#mat-select-button").prop('disabled', false);                        // enable button "ulozit"
        $('#modal-hrana-products-list').html('');
        $("#modal-hrana-mat-info").show();

        closeTree();
    });    




// ---- AJAJX for row form end ----         
    
    
    
    // zavre tree menu, pokud je otevrene
    function closeTree(){
        var mainCatId = $('#div_tree').find('ul > li:first').attr('id').match(/\d+/g);
        if(!tree.getNode(mainCatId).foldedStatus) tree.getNode(mainCatId).toggleNode();       
    }
    
});    
    
