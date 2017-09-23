function setAgeCookie(cname, cvalue, exdays){
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	// converts the string to UTC time
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getAgeCookie(cname){
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++){
		var c = ca[i];
		// substring(1) removes the blank space in charAt(0)
		while (c.charAt(0)==' ') c = c.substring(1);
		//indexOf(name) 0 indicates we have found the cookie
		if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	}
	return "";
}