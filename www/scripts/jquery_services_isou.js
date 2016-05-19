function categoryChange(index){
	if(index <= 0){
		$(".listing").css("display","block");
		$("#list h2").css("display","block");
	}else{
		$(".listing").css("display","none");
		$("#list h2").css("display","none");

		$(".listing").eq(index-1).css("display","block");
		$("#list h2").eq(index-1).css("display","block");
		$("#categorySelect option:eq("+index+")").attr("selected","selected");

		/* TRANSFORMATION DES ANCRES EN PARAMETRE D'URL */
		$(".listing .actionbox a").each(function(){
			$(this).attr("href",$(this).attr("href").replace(/&S=.*/,"&S="+(index+1)));
		});
	}
}

function closeForm(){
	$("#form-fg-layout, #form-bg-layout, #update").css("display","none");
}

function showForm(click, type){
	$("body").prepend($("<div id=\"form-fg-layout\"></div>"));
	$("body").prepend($("<div id=\"form-bg-layout\"></div>"));

	$("#form-fg-layout").append($("#update"));

	switch(type){
		case "add" : $("#form-fg-layout").append($("#form-add-service"));break;
		case "edit" : $("#form-fg-layout").append($("#form-edit-service"));break;
		case "delete" : $("#form-fg-layout").append($("#update"));break;
	}

	$("#form-fg-layout > form").css("display","block");

	if(click){
		$("#form-bg-layout").animate({"vibility":"visible","opacity":"0.5"}, {queue:false, duration: 500});
		$("#form-fg-layout").animate({"vibility":"visible","opacity":"1","width":"90%","left":"0%"}, {queue:false, duration: 1000});
		$("#update").css("display","none");
	}else{
		$("#form-bg-layout").css({"vibility":"visible","opacity":"0.5"});
		$("#form-fg-layout").css({"vibility":"visible","opacity":"1","width":"90%","left":"0%"});
	}
}

//au chargement de la page
$(document).ready(function(){

	/* * * * *
	 * MASQUE LES DEPENDANCES
	 * * * * * */
	$("div.parentsList").css("display","none");
	$(".parentsList").each(function(){
		$(this).parent().contents().filter(function(index){ return index === 0; }).wrap('<a class="td-service-name">');
	});

	$(".td-service-name").click(function(){
		$(this).parent().children(".parentsList").slideToggle("normal");
	});


	/* * * * * *
	* CREER DYNAMIQUEMENT LA LISTE DES SERVICES APPARAISSANT SUR LA PAGE
	* */
	/*var select = '';
	$("#list h2").each(function(index){
		select += "<option>"+$(this).text()+"</option>";
	});*/


	/* * * * * * * *
	 * MODIFIE LE DOM
	 * * * * */
	//$("#list").prepend("<label for=\"categorySelect\">Sélectionner une catégorie</label><select name=\"categorySelect\" id=\"categorySelect\"><option>Afficher toutes les catégories</option>"+select+"</select>");

	$("#form-add-service").css("display","none");
	$("#form-add-service").attr("action",$("#form-add-service").attr("action")+"&add");

	/* * * * *
	 * AJOUTE LE BOUTON POUR ACCEDER AU FORMULAIRE D'AJOUT D'INTERRUPTION
	 * * * * */
	$("#content .legend:first").after($("<p id=\"add-menu\"><a id=\"button-add-service\" href=\"#\">Ajouter un service ISOU</a></p>"));

	$("#button-add-service").click(function(){
		showForm(true, "add");
	});

	// modifier l'action des boutons "cancel", sauf sur #form-edit
	$("#form-add-service input[name=cancel]").click(function(){
		closeForm();
		return false;
	});

	$("#state").change(function(){
		if($("#state option:selected").val() === "0"){
			if($("#readonly option:selected").val() === "1"){
				$("#readonly option:selected").removeAttr("selected");
				$("#readonly option:eq(0)").attr("selected", "selected");
			}
		}else{
			if($("#readonly option:selected").val() === "0"){
				$("#readonly option:selected").removeAttr("selected");
				$("#readonly option:eq(1)").attr("selected", "selected");
			}
		}
	});

	/* RECUPERATION DE L'INDEX FOURNIT DANS L'URL */
	var anchor = '';
	if(window.location.search != ""){
		anchor = window.location.search.split("&S=");
		if(anchor[0] == '?service=isou&add'){
			anchor = 'add';
		}else if(window.location.hash == '#edit'){
			// window.location.hash = '#top';
			window.location.hash = '';
			anchor = 'edit';
		}else if(anchor[0] == '?service=isou'){
			anchor = anchor[0];
		}else{
			(anchor[1] == undefined)?anchor = 0:anchor = parseInt(anchor[1])+1;
		}
	}

	/* TRANSFORMATION DES ANCRES EN PARAMETRE D'URL */
	$(".listing .actionbox a").each(function(){
		$(this).attr("href",$(this).attr("href").replace(/#S.*/,"&S="+$(".listing").index($(this).parent().parent().parent().parent())));
	});

	/* * * * * * * * *
	* GERE LE CHANGEMENT DANS LA LISTE 'TRIER PAR SERVICES'
	* */
	$("#categorySelect").change(function(){
		// Need to remember that JQuery returns an array of matching elements, even if you use the '#' selector that returns only one elements based on the ID.
		categoryChange($("#categorySelect")[0].selectedIndex);
	});


	/* * * * * *
	 * GERE LE RECHARGEMENT D'UNE PAGE
	 * */
	if(anchor != ''){
		if(anchor == 'add'){
			showForm(false, "add");
			categoryChange(0);
		}else if(anchor == 'edit'){
			showForm(false, "edit");
			// setclick on add
		}else if(anchor == "?service=isou"){
			categoryChange(0);
		}else{
			categoryChange(parseInt(anchor));
		}
	}else{
		categoryChange(0);
	}

	if(window.location.search.indexOf("delete") != -1){
		showForm(false, "delete");
	}else{
		if(window.location.search.indexOf("modify") != -1){
			window.location.hash = "";
			showForm(false, "edit");
		}
	}

});
