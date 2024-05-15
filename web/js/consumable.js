$(document).ready(function() {
	if(!isMobile){
	    $('#consumable-shared_percentage').maskMoney({precision: 6});
	    $('#consumable-shared_percentage_adm').maskMoney({precision: 6});
	}
});