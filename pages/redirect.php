<?php
require_once 'assets/lib/OpenID-Connect-PHP/vendor/autoload.php';

use Jumbojett\OpenIDConnectClient;

// Configurações da aplicação
$tenantId = plugin_config_get('tenantId');
$clientId = plugin_config_get('clientId');
$clientSecret = plugin_config_get('clientSecret');
$redirectUri = plugin_config_get('redirectUri');

try {
    // Inicializa o cliente OpenID Connect
    $oidc = new OpenIDConnectClient(
        "https://login.microsoftonline.com/$tenantId/v2.0",
        $clientId,
        $clientSecret
    );

    $oidc->setRedirectURL($redirectUri);
    $oidc->addScope(['openid', 'profile', 'User.Read', 'email']);

    // Autentica e processa o login
    $oidc->authenticate();
    $accessToken = $oidc->getAccessToken();

    // Tenta extrair o email diretamente da UserInfo ou do token JWT ou da API Graph
    $email = $oidc->requestUserInfo('email') 
        ?? extract_email_from_token($accessToken) 
        ?? fetch_email_from_graph($accessToken);
    if (!$email) {
        display_error_and_exit("Não foi possível obter email para login. Tente novamente.");
    }

    // Processa o login do usuário
    process_login($email);
} catch (Exception $e) {
    echo 'Erro ao processar autenticação: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit;
}

/**
 * Extrai o email do payload do token JWT
 */
function extract_email_from_token($accessToken) {
    $jwt_payload = base64_decode($accessToken);
    preg_match('/\{.*\}/', $jwt_payload, $matches);

    if (isset($matches[0])) {
        $data_fix = str_replace("}{", "},{", $matches[0]);
        $data_fix = '{"elements":[' . $data_fix . ']}';
        $data = json_decode($data_fix);
		//echo "<br> email from token jtw" . $data->elements[1]->email;
        return $data->elements[1]->email ?? null;
    }
    return null;
}

/**
 * Busca o email do usuário via Microsoft Graph API
 */
function fetch_email_from_graph($accessToken) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/me");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $graphUser = json_decode($response);
	$email = normalize_email_format($graphUser->mail ?? $graphUser->userPrincipalName);
	//echo "<br> email from Microsoft Graph API" . $email;
    return $email ?? null;
}


/**
 * Normaliza o formato de e-mail retornado pela API Graph
 */
function normalize_email_format($email) {
    if (strpos($email, '#EXT#') !== false) {
        $parts = explode('#', $email);
        $local_part = str_replace('_', '@', $parts[0]); // Substitui "_" por "."
        return $local_part;
    }
    return $email; // Retorna o e-mail sem alterações, se já estiver correto
}

/**
 * Processa o login do usuário no MantisBT
 */
function process_login($email) {
    $user_id = user_get_id_by_email($email);

    if (!user_id_valid($user_id, $email)) {
        return false;
    }

    user_increment_login_count($user_id);
    user_reset_failed_login_count_to_zero($user_id);
    user_reset_lost_password_in_progress_count_to_zero($user_id);

    auth_set_cookies($user_id, false);
    auth_set_tokens($user_id);

    print_header_redirect(config_get('default_home_page'));
}

/**
 * Valida o usuário antes de permitir o login
 */
function user_id_valid($user_id, $email) {
    if (!$user_id) {
        display_error_and_exit("Email address: '$email' não está registrado. Registre uma nova conta primeiro.");
    }

    if (!user_is_enabled($user_id)) {
        display_error_and_exit("Conta desativada para o email: '$email'.");
    }

    if (!user_is_login_request_allowed($user_id)) {
        display_error_and_exit("Muitas tentativas de login para o email: '$email'.");
    }

    if (user_is_anonymous($user_id)) {
        display_error_and_exit("Conta anônima não permitida para login: '$email'.");
    }

    return true;
}

/**
 * Exibe uma mensagem de erro e encerra o script
 */
function display_error_and_exit($message) {
    echo "<p>$message<br/><a href='/login_page.php'>Login</a>";
    exit;
}
