$(document).ready(function(){

    $('input[name="aceite_termo"]').click(function () {
        if ($(this).is(':checked')) {
            $('#btnCadastrar').removeAttr('disabled'); //enable input
        } else {
            $('#btnCadastrar').attr('disabled', true); //disable input
        }
    });
    var exibirFormularioConjuge = function() {
        $('#partner-data-block').addClass('hidden');

        var $campoStateCivil = $('#physicalperson-marital_status');
        
        if (window.PARTNERED_MARITAL_STATUS.indexOf($campoStateCivil.val()) != -1) {
            $('#partner-data-block').removeClass('hidden');
        }
    };

    var mostraParcelas = function(){
        $('#consumer-maximum_amount').html('');
        $('#consumer-maximum_amount').removeAttr('disabled');
        var plano = "207370600-c72ef6f6-d599-472f-a653-c1c9564ab46a"
        if($('#consumer-plane_id').val() == plano){
            $('#consumer-maximum_amount').hide();
        }else{
            $('#consumer-maximum_amount').show();
            $.getJSON('/planes/get-parcelas?pay_plane=' + $('#consumer-plane_id').val() , function (dados) {

                $.each(dados, function(index, value) {
                    if (Object.keys(dados).length == 1) {
                        $('#consumer-maximum_amount').append('<option '+(value.valor == ""? "disabled" : "" )+' selected="selected" value="' + value.parcela + '">' + value.parcela + (value.valor == ""? "" : " - " + value.valor ) + '</option>');
                    } else {
                        $('#consumer-maximum_amount').append('<option '+(value.valor == ""? "disabled" : "" )+' value="' + value.parcela + '">' + value.parcela + (value.valor == ""? "" : " - " + value.valor ) + '</option>');
                    }
                });
            });
        }
    }

    $('#physicalperson-marital_status').change(exibirFormularioConjuge);

    exibirFormularioConjuge();

    $('#consumer-plane_id').on('change', mostraParcelas);

    mostraParcelas();

    $('#physicalperson-cpf').on('change',function(){
        var cpf = $('#physicalperson-cpf').val();
        $.ajax({
            url: '/api/consumers/index',
            data: {
                ConsumerSearch: {
                    nationalIdentifier: cpf
                }
            },
            dataType: 'json',
            success: function(children) {
                var url_atual = window.location.href;
                if(children.items.length > 0){
                    if(url_atual.includes("cadastro-fora")){
                        var error = $('#physicalperson-cpf').parent('.form-group');
                        error.removeClass('has-success').addClass('has-error');
                        error.find('.help-block-error').html('Já existe esse consumidor em nossa base.');
                        $("input").prop("disabled", true);
                        $('#physicalperson-cpf').prop("disabled", false);
                        $("select").prop("disabled", true);
                    }else{
                        var nome = children.items[0].name;
                        var html = "<center><h3>Favor verificar, o cpf "+cpf+" já existe!</h3><br/><h3>consumidor "+nome+" já existe.</h3></center>";
                        $('.modal .modal-header').html("<h2>Consumidor já Existe na Base de Dados.</h2>");
                        $('.modal .modal-body').html(html);
                        $('#irparaconsumidor').attr('href','/consumers/view?id='+children.items[0].id);
                        $('.modal').modal('show');
                    }
                }else{
                    $("input").prop("disabled", false);
                    $("select").prop("disabled", false);
                    $("#physicalperson-name").focus();
                }
            }
        });
    });

    // Parent selection
    var qs = function(key) {
        key = key.replace(/[*+?^$.\[\]{}()|\\\/]/g, "\\$&"); // escape RegEx meta chars
        var match = location.search.match(new RegExp("[?&]"+key+"=([^&]+)(&|$)"));
        return match && decodeURIComponent(match[1].replace(/\+/g, " "));
    };

    var loadParentWidgetWidget = function() {
        var $el = $('#consumer-parent_consumer_id');

        var parentId = $el.val();
        var currentId = qs('id');

        $('.parent-selector .children label').hide();
        $('.parent-selector .children .child-name').hide();
        $('.parent-selector .children .loading-child').hide();

        if (parentId == '') {
            $('.parent-selector .children .child-name').show();
            $('.parent-selector .children .child-name').text('(preencha o campo acima)');
            return;
        }

        $('.parent-selector .children .loading-child').show();

        $.ajax({
            url: '/api/consumers/index',
            data: {
                ConsumerSearch: {
                    parent_consumer_id: parentId
                }
            },
            dataType: 'json',
            success: function(children) {
                $('.parent-selector .children .loading-child').hide();

                var leftLoaded = false;
                var rightLoaded = false;

                for (var i in children.items) {
                    var child = children.items[i];
                    var $childEl;

                    if (child.position == 'left') {
                        leftLoaded = true;
                        $childEl = $('.parent-selector .left-child');
                    } else {
                        rightLoaded = true;
                        $childEl = $('.parent-selector .right-child');
                    }

                    // Editing
                    if (currentId == child.id) {
                        $childEl.find('label').show();
                        $childEl.find('label input').prop('checked', true);
                        $childEl.find('.child-name').hide();
                    } else {
                        $childEl.find('.child-name').text(child.name);
                        $childEl.find('.child-name').show();
                    }
                }

                if (!leftLoaded) {
                    $('.parent-selector .left-child label').show();
                    $('.parent-selector .left-child .child-name').hide();
                }

                if (!rightLoaded) {
                    $('.parent-selector .right-child label').show();
                    $('.parent-selector .right-child .child-name').hide();
                }
            }
        });
    };

    $('#consumer-parent_consumer_id').change(loadParentWidgetWidget);
    loadParentWidgetWidget();
});
