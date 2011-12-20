function strip_accents(str){
	str = str.replace(/[éèêëÊË]/gi,"e");
	str = str.replace(/[àâäÂÄ]/gi,"a");
	str = str.replace(/[îïÎÏ]/gi,"i");
	str = str.replace(/[ûùüÛÜ]/gi,"u");
	str = str.replace(/[ôöÔÖ]/gi,"o");
	str = str.replace(/[ç]/gi,"c");
	str = str.replace(/[ ']/gi,"_");
	str = str.replace(/[^a-zA-Z0-9_]/gi,"");
	return str;
}

function strtotime(str){
	// format : eg. 07 novembre 2009 13:50
	var day;
	var month;
	var year;
	var hour;
	var minute;

	day = str.substr(0,2);
	year = str.substr(-10,4);
	hour = str.substr(-5,2);
	minute = str.substr(-2);

	month = str.substr(3,(str.length-14));

	switch(month){
		case "janvier": month=0;break;
		case "février": month=1;break;
		case "mars": month=2;break;
		case "avril": month=3;break;
		case "mai": month=4;break;
		case "juin": month=5;break;
		case "juillet": month=6;break;
		case "août": month=7;break;
		case "septembre": month=8;break;
		case "octobre": month=9;break;
		case "novembre": month=10;break;
		case "décembre": month=11;break;
		default: month=false;
	}

	if(month === false){
		return false;
	}else{
		ts = new Date(year, month, day , hour, minute);
		return ts.getTime()/1000;
	}
}

//au chargement de la page
$(document).ready(function(){

	$("#content > *:not(table)").css("display","none");

	// $(".event > li > a").removeAttr("href");

	$(".event > li > a").click(function(e){
		$("#info").remove();

		var calendarDay = $(this).parent().parent().prev().attr("id");
		calendarDay = calendarDay.substr(5,calendarDay.length).replace("-"," ");
		calendarBeginDay = strtotime(calendarDay+" 00:01");
		calendarEndDay = strtotime(calendarDay+" 23:59");

		txt = '';

		$("a[name="+strip_accents($(this).text())+"] ~ ul.alert > li > span").each(function(){
			var i = 0;
			var dateBeginEvent = 0;
			var dateEndEvent = 0;
			var timestampEndEvent = 0;
			var timestampBeginEvent = 0;
			var date1RE, date2RE;
			var reasonTxt = '';

			// traitement des dates sous forme "10 décembre 2009 10:50 à 10 décembre 2009 10:55"
			date1RE = new RegExp("\\d{2} [a-zéû]* \\d{4} \\d{2}:\\d{2}","g");
			while((dateEvent = date1RE.exec($(this).text())) != null){
				if(i == 1){
					dateEndEvent = dateEvent[0];
					timestampEndEvent = strtotime(dateEvent[0]);
				}else{
					dateBeginEvent = dateEvent[0];
					timestampBeginEvent = strtotime(dateEvent[0]);
				}
				i++;
			}

			// traitement des dates sous forme "10 décembre 2009 de 10:50 à 10:55"
			date2RE = new RegExp("\\d{2} [a-zéû]* \\d{4} de \\d{2}:\\d{2}","g");
			if(dateBeginEvent == 0){
				if((dateEvent = date2RE.exec($(this).text())) != null){
					dateBeginEvent = dateEvent[0].substr(0,(dateEvent[0].length-8))+dateEvent[0].substr(-5);
					timestampBeginEvent = strtotime(dateBeginEvent);

					dateEndEvent = dateBeginEvent.substr(0,(dateBeginEvent.length-5))+$(this).text().substr(-6);
					timestampEndEvent = strtotime(dateEndEvent.substr(0,(dateEndEvent.length-1)));
				}
			}

			noTimestampEndEvent = false;
			if(timestampEndEvent == 0){
				var now = new Date();
				timestampEndEvent = now.getTime()/1000;
				noTimestampEndEvent = true;
			}

			if((calendarBeginDay <= timestampEndEvent && calendarEndDay >= timestampEndEvent) ||
				(calendarBeginDay <= timestampBeginEvent && calendarEndDay >= timestampBeginEvent) ||
				(calendarBeginDay <= timestampBeginEvent && calendarEndDay >= timestampEndEvent)||
				(calendarBeginDay >= timestampBeginEvent && calendarBeginDay <= timestampEndEvent)){
				txt += "<li>";
				if(noTimestampEndEvent){
					txt += 'depuis le '+dateBeginEvent;
				}else{
					txt += 'du '+dateBeginEvent+' au '+dateEndEvent;
				}
				if($(this).next(".reason").length == 1){
					txt += '<br />Raison :<br />'+$(this).next(".reason").html().replace('<span class="bold">Raison :</span>','');
				}
				txt += "</li>";
			}

		});
		$(this).after("<div id=\"info\"><h3>Service interrompu :</h3><ul>"+txt+"</ul></div>");

		$("#info").css("display","none");
		$("#info").css("position","absolute");

		$("#info").css("top", e.pageY);
		$("#info").css("margin","1em");
		if(e.pageX > ($(window).width()*0.8)){
			$("#info").css("left","auto");
			$("#info").css("right","2em");
		}else{
			$("#info").css("left", e.pageX);
			$("#info").css("right","auto");
		}
		$("#info").css("background-color","#FAFAFA");
		$("#info").css("border","0.1em solid #000000");
		$("#info").css("z-index","200");
		$("#info ul li").css("list-style-image","none");

		$("#info").fadeIn("normal");

		$("#info").mouseenter(function(){
			$(this).bind("mouseleave click",function(){
				$(this).unbind();
				$(this).fadeOut("normal", function(){
					$(this).remove();
				});
			});
		});
	});
});
