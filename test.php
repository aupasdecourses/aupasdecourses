<?php
/**
 * Example of retrieving the products list using Customer account via Magento REST API. OAuth authorization is used
 * Preconditions:
 * 1. Install php oauth extension
 * 2. If you were authorized as an Admin before this step, clear browser cookies for 'yourhost'
 * 3. Create at least one product in Magento and enable it for viewing in the frontend
 * 4. Configure resource permissions for Customer REST user for retrieving all product data for Customer
 * 5. Create a Consumer
 */
// $callbackUrl is a path to your file with OAuth authentication example for the Customer user
$callbackUrl = "http://dev.aupasdecourses.local/test.php";
$temporaryCredentialsRequestUrl = "http://dev.aupasdecourses.local/accueil/oauth/initiate?oauth_callback=" . urlencode($callbackUrl);
$customerAuthorizationUrl = 'http://dev.aupasdecourses.local/accueil/oauth/authorize';
$accessTokenRequestUrl = 'http://dev.aupasdecourses.local/accueil/oauth/token';
$apiUrl = 'http://dev.aupasdecourses.local/api/rest';


// Systeme / Service Web / REST -Consommateur OAUTH
$consumerKey = '8b63d81bdcc6beb22d6dfb5c630b372d';
$consumerSecret = 'ab1fe19fc4858c1ac553be8b6ffa054f';

session_start();
if (!isset($_GET['oauth_token']) && isset($_SESSION['state']) && $_SESSION['state'] == 1) {
    $_SESSION['state'] = 0;
}
try {
    $authType = ($_SESSION['state'] == 2) ? OAUTH_AUTH_TYPE_AUTHORIZATION : OAUTH_AUTH_TYPE_URI;
    $oauthClient = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, $authType);
    $oauthClient->enableDebug();

    if (!isset($_GET['oauth_token']) && !$_SESSION['state']) {
        $requestToken = $oauthClient->getRequestToken($temporaryCredentialsRequestUrl);
        $_SESSION['secret'] = $requestToken['oauth_token_secret'];
        $_SESSION['state'] = 1;
        header('Location: ' . $customerAuthorizationUrl . '?oauth_token=' . $requestToken['oauth_token']);
        exit;
    } else if ($_SESSION['state'] == 1) {
        $oauthClient->setToken($_GET['oauth_token'], $_SESSION['secret']);
        $accessToken = $oauthClient->getAccessToken($accessTokenRequestUrl);
        $_SESSION['state'] = 2;
        $_SESSION['token'] = $accessToken['oauth_token'];
        $_SESSION['secret'] = $accessToken['oauth_token_secret'];
        header('Location: ' . $callbackUrl);
        exit;
    } else {
        $oauthClient->setToken($_SESSION['token'], $_SESSION['secret']);

        $resourceUrl = "$apiUrl/products";
        $oauthClient->fetch($resourceUrl, array(), 'GET', array('Content-Type' => 'application/json', 'Accept' => '*/*'));
        $productsList = json_decode($oauthClient->getLastResponse());
        echo '<pre>';
        print_r($productsList);
        echo '<pre>';
    }
} catch (OAuthException $e) {
    echo '<pre>';
    print_r($e->getMessage());
    echo '<br/>';
    print_r($e->lastResponse);
    echo '<pre>';
}