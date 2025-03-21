# MantisBT Entra ID (Azure AD SSO) Authentication Plugin

This plugin allows user authentication in MantisBT using Microsoft Azure Active Directory (Azure AD) as the identity provider. With it, users can log in using their Azure AD credentials.

## Requirements

* PHP: Version 7.0 or higher.
  * openssl and curl PHP extensions enabled in php.ini
* MantisBT: Installed and working correctly.
* Azure AD Registration: A registered application in the Azure portal to integrate authentication.

## Azure Registration

An Application registration in Azure is required to enable this plugin.

* Go to Azure Portal > Azure Active Directory > App Registrations > New Registration
```
Name: mantisbt-app
Supported account types: Single tenant
Redirect URI: https://${your_mantis_url}/plugin.php?page=MantisAzureOauth/redirect  
```
* Click `Register`
* In the newly created `mantisbt-app` application, go to `Certificates & secrets`
* On the `Client secrets` tab, click `New client secret`
```
Description: mantisbt-app-secret
Expiration: 730 days (24 months)
```

* Take note of the `Value` of the generated secret, as this will be the only time it's displayed. This is the `Client Secret`.
* Go to `API permissions`
* Click `Add a permission`
```
Microsoft Graph - Application permissions - Group.Read.All
```
* Click `Grant admin consent for Default Directory` and confirm
* Go to `Authentication` and confirm that the `Web - Redirect URIs` field has an entry:
```
https://${your_mantis_url}/plugin.php?page=MantisAzureOauth/redirect
```

* Obtain the three pieces of information below to authenticate the plugin in Redmine:
  * Tenant ID: `Directory (tenant) ID` on the `Overview` tab of the application
  * Client ID: `Application (client) ID` on the `Overview` tab of the application
  * Client Secret: The `Client Secret` generated earlier


## Installation and Configuration in MantisBT

1. **Download the plugin**:
   - Download or clone the repository:  
     ```bash
     git clone https://github.com/ugleiton/MantisAzureOauth.git
     ```

2. **Copy the files**:
   - Copy the `MantisAzureOauth` directory to the `plugins` directory of MantisBT.

3. **Install dependencies**:
   - Access the directory `plugins/MantisAzureOauth/pages/assets/lib/OpenID-Connect-PHP` and run:
     ```bash
     composer install
     ```

4. **Activate the plugin**:
   - Open MantisBT in your browser.
   - Log in as an administrator. 
   - Go to **Manage** > **Manage Plugins**.
   - Find the **Azure SSO Authentication Module 1.0** plugin in the list and click **Install**.

5. **Configure the plugin**:
   - After installation, click on the plugin name (**Azure SSO Authentication Module 1.0**) to configure it.
   - Fill in the fields with the information 

6. **Block default access**
   - If you want to prevent login using the default method, fill in the field `Block users from logging in with local accounts` in the settings with the user names separated by comma, only these users will not be able to login using their local credentials.
   - Same thing goes for domains: fill `Block domains from logging in with local accounts` with the details.

## Login Process

When a user accesses the login page and clicks on `Microsoft Login`, the plugin redirects them to the Azure login page, where they must authenticate. After successful login, the user will be redirected back to MantisBT.
The authentication logic is handled inside the `check_authentication()` function and uses hooks to modify user auth flags during login; this allows safe updates to the MantisBT instance without losing modifications.

### Note:

If automatic user registration is not enabled, the administrator will need to manually create accounts for new users.

## Credits and References

This plugin was developed based on ideas and implementations from other authentication integration projects in MantisBT. In particular, the following were used as references:

- [MantisOIDC](https://github.com/FSD-Christian-ADM/MantisOIDC)
- [GoogleOauth Plugin for MantisBT](https://github.com/mantisbt-plugins/GoogleOauth)
- [ugleiton](https://github.com/ugleiton) for the original implementation

I thank the developers of these projects for sharing their solutions with the community.
