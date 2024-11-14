<?php

class MantisAzureOauthPlugin extends MantisPlugin {

	var $cmv_pages;
	var $current_page;

	function register() {
		$this->name        = 'Azure SSO Authentication Module';
		$this->description = 'Azure SSO authentication to MantisBT.';
		$this->page        = 'config';

		$this->version  = '1.0';
		$this->requires = array(
			'MantisCore' => '2.0.0',
		);

		$this->author  = 'Ugleiton';
		$this->contact = 'ugletion@gmail.com';
		$this->url     = 'https://github.com/ugleiton/MantisAzureOauth';
	}


    // Define as configurações padrão do plugin
    function config() {
        return array(
            'tenantId' => '',
            'clientId' => '',
            'clientSecret' => '',
            'redirectUri' => '',
        );
    }
	
	function init() {
		$this->cmv_pages    = array(
			'login_page.php',
			'login_password_page.php'
		);
		$this->current_page = basename( $_SERVER['PHP_SELF'] );
	}

    // Hook para adicionar o botão de login ao menu principal do MantisBT
    function hooks() {
        return array(
            "EVENT_LAYOUT_RESOURCES" => "add_azure_login_button"
            //"EVENT_AUTH_USER_FLAGS" => "check_authentication",
        );
    }

    // Adiciona o botão de login com Azure no menu principal
    function add_azure_login_button() {
        //return array('<a href="' . plugin_page("auth") . '">Login with Azure</a>');
		if ( ! in_array( $this->current_page, $this->cmv_pages ) ) {
			return '';
		}

		return '
			<meta name="azureauthuri" content="' . substr(config_get('path'), 0, -1).plugin_page('auth') . '" />
			<script type="text/javascript" src="'.plugin_file("plugin.js").'"></script>
		';


    }

	// Verifica a autenticação do usuário (pode ser expandido para incluir lógica de autenticação)
    //function check_authentication($event, $user_id) {
	//	// Verifica se o usuário já está autenticado no MantisBT
	//	if (auth_is_user_authenticated()) {
	//		return; // Se estiver autenticado, não faz nada
	//	}
	//
	//	// Caso contrário, verifica se temos um token de sessão da Azure na sessão
	//	if (isset($_SESSION['azure_token'])) {
	//		// Obtém as informações do usuário armazenadas na sessão
	//		$azure_user = $_SESSION['azure_user'];
	//
	//		// Tenta localizar o usuário no MantisBT pelo e-mail da Azure
	//		$user_id = user_get_id_by_email($azure_user->email);
	//		
	//		if (!$user_id) {
	//			// Caso o usuário não exista no MantisBT, crie um novo usuário
	//			$user_id = user_create(
	//				$azure_user->name,          // Nome de usuário
	//				auth_generate_random_password(), // Senha aleatória (não utilizada)
	//				$azure_user->email,         // Email
	//				REPORTER,                   // Nível de acesso padrão
	//				true                        // Habilitado
	//			);
	//		}
	//
	//		// Autentica o usuário no MantisBT
	//		auth_attempt_script_login(user_get_name($user_id));
	//		return;
	//	}
	//
	//	// Se não há sessão Azure, redireciona para a página de login do Azure
	//	print_header_redirect(plugin_page('auth', true));
	//}
	
}
