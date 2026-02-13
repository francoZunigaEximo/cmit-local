$(document).ready(function(){


    $('.js-example-basic-multiple').select2();
    $('.select2-container--default .select2-selection--multiple').css('height', '34px');

    $('.js-example-basic-multiple').on('change', function() {
        var numberOfSelected = $(this).val().length;
        if (numberOfSelected > 0) {
            $('.select2-container--default .select2-selection--multiple').css('height', 'auto');
        } else {
            $('.select2-container--default .select2-selection--multiple').css('height', '34px');
        }
    });
});