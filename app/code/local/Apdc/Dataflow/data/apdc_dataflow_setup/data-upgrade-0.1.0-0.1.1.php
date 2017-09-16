<?php

$actionsXml = '<action type="apdc_dataflow/catalog_convert_adapter_product" method="load">
<!-- <var name="store"><![CDATA[1]]></var> -->
<!-- <var name="filter/name"><![CDATA[PETIT LUTIN]]></var> -->
</action>

<action type="apdc_dataflow/convert_parser_product" method="unparse">
    <var name="store"><![CDATA[1]]></var>
    <var name="url_field"><![CDATA[0]]></var>
</action>

<action type="dataflow/convert_mapper_column" method="map">
    <var name="map">

        <map name="sku"><![CDATA[sku]]></map>
        <map name="code_ref_apdc"><![CDATA[code_ref_apdc]]></map>
        <map name="reference_interne_magasin"><![CDATA[reference_interne_magasin]]></map>
        <map name="name"><![CDATA[name]]></map>
        <map name="status"><![CDATA[status]]></map>
        <map name="status_save"><![CDATA[status_save]]></map>
        <map name="on_selection"><![CDATA[on_selection]]></map>
        <map name="unite_prix"><![CDATA[unite_prix]]></map>
        <map name="prix_public"><![CDATA[prix_public]]></map>
        <map name="short_description"><![CDATA[short_description]]></map>
        <map name="poids_portion"><![CDATA[poids_portion]]></map>
        <map name="nbre_portion"><![CDATA[nbre_portion]]></map>
        <map name="tax_class_id"><![CDATA[tax_class_id]]></map>
        <map name="marge_arriere"><![CDATA[marge_arriere]]></map>
        <map name="image"><![CDATA[image]]></map>
        <map name="_product_websites"><![CDATA[_product_websites]]></map>
        <map name="store"><![CDATA[store]]></map>
        <map name="websites"><![CDATA[websites]]></map>
        <map name="rootcategories"><![CDATA[rootcategories]]></map>
        <map name="cat_parent"><![CDATA[cat_parent]]></map>
        <map name="commercant"><![CDATA[commercant]]></map>
        <map name="nom_catcommercant"><![CDATA[nom_catcommercant]]></map>
        <map name="nom_cat"><![CDATA[nom_cat]]></map>
        <map name="nom_sous_cat"><![CDATA[nom_sous_cat]]></map>
        <map name="nom_cat_traiteur"><![CDATA[nom_cat_traiteur]]></map>
        <map name="nom_cat_epicerie"><![CDATA[nom_cat_epicerie]]></map>
        <map name="nom_cat_epicerie"><![CDATA[nom_cat_type]]></map>
        <map name="nom_cat_epicerie"><![CDATA[nom_cat_couleur]]></map>
        <map name="nom_cat_epicerie"><![CDATA[nom_cat_contenant]]></map>
        <map name="nom_cat_epicerie"><![CDATA[nom_cat_origine]]></map>
        <map name="special_cat"><![CDATA[special_cat]]></map>

        <map name="Maturité (choisir une option):drop_down:1"><![CDATA[Maturité (choisir une option):drop_down:1]]></map>
        <map name="Bouteille au frais (choisir une option):drop_down:1"><![CDATA[Bouteille au frais (choisir une option):drop_down:1]]></map>
        <map name="Produit détaillé/découpé ?:drop_down:1"><![CDATA[Produit détaillé/découpé ?:drop_down:1]]></map>
        <map name="Pain tranché?:drop_down:1"><![CDATA[Pain tranché?:drop_down:1]]></map>
        <map name="Usage (choisir une option):drop_down:1"><![CDATA[Usage (choisir une option):drop_down:1]]></map>
        <map name="Parfum:drop_down:1"><![CDATA[Parfum:drop_down:1]]></map>
        <map name="Choisissez votre taille:drop_down:1"><![CDATA[Choisissez votre taille:drop_down:1]]></map>
        <map name="Quels chocolats souhaitez vous (cochez une ou plusieurs options):checkbox:1"><![CDATA[Quels chocolats souhaitez vous (cochez une ou plusieurs options):checkbox:1]]></map>
        <map name="saisonnalite"><![CDATA[saisonnalite]]></map>
        <map name="producteur"><![CDATA[producteur]]></map>
        <map name="lien_producteur"><![CDATA[lien_producteur]]></map>
        <map name="origine"><![CDATA[origine]]></map>
        <map name="conditionnement"><![CDATA[conditionnement]]></map>
        <map name="composition"><![CDATA[composition]]></map>
        <map name="caracteristiques"><![CDATA[caracteristiques]]></map>
        <map name="labels_produits"><![CDATA[labels_produits]]></map>
        <map name="show_age_popup"><![CDATA[show_age_popup]]></map>
        <map name="produit_biologique"><![CDATA[produit_biologique]]></map>
        <map name="description"><![CDATA[description]]></map>
        <map name="suggestion_utilisation"><![CDATA[suggestion_utilisation]]></map>
        <map name="conseil_commercant"><![CDATA[conseil_commercant]]></map>
        <map name="poids_par_pers"><![CDATA[poids_par_pers]]></map>
        <map name="notes_com"><![CDATA[notes_com]]></map>
        <map name="us_skus"><![CDATA[us_skus]]></map>
        <map name="re_skus"><![CDATA[re_skus]]></map>
        <map name="cs_skus"><![CDATA[cs_skus]]></map>
        <map name="gr_skus"><![CDATA[gr_skus]]></map>
    </var>
    <var name="_only_specified">true</var>
</action>

<action type="dataflow/convert_parser_csv" method="unparse">
    <var name="delimiter"><![CDATA[,]]></var>
    <var name="enclose"><![CDATA["]]></var>
    <var name="fieldnames">true</var>
</action>

<action type="dataflow/convert_adapter_io" method="save">
    <var name="type">file</var>
    <var name="path">var/export</var>
    <var name="filename"><![CDATA[apdc-all-products-export.csv]]></var>
</action>';

$dataProfile = Mage::getModel('dataflow/profile');
$dataProfile->setName('Apdc - Export All Products')
    ->setActionsXml($actionsXml)
    ->save();
