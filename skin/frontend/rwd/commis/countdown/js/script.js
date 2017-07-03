$j(function(){
	
	var note = $j('#note'),
		
	//Get current date
		currentdate=new Date();
		dd=currentdate.getDate();
		mm=currentdate.getMonth()+1;
		yy=currentdate.getFullYear();
		hh=currentdate.getHours();
		mn=currentdate.getMinutes();
		ss=currentdate.getSeconds();
		currentdate=new Date(yy,mm-1,dd,hh,mn,ss);

	//Set date to 3pm
		ts = new Date(yy, mm-1, dd,15,0,0);
	
	// var holidays=[
	// 		new Date(yy,0,1,5,0,0).getTime(), //Jour de l'an
	// 		new Date(yy,3,6,5,0,0).getTime(), //Lundi de Pâques
	// 		new Date(yy,4,1,5,0,0).getTime(), //Fête du travail
	// 		new Date(yy,4,8,5,0,0).getTime(), //Armistice 1945
	// 		new Date(yy,4,14,5,0,0).getTime(), //Jeudi de l'Ascencion
	// 		new Date(yy,4,25,5,0,0).getTime(), //Lundi de Pentecôte
	// 		new Date(yy,6,14,5,0,0).getTime(), //Fete nationale
	// 		new Date(yy,10,1,5,0,0).getTime(), //Toussaint
	// 		new Date(yy,10,11,5,0,0).getTime(), //Armistice 1918
	// 		new Date(yy,11,25,5,0,0).getTime(), //Noel
	// 	];

		var holidays=[
			"1-1",   //Jour de l'an
			"6-4",   //Lundi de Pâques
			"1-5",   //Fête du travail
			"8-5",   //Armistice 1945
			"14-5",  //Jeudi de l'Ascencion
			"25-5",  //Lundi de Pentecôte
			"14-7",  //Fete nationale
			"1-11",  //Toussaint
			"11-11", //Armistice 1918
			"25-12"  //Noel
		];

	var testdate=new Date(yy,mm-1,dd);

	if(currentdate.getDay()==1){
		//Affichage le lundi
		$j('#countdown').html('<div class="countdown-text">Prochaine livraison demain, commandez dès maintenant!</div>');
	}else if((currentdate.getDay()==5 && currentdate > ts) || currentdate.getDay()==6 || currentdate.getDay()==0){
		//Affichage du vendredi 15h01 au dimanche minuit
		$j('#countdown').html('<div class="countdown-text">Prochaine livraison mardi prochain, commandez dès maintenant!</div>');
	}else if(holidays.indexOf(currentdate.getDate()+'-'+(currentdate.getMonth()+1))!=-1){
		//Affichage jour férié
		if(currentdate.getDay()+1==6){
			//si le lendemain est un samedi
			$j('#countdown').html('<div class="countdown-text">Prochaine livraison mardi prochain, commandez dès maintenant!</div>');
		}else{
			//autre jour
			$j('#countdown').html('<div class="countdown-text">Prochaine livraison demain, commandez dès maintenant!</div>');
		}
	}else{

		if(currentdate.getTime() > ts.getTime()){
			ts.setDate(ts.getDate()+1);
		}

		$j('#countdown').countdown({
			timestamp	: ts,
			// callback	: function(days, hours, minutes, seconds){
				
			// 	var message = "";
				
			// 	message += days + " day" + ( days==1 ? '':'s' ) + ", ";
			// 	message += hours + " hour" + ( hours==1 ? '':'s' ) + ", ";
			// 	message += minutes + " minute" + ( minutes==1 ? '':'s' ) + " and ";
			// 	message += seconds + " second" + ( seconds==1 ? '':'s' ) + " <br />";
				
			// 	if(newYear){
			// 		message += "left until the new year!";
			// 	}
			// 	else {
			// 		message += "left to 10 days from now!";
			// 	}
				
			// 	note.html(message);
			// }
		});
	}
	
});