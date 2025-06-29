<script>
(function(){
    window.pagelink = function (linetype, id, childpath) {
        if (typeof childpath === 'undefined') {
            childpath = [];
        }

        let link = BASEPATH + '/report/' + REPORT_NAME + '/' + GROUP_NAME;

        if (linetype && id) {
            link = link + ':' + linetype + '/' + id;
        }

        for (let i = 0; i < childpath.length; i++) {
            link = link + '/' + childpath[i].property;

            if (childpath[i].id) {
                link = link + '/' + childpath[i].id;
            }
        }

        return link;
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

        $form.find('input[type="number"]').each(function() {
            let value = $(this).val();

            line[$(this).attr('name')] = value !== '' && Number(value) || null;
        });

        // for saving in context of a parent, wrap in the parent unless the only_parent is present

        let topChild = childpath[childpath.length - 1];

        let nestedProperty = null;

        if (context_line && (!topChild.only_parent || !line[topChild.only_parent])) {
            let children = [line];
            line = JSON.parse(JSON.stringify(context_line));
            line[topChild.property] = children;
            nestedProperty = topChild.property;
        }

        var handleSave = function() {
            $.ajax(BASEPATH + '/ajax/save', {
                method: 'post',
                contentType: false,
                processData: false,
                data: JSON.stringify([line]),
                headers: {'X-Base-Version': base_version},
                success: function(response, status, request) {
                    let savedId;

                    if (nestedProperty && topChild.id !== (savedId = response[0][nestedProperty][0].id)) {
                        topChild.id = savedId;
                        window.history.pushState({}, document.title, pagelink(LINETYPE_NAME, LINE_ID, childpath));
                    }

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
            $.ajax(BASEPATH + '/ajax/save', {
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
    let topChild = childpath[childpath.length - 1];

    if (topChild && (child_id = topChild.id)) {
        $linerow = $('.linerow[data-id="' + child_id + '"]').first();
    } else if (LINE_ID) {
        $linerow = $('.linerow[data-type="' + LINETYPE_NAME + '"][data-id="' + LINE_ID + '"]').first();
    }

    if (typeof $linerow !== 'undefined' && $linerow.length) {
        selectOneLine.call($linerow);
    } else {
        if (LINE_ID) {
            window.history.pushState({}, document.title, pagelink(LINETYPE_NAME, LINE_ID, childpath));
        }

        jarsOnResize();
    }
})();
</script>