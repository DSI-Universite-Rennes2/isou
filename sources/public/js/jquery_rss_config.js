function genKey(){
	var sum = 0;
	$("#configForm > ul ul input:checkbox").each(function(){
		if($(this).attr("checked")){
			sum += Math.pow(2,parseInt($(this).attr("value")));
		}
	});

	var urlRE = new RegExp("(.*)/index.php","i");
	url = urlRE.exec(window.location.href);
	url = url[0];
	url = url.substring(0,(url.length-('index.php').length));
	url.toLowerCase();

	if(sum == 0){
		url = url+"rss.php";
	}else{
		url = url+"rss.php?key="+(sum.toString(16)).toUpperCase();
	}

	$("#content > p:last").html("Vous pouvez consulter les actualités des services sélectionnés précédemment en utilisant ce lien RSS : "+
	"<a href=\""+url+"\" title=\"lien vers le flux RSS\">"+url+"</a>");
}


//au chargement de la page
$(document).ready(function(){

	$("#configForm").submit(function(){
		genKey();
		return false;
	});

	$("#configForm > ul > li span").each(function(i){
		$(this).replaceWith("<input type=\"checkbox\" name=\"cat_"+i+"\" id=\"cat_"+i+"\" />"+
			"<label for=\"cat_"+i+"\">"+$(this).text()+"</label>");
	});

	// gestion du clic sur une catégorie
	$("#configForm > ul li > input:checkbox").click(function(){
		var index = $("#configForm > ul li > input:checkbox").index(this);

		if($("#configForm > ul > li:eq("+index+") > input:checkbox").attr('checked')){
			$("#configForm > ul > li:eq("+index+") input:checkbox").attr('checked','true');
		}else{
			$("#configForm > ul > li:eq("+index+") input:checkbox").removeAttr('checked');
		}
	});

	// gestion du clic sur un service
	$("#configForm > ul li > ul li > input:checkbox").click(function(){
		if($(this).parent().parent().find("input:checkbox[checked=true]").length > 0){
			$(this).parent().parent().parent().children("input").removeAttr('checked');
			if($(this).parent().parent().find("input:checkbox[checked=true]").length == $(this).parent().parent().find("input:checkbox").length){
				$(this).parent().parent().parent().children("input").attr('checked', 'true');
			}
		}
	});

});
