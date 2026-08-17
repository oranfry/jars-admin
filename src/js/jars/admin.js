(function() {
    window.adminMakeUrl = function (data) {
        let groupParts = [];

        for (let i = 0;; i++) {
            if (typeof data['path__' + i] === 'undefined') {
                break;
            }

            groupParts.push(data['path__' + i]);
            delete(data['path__' + i]);
        }

        let groupR = '/' + groupParts.join('/');
        let lineR = '';

        if (typeof data.line__value !== 'undefined') {
            lineR = ':' + data.line__value;
        }

        let childParts = [];

        for (let i = 0;; i++) {
            if (typeof data['childpath__property_' + i] === 'undefined') {
                break;
            }

            if (typeof data.line__value !== 'undefined') {
                childParts.push(data['childpath__property_' + i]);
            }

            delete(data['childpath__property_' + i]);

            if (typeof data['childpath__id_' + i] === 'undefined') {
                break;
            }

            if (typeof data.line__value !== 'undefined') {
                childParts.push(data['childpath__id_' + i]);
            }

            delete(data['childpath__id_' + i]);
        }

        let childR = !!childParts.length ? '/' + childParts.join('/') : '';

        let url = window.toolsPluginMountPoint + '/' + data.report__value + groupR + lineR + childR;

        delete(data.report__value);
        delete(data.line__value);

        if (data.showas__value === 'list') {
            delete(data.showas__value);
        }

        return url;
    };

    let findChildPropertyOptions = function (level, childPathDepth, children) {
        if (level === childPathDepth) {
            return Object.keys(children);
        }

        let property = window.contextVariableSets['childpath__property_' + level];

        children = children[property].children;

        if (typeof children === 'undefined') {
            return [];
        }

        return findChildPropertyOptions(level + 1, childPathDepth, children);
    };

    window.refreshLineCvs = function() {
        if (window.contextVariableSets.showas__value === 'raw') {
            return;
        }

        let childPathDepth;
        for (childPathDepth = 0; !!window.contextVariableSets['childpath__property_' + childPathDepth]; childPathDepth++);

        let $lineContainer = $('#cvs-line');
        let $lineSelect = $lineContainer.find('select');
        let $childpathContainer = $('#cvs-childpath');
        let $selected = $('.linerow[data-id].selected');

        let ids = $selected.map(function () {
            return {
                type: $(this).data('type'),
                id: $(this).data('id')
            };
        }).get();

        // Populate and show/hide the select box for the selected id(s)
        // May be the initial line, or may be part of the child path

        if (childPathDepth) {
            let name = 'childpath__id_' + (childPathDepth - 1);
            let $childId = $childpathContainer.find('select[data-for="' + name + '"]');
            let $childIdOption = $childId.find('option:nth-child(2)');

            if (!$childIdOption.length) {
                $childIdOption = $('<option>').appendTo($childId);
            }

            if (ids.length === 1) {
                let value = ids[0].id;

                $childIdOption
                    .html(value.substring(0, 6))
                    .val(value);

                $childId.val(value);

                window.contextVariableSets[name] = value;
                $childId.closest('.group-navigator-chunk').show();
            } else {
                $childId.val('');
                delete(window.contextVariableSets[name]);
                $childId.closest('.group-navigator-chunk').hide();
            }
        } else {
            let value = $.map(ids, function (id) {
                return id.type + '/' + id.id;
            }).join(',');

            let $lineOption = $lineSelect.find('option:nth-child(2)');

            $lineOption
                .val(value)
                .html(ids.length === 1 ? ids[0].type + '/' + ids[0].id.substring(0, 6) : 'Multiple')

            $lineSelect.val(value);

            window.contextVariableSets.line__value = value;
            $lineContainer.toggle(!!ids.length);
        }

        // Populate and show/hide the select box for the available children
        // Always part of child path, but the first property is more tricky

        let name = 'childpath__property_' + childPathDepth;
        let $property = $childpathContainer.find('select[data-for="' + name + '"]');
        let $propertyContainer = $property.closest('.group-navigator-chunk');
        let hasChildOptions = false;

        if (ids.length === 1) {
            let linetype_name = $lineSelect.val().split('/')[0];
            let linetype = window.reportLinetypes[linetype_name];
            let options = findChildPropertyOptions(0, childPathDepth, linetype.children);

            if (options.length) {
                if (!$property.length) {
                    $propertyContainer = $('<div class="group-navigator-chunk"><div class="group-navigator-chunk__input"><select class="cv-surrogate" data-for="' + name + '"></select></div></div>');
                    $property = $propertyContainer.find('select');
                    cvs.bindEvents($propertyContainer);
                    $('#cvs-childpath').append($propertyContainer);
                }

                $property
                    .empty()
                    .append($('<option>'));

                $.each(options, function () {
                    $property.append(
                        $('<option value="' + this + '">').html(this)
                    );

                    hasChildOptions = true;
                });

                $propertyContainer.show();
            } else {
                $propertyContainer.hide();
            }
        } else {
            $propertyContainer.hide();
        }

        $childpathContainer.show();
        $childpathContainer.toggle(!!childPathDepth || hasChildOptions);

        softCvsApply();
    };

    let getTopChild = function () {
        return context.childpath[context.childpath.length - 1];
    };

    window.adminUnmapLine = function (line) {
        // for saving in context of a parent, wrap in the parent unless the only_parent is present

        let topChild = getTopChild();
        window.nestedProperty = null;

        if (context.line && (!topChild.only_parent || !line[topChild.only_parent])) {
            let children = [line];
            line = JSON.parse(JSON.stringify(context.line));
            line[topChild.property] = children;
            window.nestedProperty = topChild.property;
        }

        return line;
    };

    window.adminPostSave = function (data) {
        if (typeof window.nestedProperty !== 'undefined' && window.nestedProperty) {
            let savedId = data[0][window.nestedProperty][0].id;

            if (getTopChild().id !== savedId) {
                window.contextVariableSets['childpath__id_' + (context.childpath.length - 1)] = savedId;
            }
        }
    };
})();