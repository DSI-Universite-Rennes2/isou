//au chargement de la page
$(document).ready(function(){
	$("#list").before('<form>'+
						'<fieldset style="margin: 0em 0em 0em 1em; font-weight: bold;">'+
						'<legend>Trier par type d\'interruption</legend>'+
						'<input type="checkbox" checked="checked" id="U" name="Cb1">'+
						'<label for="U" style="font-weight: normal;">Interruptions non prévues</label>'+
						'<input type="checkbox" checked="checked" id="S" name="Cb2">'+
						'<label for="S" style="font-weight: normal;">Interruptions prévues</label>'+
						'<input type="checkbox" id="R" name="Cb3">'+
						'<label for="R" style="font-weight: normal;">Interruptions régulières</label>'+
						'<input type="checkbox" checked="checked" id="C" name="Cb4">'+
						'<label for="C" style="font-weight: normal;">Services fermés</label>'+
						'<input type="checkbox" checked="checked" id="M" name="Cb5">'+
						'<label for="M" style="font-weight: normal;">Messages informatifs</label>'+
						'</fieldset></form>');
	$(".regular").css("display", "none");

	$("#U").click(function(){
		if($(this).attr("checked") == "checked"){
			$(".unscheduled").css("display", "table-row");
		}else{
			$(".unscheduled").css("display", "none");
		}
	});

	$("#S").click(function(){
		if($(this).attr("checked") == "checked"){
			$(".scheduled").css("display", "table-row");
		}else{
			$(".scheduled").css("display", "none");
		}
	});

	$("#R").click(function(){
		if($(this).attr("checked") == "checked"){
			$(".regular").css("display", "table-row");
		}else{
			$(".regular").css("display", "none");
		}
	});

	$("#C").click(function(){
		if($(this).attr("checked") == "checked"){
			$(".closed").css("display", "table-row");
		}else{
			$(".closed").css("display", "none");
		}
	});

	$("#M").click(function(){
		if($(this).attr("checked") == "checked"){
			$(".messages").css("display", "table-row");
		}else{
			$(".messages").css("display", "none");
		}
	});

});
