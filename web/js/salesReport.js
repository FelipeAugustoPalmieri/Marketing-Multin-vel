$(document).ready(function() {
    $('.alterdata').editable();
    $('.altertotal').editable({
    	ajaxOptions: {
           dataType: 'json' //assuming json response
       	},
    	success: function(response, newValue) {
    		var caminho = $("table").parent().find("tr[data-key='"+response.id+"'] > td.repasse").html(response.repasse)
    	}
    });
});