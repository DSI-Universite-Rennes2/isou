function closeForm(){
	$("#addFormMask, #bgFormMask, #update").css("display","none");
}

function directNagios(){
	if($("#directNagios").attr("checked")){
		$("#addform1 .header label").css("display","inline-block");
		$("#addform2 .header label").css("display","inline-block");
		$("#addFormMask input[name='nameForUsers']").css("display","inline-block");
		$("#addFormMask select[name='category']").css("display","inline-block");
	}else{
		$("#addform1 .header label:gt(0)").css("display","none");
		$("#addform2 .header label:gt(0)").css("display","none");
		$("#addFormMask input[name='nameForUsers']").css("display","none");
		$("#addFormMask select[name='category']").css("display","none");
	}
}

function addService(click){
	$("body").prepend($("<div id=\"addFormMask\"></div>"));
	$("body").prepend($("<div id=\"bgFormMask\"></div>"));

	$("#addFormMask").append($("<div id=\"closeAddForm\"><a href=\"#\" onclick=\"closeForm();\"><img alt=\"FERMER\" /></a></div>"));
	$("#addFormMask").append($("#update"));
	// $("#addFormMask").append($("<p><input onclick=\"directNagios();\" type=\"checkbox\" id=\"directNagios\" /><label for=\"directNagios\">Utiliser directement ce service Nagios dans Isou, sans passer par le système des dépendances</label></p>"));
	$("#addFormMask").append($("#p-search, #addform1, #addform2"));


	$("#addform1 .header label:gt(0)").css("display","none");
	$("#addform2 .header label:gt(0)").css("display","none");
	$("#addFormMask input[name='nameForUsers']").css("display","none");
	$("#addFormMask select[name='category']").css("display","none");

	$("#addFormMask").css("z-index","2");

	$("#bgFormMask").css("width","100%");
	$("#bgFormMask").css("height","100%");
	$("#bgFormMask").css("position","fixed");

	$("#bgFormMask").css("z-index","1");
	$("#bgFormMask").css("background-color","black");
	$("#bgFormMask").css("opacity","0");

	$("#addFormMask").css("position","absolute");
	$("#addFormMask, #bgFormMask").css("left","0");
	$("#addFormMask, #bgFormMask").css("top","0");

	// header : 3+2x0.5em / h1 : 1em / menubar : 3+2x0.7em == 9.4em
	$("#addFormMask").css("margin","13.4em 5% 0em 5%");
	$("#addFormMask").css("padding",".5em 1em");
	$("#addFormMask").css("width","0%");
	$("#addFormMask").css("left","50%");

	$('#addFormMask').css('opacity','0');

	$("#addFormMask").css("background-color","#FFFFFF");

	$("#closeAddForm").css("text-align","right");
	$("#closeAddForm").css("padding",".2em 1em");

	$("#p-search, #addform1, #addform2").css("display","block");
	$("#addform1, #addform2").css("padding","1em");
	$("#addform1, #addform2").css("margin","0em");
	$("#addform1 > fieldset, #addform2 > fieldset").css("margin","0em");


	if(click){
		$("#bgFormMask").animate({"vibility":"visible","opacity":"0.5"}, {queue:false, duration: 500});
		$("#addFormMask").animate({"vibility":"visible","opacity":"1","width":"90%","left":"0%"}, {queue:false, duration: 1000});
		$("#update").css("display","none");
	}else{
		$("#bgFormMask").css({"vibility":"visible","opacity":"0.5"});
		$("#addFormMask").css({"vibility":"visible","opacity":"1","width":"90%","left":"0%"});
	}
}

//au chargement de la page
$(document).ready(function(){

	// TODO: mettre cette partie dans un tpl
	$("#addform1").before('<p id="p-search" style="padding-bottom:1.5em;"><label for="search">Rechercher</label><input type="text" id="search" name="search" /></p>');

	/* * * * * * * *
	 * MODIFIE LE DOM
	 * * * * */
	$("#p-search, #addform1, #addform2").css("display","none");
	$("#addform1, #addform2").attr("action",$("#addform1").attr("action")+"&add");

	/* * * * *
	 * AJOUTE LE BOUTON POUR ACCEDER AU FORMULAIRE D'AJOUT D'INTERRUPTION
	 * * * * */
	$("#content .legend:first").after($("<p id=\"addForm\"><a href=\"#\">Ajouter un service NAGIOS</a></p>"));

	$("#addForm").css("margin","1.2em 0em");
	$("#addForm > a").css("display","block");
	$("#addForm > a").css("width","27%");
	$("#addForm > a").css("background-color","#66CDAA");
	$("#addForm > a").css("color","#101010");
	$("#addForm > a").css("padding",".5em 0em");
	$("#addForm > a").css("text-align","center");
	$("#addForm > a").css("border","0.2em ridge #6B8E23");

	$("#addForm a").mouseover(function(){
		$(this).css("border","0.2em groove #6B8E23");
	});

	$("#addForm a").mouseout(function(){
		$(this).css("border","0.2em ridge #6B8E23");
	});

	$("#addForm a").click(function(){
		addService(true);
		$(this).css("border","0.2em ridge #6B8E23");
	});

	// function qui permet de filtrer le menu déroulant listant tous les services Nagios
	$("#search").keypress(function(){
		// if($(this).val().length > 2){
			$("#servicename option, #hostname option").each(function(){
				if($(this).text().indexOf($("#search").val()) == -1){
					if(!$(this).hasClass("invisible")){
						$(this).addClass("invisible");
					}
				}else{
					if($(this).hasClass("invisible")){
						$(this).removeClass("invisible");
					}
				}
			});
		// }
        $("#servicename option, #hostname option").removeAttr("selected","selected");
		$("#servicename option[class!='invisible']").first().attr("selected","selected");
		$("#hostname option[class!='invisible']").first().attr("selected","selected");
	});

	/* RECUPERATION DE L'INDEX FOURNIT DANS L'URL */
	var anchor = '';
	if(window.location.search != ""){
		anchor = window.location.search.split("&S=");
		if(anchor[0] == '?service=nagios&add'){
			anchor = 'add';
		}
	}

	/* * * * * *
	 * GERE LE RECHARGEMENT D'UNE PAGE
	 * */
	if(anchor == 'add'){
		addService(false);
	}

});
