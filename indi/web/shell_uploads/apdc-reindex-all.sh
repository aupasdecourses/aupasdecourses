#!/bin/bash
URL="$(cd "$(dirname "$0")" && pwd)"
cd "$URL"
cd ../../../shell
php indexer.php --reindex catalog_product_attribute
echo "Reindex catalog_product_attribute OK!"
php indexer.php --reindex catalog_url
echo "Reindex catalog_url OK!"
php indexer.php --reindex catalog_product_flat
echo "Reindex catalog_product_flat OK!"
php indexer.php  --reindex catalog_category_flat
echo "Reindex catalog_category_flat OK!"
php indexer.php  --reindex catalog_category_product
echo "Reindex catalog_product_price OK!"
php indexer.php  --reindex catalogsearch_fulltext
echo "Reindex catalogsearch_fulltext OK!"
php indexer.php  --reindex catalog_product_price
echo "Reindex catalog_product_price OK!"
HERE
