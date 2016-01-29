$(document).ready(function () {

    $(document).on('click', '.disabled, .disabled a', function(e){
        e.preventDefault();
        return false;
    });

    $('[data-toggle="tooltip"]').tooltip();
    $(document).on('change', '.table_checkbox', function(){
        var others = $('.table_checkbox:checked').length;
        dropdownToggle (others);
    });

    $(document).on('change', '#mark_all_checkbox', function(){
        var table_checkboxes = $('.table_checkbox');
        var state = $(this).prop('checked');
        table_checkboxes.prop('checked', state);
        dropdownToggle(state);
    });

    $(document).on('click', '#bulk_actions_list a, #deleteConfirmed', function(){
        var url = $(this).data('url');
        if(url.indexOf('delete') > 1 && $(this).id != 'deleteConfirmed')
        {
            deleteConfirmation(url);
            return false;
        }
        $('.modal').modal('hide');
        // AJAX!

        location.reload(true);
    });

    $('#exampleInputFile').uploadify({
        'swf'      : base_url + 'assets/js/uploadify/uploadify.swf',
        'uploader' : base_url + 'categories/images_upload',
        'buttonClass' : 'btn btn-primary upload_button',
        'height' : 30,
        'buttonText' : 'BROWSE...',
        'progressData' : 'percentage',
        //'onUploadProgress' : function(file, bytesUploaded, bytesTotal, totalBytesUploaded, totalBytesTotal) {
        //    $('#progress').html(totalBytesUploaded + 'bytes.');
        //},
        'itemTemplate' : '\
                    <div id="${fileID}" class="uploadify-queue-item preview_container">\
                        <a type="button" href="javascript:$(\'#${instanceID}\').uploadify(\'cancel\', \'${fileID}\')" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></a>\
                        <div class="preview">\
                        </div>\
                        <span class="fileName">${fileName} <br>(${fileSize})</span><br><span class="data">Loading...</span>\
                        <div class="uploadify-progress"><div class="uploadify-progress-bar"><!--Progress Bar--></div></div>\
                    </div>\
                ',
        'removeCompleted' : false,
        'onUploadComplete' : function(file) {
            var path = base_url + 'assets/uploads/' + file.name;
            $('#' + file.id + ' div.preview').css('background', 'url("' + path +'") 50% 50% no-repeat').css('background-size', 'contain');
        }



        // Put your options here
    });


});


function dropdownToggle(state)
{
    var dropdown = $('#bulk_actions');
    if(state)
    {
        dropdown.removeClass('disabled');
    }
    else
    {
        dropdown.addClass('disabled');
    }
}

function deleteConfirmation(url)
{
    var modal = $('#confirmationModal');
    $('#deleteConfirm').data('url', url);
    $(modal).modal();
}