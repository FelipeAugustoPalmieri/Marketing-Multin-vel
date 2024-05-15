$(document).ready(function() {
    const variaveis = [
        'nomeconsumidor', 
        'cpfconsumidor', 
        'rgconsumidor',
        'cepconsumidor',
        'profissaoconsumidor',
        'ruaconsumidor',
        'numeroconsumidor',
        'bairroconsumidor',
        'cidadeconsumidor',
        'estadoconsumidor',
        'valorinvestimento',
        'tempoanosinvestimento',
        'tempoanosextensoinvestimento',
        'valorextensoinvestimento',
        'con-razaosocial', 
        'con-nomefantasia',
        'con-nomeproprietario',
        'con-telefone',
        'con-celular',
        'con-documento',
        'con-inscricaoest',
        'con-email',
        'con-site',
        'con-endereco',
        'con-cep',
        'con-bairro',
        'con-cidade',
        'con-estado',
        'con-responsavellegal',
        'con-cpfresponsavel',
        'con-atividades',
        'con-repasse',
        'datacreate',
        'nomepresidente',
        'cpfpresidente',
    ]
    $('.summernote').summernote({
        height: 300,
        tabsize: 2,
        followingToolbar: true,
        hint: {
            mentions: variaveis,
            match: /\B@(\w*)$/,
            search: function (keyword, callback) {
                callback($.grep(this.mentions, function (item) {
                    return item.indexOf(keyword) == 0;
                }));
            },
            content: function (item) {
                return '@' + item;
            }    
        }
    });
});