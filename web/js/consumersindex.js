$(document).ready(function(){
    $('.asdasdasd').on('click', function(){
        $.ajax({
            url: '/consumers/active',
            data: {
                id: 333
            },
            dataType: 'html',
            success: function(children) {
                $('.modal .modal-body').html(children);
                //$('#irparaconsumidor').attr('href','/consumers/view?id='+children.items[0].id);
                $('.modal').modal('show');
                loadParentWidgetWidget();
                $('#consumer-parent_consumer_id').change(loadParentWidgetWidget);
            }
        });
        return false;
    });

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
        debugger
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
                debugger
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

    $(".desabilitar-consumer").on('click', function(){
        $('.modal .modal-body').html('<h2 class="text-center">Carregando ...</h2>');
        var codigo = $(this).attr('data-id');
        $.ajax({
            url: '/consumers/view-disable',
            data: {
                id: codigo
            },
            dataType: 'json',
            success: function(dados) {
                $('#sim-disabled').attr('data-id', codigo);
                var html = '';
                if(dados.consumidor){
                    html += '<h3 class="text-center">'+dados.consumidor+'</h3><br>';
                    html += '<div class="row"><div class="col-sm-6"><p>Filhos:</p>';
                    if(dados.filhos.length > 0){
                        dados.filhos.forEach(element => {
                            html += '<p>'+element+'</p>';
                        });
                    }else{
                        html += '<p>Sem Filhos</p>';
                    }
                    html += '</div>';
                    html += '<div class="col-sm-6">';
                    html += '<p>Pai:</p>';
                    if(dados.pai){
                        html += '<p>'+dados.pai+'</p>';
                    }else{
                        html += '<p>Sem Pai</p>';
                    }
                    html += '</div>';
                }
                html +='</div>';
                $('.modal .modal-header').html('<h2>Desabilitar Consumidor</h2>');
                $('.modal .modal-body').html(html);
                $('.modal').modal('show');
            }
        });
        $('.modal').modal('show');
    });

    $(".exportasaas-consumer").on('click', function(){
        $('#nao-disabled').addClass('hide');
        $('#sim-disabled').addClass('hide');
        $('.modal .modal-body').html('<h2 class="text-center">Carregando ...</h2>');
        var codigo = $(this).attr('data-id');
        var link = $(this);
        $.ajax({
            url: '/consumers/cadastrar-asaas',
            data: {
                id: codigo
            },
            dataType: 'json',
            success: function(dados) {
                //$('#sim-disabled').attr('data-id', codigo);
                var html = '';
                html += '<h3 class="text-center">Cadastro no Asaas</h3><br>';
                if(dados.success){
                    html += '<div class="row"><div class="col-sm-12"><label class="text-success">'+dados.mensagem+'</label></div></div>';
                }else{
                    html += '<div class="row"><div class="col-sm-12"><label class="text-danger">'+dados.mensagem+'</label></div></div>';
                }
                $('.modal .modal-header').html('<h2>Cadastro do Consumidor Asaas</h2>');
                $('.modal .modal-body').html(html);
                link.addClass('disabled');
                link.addClass('exportasaas');
                link.removeClass('exportasaas-consumer');
                $('.modal').modal('show');
            }
        });
        $('.modal').modal('show');
    });

    $("#nao-disabled").on('click', function(){
        $('.modal').modal('hide');
    });

    $("#sim-disabled").on('click', function(){
        $.ajax({
            url: '/consumers/disable',
            data: {
                id: $(this).attr('data-id')
            },
            dataType: 'json',
            success: function(dados) {

            }
        });
    });
});