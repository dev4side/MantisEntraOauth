# MantisBT Azure SSO Authentication Plugin

Este plugin permite a autenticação de usuários no MantisBT usando o Microsoft Azure Active Directory (Azure AD) como provedor de identidade. Com ele, os usuários podem fazer login usando suas credenciais do Azure AD.


## Requisitos

* PHP: Versão 7.0 ou superior.
* MantisBT: Instalado e funcionando corretamente.
* Registro no Azure AD: Um aplicativo registrado no portal do Azure para integrar a autenticação.

## Registro na Azure

É necessário um registro de Aplicativo no Azure para habilitar este plugin.

* Acesse o Portal do Azure > Azure Active Directory > Registros de Aplicativos > Novo Registro
```
Nome: mantisbt-app
Tipos de conta compatíveis: Locatário único
URI de redirecionamento: https://${seu_url_mantis}/plugin.php?page=MantisAzureOauth/redirect
```
* `Registrar`
* No aplicativo recém-criado `mantisbt-app`, vá para `Certificados e segredos`
* Na guia `Segredos do cliente`, clique em `Novo segredo do cliente`
```
Descrição: mantisbt-app-secret
Expiração: 730 dias (24 meses)
```

* Anote o `Valor` do segredo gerado, pois essa será a única vez que ele será exibido. Este é o `Client Secret`.
* Vá para `Permissões de API`
* `Adicionar uma permissão`
```
Microsoft Graph - Permissões de Aplicativo - Group.Read.All
```
* Clique em `Conceder consentimento de administrador para Diretório Padrão` e confirme
* Vá para `Autenticação` e confirme que o campo `Web - URIs de Redirecionamento` tem uma entrada:
```
https://${seu_url_mantis}/plugin.php?page=MantisAzureOauth/redirect
```

* Obtenha as três informações abaixo para autenticar o plugin no Redmine:
  * ID do Locatário: `ID do Diretório (locatário)` na aba `Visão Geral` do aplicativo
  * ID do Cliente: `ID do Aplicativo (cliente)` na aba `Visão Geral` do aplicativo
  * Segredo do Cliente: `Client Secret` gerado anteriormente


## Instalação e Configuração no MantisBT

1. **Baixe o plugin**:
   - Faça o download ou clone o repositório:  
     ```bash
     git clone https://github.com/ugleiton/MantisAzureOauth.git
     ```

2. **Copie os arquivos**:
   - Copie o diretório `MantisAzureOauth` para o diretório `plugins` do MantisBT.

3. **Instale as dependências**:
   - Acesse o diretório `plugins/MantisAzureOauth/pages/assets/lib/OpenID-Connect-PHP` e execute:
     ```bash
     composer install
     ```

4. **Ative o plugin**:
   - Abra o MantisBT no navegador.
   - Faça login como administrador.
   - Acesse **Gerenciar** > **Gerenciar Plugins**.
   - Localize o plugin **Azure SSO Authentication Module 1.0** na lista e clique em **Instalar**.

5. **Configure o plugin**:
   - Após instalado, clique no nome do plugin (**Azure SSO Authentication Module 1.0**) para configurá-lo.
   - Preencha os campos com as informações


## Processo de Login

Quando um usuário acessa a página de login e clica em `Microsoft Login`, o plugin o redireciona para a página de login do Azure, onde ele deve se autenticar. Após o login bem-sucedido, o usuário será redirecionado de volta para o MantisBT.

### Nota:

Se o registro automático de usuários não estiver ativado, o administrador precisará criar contas manualmente para novos usuários.

## Créditos e Referências

Este plugin foi desenvolvido com base em ideias e implementações de outros projetos de integração de autenticação no MantisBT. Em especial, foram utilizados como referência:

- [MantisOIDC](https://github.com/FSD-Christian-ADM/MantisOIDC)  
- [GoogleOauth Plugin para MantisBT](https://github.com/mantisbt-plugins/GoogleOauth)

Agradeço aos desenvolvedores destes projetos por compartilharem suas soluções com a comunidade.
