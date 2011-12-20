/* function appelé lors de l'utilisation du menu déroulant contenant les différents types d'évènement */
function changeOperation(){

	var operationId = $("#scheduled option").eq($("#scheduled")[0].selectedIndex).attr("value");

	if(operationId == 2){
		$("#p-period").css("display","block");
		$("#p-forced").css("display","none");
		$("#description").parent().css("display","block");
		$("#beginDate").prev().text("Date de la prochaine opération régulière");
		$("#endDate").prev().text("Date de fin de la prochaine opération régulière");
	}else if(operationId == 3){
		$("#p-period").css("display","none");
		$("#p-forced").css("display","none");
		$("#description").parent().css("display","block");
		$("#beginDate").prev().text("Date de la prochaine fermeture");
		$("#endDate").prev().text("Date de réouverture (optionnel)");
	}else if(operationId == 0){
		$("#p-period").css("display","none");
		$("#p-forced").css("display","block");
		$("#description").parent().css("display","block");
		$("#beginDate").prev().text("Date de début");
		$("#endDate").prev().text("Date de fin de l'interruption (optionnel)");
	}else{
		$("#scheduled option:eq(1)").attr("selected",true);
		$("#p-period").css("display","none");
		$("#p-forced").css("display","none");
		$("#description").parent().css("display","block");
		$("#beginDate").prev().text("Date de la prochaine maintenance");
		$("#endDate").prev().text("Date de fin de la maintenance");
	}
}

/* fonction appelée lorsque le bouton annulé des formulaires est utilisé */
function closeForm(){
	$("#form-fg-layout, #form-bg-layout").css("display","none");
	$("#update").remove();
}

/* function appelé lorsqu'on ajoute/modifie/supprime un évènement */
/* mise en page du formulaire en transparence en plein écran */
function showForm(click, type){
	$("body").prepend($("<div id=\"form-fg-layout\"></div>"));
	$("body").prepend($("<div id=\"form-bg-layout\"></div>"));

	$("#form-fg-layout").append($("#update"));

	switch(type){
		case "info" : $("#form-fg-layout").append($("#form-add-info"));break;
		case "event" : $("#form-fg-layout").append($("#form-add-event"));break;
		case "delete" : $("#form-fg-layout").append($("#update"));break;
		case "modify" : $("#form-fg-layout").append($("#form-edit"));break;
	}

	switch(type){
		case "info":
						$("#form-add-info").css("display","block");
						$("#form-add-info").css("padding","1em");
						$("#form-add-info").css("margin","0em");
						$("#form-add-info > fieldset").css("margin","0em");
						break;
		case "event":
						$("#form-add-event").css("display","block");
						$("#form-add-event").css("padding","1em");
						$("#form-add-event").css("margin","0em");
						$("#form-add-event > fieldset").css("margin","0em");
						break;
	}

	if(click){
		$("#form-bg-layout").animate({"vibility":"visible","opacity":"0.5"}, {queue:false, duration: 500});
		$("#form-fg-layout").animate({"vibility":"visible","opacity":"1","width":"90%","left":"0%"}, {queue:false, duration: 1000});
	}else{
		$("#form-bg-layout").css({"vibility":"visible","opacity":"0.5"});
		$("#form-fg-layout").css({"vibility":"visible","opacity":"1","width":"90%","left":"0%"});
	}
}

//au chargement de la page
$(document).ready(function(){
	// masque le formulaire pour ajouter un évènement
	$("#form-add-event").css("display","none");
	$("#form-add-event").attr("action",$("#form-add-event").attr("action")+"?add");

	// masque le formulaire pour ajouter un message informatif
	$("#form-add-info").css("display","none");
	$("#form-add-info").attr("action",$("#form-add-info").attr("action")+"?info");
	$(".info").css("display","none");
	if($("#forced") && $("#forced").val() == "0"){
		$("#warning-forced").css("display","inline");
	}else{
		$("#warning-forced").css("display","none");
	}
	$("#forced").change(function(){
		if($(this).val() == "0"){
			$("#warning-forced").css("display","inline");
		}else{
			$("#warning-forced").css("display","none");
		}
	});

	// supprimer les ancres de remonter de page
	$(".form").next().remove();

	// supprimer les ? d'aide
	$(".help").remove();

	// supprimer la liste des ancres
	$("#events-quick-access").remove();

	// supprimer le lien "Retour au formulaire"
	$(".quickaccess-form").remove();

	// ajoute un bouton 'fermer' sur les formulaires
	$("#form-add-event, #form-add-info").prepend('<p style="text-align:right;"><a class="close" href="#">Fermer</a></p>');

	// modifier l'action des boutons "cancel", sauf sur #form-edit
	$("#form-add-event input[name=cancel], #form-add-info input[name=cancel], .close").click(function(){
		closeForm();
		return false;
	});

	// ajustement des marges du bouton "ajouter un évènement"
	$("#add-form, #add-info").css({"margin":"2em 0"});

	$("#button-add-event, #button-add-info").attr("href", "#");

	/* * * * * * *
	 * ASSIGNER LE CALENDRIER AUX 2 INPUT
	 * * * * * */

	var urlRE = new RegExp("(.*)/index.php","i");
	url = urlRE.exec(window.location.href);
	url = url[0];
	url = url.substring(0,(url.length-('index.php').length));
	url.toLowerCase();

	// ajoute un calendrier après les inputs des formulaires
	$("#beginDate, #endDate, #beginDateMessage, #endDateMessage, #beginDateUpd, #endDateUpd").after("<a href=\"#\"><img src=\""+url+"/images/calendar.png\" alt=\"calendrier\" title=\"afficher le calendrier\" width=\"1em\" height=\"1em\" /></a>");

	jQuery("#beginDate, #endDate, #beginDateMessage, #endDateMessage, #beginDateUpd, #endDateUpd").dynDateTime({
							showsTime: true,
							ifFormat: "%d/%m/%Y %H:%M",
							align: "TL",
							electric: false,
							singleClick: false,
							button: ".next()" //next sibling
						});

	// définit l'anchor
	var anchor = '';
	if(window.location.search != ""){
		anchor = window.location.search.split("&D=");
		if(anchor[0] == '?add'){
			anchor = 'add';
		}else if(anchor[0] == '?info'){
			anchor = 'info';
		}else{
			anchor = anchor[1];
		}
	}

	$("#button-add-event").click(function(){
		showForm(true, 'event');
	});

	$("#button-add-info").click(function(){
		showForm(true, 'info');
	});

	$("#scheduled").change(function(){
		changeOperation();
	});

	/* * * * * *
	 * GERE LE RECHARGEMENT D'UNE PAGE
	 * */
	if(anchor == "add"){
		showForm(false, "event");
	}else{
		if(anchor == "info"){
			showForm(false, "info");
		}
	}

	if(window.location.search.indexOf("delete") != -1){
		showForm(false, 'delete');
	}else{
		if(window.location.search.indexOf("modify") != -1){
			window.location.hash = "#form-edit";
			showForm(false, "modify");
		}
	}

	// changeOperation();
});
