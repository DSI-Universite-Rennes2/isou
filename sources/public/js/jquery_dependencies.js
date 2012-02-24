function changeService(index){
	// index--;
	$(".service").css("display","none");
	$(".service").eq(index).css("display","block");
	$("#isoulist li:eq("+index+") a").css("background-color","#87CEFA");

}

function closeForm(){
	$("#addFormMask, #bgFormMask, #update").css("display","none");
}

function addDependance(click){
	$("body").prepend($("<div id=\"addFormMask\"></div>"));
	$("body").prepend($("<div id=\"bgFormMask\"></div>"));

	$("#addFormMask").append($("<div id=\"closeAddForm\"><a href=\"#\" onclick=\"closeForm();\"><img alt=\"FERMER\" /></a></div>"));
	$("#addFormMask").append($("#update"));
	$("#addFormMask").append($("#addform"));

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

	$("#addform").css("display","block");
	$("#addform").css("padding","1em");
	$("#addform").css("margin","0em");
	$("#addform > fieldset").css("margin","0em");

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
	$(".legend").css("display","none");
	$("#addform").css("display","none");
	$("#addform").attr("action",$("#addform").attr("action")+"?add");

	$("input[name=cancel]").click(function(){
		// show image to edit/delete
		$(this).parent().prev().children("a").removeClass("hidden");
		// move out from form the 1st paragraph
		$(this).parent().parent().parent().append($(this).parent().prev());
		// drop form
		$(this).parent().parent().remove();
		return false;
	});

	$('<p id=\"addForm\"><a href=\"#\">Ajouter une dépendance</a></p>').insertBefore($("#services"));

	$("#addForm").css("margin","1.2em 0em");
	$("#addForm a").css("display","block");
	$("#addForm a").css("width","27%");
	$("#addForm a").css("background-color","#66CDAA");
	$("#addForm a").css("color","#101010");
	$("#addForm a").css("border","0.2em ridge #6B8E23");
	$("#addForm a").css("padding",".5em 0em");
	$("#addForm a").css("text-align","center");

	$("#addForm a").mouseover(function(){
		$(this).css("border","0.2em groove #6B8E23");
	});

	$("#addForm a").mouseout(function(){
		$(this).css("border","0.2em ridge #6B8E23");
	});

	$("#addForm a").click(function(){
		addDependance(true);
		$(this).css("border","0.2em ridge #6B8E23");
	});


	/* RECUPERATION DE L'INDEX FOURNIT DANS L'URL */
	var anchor = '';
	if(window.location.search != ""){
		anchor = window.location.search.split("&S=");
		if(anchor[0] == '?add'){
			anchor = 'add';
		}else{
			anchor = anchor[1];
		}
	}

	/* TRANSFORMATION DES ANCRES EN PARAMETRE D'URL */
	$(".service dl dd p a:lt(2)").each(function(index){
		$(this).attr("href",$(this).attr("href").replace("#S","&S="));
	});

	/* generate left menu */
	$("#services h2").css("text-align","center");
	$("#services h2").css("margin","1em 0em");
	$("#services h2").after($("<div id=\"isoulist\"><ul>"));
	$("#content").prepend($("#services h2"));

	var tmp = '';
	var id = 0;

	$("dt").each(function(index){
		var name = $(this).text().replace(/"/g,'').replace(/</g,'&lt;').replace(/>/g,'&gt;');
		if(tmp != name){
			tmp = name;
			$("#isoulist").append($("<li><a href=\"javascript:changeService("+id+")\" title=\"Afficher les dépendances du service '"+name+"'\">"+name+"</a></li>"));
			id++;
		}
	});

	$("#isoulist").append($("</ul>"));
	/* !generate left menu */

	$("#isoulist").css("float","left");
	$("#isoulist li").css("list-style-type","none");
	$("#isoulist li").css("margin",".3em");

	$("#isoulist li a").hover(
		function(){
			if($(this).css("background-color") != "rgb(135, 206, 250)"){
				$(this).css("background-color","#D8BFD8");
			}
		},
		function(){
			if($(this).css("background-color") != "rgb(135, 206, 250)"){
				$(this).css("background-color","#B0E0E6");
			}
		}
	);

	$("#isoulist li a").click(function(){
		$("#isoulist li a").css("background-color","#B0E0E6");
		$(this).css("background-color","#87CEFA");
	});

	$("#isoulist li a").css("display","block");
	$("#isoulist li a").css("padding",".2em .5em");
	$("#isoulist li a").css("color","black");
	$("#isoulist li a").css("background-color","#B0E0E6");
	$("#isoulist li a").css("text-decoration","none");
	$("#isoulist").css("width","27%");
	$("#isoulist li").css("overflow","hidden");
	$("#isoulist").css("overflow-y","scroll");
	$("#isoulist").css("height","24em");
	$("#isoulist").css("border-color","#C0C0C0");
	$("#isoulist").css("border-width","0.2em");
	$("#isoulist").css("border-style","double");

	$(".service").css("float","left");
	$(".service").css("clear","none");
	$(".service").css("width","70%");

	$("dl").css("padding-left","5%");
	$("dl").css("width","45%");

	$(".service").css("display","none");
	$(".service").eq(0).css("display","block");
	$(".service span").css("font-size","0.85em");


	/* * * * * *
	 * GERE LE RECHARGEMENT D'UNE PAGE
	 * */
	if(anchor != ''){
		if(anchor == 'add'){
			addDependance(false);
			changeService(0);
		}else{
			changeService(parseInt(anchor));
		}
	}else{
		if(window.location.hash != ''){
			changeService(parseInt(window.location.hash.substr(1)));
		}else{
			changeService(0);
		}
	}

});
