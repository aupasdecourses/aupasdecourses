<script src="libs/js/jspdf.debug.js"></script>
<script src="libs/js/jspdf.plugin.autotable.js"></script>
<script>

//generate column and JSON data from table

	var columns = [
		{title: "Produit", key: "produit"},
		{title: "SKU", key: "sku"},
		{title: "Référence", key: "reference"},
		{title: "Prix Unitaire", key: "prix_unitaire"},
		{title: "Quantité Facturée", key: "quantite_facturee"},
		{title: "Prix Total", key: "prix_total"},
		{title: "Commentaires", key: "commentaires"}
	];

	var JSONObject = {};
	$('.order-box').each(function(index){
		//inos clients
		var infos_client=[];
			infos_client.push({
				increment_id:$(this).children('.infos_client').children('.order-ref').children('span').text(),
				name:$(this).children('.infos_client').children('dl').children('dd.name').text(),
			    delivery_date:$(this).children('.infos_client').children('dl').children('dd.delivery_date').text(),
				delivery_time:$(this).children('.infos_client').children('dl').children('dd.delivery_time').text()
			});
		
		//items
		var items=[];
		var elt=$(this).children(".table-responsive").children('#infocommande').children('tbody').children("tr.item");
		
		elt.each(function(i,t) {
			var item={};
			$.each(columns,function(i,k){
				item[k.key] = elt.children('td.'+k.key).text();
			});
			items.push(item);
		});

		//Create JSON
		JSONObject[infos_client[0].increment_id]={infos_client:infos_client,items:items};
	});

	JSONString=JSON.stringify(JSONObject);
	console.log(JSONString);
</script>