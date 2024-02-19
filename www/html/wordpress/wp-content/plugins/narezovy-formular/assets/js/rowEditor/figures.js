
// (1,2)/(3,4)

jQuery(document).ready(function($) {
    
    window.applyFormula = function(matRow){
        
        var allNumbers = {};

        var formula = matRow.find('.parts-table-input-figure').val();
        var numbers = formula.match(/\d+/g);                                        // get numbers to array
        if (numbers === null) numbers = [];                                         // to keep function running if there is no formula

        numbers.forEach(function(number) {                                          // Iterate through each number and set it as a key with the original formula
            allNumbers[number] = {'formula': formula};
        });

        var partRows = matRow.nextUntil($('tr.NF-edit-group-material'));          // get all part rows of material group

        numbersCheck(allNumbers, partRows, matRow);
        syntaxCheck(formula, matRow);

        
        partRows.not(':last').each(function () {
            // clear row and its inputs
            $(this).find('.fig-formula-visible').html('');                                     
            $(this).find('.fig_name').val('');
            $(this).find('.fig_part_code').val('');
            $(this).find('.fig_formula').val('');
            $(this).css('background-color', 'unset');                    
            
            var groupNumber = $(this).find('#group-number').val();
            if(groupNumber in allNumbers) {
                $(this).find('.fig-formula-visible').html(allNumbers[groupNumber].formula);
                $(this).find('.fig_name').val(matRow.find('.group-material-info').attr('id'));      // set mat id as figure name
                $(this).find('.fig_part_code').val(groupNumber);
                $(this).find('.fig_formula').val(allNumbers[groupNumber].formula);                
            }
        });
        
        colorRows();
    };     
  
    // check if all part numbers in formula are in material group
    function numbersCheck(figuresData, partRows, matRow){
        
        // get all row numbers in group
        var groupNubers = [];
        partRows.not(':last').each(function () {
            groupNubers.push($(this).find('#group-number').val());
        });
        
        figureNubers = Object.keys(figuresData);                                // convert figures numbers to array
        
        // check if all figure numbers are in row numbers
        var numbersCheck = figureNubers.every(function(value) {
          return groupNubers.includes(value);
        });        

        matRow.find('#alert-fig-numbers-check').toggle(!numbersCheck);          // show/hide warning
        
        return numbersCheck;
    }
    
    function syntaxCheck(formula, matRow){
        var pattern = /^([0-9(),/]+)?$/;
        var containsOnlyAllowedCharacters = pattern.test(formula);        
        matRow.find('#alert-fig-syntax-check').toggle(!containsOnlyAllowedCharacters);          // show/hide warning
    }
    
    window.colorRows = function(){
      $('.parts-table tr').each(function() {
          var partCode = $(this).find('.fig_name').val();
          if(partCode) $(this).css('background-color', 'rgba(40, 100, 180, 0.1)' );
      });
    };
    
});