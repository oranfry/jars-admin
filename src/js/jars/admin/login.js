$('#loginform').on('submit', function(e){
    e.preventDefault();

    let username = $(this).find('[name="username"]').val();
    let password = $(this).find('[name="password"]').val();

    $.ajax('/ajax/login', {
        method: 'post',
        contentType: false,
        processData: false,
        data: JSON.stringify({username: username, password: password}),
        success: function(data) {
            if (typeof data.token === 'string') {
                setCookie('token', data.token);
                window.location.reload();
            }
        },
        error: function(data){
            alert(data.responseJSON && data.responseJSON.message || 'Unknown error');
        }
    });
});

$('#tokenform').on('submit', function(e){
    e.preventDefault();

    let token = $(this).find('[name="token"]').val();

    setCookie('token', token);
    window.location.reload();
});

window.refreshPanel = function () {
    let $allChoosers = $('.panel-choosers .panel-chooser');
    let $allPanels = $('.panels .panel');

    let index = $allChoosers.index($allChoosers.filter('.current')[0]);
    let $panel = $allPanels.filter(':nth-child(' + (index + 1) + ')');

    $allPanels.hide();
    $panel.show();
}

$('.panel-choosers').on('click', '.panel-chooser', function () {
    $('.panel-choosers').find('.panel-chooser').removeClass('current');
    $(this).addClass('current');
    refreshPanel();
});

$('.showpassword').on('click', function () {
    let $field = $(this).next()
    let hidden = $field.attr('type') == 'password';

    hidden = !hidden;

    $field.attr('type', hidden && 'password' || 'text');
    $(this).html(hidden && '🙈' || '🐵');
});