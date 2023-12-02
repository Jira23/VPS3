    jQuery(document).ready(function($) {
        var gallery = $('.result-gallery .result-thumbnails a').simpleLightbox({nav: true});    // simple lightbox

        $('.result-gallery .result-thumbnails a img').on('click', function(e) {                 // prevent other libraries with <a><img/></a> syntax to recognize gallery system to start. This will avoid conflicts and make sure simple lightbox will be only galery library started
            e.preventDefault();
            e.stopPropagation();

            var clickedImageIndex = $(this).parent().index();                   // show currently clicked image
            gallery.openPosition(clickedImageIndex);
        });
    });