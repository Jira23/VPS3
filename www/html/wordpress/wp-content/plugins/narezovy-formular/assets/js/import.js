
// for uploading import file
jQuery(document).ready(function($) {

    // Add a drag and drop event listener to the drop area
    var dropArea = $('#file-drop-area');
    dropArea.on('dragover', function(e) {
        e.preventDefault();
        dropArea.addClass('drag-over');
    });

    dropArea.on('dragleave', function(e) {
        e.preventDefault();
        dropArea.removeClass('drag-over');
    });

    dropArea.on('drop', function(e) {
        e.preventDefault();
        dropArea.removeClass('drag-over');

        var files = e.originalEvent.dataTransfer.files;

        if (files.length > 0) {
            var file = files[0];

            if (file.name.endsWith('.csv')) {                                   // Check if the selected file has the correct extension
                showWaitingIcon($('#file-drop-area'));
                var formData = new FormData();
                formData.append('file', file);
                formData.append('action', 'import_upload');
                ajaxRequest(formData, $('#results'));                           // Perform the AJAX request with the formData
            } else {
                $('#file-info-block').show(500);
            }
        }
    });

    // Trigger file input when clicking on the drop area
    dropArea.on('click', function() {
        $('#file-input').click();
    });

    // Handle file input change (when a file is selected via the file input)
    $('#file-input').on('change', function() {
        var fileInput = this;
        if (fileInput.files.length > 0) {
            var file = fileInput.files[0];

            if (file.name.endsWith('.csv')) {                                   // Check if the selected file has the correct extension
                showWaitingIcon($('#file-drop-area'));
                var formData = new FormData($('#file-upload-form')[0]);
                formData.append('action', 'import_upload');
                ajaxRequest(formData, $('#results'));                           // Perform the AJAX request with the formData
            } else {
                $('#file-info-block').show(500);
            }
        }
    });
    
    function ajaxRequest (request, target) {

        latestRequest = request;                                                // used for strategy to show last request if there are multiple
        latestTarget = target;

        jQuery.ajax({
            url: NF_ajaxUrl,
            type: 'POST',
            data: request,
            processData: false,
            contentType: false,            
            success: function (response) {
                if (request === latestRequest && target === latestTarget) {     // Check if this is the response for the latest request
                    $('#file-drop-area').hide(500);
                    $('#file-info-block').hide(500);
                    target.html(response);
                    target.show(500);
                }
            }
        });
    }
    
    function showWaitingIcon(target) {
        target.html('<h3>Importuji...</h3>');
        target.append('<img width="200" id="loadingIcon" src="' + NF_wpUrl + '/wp-content/plugins/narezovy-formular/assets/img/Loading_icon.gif" />');        
    };    
   
// jen testovaci kod, pak smazat
$('#test').on('click', function () {
    var formData = new FormData($('#file-upload-form')[0]);
    formData.append('action', 'import_upload');
    ajaxRequest (formData, $('#results'));
});
    
});