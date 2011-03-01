function getColorFlag(state){
	var color = '';
	switch(parseInt(state)){
		case 1: color = "orange";break;
		case 2: color = "red";break;
		case 3: color = "blue";break;
		case 4: color = "white";break;
	}
	return color;
}

function showNotification(title, msg) {
	var icon = "chrome://fisou/content/images/favicon_32x32.png";
	var notification = Components.classes['@mozilla.org/embedcomp/window-watcher;1']
						.getService(Components.interfaces.nsIWindowWatcher)
						.openWindow(null, 'chrome://global/content/alerts/alert.xul',
							'_blank', 'chrome,titlebar=no,popup=yes', null);
	notification.arguments = [icon, title, msg, false, ''];
}

var Fisou = {
	mainTimeout : '',
	openURL : function() {
		tab = gBrowser.addTab("https://services.univ-rennes2.fr/isou");
		gBrowser.selectedTab = tab;
	},
	togglePopup : function(){
		var moretip = document.getElementById("fisou-popup");
		if(moretip.state == "open"){
			moretip.hidePopup();
		}else{
			var contextmenu = document.getElementById("fisou-context");
			if(contextmenu.state == "closed"){
				var statusBar = document.getElementById("fisou-status");
				moretip.openPopup(statusBar, "before_end");
			}
		}
	},
	start : function(){
		menu4 = document.getElementById('menu-4');
		if(this.mainTimeout == ''){
			// on demarre fisou
			this.refreshData();
			menu4.setAttribute('label','Arrêter');
			statusicon = document.getElementById('fisou-status-icon');
			statusicon.setAttribute('class','fisou-status-icon-on');
		}else{
			// on stop fisou
			clearTimeout(this.mainTimeout);
 			this.mainTimeout = '';
			menu4.setAttribute('label','Démarrer');
			statusicon = document.getElementById('fisou-status-icon');
			statusicon.setAttribute('class','fisou-status-icon-off');
			var statusLabel = document.getElementById("fisou-status-label");
			statusLabel.setAttribute("value","");
		}
	},
	refreshData: function(){
		var httpRequest = null;
		var jsonUrl = "https://services.univ-rennes2.fr/isou/isou.json";
		// var jsonUrl = "http://localhost/isou_package/sources/isou.json";

		const preferencesService = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService ).getBranch("");
		if(preferencesService.prefHasUserValue("fisou.key")){
			if(preferencesService.getCharPref("fisou.key") != ""){
				var jsonUrl = jsonUrl+"?key="+preferencesService.getCharPref("fisou.key");
			}
		}

		httpRequest = new XMLHttpRequest();
		httpRequest.open("GET", jsonUrl, true);
		httpRequest.onreadystatechange=function(){
			if (httpRequest.readyState == 4){
				if (httpRequest.status == 200){
					// vérifie que l'icone est verte
					var fisoustatusicon = document.getElementById("fisou-status-icon");
					fisoustatusicon.setAttribute("class","fisou-status-icon-on");

					// vide la tooltip
					var tooltip = document.getElementById("fisou-popup");
					while (tooltip.firstChild) {
					  	tooltip.removeChild(tooltip.firstChild);
					}

					var statusLabel = document.getElementById("fisou-status-label");
					var lastScan = new Date();

					var json = JSON.parse(httpRequest.responseText);

					var icount = json.fisou.services.length;
					if(icount == 0){
						fisoustatusicon.setAttribute("class","fisou-status-icon-on");
						fisoustatusicon.style.display = "block";
						statusLabel.setAttribute("value","");

						description = document.createElement("description");
						description.setAttribute("value","Aucun service perturbé");
						tooltip.appendChild(description);
					}else{
						// masquer l'icone
						fisoustatusicon.style.display = "none";

						// change le libellé dans la barre de status
						if(json.fisou.services.length == 1){
							statusLabel.setAttribute("value","1 service perturbé");
						}else{
							statusLabel.setAttribute("value",json.fisou.services.length+" services perturbés");
						}

						// ajoute les nouveaux évènements dans la tooltip
						var description;
						var newEvent = false;

						if(preferencesService.prefHasUserValue("fisou.delaysync")){
							var delay = preferencesService.getIntPref("fisou.delaysync");
						}else{
							var delay = 5;
						}

						var servicesNotification = "";
						var servicesNotificationCount = 0;

						// parcours chaque service
						for(var i=0;i<icount;i++){
							// ajoute le nom du service à la tooltip
							description = document.createElement("description");
							description.setAttribute("value",json.fisou.services[i].name);
							description.setAttribute("style","padding-left:20px;background-repeat: no-repeat;background-image:url('chrome://fisou/content/images/flag_"+getColorFlag(json.fisou.services[i].state)+".gif');");

							// ajout une ou des raisons sous le nom du service
							if(tooltip.appendChild(description)){
								var jcount = json.fisou.services[i].description.length;
								for(j=0;j<jcount;j++){
									description = document.createElement("description");
									description.setAttribute("value",json.fisou.services[i].description[j]);
									description.setAttribute("style","padding-left:30px;font-size:0.8em;");
									tooltip.appendChild(description);
								}
							}

							if((json.fisou.services[i].date*1000+delay*60000) >= lastScan.getTime()){
								servicesNotificationCount++;
								servicesNotification += json.fisou.services[i].name+", ";
							}
						}

						// affiche une notification si l'utilisateur l'a demandé
						if(preferencesService.prefHasUserValue("fisou.shownotification")){
							if(preferencesService.getIntPref("fisou.shownotification") == 1 && servicesNotificationCount > 0){
								servicesNotification = servicesNotification.substr(0, servicesNotification.length-2);
								// tronque la notification pour qu'elle ne soit pas trop longue
								if(servicesNotification.length > 75){
									servicesNotification = servicesNotification.substr(0,75);
									if(servicesNotification.substr(servicesNotification.length-1) === ","){
										servicesNotification = servicesNotification.substr(0, servicesNotification.length-1);
									}
									servicesNotification = servicesNotification+"...";
								}
								if(servicesNotificationCount == 1){
									showNotification("Nouveau service perturbé", servicesNotification);
								}else{
									showNotification("Nouveaux services perturbés", servicesNotification);
								}
							}
						}

						// ajout de la date de scan
						// var lastScan = new Date();
						lastScan = "Dernière MAJ : "+lastScan.toLocaleTimeString();
						description = document.createElement("description");
						description.setAttribute("value","");
						tooltip.appendChild(description);
						description = document.createElement("description");
						description.setAttribute("value",lastScan);
						description.setAttribute("style","font-size:0.8em;color: gray;text-decoration:underline;");
						tooltip.appendChild(description);
					}
				}else{
 					// cas où isou ne peut être contacté
					// vide la tooltip
					var tooltip = document.getElementById("fisou-popup");
					while (tooltip.firstChild) {
						tooltip.removeChild(tooltip.firstChild);
					}

					// ajoute la description du popup
					description = document.createElement("description");
					description.setAttribute("value","Le serveur ISOU est indisponible.");
					tooltip.appendChild(description);

					// ajout de la date de scan
					var lastScan = new Date();
					lastScan = "Dernière MAJ : "+lastScan.toLocaleTimeString();
					description = document.createElement("description");
					description.setAttribute("value","");
					tooltip.appendChild(description);
					description = document.createElement("description");
					description.setAttribute("value",lastScan);
					description.setAttribute("style","font-size:0.8em;color: gray;text-decoration:underline;");
					tooltip.appendChild(description);

					var fisoustatusicon = document.getElementById("fisou-status-icon");
					fisoustatusicon.setAttribute("class","fisou-status-icon-unavailable");
					fisoustatusicon.style.display = "block";

					// supprime l'éventuel label indiquant un nombre de service interrompu
					var statusLabel = document.getElementById("fisou-status-label");
					statusLabel.setAttribute("value","");
				}
			}
		}
		httpRequest.send(null);

		// toutes les X mins, on remet à jour les données
		if(preferencesService.prefHasUserValue("fisou.delaysync")){
			var delay = preferencesService.getIntPref("fisou.delaysync");
		}else{
			var delay = 5;
		}
		clearTimeout(this.mainTimeout);
		this.mainTimeout = setTimeout("Fisou.refreshData()", delay*60000);
	}

}

function init_fisou(){
	const preferencesService = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService ).getBranch("");

	// version 0.4
	if(!preferencesService.prefHasUserValue("fisou.version")){
		preferencesService.setCharPref("fisou.version", "0.4");
		preferencesService.setIntPref("fisou.shownotification", 1);
		preferencesService.setIntPref("fisou.delaysync", 1);
	}

	// version 0.4.1
	// bug issue de la v0.4
	if(preferencesService.prefHasUserValue("fisou.version")){
		if(preferencesService.getPrefType("fisou.version") == preferencesService.PREF_INT){
			preferencesService.deleteBranch("fisou.version");
			preferencesService.setCharPref("fisou.version","0.4.1");
		}
	}

	// bug issue de la v0.2
	if(preferencesService.prefHasUserValue("fisou.key")){
		if(preferencesService.getPrefType("fisou.key") == preferencesService.PREF_INT){
			preferencesService.deleteBranch("fisou.key");
			preferencesService.setCharPref("fisou.key","");
		}
	}

	if(preferencesService.prefHasUserValue("fisou.autosync")){
		var syncauto = preferencesService.getIntPref("fisou.autosync");
		var statusicon = document.getElementById("fisou-status-icon");
		if(syncauto == 1){
			// affiche l'icone
			statusicon.setAttribute("class","fisou-status-icon-on");
			statusicon.setAttribute("style","display:inline");

			// rafraichit les donnees
			Fisou.refreshData();

			// change le menu contextuel
			menu4 = document.getElementById('menu-4');
			menu4.setAttribute('label','Arrêter');
		}else{
			// afficher l'icone rouge
			statusicon.setAttribute("style","display:inline");
		}
	}else{
		// récupère les préférences
		preferencesService.setCharPref("fisou.key","");
		preferencesService.setIntPref("fisou.shownotification", 1);
		preferencesService.setIntPref("fisou.delaysync", 1);
		preferencesService.setIntPref("fisou.autosync", 1);

		// affiche l'icone
		statusicon = document.getElementById("fisou-status-icon");
		statusicon.setAttribute("class","fisou-status-icon-on");
		statusicon.setAttribute("style","display:inline");
		var statusbar = document.getElementById("fisou-status");
		statusbar.appendChild(statusicon);

		// rafraichit les donnees
		Fisou.refreshData();

		// change le menu contextuel
		menu4 = document.getElementById('menu-4');
		menu4.setAttribute('label','Arrêter');
	}

	var statusLabel = document.getElementById("fisou-status-label");
	statusLabel.setAttribute("value","");

	return true;
}

// waiting 5s for dom loading
setTimeout("init_fisou()", 5000);
