<script>
(function(){
    window.pagelink = function (linetype, id) {
        let link = '/report/' + REPORT_NAME + '/' + GROUP_NAME;

        if (linetype && id) {
            link = link + ':' + linetype + '/' + id;
        }

        return link;
    };

    $('.linerow').on('click', selectOneLine);

    $('.trigger-add-line').on('click', selectEmptyLine);

    $('.edit-form .rawline').on('click', function(e) {
        e.preventDefault();

        let $line = $(this).closest('.line');
        let $form = $(this).closest('form');
        let id = $form.find('input[name="id"]').val();

        if (id) {
            location.href = '/raw/' + REPORT_NAME + '/' + GROUP_NAME + ':' + $line.attr('data-type') + '/' + id;
        }
    });

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

        $form.find('input[type="number"]').each(function() {
            line[$(this).attr('name')] = Number($(this).val());
        });

        var handleSave = function() {
            $.ajax('/ajax/save', {
                method: 'post',
                contentType: false,
                processData: false,
                data: JSON.stringify([line]),
                headers: {'X-Base-Version': base_version},
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

    $(window).on('resize', jarsOnResize);

    let $linerow;

    if (LINE_ID) {
        $linerow = $('.linerow[data-type="' + LINETYPE_NAME + '"][data-id="' + LINE_ID + '"]').first();
    }

    if (typeof $linerow !== 'undefined' && $linerow.length) {
        selectOneLine.call($linerow);
    } else {
        if (LINE_ID) {
            window.history.pushState({}, document.title, pagelink());
        }

        jarsOnResize();
    }
})();
</script>