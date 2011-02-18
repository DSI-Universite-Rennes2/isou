function doOK() {

	var key = document.getElementById('fisou-key').value;
	var shownotification = document.getElementById('fisou-show-notification').checked;
	var delaysync = document.getElementById('fisou-delay-sync').value;
	var autosync = document.getElementById('fisou-auto-sync').checked;

	const preferencesService = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService ).getBranch("");

	// json key
	if(key.substr(0,1) == "#"){
		key = key.substr(1, key.length);
	}
	preferencesService.setCharPref("fisou.key",key);

	// show notification
	if(shownotification == true){
		preferencesService.setIntPref("fisou.shownotification",1);
	}else{
		preferencesService.setIntPref("fisou.shownotification",0);
	}

	// synchro delay
	if(isNaN(parseInt(delaysync)) || parseInt(delaysync) < 1){
		preferencesService.setIntPref("fisou.delaysync",1);
	}else{
		preferencesService.setIntPref("fisou.delaysync",delaysync);
	}

	// synchro automatique
	if(autosync == true){
		preferencesService.setIntPref("fisou.autosync",1);
	}else{
		preferencesService.setIntPref("fisou.autosync",0);
	}
}


function doCancel() {}


function init_prefs() {

	const preferencesService = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService ).getBranch("");

	// json key
	if(preferencesService.prefHasUserValue("fisou.key")){
		var key = preferencesService.getCharPref("fisou.key");
		document.getElementById("fisou-key").value = "#"+key;
	}else{
		preferencesService.setCharPref("fisou.key","#");
		document.getElementById("fisou-key").value = "#";
	}

	// show notification
	if(preferencesService.prefHasUserValue("fisou.shownotification")){
		var shownotification = preferencesService.getIntPref("fisou.shownotification");
		if(shownotification == 1){
			document.getElementById("fisou-show-notification").checked = true;
		}else{
			document.getElementById("fisou-show-notification").checked = false;
		}
	}else{
		preferencesService.setIntPref("fisou.shownotification",0);
		document.getElementById("fisou-show-notification").checked = false;
	}

	// synchro delay
	if(preferencesService.prefHasUserValue("fisou.delaysync")){
        var syncdelay = preferencesService.getIntPref("fisou.delaysync");
		document.getElementById('fisou-delay-sync').value = syncdelay;
    }else{
		preferencesService.setIntPref("fisou.delaysync",5);
		document.getElementById("fisou.delaysync").value = 5;
	}

	// synchro automatique
	if(preferencesService.prefHasUserValue("fisou.autosync")){
		var syncauto = preferencesService.getIntPref("fisou.autosync");
		if(syncauto == 1){
			document.getElementById("fisou-auto-sync").checked = true;
		}else{
			document.getElementById("fisou-auto-sync").checked = false;
		}
	}else{
		preferencesService.setIntPref("fisou.autosync",1);
		document.getElementById("fisou.autosync").checked = true;
	}


}
