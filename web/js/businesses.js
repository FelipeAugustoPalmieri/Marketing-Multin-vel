$(document).ready(function(){

    $('#legalperson-person_class input').on('change', function (e) {
        //e.stopImmediatePropagation()
        //$(this).button('toggle');
        exibirFormularioLegalPerson();
    });

    exibirFormularioLegalPerson();

    $("#legalperson-person_class").button('toggle');


    $("#juridicalperson-cnpj").on('change', function(){
        var cnpjform = $(this).val();
        $.ajax({
            url: 'consulta-cnpj',
            method: 'POST',
            data:{
                cnpj: cnpjform,
            },
            dataType: 'json',
            success: function(dadosConvenio) {
                $('#juridicalperson-company_name').val(dadosConvenio.nome);
                $('#juridicalperson-trading_name').val(dadosConvenio.nome);
                if(dadosConvenio.qsa){
                    var qsa = dadosConvenio.qsa.filter(function(x){ return x.qual == '49-Sócio-Administrador' });
                    $('#juridicalperson-contact_name').val(qsa[0].nome);
                    $('#business-representative_legal').val(qsa[0].nome);
                }
                if(dadosConvenio.atividade_principal){
                    $('#business-economic_activity').val(dadosConvenio.atividade_principal[0].text);
                }
                $('#legalperson-email').val(dadosConvenio.email);
                $('#legalperson-address').val(dadosConvenio.logradouro);
                $('#legalperson-zip_code').val(dadosConvenio.cep);
                $('#legalperson-district').val(dadosConvenio.bairro);
                $('#legalperson-cell_number').val(dadosConvenio.telefone);
            }
        });
    });

});


var exibirFormularioLegalPerson = function() {

    $('#juridical-person-form').addClass('hidden');
    $('#physical-person-form').addClass('hidden');
    //$('#business-form').addClass('hidden');

    var $radioButton = $('#legalperson-person_class').find('label.active').find('input');
    var $hiddenInput = $('input[type=hidden][name="LegalPerson[person_class]"]');
    
    if ($radioButton.val() || $hiddenInput.val()) {
        $('#business-form').removeClass('hidden');
    }

    if ($radioButton.val() == 'JuridicalPerson' || $hiddenInput.val() == 'JuridicalPerson') {
        $('#juridical-person-form').removeClass('hidden');
    }

    if ($radioButton.val() == 'PhysicalPerson' || $hiddenInput.val() == 'PhysicalPerson') {
        $('#physical-person-form').removeClass('hidden');
    }
};