
// (1,2)/(3,4)

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
        var allNumbers = {};

        var i = 1;
        $('.figure-input').each(function() {
            var formula = $(this).val();
            var numbers = formula.match(/\d+/g);                                        // get numbers to array

            numbers.forEach(function(number) {                                          // Iterate through each number and set it as a key with the original formula and figure as value
                allNumbers[number] = {'formula': formula, 'figure': intToChar(i)};
            });
            i++;
        });

        var checkedData = check(allNumbers);

        // clear data
        $('.fig-formula-visible').html('');                                     
        $('.fig_name').val('');
        $('.fig_part_code').val('');
        $('.fig_formula').val('');
        $('.parts-table tr').css('background-color', 'unset');
        
        $.each(checkedData, function(key, item) {
            var row = $('tr[row-id="' + key + '"]');
            row.find('.fig_name').val(item.figure);
            row.find('.fig_part_code').val(key);
            row.find('.fig_formula').val(item.formula);
            row.find('.fig-formula-visible').html(item.formula);
         });
         
        colorRows();
    }  
    
    function intToChar(number){
        var alphabetIndex = parseInt(number, 10) - 1;
        if (alphabetIndex >= 0 && alphabetIndex < 26) {
            return String.fromCharCode('A'.charCodeAt(0) + alphabetIndex);
        } else {
            return false;
        }
    }

    function check(figuresData){
        return figuresData;
    }
    
    function colorRows(){
       
        var colors = {
          "A": "rgba(40, 100, 180, 0.1)",   // Blue
          "B": "rgba(255, 90, 90, 0.1)",    // Red
          "C": "rgba(100, 200, 120, 0.1)",  // Green
          "D": "rgba(160, 140, 160, 0.1)",  // Lavender
          "E": "rgba(250, 150, 60, 0.1)",   // Tangerine
          "F": "rgba(240, 210, 80, 0.1)",   // Yellow
          "G": "rgba(90, 140, 160, 0.1)",   // Teal
          "H": "rgba(140, 180, 100, 0.1)",  // Olive
          "I": "rgba(200, 100, 200, 0.1)",  // Pink
          "J": "rgba(30, 170, 220, 0.1)",   // Cyan
          "K": "rgba(220, 120, 40, 0.1)",   // Orange
          "L": "rgba(180, 50, 180, 0.1)",   // Purple
          "M": "rgba(100, 120, 180, 0.1)",  // Indigo
          "N": "rgba(70, 180, 70, 0.1)",    // Lime
          "O": "rgba(60, 130, 100, 0.1)",   // Forest Green
          "P": "rgba(120, 60, 60, 0.1)",    // Maroon
          "Q": "rgba(120, 180, 200, 0.1)",  // Sky Blue
          "R": "rgba(50, 100, 50, 0.1)",    // Forest
          "S": "rgba(200, 120, 140, 0.1)",  // Rose
          "T": "rgba(90, 60, 120, 0.1)"     // Grape
        };
          
          $('.parts-table tr').each(function() {
              var partCode = $(this).find('.fig_name').val();
              $(this).css('background-color', colors[partCode] );
          });
    }
        
    colorRows();    

    
});