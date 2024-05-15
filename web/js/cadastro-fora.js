$(document).ready(function(){
    $('#legalperson-zip_code').on('change', function(){
        var cep = $('#legalperson-zip_code').val().replace("-", "");
        $.ajax({
            url: 'https://viacep.com.br/ws/'+cep+'/json/',
            dataType: 'json',
            success: function(endereco) {
                $('#legalperson-address').val(endereco.logradouro);
                $('#legalperson-district').val(endereco.bairro);
            }
        });
    });
});
