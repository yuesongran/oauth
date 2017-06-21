<?php

// error reporting (this is a demo, after all!)
ini_set('display_errors', 1);
error_reporting(E_ALL);

class OauthServer
{
    public $server = null;
    public $storage = null;

    public function __construct()
    {
        if(Empty($this->server))
        {
            $dsn = 'mysql:dbname=test;host=localhost';
            $username = 'root';
            $password = '';

            // Autoloading (composer is preferred, but for this example let's just do this)
            require_once(__DIR__.'/OAuth2/Autoloader.php');
            OAuth2\Autoloader::register();

            // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
            $this->storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

            // Pass a storage object or array of storage objects to the OAuth2 server class
            //$server = new OAuth2\Server($storage);
            $this->server = new OAuth2\Server($this->storage, array(
                'allow_implicit' => true,
                'access_lifetime'=> 7200,
                'refresh_token_lifetime'=> 2419200,
            ));
            // Add the "Client Credentials" grant type (it is the simplest of the grant types)
            $this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($this->storage));
            // Add the "Authorization Code" grant type (this is where the oauth magic happens)
            $this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($this->storage));
            //Resource Owner Password Credentials (资源所有者密码凭证许可）
            $this->server->addGrantType(new OAuth2\GrantType\UserCredentials($this->storage));
            //can RefreshToken set always_issue_new_refresh_token=true
            $this->server->addGrantType(new OAuth2\GrantType\RefreshToken($this->storage, array(
                'always_issue_new_refresh_token' => true
            )));
            // configure your available scopes
            $defaultScope = 'basic';
            $supportedScopes = array(
                'basic',
                'postonwall',
                'accessphonenumber'
            );
            $memory = new OAuth2\Storage\Memory(array(
                'default_scope' => $defaultScope,
                'supported_scopes' => $supportedScopes
            ));
            $scopeUtil = new OAuth2\Scope($memory);
            $this->server->setScopeUtil($scopeUtil);
        }
    }

    public function getCode()
    {
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();

        // validate the authorize request
        if (!$this->server->validateAuthorizeRequest($request, $response)) {
            $response->send();
            die;
        }
        // display an authorization form
        if (empty($_POST)) {
            exit('  
                <form method="post">  
                  <label>Do You Authorize '.$_REQUEST['client_id'].'?</label><br /><br />  
                  用户名：<input type="text" name="username" value="">  <br /><br /> 
                  密  码：<input type="password" name="password" value="">  <br /><br /> 
                  <input type="submit" name="authorized" value="Yes">  
                  <input type="submit" name="authorized" value="No">  
                </form>');
        }
        else
        {
            // print the authorization code if the user has authorized your client
            $is_authorized = ($_POST['authorized'] === 'Yes');

            if($is_authorized)
            {
                $userObj = $this->server->getGrantType('password');
                if(!$userObj->validateRequest($request, $response)) die("帐户名和密码错误\n");
                $this->server->handleAuthorizeRequest($request, $response, $is_authorized,$userObj->getUserId());
                // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
                //$code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=') + 5, 40);
                $response->send();
            }
            else
            {
                die(json_encode(array('error'=>'user denied','error_description'=>'The user denied access to your application')));
            }
        }
    }

    public function getToken()
    {
        $this->server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
    }

    public function getUserInfo($token)
    {
        $token = $this->storage->getAccessToken($token);
        if($token['expires']<=time()) die(json_encode(array('error'=>'expires invalid')));
        return $this->storage->getUserById($token['user_id']);
    }
}