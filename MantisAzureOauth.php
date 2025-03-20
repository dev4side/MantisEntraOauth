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
			'allowedUsersStandardLogin' => 'Administrator',
			'blockedDomainsStandardLogin' => '',
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
            'EVENT_LAYOUT_RESOURCES' => 'add_azure_login_button',
            'EVENT_AUTH_USER_FLAGS' => 'check_authentication',
        );
    }

	function check_authentication( $p_event, $p_args ) {

			$p_username = $p_args['username'];

		    // Get list of users allowed to use standard login
			$allowed_users = plugin_config_get('allowedUsersStandardLogin', '');
			$blocked_domains = plugin_config_get('blockedDomainsStandardLogin', ''); 
			
			$allowed_users_array = array_map('trim', explode(',', $allowed_users));
			$blocked_domains_array = array_map('trim', explode(',', $blocked_domains));
			
			$t_flags = new AuthFlags();
			// Check if user is from a blocked domain
			$domain = '';
			if (strpos($p_username, '@') !== false) {
				list($user, $domain) = explode('@', $p_username);
				
				// If user's domain is in the blocked list, disable standard login
				if (in_array(strtolower($domain), $blocked_domains_array)) {
					$t_flags->setCanUseStandardLogin( false );
					$t_flags->setPasswordManagedExternallyMessage( plugin_lang_get('passwordManagedElsewhereMessage') );

					# No long term session for identity provider to be able to kick users out.
					$t_flags->setPermSessionEnabled( false );

					# Enable re-authentication and use more aggressive timeout.
					$t_flags->setReauthenticationEnabled( true );
					$t_flags->setReauthenticationLifetime( 10 );
					return $t_flags;
				}
			}
			
			// If we have restrictions and user is not on allowed list, block standard login
			// TODO: might accidentally block all users except from IdP 
			if (!empty($allowed_users) && !in_array($p_username, $allowed_users_array)) {
				$t_flags->setCanUseStandardLogin( false );
			}
			
			return $t_flags;
	}

    // Adiciona o botão de login com Azure no menu principal
    function add_azure_login_button() {
        //return array('<a href="' . plugin_page("auth") . '">Login with Azure</a>');
		if ( ! in_array( $this->current_page, $this->cmv_pages ) ) {
			return '';
		}

		return '
			<meta name="azureauthuri" content="' . substr(config_get('path'), 0, -1).plugin_page('auth') . '" />
			<meta name="microsoftlogo" content="' . plugin_file("microsoft-logo.png") . '" />
			<meta name="microsoftlogintext" content="' . plugin_lang_get('microsoftLoginText') . '" />
			<style> 
			.btn-microsoft { 
				background-color: #fff; 
				color: #000; 
				padding: 10px 27px; 
				border: none; 
				border-radius: 5px; 
				font-size: 14px; 
				cursor: pointer; 
				text-align: center; 
				display: inline-block; 
				text-decoration: none; 
			} 
			.btn-microsoft i { 
				margin-right: 8px; 
			} 
			.btn-microsoft:hover { 
				background-color: #cdcdcd; 
			} 
			</style> 
			<script type="text/javascript" src="'.plugin_file("plugin.js").'"></script>
		';


    }
	
}
