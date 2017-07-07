<?php 

$block_data=[
	[
		'title'=>"Avantages",
		'identifier'=>"block_advantages",
		'content'=>'<div class="block-advantages row text-center">
			<div class="col-md-3">
			<img src="{{media url="wysiwyg/ico-products.jpg"}}" alt="" />
			<p>Belle sélection de produits, au même prix qu\'en boutique</p>
			</div>
			<div class="col-md-3">
			<img src="{{media url="wysiwyg/ico-delivery.jpg"}}" alt="" />
			<p>Livraison en soirée du mardi au vendredi</p>
			</div>
			<div class="col-md-3">
			<img src="{{media url="wysiwyg/ico-quality.jpg"}}" alt="" />
			<p>Qualité et fraîcheur de la commande à la livraison</p>
			</div>
			<div class="col-md-3">
			<img src="{{media url="wysiwyg/ico-time.jpg"}}" alt="" />
			<p>Gagnez du temps et mangez bon</p>
			</div>
			</div>',
	],
	[
		'title'=>"Sponsor",
		'identifier'=>"block_sponsor",
		'content'=>'<div class="block-sponsor">
			<h2>Parrainez vos amis et bénéficiez de coupons de réduction</h2>
			<div class="row">
			<div class="col-md-5">
			<img src="{{media url="wysiwyg/sponsor.png"}}" alt="" />
			</div>
			<div class="col-md-7">
			<p>Dès votre première commande vous recevrez un code parrain unique que vous pourrez communiquer à vos filleuls.</p>
			</div>
			</div>
			</div>',
	],
];

//if you want one block for each store view, get the store collection
//$stores = Mage::getModel('core/store')->getCollection()->addFieldToFilter('store_id', array('gt'=>0))->getAllIds();
//if you want one general block for all the store viwes, uncomment the line below
$stores = array(0);
foreach ($stores as $store){
	foreach($block_data as $data){
	    $block = Mage::getModel('cms/block');
	    $block->setTitle($data['title']);
	    $block->setIdentifier($data['identifier']);
	    $block->setStores(array($store));
	    $block->setIsActive(1);
	    $block->setContent($data['content']);
	    $block->save();
	}
}