<?php 

$cmsPageData=[
	[
		'title'=>"Landing Page Commerçants",
		'root_template' => 'landingpage_commercants',
	    'meta_keywords' => '',
	    'meta_description' => '',
	    'identifier' => 'commercants',
	    'content_heading' => 'Au Pas De Courses vous livre tous vos commerçants de Paris dans la journée.',
	    'stores' => array(2),
	    'content' => "",
	],
	[
		'title'=>"Landing Page Boucher",
		'root_template' => 'landingpage_boucher',
	    'meta_keywords' => '',
	    'meta_description' => '',
	    'identifier' => 'boucher',
	    'content_heading' => 'Au Pas De Courses vous livre tous les bouchers de Paris dans la journée.',
	    'stores' => array(2),
	    'content' => "",
	],
];

foreach($cmsPageData as $data){
    Mage::getModel('cms/page')->setData($data)->save();
}
