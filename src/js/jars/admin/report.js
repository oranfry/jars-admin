window.clearInputs = function() {
    $(this).find('input[type="file"]').each(function(){
        var $controls = $(this).closest('.file-field-controls');
        var $actions = $controls.find('.file-field-controls__actions');
        var $inputs = $controls.find('.file-field-controls__input');
        var $downloadlink = $controls.find('a');
        var $cancel = $controls.find('.file-field-controls__cancel');

        $actions.hide();
        $cancel.hide();
        $inputs.show();
        $downloadlink.removeAttr('href');
    });

    $(this).find('[name]').each(function () {
        if ($(this).is('[type="checkbox"]')) {
            $(this).removeAttr('checked');
        } else {
            $(this).val(null);
        }
    });
};

window.selectOneLine = function() {
    var $linerow = $(this);
    var linetype = $linerow.attr('data-type');
    var id = $linerow.attr('data-id');
    var $line = $('.line[data-type="' + linetype + '"]');

    $('.linerow').not($linerow).removeClass('selected').find('.select-column [type="checkbox"]').prop('checked', false);

    $linerow.addClass('selected').find('.select-column [type="checkbox"]').prop('checked', true);

    $line.find('.saveline').show();
    $line.find('.bulkadd').hide();

    clearInputs.apply($line);

    var line = {};

    $linerow.find('[name]').each(function () {
        line[$(this).attr('name')] = $(this).val();
    });

    $line.show();
    $('.line').not($line).hide();

    for (const _property in line) {
        $field = $line.find('[name="' + _property + '"]');

        if ($field.is('input[type="checkbox"]')) {
            if (line[_property]) {
                $field.attr('checked', true);
            } else {
                $field.removeAttr('checked');
            }
        } else {
            $field.val(line[_property]);
        }
    }

    $line.find('.raw').html($linerow.find('.raw').html());
};

window.selectEmptyLine = function() {
    var linetype = $(this).attr('data-type');
    var $line = $('.line[data-type="' + linetype + '"]');

    clearInputs.apply($line);

    $line.find('.saveline').show();
    $line.find('.bulkadd').hide();

    $line.show();
    $('.line').not($line).hide();

};

$('.linerow').on('click', selectOneLine);

$('.trigger-add-line').on('click', selectEmptyLine);

$('.edit-form .saveline').on('click', function(e) {
    e.preventDefault();

    var $line = $(this).closest('.line');
    var $form = $(this).closest('form');
    var formData = new FormData($form[0]);
    var line = Object.fromEntries(formData);

    line.type = $line.attr('data-type');

    $form.find('input[type="checkbox"]').each(function() {
        line[$(this).attr('name')] = $(this).is(':checked');
    });

    var handleSave = function() {
        $.ajax('/ajax/save', {
            method: 'post',
            contentType: false,
            processData: false,
            data: JSON.stringify([line]),
            success: function(response, status, request) {
                if ('URLSearchParams' in window) {
                    var searchParams = new URLSearchParams(window.location.search);
                    searchParams.set("version", request.getResponseHeader('X-Version'));
                    window.location.search = searchParams.toString();
                } else {
                    window.location.reload();
                }
            },
            error: function(data){
                alert(data.responseJSON.error);
            }
        });
    };

    var $fileInputs = $form.find('input[type="file"]');
    var numLoadedFiles = 0;

    if (!$fileInputs.length) {
        handleSave();
    }

    $fileInputs.each(function(){
        var $input = $(this);
        var file = $input[0].files[0];
        delete line[$input.attr('name')];

        if (!file) {
            numLoadedFiles++;

            if (numLoadedFiles == $fileInputs.length) {
                handleSave();
            }

            return;
        }

        var reader = new FileReader();

        reader.onload = function(event) {
            line[$input.attr('name')] = btoa(event.target.result);
            numLoadedFiles++;

            if (numLoadedFiles == $fileInputs.length) {
                handleSave();
            }
        };

        reader.readAsBinaryString(file);
    });
});

$('.edit-form .deleteline').on('click', function(e) {
    e.preventDefault();

    var $line = $(this).closest('.line');
    var linetype = $line.attr('data-type');
    var $form = $(this).closest('form');
    var formData = new FormData($form[0]);
    var id = Object.fromEntries(formData).id;

    line = {
        type: linetype,
        id: id,
        _is: false
    };

    if (confirm('delete ' + linetype + ' ' + id + '?')) {
        $.ajax('/ajax/save', {
            method: 'post',
            contentType: false,
            processData: false,
            data: JSON.stringify([line]),
            success: function(response, status, request) {
                if ('URLSearchParams' in window) {
                    var searchParams = new URLSearchParams(window.location.search);
                    searchParams.set("version", request.getResponseHeader('X-Version'));
                    window.location.search = searchParams.toString();
                } else {
                    window.location.reload();
                }
            },
            error: function(data){
                alert(data.responseJSON.error);
            }
        });
    }
});

$(document).ready(function() {
    if (!'URLSearchParams' in window) {
        return;
    }

    let searchParams = new URLSearchParams(window.location.search);
    let version = searchParams.get("version");

    if (version) {
        window.history.pushState({}, document.title, window.location.href.split('?')[0]);
    }
});