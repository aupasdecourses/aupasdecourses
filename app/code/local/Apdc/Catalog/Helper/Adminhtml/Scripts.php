<?php

class Apdc_Catalog_Helper_Adminhtml_Scripts extends Mage_Catalog_Helper_Data
{

	public function getSqlRequests(){
		return [
            'without_sku' => [
                'label' => 'Produits sans SKU',
                'hint' => 'Trouver produits sans SKU',
                'sql' => 'SELECT COUNT(sku), sku FROM catalog_product_entity GROUP BY sku HAVING COUNT(sku) >1;',
            ],
            'doublon_sku' => [
                'label' => 'Doublons SKU',
                'hint' => 'Trouver les doublons de SKU (normalement impossible)',
                'sql' => 'SELECT COUNT(sku) , sku FROM catalog_product_entity GROUP BY sku HAVING COUNT(sku) >1;',
            ],
            'illegal_characters_active' => [
                'label' => 'Mauvais caractères in SKU',
                'hint' => 'Trouver les SKU avec des caractères non ASCII, des espaces ou des retours à la ligne ou pas le bon format. Uniquement les produits activés',
                'sql' => "SELECT table1.entity_id, table1.sku FROM apdcdev.catalog_product_entity AS table1 LEFT JOIN apdcdev.catalog_product_entity_int AS table2 ON (table1.entity_id = table2.entity_id) LEFT JOIN eav_attribute AS table3 ON (table2.attribute_id = table3.attribute_id) WHERE (attribute_code='status' AND value=1 AND (sku <> CONVERT(sku USING ASCII) OR sku LIKE '% %' OR sku REGEXP '\r\n' OR sku REGEXP '\n' OR sku NOT REGEXP '[[:<:]]([[:alnum:]]{1,}-[[:alnum:]]{1,}-[[:alnum:]]{1,})[[:>:]]'));",
             ],
            'no_images' => [
                'label' => 'Images manquantes',
                'hint' => 'Trouver les produits activés n’ayant pas d’image ou non sélectionné',
                'sql' => "SELECT a.entity_id, a.sku FROM  catalog_product_entity AS a INNER JOIN  eav_attribute AS attribute ON attribute.attribute_code =  'image' AND attribute.frontend_input =  'media_image' LEFT JOIN  catalog_product_entity_varchar AS b ON a.entity_id = b.entity_id AND b.attribute_id = attribute.attribute_id INNER JOIN  eav_attribute AS attribute2 ON attribute2.attribute_code =  'status' LEFT JOIN  catalog_product_entity_int AS b2 ON a.entity_id = b2.entity_id AND b2.attribute_id = attribute2.attribute_id WHERE b2.value=1 AND type_id = 'simple' AND (b.value =  '' OR b.value IS NULL  OR b.value =  'no_selection');",
             ],
            'prices_with_euros' => [
                'label' => 'Mauvais format prix',
                'hint' => 'Trouver les produits avec des “€” dans le prix public',
                'sql' => "SELECT a.entity_id, a.sku, b.value FROM  catalog_product_entity AS a  INNER JOIN  eav_attribute AS attribute ON attribute.attribute_code =  'prix_public' LEFT JOIN  catalog_product_entity_varchar AS b ON a.entity_id = b.entity_id AND b.attribute_id = attribute.attribute_id WHERE b.value LIKE '%€%';",
             ],
            'no_ref_code' => [
                'label' => 'Référentiel code manquant',
                'hint' => 'Trouver les produits activés qui n’ont pas de code référentiel APDC (avec noms du commerçants)',
                'sql' => "SELECT a.entity_id, a.sku, o.value FROM  catalog_product_entity AS a INNER JOIN  eav_attribute AS attribute ON attribute.attribute_code =  'code_ref_apdc' LEFT JOIN  catalog_product_entity_varchar AS b ON a.entity_id = b.entity_id AND b.attribute_id = attribute.attribute_id INNER JOIN  eav_attribute AS attribute2 ON attribute2.attribute_code =  'status' LEFT JOIN  catalog_product_entity_int AS b2 ON a.entity_id = b2.entity_id AND b2.attribute_id = attribute2.attribute_id INNER JOIN  eav_attribute AS attribute3 ON attribute3.attribute_code =  'commercant' LEFT JOIN  catalog_product_entity_int AS b3 ON a.entity_id = b3.entity_id AND b3.attribute_id = attribute3.attribute_id LEFT JOIN  eav_attribute_option_value AS o ON o.option_id = b3.value WHERE b.value is null AND b2.value=1 ORDER BY a.sku;",
             ],
            'no_shops' => [
                'label' => 'Commerçants manquants',
                'hint' => 'Liste des produits n’ayant pas de commerçant indiqués (ou dont le code de l’attribut commerçant n’existe pas dans les options de l’attribut commerçant)',
                'sql' => "SELECT * FROM  catalog_product_entity AS a INNER JOIN  eav_attribute AS attribute ON attribute.attribute_code =  'commercant' LEFT JOIN  catalog_product_entity_int AS b ON a.entity_id = b.entity_id AND b.attribute_id = attribute.attribute_id LEFT JOIN  eav_attribute_option_value AS o ON o.option_id = b.value WHERE a.type_id='simple' AND o.value is null;",
             ],
            'orphan_products' => [
                'label' => 'Produits orphelins',
                'hint' => 'Lister les produits orphelins (n’appartenant à aucune catégorie)',
                'sql' => "SELECT type_id,sku from catalog_product_entity a left join catalog_category_product cp on cp.`product_id` = a.entity_id left join catalog_product_relation cpr on cpr.child_id = a.entity_id where cp.product_id is null and cpr.parent_id is null and a.type_id != 'configurable';",
             ],
            'bundles' => [
                'label' => 'Bundles',
                'hint' => 'Liste les bundles et indique le nombre total de produits liés, le nombre de produits liés désactivés et le rapport des deux',
                'sql' => "SELECT a.sku, count(b.sku) as 'nbe produits total',sum(c.value) as 'nbe produits desactives',sum(c.value)/count(b.sku) as percent FROM  catalog_product_entity AS a INNER JOIN  catalog_product_bundle_selection AS child ON child.parent_product_id = a.entity_id INNER JOIN  catalog_product_entity AS b ON child.product_id = b.entity_id INNER JOIN  eav_attribute AS attribute ON attribute.attribute_code =  'status' LEFT JOIN  catalog_product_entity_int AS c ON b.entity_id = c.entity_id AND c.attribute_id = attribute.attribute_id WHERE a.type_id='bundle' GROUP BY a.sku ORDER BY percent DESC;",
             ],
            'cats_n_products' => [
                'label' => 'Nb produits par catégories',
                'hint' => 'Liste toutes les catégories et compte le nombre de produits dans la catégorie (avec une limite en commentaire)',
                'sql' => 'SELECT cat.product_id, COUNT(a.entity_id) as nb_produits FROM  catalog_product_entity AS a INNER JOIN catalog_category_product AS cat ON a.entity_id=cat.product_id GROUP BY cat.product_id ORDER BY nb_produits;',
             ],
            'plural_products' => [
                'label' => 'Produits au pluriel',
                'hint' => 'Lister les produits au pluriel',
                'sql' => "SELECT sku, b.value as product_name FROM  catalog_product_entity AS a INNER JOIN  eav_attribute AS attribute ON attribute.attribute_code =  'name' AND attribute.entity_type_id = a.entity_type_id LEFT JOIN  catalog_product_entity_varchar AS b ON a.entity_id = b.entity_id AND b.attribute_id = attribute.attribute_id WHERE (b.value REGEXP '^[[:alnum:]]*[s][[:>:]]' AND SUBSTRING_INDEX(SUBSTRING_INDEX(sku,'-',2),'-',-1) NOT IN ('VIN') AND SUBSTRING_INDEX(b.value,' ',1) NOT IN ('Tournedos', 'Souris', 'Anchois', 'Dos', 'Charolais', 'Epoisses','Maïs'));",
             ],
        ];
	}

}