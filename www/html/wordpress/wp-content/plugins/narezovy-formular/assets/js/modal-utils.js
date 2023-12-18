    jQuery(document).ready(function($) {    
        
        console.log('******');
        
        // Get the modal element
        var modal = $("#mod_material_desky");

        // Get the button that opens the modal
        var btn = $("#openModalBtn");

        // Get the close button element
        var span = $(".close");

        // Open the modal when the button is clicked
         $('#material_deska, #material_hrana').on("click", function(){ 
            modal.css("display", "block");
        });
        
        

        // Close the modal when the close button is clicked
        span.on("click", function () {
            modal.css("display", "none");
        });

        // Close the modal when the user clicks anywhere outside the modal
        $(window).on("click", function (event) {
            if (event.target === modal[0]) {
                modal.css("display", "none");
            }
        });
    });