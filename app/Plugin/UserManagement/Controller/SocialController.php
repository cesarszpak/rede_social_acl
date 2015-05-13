<?php
App::uses('UserManagementAppController', 'UserManagement.Controller');
App::import('Vendor', 'UserManagement.Facebook',array('file'=>'Facebook'.DS.'facebook.php'));
App::import('Vendor', 'UserManagement.twitteroauth',array('file'=>'twitteroauth'.DS.'twitteroauth.php'));
App::import('Vendor', 'UserManagement.Google_Client', array('file' => 'Google' . DS . 'src' . DS . 'Google_Client.php'));
App::import('Vendor', 'UserManagement.Google_Oauth2Service', array('file' => 'Google' . DS . 'src' . DS . 'contrib' . DS . 'Google_Oauth2Service.php'));
//App::import('Vendor', 'twitteroauth/twitteroauth');
/**
 * Social Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class SocialController extends UserManagementAppController {
    /**
     * This controller does not use a model
     *
     * @var array
     */
    private $facebookAppId;
    private $facebookSecret;
    private $facebook_return_url;
    private $googleClientId;
    private $googleClientSecret;
    private $googleDeveloperKey;
    private $google_return_url;
    private $twitterConsumerKey;
    private $twitterConsumerSecret;
    private $twitter_return_url;
    private $linkedinApiKey;
    private $linkedinApiSecret;
    private $linkedinScope;
    private $linkedin_return_url;
    private $socialLoginGroupId;
    public $uses=array('UserManagement.User','UserManagement.Usermeta');

    public function beforeFilter() {
        parent::beforeFilter();
        // For CakePHP 2.1 and up
        $this->Auth->allow();
        $this->facebookAppId=Configure::read('facebook-cred.appId');
        $this->facebookSecret=Configure::read('facebook-cred.secret');
        $this->facebook_return_url=Router::url(array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'connectFacebook'),true);

        $this->googleClientId=Configure::read('google-cred.clientID');
        $this->googleClientSecret=Configure::read('google-cred.clientSecret');
        $this->googleDeveloperKey=Configure::read('google-cred.developerKey');
        $this->google_return_url=Router::url(array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'googleLogin'),true);

        $this->twitterConsumerKey=Configure::read('twitter-cred.consumer_key');
        $this->twitterConsumerSecret=Configure::read('twitter-cred.consumer_secret');
        $this->twitter_return_url=Router::url(array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'callback'),true);

        $this->linkedinApiKey=Configure::read('linkedin-cred.API_KEY');
        $this->linkedinApiSecret=Configure::read('linkedin-cred.API_SECRET');
        $this->linkedinScope=Configure::read('linkedin-cred.SCOPE');
        $this->linkedin_return_url=Router::url(array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'linkedinLogin'),true);

        $this->socialLoginGroupId=Configure::read('socialLoginGroupId');

        $this->layout=null;

    }

    private function makeUserLogin($userdata=array()){
        if(!empty($userdata)){
            $this->Auth->login($userdata['User']);
        }
        return true;

    }


    /*Facebook section starts*/
    function facebookLogin()
    {
        $facebook = new Facebook(array(
            'appId'		=>  $this->facebookAppId,
            'secret'	=> $this->facebookSecret,
        ));
        $loginUrl = $facebook->getLoginUrl(array(
            'scope'			=> 'email,read_stream, publish_stream, user_birthday, user_location, user_work_history, user_hometown, user_photos',
            'redirect_uri'	=> $this->facebook_return_url,
            'display'=>'popup'
        ));

        $this->redirect($loginUrl);
    }
    function connectFacebook()
    {
        $this->autoRender=false;
        $facebook = new Facebook(array(
            'appId'		=>  $this->facebookAppId,
            'secret'	=> $this->facebookSecret,
        ));
        $user = $facebook->getUser();

        if($user){
            try{
                $user_profile = $facebook->api('/me');

                $userinfo = $this->User->find('first', array('conditions' => array('User.username' => (isset($user_profile['id'])) ? $user_profile['id'] : $user_profile['email'],'User.social_connect'=>'facebook')));

                if (empty($userinfo)) {
                    $data['User']['username'] = (isset($user_profile['id'])) ? $user_profile['id'] : $user_profile['email'];
                    $data['User']['password'] = $user_profile['id'];
                    $data['User']['group_id'] = $this->socialLoginGroupId;
                    $data['User']['social_connect'] = 'facebook';
                    $data['User']['social_info'] = json_encode($user_profile);

                    $this->User->create();
                    $this->User->save($data);
                    $userinfo = $this->User->find('first', array('conditions' => array('User.id' => $this->User->id )));

                    $this->makeUserLogin($userinfo);

                }else{
                    $this->makeUserLogin($userinfo);
                }
            }
            catch(Exception $e){
                error_log($e->getMessage());
                $this->Session->setFlash($e->getMessage());
                $user = NULL;
            }
        }
        else
        {
            $this->Session->setFlash('Sorry.Please try again');
        }
        $this->render('Social/close_window');
    }
    /*Facebook section ends*/

    /*Twitter section starts*/
    private function createClient() {
        $connection = new TwitterOAuth($this->twitterConsumerKey, $this->twitterConsumerSecret);
        return $connection;
    }
    public function twitterLogin() {
        $connection = $this->createClient();

        /* Get temporary credentials. */
        $request_token = $connection->getRequestToken($this->twitter_return_url);

        /* Save temporary credentials to session. */
        $token = $request_token['oauth_token'];
        $this->Session->write('oauth_token', $request_token['oauth_token']);
        $this->Session->write('oauth_token_secret', $request_token['oauth_token_secret']);

        /* If last connection failed don't display authorization link. */
        switch ($connection->http_code) {
            case 200:
                /* Build authorize URL and redirect user to Twitter. */
                $url = $connection->getAuthorizeURL($token);
                $this->redirect($url);
                break;
            default:
                /* Show notification if something went wrong. */
                echo 'Could not connect to Twitter. Refresh the page or try again later.';
        }
    }

    public function callback() {

        $this->autoRender = false;
        if (isset($_REQUEST['oauth_token']) && $this->Session->read('oauth_token') !== $_REQUEST['oauth_token']) {

            $this->Session->write('oauth_status', 'oldtoken');
            $this->clearSession();
        }
        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new TwitterOAuth($this->twitterConsumerKey, $this->twitterConsumerSecret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
        try{

            $user_profile = $connection->get('account/verify_credentials');
            //debug($user_profile);die();

            $userinfo = $this->User->find('first', array('conditions' => array('User.username' => (isset($user_profile->screen_name)) ? $user_profile->screen_name : $user_profile->id,'User.social_connect'=>'twitter')));

            if (empty($userinfo)) {
                $data['User']['username'] = (isset($user_profile->screen_name)) ? $user_profile->screen_name : $user_profile->id;
                $data['User']['password'] = $user_profile->id;
                $data['User']['group_id'] = $this->socialLoginGroupId;
                $data['User']['social_connect'] = 'twitter';
                $data['User']['social_info'] = json_encode($user_profile);

                $this->User->create();
                $this->User->save($data);

                /*twitter key and twitter secret is not needed now..If required just uncomment the line.*/
                /*$metadata = array();
                foreach ($access_token as $tokenkey => $tokenvalue) {
                    if ($tokenkey == 'oauth_token') {
                        $this->Usermeta->create();

                        $metadata['Usermeta']['user_id'] = $this->User->id;
                        $metadata['Usermeta']['metakey'] = 'twitter-key';
                        $metadata['Usermeta']['metavalue'] = $tokenvalue;

                        $this->Usermeta->save($metadata);
                    }
                    if ($tokenkey == 'oauth_token_secret') {
                        $this->Usermeta->create();

                        $metadata['Usermeta']['user_id'] = $this->User->id;
                        $metadata['Usermeta']['metakey'] = 'twitter-secret';
                        $metadata['Usermeta']['metavalue'] = $tokenvalue;

                        $this->Usermeta->save($metadata);
                    }
                }*/
                /* Remove no longer needed request tokens */
                $this->Session->delete('oauth_token');
                $this->Session->delete('oauth_token_secret');

                $userinfo = $this->User->find('first', array('conditions' => array('User.id' => $this->User->id )));
                $this->makeUserLogin($userinfo);
            }else{
                $this->makeUserLogin($userinfo);
            }
        }
        catch(Exception $e){
            error_log($e->getMessage());
            $this->Session->setFlash($e->getMessage());
            $user = NULL;
        }
        $this->render('Social/close_window');
    }
    private function clearSession() {
        $this->autoRender = false;
        /* Load and clear sessions */
        $this->Session->destroy();
        $this->redirect($this->Auth->logout());
    }

    /*Twitter section ends*/

    /*Linkedin section starts*/
    public function linkedinLogin(){
        $this->autoRender=false;

        // OAuth 2 Control Flow
        if (isset($_GET['error'])) {
            // LinkedIn returned an error
            print $_GET['error'] . ': ' . $_GET['error_description'];
            exit;
        } elseif (isset($_GET['code'])) {
            // User authorized your application

            if ($this->Session->read('state') == $_GET['state']) {
                // Get token so you can make API calls
                $this->getAccessToken();
            } else {
                // CSRF attack? Or did you mix up your states?
                exit;
            }
        } else {
            $exp=$this->Session->read('expires_at');
            $acc_tok=$this->Session->read('access_token');
            if (empty($exp) || (time() >  $this->Session->read('expires_at'))) {
                // Token has expired, clear the state
                $this->Session->destroy();
            }
            if (empty($acc_tok)) {
                // Start authorization process
                $this->getAuthorizationCode();
            }
        }

        try{
            // Congratulations! You have a valid token. Now fetch your profile
            $user_profile = $this->fetchData('GET', '/v1/people/~:(firstName,lastName,email-address,id,picture-url)');
            $userinfo = $this->User->find('first', array('conditions' => array('User.username' =>(isset($user_profile->id)) ? $user_profile->id : $user_profile->emailAddress ,'User.social_connect'=>'linkedin')));

            if (empty($userinfo)) {
                $data['User']['username'] = (isset($user_profile->id)) ? $user_profile->id : $user_profile->emailAddress;
                $data['User']['password'] = $user_profile->id;
                $data['User']['group_id'] = $this->socialLoginGroupId;
                $data['User']['social_connect'] = 'linkedin';
                $data['User']['social_info'] = json_encode($user_profile);

                $this->User->create();
                $this->User->save($data);
                $userinfo = $this->User->find('first', array('conditions' => array('User.id' => $this->User->id )));
                $this->makeUserLogin($userinfo);
            }else{
                $this->makeUserLogin($userinfo);
            }
        }
        catch(Exception $e){
            error_log($e->getMessage());
            $this->Session->setFlash($e->getMessage());
            $user = NULL;
        }
        $this->render('Social/close_window');

    }
    private function getAuthorizationCode() {

        $params = array('response_type' => 'code',
            'client_id' => $this->linkedinApiKey,
            'scope' => $this->linkedinScope,
            'state' => uniqid('', true), // unique long string
            'redirect_uri' => $this->linkedin_return_url,
        );

        // Authentication request
        $url = 'https://www.linkedin.com/uas/oauth2/authorization?' . http_build_query($params);

        // Needed to identify request when it returns to us
        $this->Session->write('state',$params['state']);
        //$_SESSION['state'] = $params['state'];

        // Redirect user to authenticate
        //header("Location: $url");
        $this->redirect($url);
        exit;
    }

    private function getAccessToken() {


        $params = array('grant_type' => 'authorization_code',
            'client_id' => $this->linkedinApiKey,
            'client_secret' => $this->linkedinApiSecret,
            'code' => $_GET['code'],
            'redirect_uri' => $this->linkedin_return_url,
        );

        // Access Token request
        $url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query($params);

        // Tell streams to make a POST request
        $context = stream_context_create(
            array('http' =>
                array('method' => 'GET',
                )
            )
        );

        // Retrieve access token information
        $response = file_get_contents($url, false, $context);


        // Native PHP object, please
        $token = json_decode($response);

        // Store access token and expiration time
        $this->Session->write('access_token',$token->access_token);
        $this->Session->write('expires_in',$token->expires_in);
        $this->Session->write('expires_at',time() + $_SESSION['expires_in']);

        return true;
    }

    private function fetchData($method, $resource, $body = '') {
        $params = array('oauth2_access_token' => $_SESSION['access_token'],
            'format' => 'json',
        );
        // Need to use HTTPS
        $url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
        // Tell streams to make a (GET, POST, PUT, or DELETE) request
        $context = stream_context_create(
            array('http' =>
                array('method' => $method,
                )
            )
        );
        // Hocus Pocus
        $response = file_get_contents($url, false, $context);

        // Native PHP object, please
        return json_decode($response);
    }

    /*Linkedin section ends*/

    /*Google section starts*/
    public function googleLogin(){
        
        $googleClient = new Google_Client();

        $googleClient->setApplicationName('Login');
        $googleClient->setClientId($this->googleClientId);
        $googleClient->setClientSecret($this->googleClientSecret);
        $googleClient->setRedirectUri($this->google_return_url);
        $googleClient->setDeveloperKey($this->googleDeveloperKey);
        $googleClient->setAccessType('offline');
        $googleClient->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));
        $googleClient->setApprovalPrompt('force');
        $google_oauthV2 = new Google_Oauth2Service($googleClient);

        if (isset($this->request->query['code'])) {
            $googleClient->authenticate($this->request->query['code']);
            $googleuserAccessToken = $googleClient->getAccessToken();
            $googleClient->setAccessToken($googleuserAccessToken);

        } else {
            $url = $googleClient->createAuthUrl();
            $this->redirect($url);
        }

        try{
            // Congratulations! You have a valid token. Now fetch your profile
            $user_profile = $google_oauthV2->userinfo->get();
            $userinfo = $this->User->find('first', array('conditions' => array('User.username' =>(isset($user_profile['id'])) ? $user_profile['id'] : $user_profile['email'] ,'User.social_connect'=>'google')));

            if (empty($userinfo)) {
                $data['User']['username'] = (isset($user_profile['id'])) ? $user_profile['id'] : $user_profile['email'];
                $data['User']['password'] = $user_profile['id'];
                $data['User']['group_id'] = $this->socialLoginGroupId;
                $data['User']['social_connect'] = 'google';
                $data['User']['social_info'] = json_encode($user_profile);

                $this->User->create();
                $this->User->save($data);
                $userinfo = $this->User->find('first', array('conditions' => array('User.id' => $this->User->id )));
                $this->makeUserLogin($userinfo);
            }else{
                $this->makeUserLogin($userinfo);
            }
        }
        catch(Exception $e){
            error_log($e->getMessage());
            $this->Session->setFlash($e->getMessage());
            $user = NULL;
        }

        $this->render('Social/close_window');
    }

    /*Google section ends*/
}