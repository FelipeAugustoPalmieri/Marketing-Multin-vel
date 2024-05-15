$(document).ready(function() {
	modalNetworkTree();
});

function modalNetworkTree()
{
	$('#network-tree a').click(function(e) {
		e.preventDefault();
		$('#modal-network-tree').modal('show').find('.modal-body').load($(this).attr('href'));
	});
}