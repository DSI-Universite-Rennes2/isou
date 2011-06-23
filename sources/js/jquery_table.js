function changeService(index){
	if(index == 0){
		// afficher tous les services
		$("#table tbody tr").css("display","table-row");
	}else{
		// afficher un seul service
		$("#table tbody tr").css("display","none");

		var end = $("#table tbody tr").index($("#table tbody tr th:eq("+index+")").parent());
		index--;
		var start = $("#table tbody tr").index($("#table tbody tr th:eq("+index+")").parent());

		$("#table tbody tr:eq("+start+")").css("display","table-row");
		if(end == -1){
			$("#table tbody tr:gt("+start+")").css("display","table-row");
		}else{
			$("#table tbody tr:lt("+end+"):gt("+start+")").css("display","table-row");
		}
	}
}

//au chargement de la page
$(document).ready(function(){

	if (jQuery.browser.msie && jQuery.browser.version < 8){
		return false;
	}

	// si le tableau est inférieure à 50 ligne, afficher toutes les lignes, sinon trier par catégorie
	if($("#table tbody tr").length < 50){
		/* * * * * *
		 * CREER DYNAMIQUEMENT LA LISTE DES SERVICES APPARAISSANT SUR LA PAGE
		 * */
		var list = new Array();
		$("#table tbody > tr > th").each(function(index){
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

		$("#table").before("<p><label for=\"select-service\">Afficher par catégorie de service</label><select id=\"select-service\" name=\"select-service\"><option>Afficher toutes les catégories</option>"+list+"</select></p>");

		changeService(0);

		$("#select-service").change(function(){
			// Need to remember that JQuery returns an array of matching elements, even if you use the '#' selector that returns only one elements based on the ID.
			changeService($("#select-service")[0].selectedIndex);
		});
	}else{
		// affiche les lignes de la première catégorie
		changeService(1);

		// ajoute un aspect lien sur le nom des catégories
		$(".tr-category").css({"display":"table-row", "cursor":"pointer", "color":"#003C60", "text-decoration":"underline"});

		// pour chaque catégorie, regarder quel est le statut le plus élevé
		$(".tr-category").each(function(){
			var state = 0;

			// changer de version de jquery, et utiliser nextUntil
			// http://api.jquery.com/nextUntil/
			$(this).nextAll("tr").each(function(){
				if($(this).hasClass("tr-category")){
					// stop loop
					return false;
				}else{
					if($(this).hasClass("tr-status-warning")){
						if(state == 0){
							state = 1;
						}
					}else if($(this).hasClass("tr-status-critical")){
						state = 2;
					}
				}
			});

			/* trouver un moyen pour afficher l'information, sans css */
			switch(state){
				case 1: $(this).children("th").addClass("tr-warning-category");break;
				case 2: $(this).children("th").addClass("tr-critical-category");break;
				default: $(this).children("th").addClass("tr-ok-category");break;
			}
		});

		// gère le toggle onclick
		$("#table tbody tr th").click(function(){
			changeService($("#table tbody tr th").index($(this))+1);
			$(".tr-category").css("display","table-row");
		});

	}

});
