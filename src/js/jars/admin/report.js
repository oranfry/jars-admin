window.alert_exception = function (data) {
    let exception = data.responseJSON.exception ?? 'Unknown Exception';
    let message = data.responseJSON.message ?? 'No message was given';

    if (typeof data.responseJSON.private_message !== 'undefined') {
        exception = data.responseJSON.private_exception;
        message = data.responseJSON.private_message;
    }

    alert(message);
};

window.clearInputs = function() {
    $(this).find('input[type="file"]').each(function(){
        var $controls = $(this).closest('.file-field-controls');
        var $actions = $controls.find('.file-field-controls__actions');
        var $inputs = $controls.find('.file-field-controls__input');
        var $downloadlink = $controls.find('.download-button');
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

            var $downloadlink = $line.find('.download-button[data-for="' + _property + '"]');

            if ($downloadlink.length) {
                var table = $downloadlink.data('table');
                $downloadlink.attr('href', '/download/' + table + '/' + line[_property]);
            }
        }
    }

    $line.find('.raw').val($linerow.find('.raw').val());
    window.jarsOnResize();
};

window.selectEmptyLine = function() {
    var linetype = $(this).attr('data-type');
    var $line = $('.line[data-type="' + linetype + '"]');

    clearInputs.apply($line);

    $line.find('.saveline').show();
    $line.find('.bulkadd').hide();

    $line.show();
    $('.line').not($line).hide();
    window.jarsOnResize();
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

    for (key in line) {
        if (line[key] === '') {
            line[key] = null;
        }
    }

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
            error: alert_exception
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

$('.edit-form .savelineraw').on('click', function(e) {
    e.preventDefault();
    var data;

    try {
        data = JSON.parse($(this).closest('form').find('[name="raw"]').val());
    } catch(e) {
        alert(e);

        return;
    }

    if (data.constructor !== Array) {
        if (typeof data === 'object') {
            data = [data];
        } else {
            alert('Please provide an object or array thereof!');

            return;
        }
    }

    $.ajax('/ajax/save', {
        method: 'post',
        contentType: false,
        processData: false,
        data: JSON.stringify(data),
        success: function(response, status, request) {
            if ('URLSearchParams' in window) {
                var searchParams = new URLSearchParams(window.location.search);
                searchParams.set("version", request.getResponseHeader('X-Version'));
                window.location.search = searchParams.toString();
            } else {
                window.location.reload();
            }
        },
        error: alert_exception
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
            error: alert_exception
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

window.isOverflowingX = function(el) {
    var curOverflow = el.style.overflowX;

    if (!curOverflow || curOverflow === "visible") {
        el.style.overflowX = "hidden";
    }

    var result = el.clientWidth < el.scrollWidth;

    el.style.overflowX = curOverflow;

    return result;
};

window.jarsOnResize = function () {
    $('body').css('height', Math.max($(document).height(), $(window).height()) + 'px');

    var widths = [];
    var maxwidths = [];
    var margins = [];
    var $areas = $('.sidebar, .area');

    $areas.each(function() {
        var maxwidth = 3920;
        var minwidth = 0;
        var margin = $(this).data('area-margin') || 0;
        var steps = [100, 10, 1];
        var prev = maxwidth + steps[0] - margin;
        var area = this;

        steps.forEach(function(step) {
            for (var w = prev - step; w >= minwidth - margin; w = w - step) {
                $(area).css('width', w + 'px');

                if (isOverflowingX(area)) {
                    break;
                }

                prev = w;
            }
        });

        let width = prev + margin;

        $(area).css({
            'width': width + 'px',
            'left': widths.reduce((c, w) => c + w, 0) + 'px'
        });

        widths.push(width);
        maxwidths.push($(this).data('area-maxwidth'));
        margins.push(margin);
    });

    var targetwidth = $(window).width();
    var allocatedWidth = widths.reduce((c, w) => c + w, 0);
    var allocatedFixedWidth = 0;

    for (var i = 0; i < widths.length; i++) {
        if (maxwidths[i]) {
            var width = Math.max(widths[i], Math.min(maxwidths[i], widths[i] * targetwidth / allocatedWidth));
            allocatedFixedWidth += width;
            widths[i] = width;
        }
    }

    var targetFluidWidth = targetwidth - allocatedFixedWidth;
    var allocatedFluidWidth = 0;

    for (var i = 0; i < widths.length; i++) {
        if (!maxwidths[i]) {
            allocatedFluidWidth += widths[i];
        }
    }

    for (var i = 0; i < widths.length; i++) {
        if (!maxwidths[i]) {
            var width = Math.floor(widths[i] * targetFluidWidth / allocatedFluidWidth);
            widths[i] = width;
        }
    }

    var rem = targetwidth - widths.reduce((c, w) => c + w, 0);

    while (widths.length && rem > 0) {
        for (var i = widths.length - 1; rem > 0 && i >= 0; i--) {
            widths[i]++;
            rem--;
        }
    }

    var allocatedWidth = 0;

    $areas.each(function() {
        var width = widths.shift();
        $(this).css({
            'width': width + 'px',
            'left': allocatedWidth + 'px'
        });

        allocatedWidth += width;
    });
};

$(window).on('resize', jarsOnResize);
jarsOnResize();
