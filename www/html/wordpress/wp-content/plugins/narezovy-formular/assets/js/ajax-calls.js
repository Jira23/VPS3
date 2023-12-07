
jQuery(document).ready(function($) {
    
    // AJAX dotaz, nacte seznam desek v jejichz nazvu je klicove slovo    
    jQuery('#input-deska').bind('keyup select',function() {                                                 // select znamena, ze spusti event pokud vyberu polozku z naseptavace
        jQuery("button[name='btn_ulozit']").prop('disabled', true);                                         // disabluju talcitko ulozit dil, aby se nedal ulozit dil dokud uzivatel neklikne na konkretni lamino (ve fci jQuery('#deska-products-list').on("click", "tr", function() ) se zase enabluje
        delay(function(){
                var request = {'action': 'get_desky', 'keyword': jQuery('#input-deska').val(), 'source':'input' };
                if(jQuery('#input-deska').is(':focus')){                                                    // dotaz na AJAX posilam pouze pokud je input s klicovym slovem aktivni (aby se nezobrazoval seznam vysledku, pokud uzivatel jen projede tabulatorem)
                    showWaitingIcon(jQuery('#deska-products-list'));
                    ajaxRequest(request, jQuery("#deska-products-list"));
                }
        }, 300 );                                                                                           // doba zpozdeni po keyup
    });
    
    // AJAX dotaz, nacte seznam desek z kategorie vybrane ve stome
    jQuery('[id*=div_g_div_treenode_]').click(function(e) {  
        var isVisible = jQuery(this).find('i').css('display');
        if(isVisible != 'none') return;                                                                     // seznam zobrazuji pouze pro posledni kategorii
        
        jQuery('.ptree-selected').removeClass('ptree-selected');                                            // vymazu oznaceni vybrane kategorie (predchozi)
        jQuery(this).addClass('ptree-selected');                                                            // oznacim vybranou kategorii
        showWaitingIcon(jQuery('#deska-products-list'));
        
        var thisId = jQuery(this).attr('id').replace('div_g_div_treenode_', '');                            // najdu id nodu na ktery uzivatel kliknul
        var request = {'action': 'get_desky','keyword': tree.getNode(thisId).addional, 'source':'ptree'};   // pripravim parametry pro AJAX volani
        ajaxRequest(request, jQuery("#deska-products-list"));                                               // zavolam AJAX a vykreslim vysledek
        jQuery("button[name='btn_ulozit']").prop('disabled', true);                                         // disabluju talcitko ulozit dil, aby se nedal ulozit dil dokud uzivatel neklikne na konkretni lamino (ve fci jQuery('#deska-products-list').on("click", "tr", function() ) se zase enabluje
    });
    
    // AJAX dotaz, nacte seznam hran, v jejichz nazvu je klicove slovo 
    jQuery('#input-hrana').bind('keyup select',function() {                                                 // select znamena, ze spusti event pokud vyberu polozku z naseptavace
        jQuery("button[name='btn_ulozit']").prop('disabled', true);                                         // disabluju talcitko ulozit dil, aby se nedal ulozit dil dokud uzivatel neklikne na konkretni lamino (ve fci jQuery('#deska-products-list').on("click", "tr", function() ) se zase enabluje
        delay(function(){
                var request = {'action': 'get_hrany_list', 'keyword': $('#input-hrana').val(),'tupl' : $('#tupl').val()};
                if(jQuery('#input-hrana').is(':focus')){                                                    // dotaz na AJAX posilam pouze pokud je input s klicovym slovem aktivni (aby se nezobrazoval seznam vysledku, pokud uzivatel jen projede tabulatorem)
                    showWaitingIcon(jQuery('#hrana-products-list'));
                    ajaxRequest(request, jQuery("#hrana-products-list"));
                }
        }, 300 );                                                                                           // doba zpozdeni po keyup
    });    
    
    // AJAX dotaz, nacte vysledek optimalizace
    $("button[name='btn_optimalizovat']").click(function() {
        $('#optimized-block').show();
        $('#optimized-results-table').html('');
        showWaitingIcon($('#optimized-results-table'), true);
        $('#optimized-results-table').append('<h5>Probíhá optimalizace. Při velkém počtu dílů může trvat i několik minut.</h5>');
        
        if($("button[name='btn_optimalizovat']").index(this) === 0) $(window).scrollTop($('#optimized-results-table').offset().top);       // on top button click, scroll down
        
        var formId = new URLSearchParams(window.location.search).get('form_id');                            // get form id value from page URL
        var request = {'action': 'optimize', 'form_id' : formId};                                           // pripravim parametry pro AJAX volani
        ajaxRequest(request, $("#optimized-results-table"));                                                // zavolam AJAX a vykreslim vysledek
    });    
    
    function getWpUrl(){
        var currentUrl = window.location.href;
        var matches = currentUrl.match(/^(https?:\/\/[^/]+\/[^/]+\/)/);         // Extract the WordPress installation directory (the part after the host and before any query parameters)                
        return (matches[1]);
    }

    // zpozduje keyup pri vyplnovani inputu, aby se neposilal za kazdym keyupem ale pockal danou dobu a poslal se az pak
    var delay = (function(){
      var timer = 0;
      return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
      };
    })();

    function showWaitingIcon(target, no_tag = false) {
        if(!no_tag) target.html('<h3>Hledám...</h3>');
        target.append('<img width="200" id="loadingIcon" src="' + getWpUrl() + '/wp-content/plugins/narezovy-formular/assets/img/Loading_icon.gif" />');        
    };

    function ajaxRequest (request, target) {
        var ajaxUrl = getWpUrl() + 'wp-admin/admin-ajax.php';

        latestRequest = request;                                                // used for strategy to show last request if there are multiple
        latestTarget = target;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: request,
            success: function (response) {
                if (request === latestRequest && target === latestTarget) {     // Check if this is the response for the latest request
                    target.html(response);
                }
            }
        });
    }    

});