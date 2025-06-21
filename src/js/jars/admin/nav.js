$('select.navigable').on('change', function () {
    let $option = $(this).find('[value="' + this.value + '"]')

    window.location.href = $option.data('url');
});
