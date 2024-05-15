$(document).ready(function() {

	$('#consumer-plane_id').change(function() {

		var value = $(this).val();
		var btnTarget = $($(this).attr('data-target'));

		if($.trim(value) != '') {
			btnTarget.attr('data-id', value);
			btnTarget.attr('disabled', false);
			return;
		}
		btnTarget.attr('data-id', '');
		btnTarget.attr('disabled', true);
	});
	$('#id-button').click(function() {
		if(!$(this).attr('disabled')) {
			window.open('https://www.mercadopago.com/mlb/checkout/start?pref_id=' + $(this).attr('data-id'), '_blank');		
		}
	});
});