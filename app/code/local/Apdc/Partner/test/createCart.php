<?php 
$key = 'e6607e00e283344614586a755e97d065';
$secret = 'f67740ba2c25c7ed695da47817df3879';
$email = 'mysthr21@gmail.com';
$tz = 'Europe/Paris';
$dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
$data = $key . $secret . $dt->format('Y-m-d');
$signature = base64_encode(hash_hmac('sha256', $data, $email, true));

$products = [
    [
        'type' => 'apdc',
        'product_id' => 4107,
        'qty' => 1
    ],
    [
        'type' => 'apdc',
        'product_id' => 4111,
        'qty' => 1
    ],
    [
        'type' => 'apdc',
        'product_id' => 4851,
        'qty' => 2
    ],
    [
        'type' => 'apdc',
        'product_id' => 7146,
        'qty' => 1
    ],
    [
        'type' => 'franprix',
        'qty' => 1,
        'product_data' => [
            'category_picto' => 'cat_fruit',
            'desc_nutri' => '',
            'price' => 6.99,
            'desc_preparation' => '',
            'id' => '25055',
            'cf_departement' => '1',
            'stocks' => [
                '6755',
                '8044',
                '5196',
                '5390',
                '8383',
                '5028',
                '5197',
                '8045',
                '5121',
                '6329',
                '5511',
                '6753',
                '5671',
                '5529',
                '5613',
                '6234',
                '6738'
            ],
            'old_price' => null,
            'desc_conservation' => '',
            'imgs' => [
                'http://images.shoppingadventure.fr/image/franprix/25055/949117.jpg'
            ],
            'measure' => '500g',
            'cf_rayon' => '1',
            'unit' => '',
            'maturity' => true,
            'desc_ingredient' => '',
            'fids' => 0,
            'desc_avertissement' => '',
            'ean' => '8093',
            'max_q' => 5,
            'menus' => [
                [
                    'picto' => 'cat_fruit',
                    'name' => 'Fruits et légumes',
                    'id' => '83c76c8f0'
                ],
                [
                    'name' => 'Légumes du marché',
                    'id' => '555678a6e'
                ]
            ],
            'description_html' => '<b>Dénomination légale</b>\n<p>500g. Origine Espagne. Catégorie 2.</p>\n',
            'prix_cond' => 'kg',
            'title' => 'Tomate grappe bio',
            'is_alcohol' => false,
            'brand' => '',
            'category' => 'Fruits et légumes > Légumes du marché',
            'desc' => '',
            'cf_code_ub' => '50003',
            'desc_deno_legale' => '500g. Origine Espagne. Catégorie 2.',
            'cond' => '0.5',
            'cf_code_famille' => '13',
            'desc_origine' => '',
            'desc_allergene' => '',
            'cf_code_sous_famille' => '500'
        ]

    ],
];




$postData = array(
    'key' => $key,
    'customer_id' => null,
    'postcode' => '75017',
    'products' => json_encode($products)
);

$header = [
    'Content-type: multipart/form-data',
    'Authorization: Bearer ' . $signature
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://dev.aupasdecourses.local/accueil/partner/cart/create");
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
echo "\n";
print_r($response);
echo "\n";
/*
    $response : json
    [
        'quote_id' => (int),
        'redirect_url' => (string)
    ]
 */
$response = json_decode($response, true);
print_r($response);

curl_close($ch);
