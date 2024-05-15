$(document).ready(function(){
    $('.forgotmypassword').on('click', function(){
        var valores = $('#login-form').serializeArray();
        $('#loginform-username').attr('disabled','');
        $('#loginform-password').attr('disabled','');
        $('#carregamento').removeClass('hide');
        $.ajax({
            url: '/site/forgot-my-password',
            data: valores,
            dataType: 'json',
            success: function(result) {
                $('.alert').remove();
                if(result.status == 'success'){
                    $('#login-form').append('<div class="alert alert-success" role="alert">'+result.msg+'</div>');
                }else{
                    $('#login-form').append('<div class="alert alert-danger" role="alert">'+result.msg+'</div>');
                }
                $('#carregamento').addClass('hide');
                $('#loginform-username').removeAttr('disabled');
                $('#loginform-password').removeAttr('disabled');
            }
        });
        return false;
    });
});