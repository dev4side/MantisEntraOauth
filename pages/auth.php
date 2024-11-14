<?php

require_once 'assets/lib/OpenID-Connect-PHP/vendor/autoload.php';

use Jumbojett\OpenIDConnectClient;

// Ativa exibição de erros
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$tenantId = plugin_config_get('tenantId');
$clientId = plugin_config_get('clientId');
$clientSecret = plugin_config_get('clientSecret');
$redirectUri = plugin_config_get('redirectUri');


// Inicializa o cliente OpenID Connect
$oidc = new OpenIDConnectClient(
    "https://login.microsoftonline.com/$tenantId/v2.0",
    $clientId,
    $clientSecret
);

// Define a URL de redirecionamento e escopos
$oidc->setRedirectURL($redirectUri);
$oidc->addScope(['openid', 'profile', 'User.Read','email']);

// Inicia a autenticação
$oidc->authenticate();