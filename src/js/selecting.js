$('.select-column input').on('change', function() {
    $(this).closest('tr[data-id]').toggleClass('selected', $(this).is(':checked'));
});

$('.easy-table tr .selectall').on('click', function(e){
    var $table = $(this).closest('table');
    var $tbody = $(this).closest('tbody');
    var $block;

    if ($tbody.length) {
        $block = $tbody;
    } else {
        $block = $table;
    }

    var $boxes = $block.find('tr[data-id] .select-column input[type="checkbox"]');
    var checked = $boxes.filter(':checked').length > 0;
    $boxes.prop('checked', !checked);
    $boxes.each(function(){
        $(this).closest('tr[data-id]').toggleClass('selected', $(this).is(':checked'));
    });
});

$('.select-column input').on('click', function(event) {
    event.stopPropagation();
});
