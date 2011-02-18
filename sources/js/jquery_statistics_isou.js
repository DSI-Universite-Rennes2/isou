//au chargement de la page
$(document).ready(function(){
	$("#table-weekly").visualize({type: 'area'});
	$("#table-browsers").visualize({type: 'line'});
	$("#table-os").visualize({type: 'line'});

	$("table").css("display","none");
});
