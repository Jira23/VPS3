    
    
jQuery(document).ready(function($) {
    
    // zajisti, ze je ve stromovem menu oteverna vzdy jen aktualni vetev (zavre vsechny otevrene vetve, ktere nejsou rodicem te, na kterou bylo kliknuto)
    jQuery('[id*=div_g_div_treenode_]').click(function(e) {  

        var parents = jQuery(this).parentsUntil('#div_tree', 'li');             // najdu rodice elementu na ktery bylo kliknuto
        
        var parentsIds = [];                                                    // zapisu idcka techto rodicu do pole
        jQuery.each(parents, function () {
            parentsIds.push(jQuery(this).attr('id'));
        }); 
        
        var minuses = jQuery(".fa-minus").map(function() {                      // najdu vsechny otevrene elementy (ty co maji ikonu "-")
            return jQuery(this).attr('id').replace('i_', '');
        }).get();        
        
        var toClose = minuses.filter(n => !parentsIds.includes(n));             // odstranim spolecne prvky obou poli -> zustanou jen otevrene nerodicovske prvky

        jQuery.each(toClose, function (index, value) {                          // tyto prvky zavru
            var toCloseId = value.replace('div_treenode_', '');
                tree.getNode(toCloseId).toggleNode();
        });
    });
});