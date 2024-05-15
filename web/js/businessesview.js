$(document).ready(function(){
    $('#btn-contract').on('click', function(e){
        e.preventDefault();
        $('#modal-contract').modal('show');
        var url = $('#btn-contract').attr('data-url');
        console.log(url);
        $('#contract-confirm').click(function(e) {
            e.preventDefault();
            $('#modal-contract').modal('hide');
            window.open(url, '_blank');
        });
        $('#contract-not').click(function(e) {
            e.preventDefault();
            $('#modal-contract').modal('hide');
        });
    });
});