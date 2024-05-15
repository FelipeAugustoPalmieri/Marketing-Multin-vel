$(document).ready(function() {
    $('#download').on('click', function(){
        var consumerid = $(this).attr('data-id');
        window.open('https://sistema.tbest.com.br/consumers/download-investimento?id='+consumerid, '_blank');
    });

    $('#visualizarfatura').on('click', function(){
        var link = $(this).attr('data-urlinvoice');
        window.open(link, '_blank');
    });
});