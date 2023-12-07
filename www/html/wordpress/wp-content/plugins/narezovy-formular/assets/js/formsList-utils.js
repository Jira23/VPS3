
jQuery(document).ready(function($) {
    
    $(".clickable-row").on("click", function(event) {                           // redirect after click on row
        if (!$(event.target).closest("button").length) {                        // except buttons
            window.location = $(this).data("href");
        }
    });
}); 

