$(document).ready(function(){

    $('#ativarUsuario').click(function () {
        $('#ativarUsuario').attr('disabled', 'disabled');
        $('form').submit();
    });
});
