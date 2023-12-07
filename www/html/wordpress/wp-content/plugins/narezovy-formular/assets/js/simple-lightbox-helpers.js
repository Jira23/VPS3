    jQuery(document).ready(function($) {
        initLightbox();                                                                             // init on pageload

        $('#optimized-results-table').on('DOMSubtreeModified', function() {                         // reinit on gallery change
            initLightbox();
        });
        
    });
    
    function initLightbox(){
        var gallery = jQuery('.result-gallery .result-thumbnails a').simpleLightbox({nav: true});    // simple lightbox

        jQuery('.result-gallery .result-thumbnails a img').on('click', function(e) {                 // prevent other libraries with <a><img/></a> syntax to recognize gallery system to start. This will avoid conflicts and make sure simple lightbox will be only galery library started
            e.preventDefault();
            e.stopPropagation();

            var clickedImageIndex = jQuery(this).parent().index();                                   // show currently clicked image
            gallery.openPosition(clickedImageIndex);
        });        
    }
