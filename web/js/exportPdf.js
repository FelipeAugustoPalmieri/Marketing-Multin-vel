 $(document).ready(function() {

    $("#exportButton").click(function(e) {
        e.preventDefault();

        var name_map = '';
        $(".form-control").each(function(i) {
            if(i > 0) {
                name_map += '&';
            }

            var name = $(this).attr('name');
            var value = $(this).val();
            name_map +=  name + '=' + value;

        });

        $("form input:checked").each(function(i) {
            if(name_map.length > 0) {
                name_map += '&';
            }

            var name = $(this).attr('name');
            var value = $(this).val();
            name_map +=  name + '=' + value;

        });

        name_map = '?' + name_map;
        var caminho = $(this).data('url');
        var hrefaux = $(this).attr('href');
        if(hrefaux !== undefined){
            var opn = open(hrefaux + name_map, "_blank");
        }else if(caminho !== undefined){
            var opn = open("representative-comission-export" + name_map, "_blank");
        }else{
            var opn = open("report-export" + name_map, "_blank");
        }
        
        displayBook(opn);
        ebookStore.add(opn);
        ebookStore.sync();
    });

});