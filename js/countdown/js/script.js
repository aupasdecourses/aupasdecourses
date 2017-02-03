$j(function(){
	
	var note = $j('#note'),
		
	//Get current date
		currentdate=new Date();
		dd=currentdate.getDate();
		mm=currentdate.getMonth();
		yy=currentdate.getFullYear();
		hh=currentdate.getHours();
		mn=currentdate.getMinutes();
		ss=currentdate.getSeconds();
		currentdate=new Date(yy,mm,dd,hh,mn,ss);

	//Set date to 3pm
		ts = new Date(yy, mm, dd,15,0,0);

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

	var testdate=new Date(yy,mm,dd+1,10,0,0);

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