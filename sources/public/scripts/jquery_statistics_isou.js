//au chargement de la page
$(document).ready(function(){
	$("#table-weekly").visualize({type: 'area'});
	$("#table-browsers").visualize({type: 'line'});
	$("#table-os").visualize({type: 'line'});

	$("table").css("display","none");
	$("table").before('<p><a class="toggleCanvas">Basculer en mode tableau</a></p>');
	$(".toggleCanvas").click(function(){
		if($(this).parent().next().css("display") == "none"){
			$(this).parent().next().css("display", "table");
			$(this).parent().next().next().css("display", "none");
		}else{
			$(this).parent().next().css("display", "none");
			$(this).parent().next().next().css("display", "block");
		}

	});
});
