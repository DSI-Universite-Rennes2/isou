//au chargement de la page
$(document).ready(function(){

	$(".parentsList, .top").css("display","none");
	$(".alert > li > ul:not(.reason)").css("display","none");

	$("#legend").css("position","fixed");
	$("#legend").css("right","0em");
	$("#legend").click(function(){
		$(this).css("display","none");
	});

	$(".alert li").each(function(){
		if($(this).children("ul:not(.reason)").is("ul")){
			$(this).children("span").hover(
				function(){
					$(this).css({"cursor":"pointer","text-decoration":"underline","color":"#000000"});
				},
				function(){
					$(this).css({"cursor":"auto","text-decoration":"line-through","color":"#808080"});
				}
			);
		}
	});

	$(".alert").each(function(){
		if($(this).children().length > 3){
			$(this).before("<p class=\"jsroll\"><span title=\"Dérouler la liste\">[+] dérouler</span></p>");
			$(".jsroll span").hover(
				function(){
					$(this).css("text-decoration","underline");
				},
				function(){
					$(this).css("text-decoration","none");
				}
			);
			$(this).children(":gt(2)").css("display","none");//(list-item
		}
	});

	$(".alert li").click(function(){
		$(this).children("ul:not(.reason)").slideToggle("normal");
	});

	$(".service > li > span").each(function(){
		if($(this).next(".parentsList").length == 1){
			$(this).css("cursor","pointer");
		}
	});

 	$(".service > li > span").click(function(){
 		$(this).next(".parentsList").slideToggle("normal");
	});

	$(".jsroll span").click(function(){
		$(this).parent().next(".alert").children(":gt(2)").each(function(){
			$(this).slideToggle("slow", function(){
				if($(this).css("display") == "block"){
					$(this).css("display","list-item");
				}
			});
		});

		if($(this).text() == "[-] enrouler"){
			$(this).text("[+] dérouler");
			$(this).attr("title","Dérouler la liste");
		}else{
			$(this).text("[-] enrouler");
			$(this).attr("title","Enrouler la liste");
		}
	});
});
