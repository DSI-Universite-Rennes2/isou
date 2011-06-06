//au chargement de la page
$(document).ready(function(){
	//$('table').visualize().appendTo('content').trigger('visualizeRefresh');

	// créer un menu déroulant proposant des types de graphique
	$("form p:last").before('<p><label class="label" for="typeGraphic">Type de graphique</label><select id="typeGraphic" name="typeGraphic"><option value="histogram">Histogramme</option><option value="line">Courbe</option><option value="pie">Camembert</option><option value="table">Tableau</option></select></p>');

	$("#service-fieldset br").remove();
	var countCheckboxes = $("#service-fieldset > label").length;

	var ix = Math.round(countCheckboxes/3);

	for(var i=0;i<3;i++){
		$("<div></div>").appendTo("#service-fieldset");
		$("#service-fieldset > label:lt("+ix+")").appendTo("#service-fieldset > div:last");
	}

	$("#service-fieldset div").css("float","left");
	$("#service-fieldset > div > label").css("display","block");

	// désactive l'option "courbe" lors qu'on choisit "tout grouper" (impossibilité de faire une courbe sur 1 valeur)
	if($("#groupbySelect option:selected").val() === "a"){
		$('#typeGraphic option[value="line"]').attr("disabled", "disabled");
		if($("#typeGraphic option:selected").val() === "line"){
			$("#typeGraphic option:selected").next().attr("selected", "selected");
		}
	}

	// désactive l'option "courbe" lors qu'on choisit "tout grouper" (impossibilité de faire une courbe sur 1 valeur)
	$("#groupbySelect").change(function(){
		if($("#groupbySelect option:selected").val() === "a"){
			$('#typeGraphic option[value="line"]').attr("disabled", "disabled");
			if($("#typeGraphic option:selected").val() === "line"){
				$("#typeGraphic option:selected").next().attr("selected", "selected");
			}
		}else{
			$('#typeGraphic option[value="line"]').removeAttr("disabled");
		}
	});

	// décoche tous les checkboxes 'tous les services' lorsqu'on coche une autre case
	$("#service-fieldset input[name=serviceSelect[]]:gt(0)").click(function(){
		$("#service-fieldset input[value=all]").removeAttr("checked");
	});

	// lorsqu'on décoche "tous les services", il remet les services présents dans l'url
	$("#service-fieldset input[value=all]").click(function(){
		if($(this).attr("checked") === true){
			$("#service-fieldset input:gt(0)").each(function(){
				$(this).removeAttr("checked");
			});
		}else{
			var regexp = new RegExp("[\\?&]serviceSelect\\[\\]=([^&#]*)","g");
			while((serviceSelect = regexp.exec(window.location.href)) != null){
				$("input[value="+serviceSelect[1]+"]").attr("checked", "checked");
			}
			$("#service-fieldset input[value=all]").removeAttr("checked");
		}
	});



	/* typeGraphic */
	var regexp = new RegExp("[\\?&]typeGraphic=([^&#]*)");
	var typeGraphic = regexp.exec(window.location.href);
	if(typeGraphic !== null){
		typeGraphic = typeGraphic[1];
		$("option[value="+typeGraphic+"]").attr("selected", "selected");
	}

	// vérifie qu'il y a suffisamment de données pour réaliser une courbe
	if(typeGraphic === 'line' && $('table thead tr th').length < 3){
		$('table').before('<p id="no-event">Données insuffisantes pour réaliser une courbe</p>');
		typeGraphic = '';
	}

	if(typeGraphic === 'line' || typeGraphic === 'histogram' || typeGraphic === 'pie'){
		if($("#groupbySelect option:selected").val() !== 'a'){
			$("table thead tr").children("th:last").remove();
			$("table tbody tr").each(function(){
				$(this).children("td:last").remove();
			});
		}

		switch(typeGraphic){
			case "line" : $('table').visualize({type: 'area'});break;
			case "histogram" : $('table').visualize();break;
			case "pie" : $('table').visualize({type: 'pie', pieMargin: 10});break;
		}

		$('table').css("display","none");
	}



});
