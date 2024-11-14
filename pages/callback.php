<?php
require_once 'assets/lib/OpenID-Connect-PHP/vendor/autoload.php';

use Jumbojett\OpenIDConnectClient;

// Configurações da aplicação
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
$oidc->addScope(['openid', 'profile', 'User.Read', 'email']);  // Adiciona o escopo 'email'

//if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
//    $client->setAccessToken($_SESSION['access_token']);
//}


try {
    // Autentica usando o código de autorização retornado na URL de callback
    $oidc->authenticate(); // caso tenha recebido o code via parametro ele decodifica o parametro para obter o token

    $accessToken = $oidc->getAccessToken();

	$jwt_payload = base64_decode($accessToken);

	echo "\n";

	// Extraindo apenas a parte JSON do payload do JWT, ignorando possíveis caracteres adicionais
	preg_match('/\{.*\}/', $jwt_payload, $matches);

	if (isset($matches[0])) {
		$data_fix = str_replace("}{", "},{",$matches[0]);
		$data_fix = '{"elements":['.$data_fix.']}';
		echo "\n";
		$data = json_decode($data_fix);
		
		if (isset($data->elements[1]->email)) {
			echo 'Email: ' . $data->elements[1]->email;
            
            $user_id = user_get_id_by_email($email);
            if ($user_id !== false) {
                auth_attempt_script_login(user_get_field($user_id, 'username'));
                print_header_redirect(config_get('default_home_page'));
            } else {
                echo "Usuário não encontrado. Entre em contato com o administrador.";
            }

		} else {
			echo 'Email não encontrado no payload azure';
		}
	} else {
		echo 'Formato JSON inválido.';
	}

} catch (Exception $e) {
    echo 'Erro ao processar autenticação: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}