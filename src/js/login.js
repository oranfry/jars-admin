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
            if (typeof data.token != 'undefined') {
                setCookie('token', data.token);
                window.location.reload();
            } else {
                alert(data.error || 'Unknown error');
            }
        },
        error: function(data){
            alert(data.responseJSON && data.responseJSON.error || 'Unknown error');
        }
    });
});
