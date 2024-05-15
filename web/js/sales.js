 $(document).ready(function() {

    $('#modalCadastroConvenio').modal('show');

    $('#sale-consumable_id').attr('disabled', 'disabled');
    $('#sale-consumer_sale_id').attr('disabled', 'disabled');

    $('#sale-business_id').change(function() {

        $('#sale-consumable_id').html('');

        if ($(this).val() == '') {
            $('#sale-consumable_id').attr('disabled', 'disabled');
            $('#sale-consumable_id').append('<option value="">' + textoSelecione + '</option>');
        } else {
            $('#sale-consumable_id').removeAttr('disabled');
            $.getJSON('/sales/get-consumables?id=' + $(this).val() , function (dados) {

                if (Object.keys(dados).length > 1) {
                    $('#sale-consumable_id').append('<option value="">' + textoSelecioneUm + '</option>');
                    $.getJSON('/sales/get-coperchap?id=' + $('#sale-business_id').val() , function (dados) {
                        liberarVendedor(dados);
                    });
                }

                $.each(dados, function(index, value) {
                    $('#sale-consumable_id').append('<option value="' + index + '">' + value + '</option>');
                });
            });
        }
    });

    if (!!$('#sale-business_id').val()) {

        $('#sale-consumable_id').html('');
        $('#sale-consumable_id').removeAttr('disabled');
        $.getJSON('/sales/get-consumables?id=' + $('#sale-business_id').val() , function (dados) {

            if (Object.keys(dados).length > 1) {
                $('#sale-consumable_id').append('<option value="">' + textoSelecioneUm + '</option>');
                $.getJSON('/sales/get-coperchap?id=' + $('#sale-business_id').val() , function (dados) {
                    liberarVendedor(dados);
                });
            }

            $.each(dados, function(index, value) {
                if (Object.keys(dados).length == 1) {
                    $('#sale-consumable_id').append('<option selected="selected" value="' + index + '">' + value + '</option>');
                } else {
                    $('#sale-consumable_id').append('<option value="' + index + '">' + value + '</option>');
                }
            });
        });
    }

    $('#sale-consumable_id').change(function(){
        $.getJSON('/sales/get-coperchap?id=' + $('#sale-business_id').val() , function (dados) {
            if(dados){
                $.getJSON('/sales/get-documento-investimento?consumerid=' + $('#sale-consumer_id').val() , function (dados) {
                    $('#sale-invoice_code').val(dados);
                });
            }
        });
        
    });

    if (!isMobile) {
        $('#sale-total').maskMoney();
    }

    $('button[type=submit]').click(function() {
        $(this).attr('disabled', 'disabled');
        $(this).parents('form').submit()
    })

    $("#salvarComplemento").on('click', function(){
        var whats = $("#whatsapp").val();
        var comercial = $("#comercial").val();
        $('#salvarComplemento').attr("disabled", "disabled");
        $('#buttoncancel').attr("disabled", "disabled");
        if(whats != "" || comercial != ""){
            $.ajax({
                url: '/businesses/cadastro-complementar',
                data: {
                    whats: whats,
                    comercial: comercial
                },
                method: 'POST',
                dataType: 'json',
                success: function(retorno) {
                    $('#salvarComplemento').removeAttr("disabled");
                    $('#buttoncancel').removeAttr("disabled");
                    if(retorno.success){
                        $('#modalCadastroConvenio').modal('hide');
                    }
                }
            });
        }else{
            $("#mensagemerro").removeClass("hidden");
            $('#salvarComplemento').removeAttr("disabled");
            $('#buttoncancel').removeAttr("disabled");
        }
        return false;
    });
});


function liberarVendedor(mostra){
    if(mostra){
        $('#vendedorid').removeClass('hide');
        $('#sale-consumer_sale_id').removeAttr('disabled');
    }else{
        $('#vendedorid').attr('class','row hide');
        $('#sale-consumer_sale_id').attr('disabled', 'disabled');
    }
}