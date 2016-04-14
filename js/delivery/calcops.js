//Self-invoking functions
$(function(){
  
	//Lorsque l'on sélectionne un increment id dans le menu déroulant, requete pour récupérer la commande correspondante et l'afficher
	$("#incrementid-dropdown").change(function(){
		window.location='index.php?module=remboursement&action=view&increment_id=' + this.value;
	    $("#warning-message").removeClass("hidden");
	});

	//Fonction calculant la différence entre le prix final et le prix initial des produits
	function price_difference(){

		// var limite_prix=5;
		// var diff_max_prix=0.5;
		// var diff_max_percent=0.10;

		$('input.prixfinal').each(function(){
			totalprice=$(this).parent().parent().children("td")[5];
			totalprice=parseFloat($(totalprice).text());
			if($(this).val()!=""){
				valeur=$(this).val().replace(/\,/g, '.');
				valeur=parseFloat(valeur);
				diff=totalprice-parseFloat(valeur);

				//Mise à jour de la colonne Diff Prix final pour la ligne correspondante
				$(this).parent().parent().children('.diffprixfinal').html(diff.toFixed(2));

				//Mise à jour de la colonne Diff Prix CGV pour la ligne correspondante
				// if(totalprice>limite_prix){
				// 	if(Math.abs(diff)>diff_max_prix){
				// 		$(this).parent().parent().children('.diffprixcgv').html(diff.toFixed(2));
				// 	}else{
				// 		$(this).parent().parent().children('.diffprixcgv').html(0);
				// 	}
				// }else{
				// 	if(Math.abs(diff/totalprice)>diff_max_percent){
				// 		$(this).parent().parent().children('.diffprixcgv').html(diff.toFixed(2));
				// 	}else{
				// 		$(this).parent().parent().children('.diffprixcgv').html(0);
				// 	}
				// }
			}
		});
	}

	//Mise à jour des totaux des colonnes Prix Final / Diff Prix final et Diff Prix CGV (prise en compte uniquement des données affichées)
	function updatetotals(){
		
		var sumPrixFinal=0;
		var sumDiffPrixFinal=0;
		var sumDiffPrixCGV=0;
		var table = $('#data_commande').DataTable();

		//prixfinal
		var column = table.column('#prixfinal',{page:'current'});
		 $('input.prixfinal').each(function(){
			sumPrixFinal+=parseFloat($(this).val().replace("",0));
		});
		 $( column.footer() ).html(sumPrixFinal.toFixed(2));

		 //diffprixfinal
		 var column = table.column('#diffprixfinal',{page:'current'});
		 $('td.diffprixfinal').each(function(){
			sumDiffPrixFinal+=parseFloat($(this).text());
		});
		 $( column.footer() ).html(sumDiffPrixFinal.toFixed(2));

		 //diff prix CGV
		 var column = table.column('#diffprixCGV',{page:'current'});
		 $('td.diffprixcgv').each(function(){
			sumDiffPrixCGV+=parseFloat($(this).text());
		});
		 $( column.footer() ).html(sumDiffPrixCGV.toFixed(2));
	}

	/*-----------------------------------------------*/
	/*      UPDATE EN FOCNTION D'EVENEMENTS          */
	/*-----------------------------------------------*/

	//Mise à jour lorsque qu'input prix final modifié 
	$('input').each(function() {
	   var elem = $(this);
	   // Save current value of element
	   elem.data('oldVal', elem.val());
	   // Look for changes in the value
	   elem.bind("propertychange change click keyup input paste", function(event){
	      // If value has changed...
	      if (elem.data('oldVal') != elem.val()) {
	       // Updated stored value
	       elem.data('oldVal', elem.val());
	       price_difference();
	       updatetotals();
	     }
	   });
	 });

	//Mise à jour lorsque clic sur le tableau
	$('#data_commande').on('click', 'tr', function () {
		updatetotals();
	});

	//Mise à jour lorsque dessin du tableau
	$('#data_commande').on('draw.dt', function () {
		updatetotals();
	} );

	//Screenshot function

	
	
});

//Fonction se lance lorsque la page a chargé
$(document).ready(function() {
	//Setup de la table de données
    var table=$('#data_commande').DataTable({
    	"pageLength": -1,
    	"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Tous"] ],
    	initComplete: function () {
            this.api().columns(['.sort']).every(function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $('#filter') )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            });
        },

        "footerCallback": function ( row, data, start, end, display ) {
        	var sumPrixTotal=0;
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

 			this.api().columns(['.float']).every(function (){
 				var index = this.index();
	 
	            // Total over this page
	            pageTotal = api
	                .column( index, { page: 'current'} )
	                .data()
	                .reduce( function (a, b) {
	                    return intVal(a) + intVal(b);
	                }, 0 );

	            pageTotal=pageTotal.toFixed(2);

	            // Update footer
	            $( api.column( index ).footer() ).html(pageTotal);
	        });

	        $('input.prixfinal').each(function(){
 				sumPrixTotal+=parseFloat($(this).val().replace("",0));
				$( api.column('#prixfinal').footer() ).html(sumPrixTotal);
 			});
        }
    } );

});