/* fonction appelée lors de l'utilisation du menu déroulant contenant tous les services */
function changeService(index){
	index--;
	$(".service").css("display","none");
	$(".service").eq(index).css("display","block");
	$("#isoulist li:eq("+index+") a").css("background-color","#87CEFA");
}

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
		$("#endDate").prev().text("Date de fin de la prochaine fermeture (optionnel)");
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

/*function redirectForm(){
	window.location.href = window.location.protocol+"//"+window.location.hostname+window.location.pathname;
}*/

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

/* * * * * * *
 * ENREGISTRE LES PREFERENCES DANS UN COOKIE
 */
function set_pref(id,value){
	var exdate = new Date();
	exdate.setDate(exdate.getDate()+31);
	document.cookie = id+ "=" +escape(value)+
	((31==null) ? "" : ";expires="+exdate.toGMTString());
}

/* * * * * * *
 * LIT LES PREFERENCES DANS LE COOKIE
 */
function get_pref(id){
	if(document.cookie.length>0){
		c_start=document.cookie.indexOf(id + "=");
		if(c_start!=-1){
			c_start=c_start + id.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if(c_end==-1) c_end=document.cookie.length;
			return unescape(document.cookie.substring(c_start,c_end));
		}
	}
	return "";
}


function sortByCheckboxValues(init){
	if(init == true){
		var idAttr = '';
	 	$(":checkbox").each(function(){
	 		if(get_pref($(this).attr("id")) == "false"){
	 			$(this).removeAttr("checked");
			}else{
				$(this).attr("checked","checked");
			}
		});
	}else{
		var idAttr = init;
	}

	$("#content div h3 a").each(function(index){
		if($("#"+$(this).attr("name").substr(0,1)).attr("checked") == true){
			$("#content .events:eq("+index+")").css("display","block");
			set_pref($(this).attr("name"),"true");
		}else{
			$("#content .events:eq("+index+")").css("display","none");
			set_pref($(this).attr("name"),"false");
		}
	});
}

function sortBySelectValue(init){
	if(init){
		$("#select-services option").each(function(index){
			if($(this).text() == get_pref(init)){
				$(this).attr("selected","selected");
			}
		});
	}

	if($("#select-services option:selected").text() == 'Afficher tous les services'){
		$("#content ul li").css("display","list-item");
	}else{
		$("#content ul li").each(function(index){
			if($("#content ul li:eq("+index+") p span").text() == $("#select-services option:selected").text() || $("#content ul li:eq("+index+") form").attr("id") == "form-edit"){
				$("#content ul li:eq("+index+")").css("display","list-item");
			}else{
				$("#content ul li:eq("+index+")").css("display","none");
			}
		});
	}
	set_pref("select-services",$("#select-services option:selected").text());
}

//au chargement de la page
$(document).ready(function(){
	// masque le formulaire pour ajouter un évènement
	$("#form-add-event").css("display","none");
	$("#form-add-event").attr("action",$("#form-add-event").attr("action")+"?add");

	// masque le formulaire pour ajouter un message informatif
	$("#form-add-info").css("display","none");
	$("#form-add-info").attr("action",$("#form-add-info").attr("action")+"?info");

	// supprimer les ancres de remonter de page
	$(".form").next().remove();

	// supprimer les ? d'aide
	$(".help").remove();

	// supprimer les ancres de remonter vers le formulaire
	$(".quickaccess-form").remove();

	// modifier l'action des boutons "cancel", sauf sur #form-edit
	$("#form-add-event input[name=cancel], #form-add-info input[name=cancel]").click(function(){
		closeForm();
		return false;
	});


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

	/* * * * * *
	 * CREER DYNAMIQUEMENT LA LISTE DES SERVICES APPARAISSANT SUR LA PAGE
	 * */
	var list = new Array();
	$("#content h3:lt(4) ~ ul p span").each(function(index){
		i = 0;
		find = false;

		while(i<list.length && !find){
			if(list[i] == '<option>'+$(this).text()+'</option>'){
				find = true;
			}
			i++;
		}

		if(!find){
			list[list.length] = "<option>"+$(this).text()+"</option>";
		}
	});

	list.sort();


	/* * * * *
	 * CREE UN FORMULAIRE POUR FILTRER LES EVENEMENTS
	 * */
	$("#form-add-event").after($("<form id=\"form-filter\" action="+window.location.href+">"+
							"<p>"+
							"<label for=\"select-services\">Trier par services</label>"+
							"<select id=\"select-services\" name=\"select-services\"><option>Afficher tous les services</option>"+list+"</select>"+
							"</p>"+
							"<fieldset>"+
							"<legend>Trier par type d'interruption</legend>"+
							"<input type=\"checkbox\" name=\"Cb1\" id=\"U\" checked=\"checked\"><label for=\"U\">Interruptions non prévues</label>"+
							"<input type=\"checkbox\" name=\"Cb2\" id=\"S\" checked=\"checked\"><label for=\"S\">Interruptions prévues</label>"+
							"<input type=\"checkbox\" name=\"Cb3\" id=\"R\" checked=\"checked\"><label for=\"R\">Interruptions régulières</label>"+
							"<input type=\"checkbox\" name=\"Cb4\" id=\"C\" checked=\"checked\"><label for=\"C\">Services fermés</label>"+
							"<input type=\"checkbox\" name=\"Cb5\" id=\"M\" checked=\"checked\"><label for=\"M\">Messages informatifs</label>"+
							"</fieldset>"+
							"</form>"));

	/* * * * * * * * *
	 * GERE LE CLICK DANS LES CHECKBOX 'TRIER PAR TYPE D'INTERRUPTION'
	 * */
	sortByCheckboxValues(true);
	$(":checkbox").click(function(){
		sortByCheckboxValues($(this).attr("id"));
	});


	/* * * * * * * * *
	 * GERE LE CHANGEMENT DANS LA LISTE 'TRIER PAR SERVICES'
	 * */
	sortBySelectValue("select-services");
	$("#select-services").change(function(){
		sortBySelectValue();
	});


	/* * * * *
	 * AJOUTE LE BOUTON POUR ACCEDER AU FORMULAIRE D'AJOUT D'EVENEMENTS ET DE MESSAGES INFORMATIFS
	 * * * * */
	$("#form-filter").after($("<p id=\"add-menu\">"+
								"<a href=\"#\" id=\"button-add-event\">Ajouter un évènement</a>"+
								"<a href=\"#\" id=\"button-add-info\">Ajouter un message informatif</a>"+
							"</p>"));

	$("#button-add-event").click(function(){
		showForm(true, 'event');
		$(this).css("border","0.2em ridge #6B8E23");
	});

	$("#button-add-info").click(function(){
		showForm(true, 'info');
		$(this).css("border","0.2em ridge #6B8E23");
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
			window.location.hash = "";
			showForm(false, "modify");
		}
	}

	changeOperation();
});
