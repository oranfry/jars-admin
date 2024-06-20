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

    window.history.pushState({}, document.title, pagelink(linetype, id));

    $('.rawline').attr('href', '/raw/' + REPORT_NAME + '/' + GROUP_NAME + ':' + linetype + '/' + id).show();

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
    $('.rawline').removeAttr('href').hide();

    window.history.pushState({}, document.title, pagelink());

    window.jarsOnResize();
};

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
