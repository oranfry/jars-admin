<script>
(function(){
    $('.savelineraw').on('click', function(e) {
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
                alert('Please provide an object or array of objects');

                return;
            }
        }

        $.ajax('/ajax/save', {
            method: 'post',
            contentType: false,
            processData: false,
            data: JSON.stringify(data),
            headers: {'X-Base-Version': base_version},
            success: function(response, status, request) {
                window.location.href = BACK + '?version=' + request.getResponseHeader('X-Version');
            },
            error: alert_exception
        });
    });
})();
</script>