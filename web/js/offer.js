$(document).ready(function() {
    var dropzone = new Dropzone("#my-awesome-dropzone", { 
        url: "/offer/uploadimagem",
        maxFilesize: 1,
        maxFiles: 1,
        paramName: "imagemoferta",
        dictDefaultMessage: "Clique aqui ou jogue a sua imagem aqui!",
        dictFileTooBig: "Arquivo tamanho muito grande ({{filesize}}MiB). Tamanho maximo aceito {{maxFilesize}} MiB",
        dictFallbackMessage: "Seu browser não suporta o Drag Drop, por favor acessa em outro browser",
        maxfilesexceeded: function(file) {
            this.removeAllFiles();
            this.addFile(file);
        },
        accept: function(file, done) {
            done();
        },
    });

    dropzone.on('sending', function(file, xhr, data){
        data.append('antigo', document.getElementById("offer-image").value);
    });

    dropzone.on('success', function(file, resp){
        console.log(document.getElementById("offer-image").value);
        var result = JSON.parse(resp);
        console.log(result.link);
        $("#offer-image").attr('value', result.link);
    });

    dropzone.on('complete', function(file){
        $('#imagemoferta').attr('src', file.dataURL);
        var html = '<div class="alert-success alert fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Imagem carregada com sucesso!</div>';
        $('#msg').html(html);
    });
});