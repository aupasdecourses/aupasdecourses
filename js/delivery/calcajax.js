/*

headers: { "cache-control": "no-cache" },

Fix for Safari iOS6 ajax cache problem: http://stackoverflow.com/questions/12506897/is-safari-on-ios-6-caching-ajax-results
*/



//Init message div

function init_msg(caller){
	//diplay loading and erase message
	$(caller).find('img.modal').css({'display':'inline'});
	$(caller).find('span.ajax-success').css({'display':'none'});
	$(caller).find('span.ajax-error').css({'display':'none'});
    $(caller).find('span.ajax-msg').css({'display':'none','color':'auto'});
    return false;
}


//save Ticket function
	function submitFormTicket(caller) {
	    var order_id=$("#incrementid-dropdown").val();
		var fd = new FormData(document.getElementById("formticket"));
		fd.append("orderid", order_id);
		fd.append("type", "ticket");
		
		//diplay loading and erase message
		init_msg(caller);

	    $.ajax({
	      url: "../batignolles/deliveryfront/index/processajax",
	      type: "POST",
	      data: fd,
	      enctype: 'multipart/form-data',
	      processData: false,  // tell jQuery not to process the data
	      contentType: false,   // tell jQuery not to set contentType
	      cache:false,
	      headers: { "cache-control": "no-cache" },
	      success: function (ret) {
	            if(ret){
	            	msg="Ticket créé/modifié";
		            $(caller).find('img.modal').css({'display':'none'});
		            $(caller).find('span.ajax-success').css({'display':'inline'});
		            $(caller).find('span.ajax-msg').css({'display':'inline','color':'green'}).html(msg);
		            d=new Date();
		            $("#uploaded_ticket").attr('src',$("#uploaded_ticket").attr('src')+'?timestamp='+d.getTime());
		            $("#uploaded_ticket_div").load(document.URL+" #uploaded_ticket_div");
		        }else{
		        	msg="Ticket existant non modifié ou fichier invalide";
		        	$(caller).find('img.modal').css({'display':'none'});
	            	$(caller).find('span.ajax-error').css({'display':'inline'});
	            	$(caller).find('span.ajax-msg').css({'display':'inline','color':'red'}).html(msg);
		        }
	        },
	        error: function (msg) {
	            alert(msg);
	        }
	    });
	    return false;
	}

	//save database and screenshot
	function datamergedbbsshot(caller){
		
		//shared params
		var supscreenshot=$('#supsubmit').is(":checked");
		var order_id=$("#incrementid-dropdown").val();
		
		//diplay loading and erase message
		$(caller).find('img.modal').css({'display':'inline'});
		$(caller).find('span.ajax-success').css({'display':'none'});
		$(caller).find('span.ajax-error').css({'display':'none'});
	    $(caller).find('span.ajax-msg').css({'display':'none','color':'auto'}).html('');

		//dataddb params

		var TableData=new Array();
		$('#data_commande tbody tr').each(function(row, tr){

			//Comment
			var comment=$(this).find('td.comment input').val();
			if(comment!=""){
				comment=$(this).find('td.name a').text()+': '+comment;
			}

	        TableData[row]={
	        	"order_item_id":$(this).attr("id"),
		        "commercant" :$(this).find('td.commercant').html(),
		        "item_name" :$(this).find('td.name a').text(),
		        "prix_initial" :$(this).find('td.prixinitial').html(),
		        "prix_final" :$(this).find('td.prixfinal input').val(),
		        "diffprixfinal" :$(this).find('td.diffprixfinal').html(),
		        "in_ticket" :+$(this).find('td.in-ticket input').is(":checked"),
		        "comment" :comment,
		    }
		});

		//datadbb ajax request
	    $.ajax({
	        url: '../batignolles/deliveryfront/index/processajax',
			type: 'post',
			cache:false,
			headers: { "cache-control": "no-cache" },
			data: {
				data:TableData,
				supscreenshot:supscreenshot,
				type:'ddb',
				orderid:order_id,
			},
	        success: function (ret) {
	        	if(ret){
	        		msg="Création/Mise à jour bdd terminée. ";
		             $(caller).find('img.modal').css({'display':'none'});
		            $(caller).find('span.ajax-success').css({'display':'inline'});
		            $(caller).find('span.ajax-msg').css({'display':'inline','color':'green'}).append(msg);
	            }else{
	            	msg="Pas de mise à jour/création de la bdd réalisée. ";
	            	$(caller).find('img.modal').css({'display':'none'});
		            $(caller).find('span.ajax-error').css({'display':'inline'});
		            $(caller).find('span.ajax-msg').css({'display':'inline','color':'red'}).append(msg);
	            }
	        },
	        error: function (msg) {
	            alert(msg);
	        }
	    });

	    //screenshot ajax request

	    //remove scrolling to top
	    var scrollPos = document.body.scrollTop;
		html2canvas($('#data_commande'), {
			onrendered: function(canvas) {
			var myImage = canvas.toDataURL("image/jpg");
			Pic = myImage.replace(/^data:image\/(png|jpg);base64,/, "");
		    // Sending the image data to Server
		    $.ajax({
		        url: '../batignolles/deliveryfront/index/processajax',
				type: 'post',
				cache:false,
				headers: { "cache-control": "no-cache" },
		        data: {
		        	type:'screenshot',
		            orderid:order_id,
		            image: Pic,
		            supscreenshot:supscreenshot
		        },
		        success: function (ret) {
		        	if(ret){
		        		msg="Image enregistrée, modifiée. ";
			            $(caller).find('img.modal').css({'display':'none'});
	        			$(caller).find('span.ajax-success').css({'display':'inline'});
	        			$(caller).find('span.ajax-msg').css({'display':'inline'}).css({'color':'green'}).append(msg);
			            d=new Date();
	        			$("#uploaded_remb").attr('src',$("#uploaded_remb").attr('src')+'?timestamp='+d.getTime());
	        			$("#uploaded_remb_div").load(document.URL+" #uploaded_remb_div");	            	
		            }else{
		            	msg="Image existante non modifiée. ";
		            	$(caller).find('img.modal').css({'display':'none'});
			            $(caller).find('span.ajax-error').css({'display':'inline'});
			            $(caller).find('span.ajax-msg').css({'display':'inline'}).css({'color':'red'}).append(msg);
		            }
		        },
		        error: function (msg) {
		            alert(msg);
		        }
		    });
		    window.scrollTo(0,scrollPos);
			}
		});

		return false;	
	};

	//save Comment function
	function submitCommentTicket(caller) {
		var comment_remboursement=$('#comment_remboursement').val();
		var comment_commande=$('#comment_commande').val();
		var comment_ticket=$('#comment_ticket').val();
		var comment_fraislivraison=$('#comment_fraislivraison').val();
		var order_id=$("#incrementid-dropdown").val();
		var supcomments=$('#supcomments').is(":checked");
		
		//diplay loading and erase message
		$(caller).find('img.modal').css({'display':'inline'});
		$(caller).find('span.ajax-success').css({'display':'none'});
		$(caller).find('span.ajax-error').css({'display':'none'});
	    $(caller).find('span.ajax-msg').css({'display':'none','color':'auto'});

	    $.ajax({
	        url: '../batignolles/deliveryfront/index/processajax',
			type: 'post',
			cache:false,
			headers: { "cache-control": "no-cache" },
	        data: {
	            type:'comment',
	            orderid:order_id,
	            commentremboursement:comment_remboursement,
	            commentcommande:comment_commande,
	            commentticket:comment_ticket,
	            commentfraislivraison:comment_fraislivraison,
	            supcomments:supcomments
	        },
	        success: function (ret) {
	        	if(ret){
		            msg="Commentaires mis à jour."
		            $(caller).find('img.modal').css({'display':'none'});
	    			$(caller).find('span.ajax-success').css({'display':'inline'});
	    			$(caller).find('span.ajax-msg').css({'display':'inline','color':'green'}).html(msg);
	            	$("#formcomments").load(document.URL+" #formcomments");
	            }else{
	            	msg="Commentaires existants non mis à jour."
	            	$(caller).find('img.modal').css({'display':'none'});
	            	$(caller).find('span.ajax-error').css({'display':'inline'});
	            	$(caller).find('span.ajax-msg').css({'display':'inline'}).css({'color':'red'}).html(msg);
	            }
	        },
	        error: function (msg) {
	       		alert(msg);
	        }
	    });
	    return false;
	};

	//save creditmemo
	function creditmemo(caller) {
		var order_id=$("#incrementid-dropdown").val();
		var confirmcredit=$("#confirmcredit").is(":checked");
		if(confirmcredit){
			var TableData=new Array();
			var TotalData=new Array();
			$('#data_commande tbody tr').each(function(row, tr){
		        TableData[row]={
		        	"order_item_id":$(this).attr("id"),
			        "commercant" :$(this).find('td.commercant').html(),
			        "item_name" :$(this).find('td.name a').text(),
			        //"qty" :$(this).find('td.qty').html(),
			        "prix_initial" :$(this).find('td.prixinitial').html(),
			        "prix_final" :$(this).find('td.prixfinal input').val(),
			        "diffprixfinal" :$(this).find('td.diffprixfinal').html(),
			        "in_ticket" :+$(this).find('td.in-ticket input').is(":checked"),
			        "comment" :$(this).find('td.comment input').val(),
			    }
			});

			var totalrow=$('#data_commande tfoot tr');
			TotalData={
				'total_qty':totalrow.find('th#total_qty').html(),
				'total_commande':totalrow.find('th#total_commande').html(),
				'total_ticket':totalrow.find('th#total_ticket').html(),
				'total_remboursement':totalrow.find('th#total_remboursement').html(),
			};

			//diplay loading and erase message
			$(caller).find('img.modal').css({'display':'inline'});
			$(caller).find('span.ajax-success').css({'display':'none'});
			$(caller).find('span.ajax-error').css({'display':'none'});
		    $(caller).find('span.ajax-msg').css({'display':'none'});

			$(caller).find('img.modal').css({'display':'inline'});
		    $.ajax({
		        url: '../batignolles/deliveryfront/credit/processcredit',
				type: 'post',
				cache:false,
				headers: { "cache-control": "no-cache" },
				data: {
					data:TableData,
					total:TotalData,
					order_id:order_id,
				},
		        success: function (msg) {
		            $(caller).find('img.modal').css({'display':'none'});
		            $(caller).find('span.ajax-success').css({'display':'inline'});
		            //msg=$.parseJSON(msg);
		            $(caller).find('span.ajax-msg').css({'display':'inline'}).html(msg);
	        		//$("#credit-memo-list").effect("highlight", {}, 1000).load(document.URL+" #credit-memo-list");
	        		$("#formcomments").effect("highlight", {}, 1000).load(document.URL+" #formcomments");
	        		$("#invoice-credit-memo").load(document.URL+" #invoice-credit-memo");
		        },
		        error: function (msg) {
		            $(caller).find('img.modal').css({'display':'none'});
		            $(caller).find('span.ajax-error').css({'display':'inline'});
		            $(caller).find('span.ajax-msg').css({'display':'inline'}).html(msg);
		        }
		    });
		 }else{
		 	$(caller).find('#confirmcredit').parent().effect("highlight", {}, 1000);
		 }
		return false;	
	};

	//Close order
	function submitCloseOrder(caller){
		var order_id=$("#incrementid-dropdown").val();
		var confirmclose=$('#confirmclose').is(":checked");

		//diplay loading and erase message
		$(caller).find('img.modal').css({'display':'inline'});
		$(caller).find('span.ajax-success').css({'display':'none'});
		$(caller).find('span.ajax-error').css({'display':'none'});
	    $(caller).find('span.ajax-msg').css({'display':'none'});

		if(confirmclose){
			$.ajax({
		      url: "../batignolles/deliveryfront/index/processajax",
		      type: "POST",
		      cache:false,
		      headers: { "cache-control": "no-cache" },
		      data: {
		      	type:'close',
		      	orderid:order_id,
		      },
		      success: function (ret) {
		            if(ret){
		            	msg="Commande cloturée";
			            $(caller).find('img.modal').css({'display':'none'});
			            $(caller).find('span.ajax-success').css({'display':'inline'});
			            $(caller).find('span.ajax-msg').css({'display':'inline','color':'green'}).html(msg);
			            $("#cloture-commande").load(document.URL+" #cloture-commande");
			            $("#order-status").load(document.URL+" #order-status");
			        }else{
			        	msg="Commande non cloturée";
			        	$(caller).find('img.modal').css({'display':'none'});
		            	$(caller).find('span.ajax-error').css({'display':'inline'});
		            	$(caller).find('span.ajax-msg').css({'display':'inline','color':'red'}).html(msg);
			        }
		        },
		        error: function (msg) {
		            alert(msg);
		        }
		    });
		}else{
			$(caller).find('#confirmclose').parent().effect("highlight", {}, 1000);
		}
	    return false;
	}