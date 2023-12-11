
jQuery(document).ready(function($) {
    $('#figures-add-button').click(function() {
        var htmlToAdd = '<div class="form-section figures-section"><input type="text" class="figure-input input-small"><span class="dashicons dashicons-trash figure-delete-button"></span></div>';
        $('#figures-inputs-section').append(htmlToAdd);
    });
    
    $('#figures-inputs-section').on('click', '.figure-delete-button', function() {
        $(this).parent('.form-section').remove();
    });
    
    $('#apply-changes-button').click(function() {
        applyFormulas();
    });
    
    function applyFormulas(){
        
        
        //var yourString = "Your string with unwanted characters here";
        //var cleanedString = yourString.replace(/[^(),/]/g, '');
        
        var allNumbers = {};

        $('.figure-input').each(function() {
            var formula = $(this).val();
            var cleanedString = formula.replace(/\D/g, '');                     // remove everything but numbers
            numbers = cleanedString.split('').map(Number);
            
            numbers.forEach(function(number) {                                  // Iterate through each number and set it as a key with the original formula as the value
                allNumbers[number] = formula;
            });
        });

        console.log(allNumbers);

    }  
});