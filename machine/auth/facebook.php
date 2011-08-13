<?php

namespace _m\auth;

class facebook extends _base_oauth
{
    
    public function login($data)
    {
       
        $config['appId']        = \_m\settings::get(array('facebook','id'));
        $config['secret']       = \_m\settings::get(array('facebook','secret'));
        $config['fileUpload']   = \_m\settings::get(array('facebook','file_upload'));
        
         \_m::inc('facebook.php');
        
        $facebook=false;
        try
        {
            \_m\auth::$oauth['facebook'] = new \Facebook($config);
        }
        catch(Exception $e)
        {
           return \_m::setError('Unable to connect to Facebook');
        }
        
       // Get User ID
        $user = \_m\auth::$oauth['facebook']->getUser();

        // We may or may not have this data based on whether the user is logged in.
        //
        // If we have a $user id here, it means we know the user is logged into
        // Facebook, but we don't know if the access token is valid. An access
        // token is invalid if the user logged out of Facebook.
        
        if ($user) {
          try {
            // Proceed knowing you have a logged in user who's authenticated.
            $user_profile = \_m\auth::$oauth['facebook']->api('/me');
          } catch (FacebookApiException $e) {
            error_log($e);
            $user = null;
          }
        }
        // This call will always work since we are fetching public data.
        
        self::setAccessCode();
        
        return $facebook;
    }
    
    public function logout($url='/login')
    {
        //@TODO: call facebook logout
        \_m\auth::delData();
        \_m::go($url);
    }
    
    public function register($data)
    {
        
    }
    
    
    public function check()
    {
        
    }
    
    public function loggedIn()
    {
        if(\_m\auth::$oauth['facebook']->getUser()) return true;
        return false;
    }
    
    public function loginURL()
    {
        $config = array();
        
        if(\_m\settings::get(array('facebook','redirect_uri')))
            $config['redirect_uri'] = \_m\settings::get(array('facebook','redirect_uri'));
        $config['scope'] = \_m\settings::get(array('facebook','scope'));
        
        return \_m\auth::$oauth['facebook']->getLoginUrl($config);
    }
    
    public function logoutURL()
    {
        $config = array();
        
        if(\_m\settings::get(array('facebook','logout_next')))
            $config['next'] = \_m\settings::get(array('facebook','logout_next'));
        
        return \_m\auth::$oauth['facebook']->getLogoutUrl();
    
        
    }
    
    public function userData()
    {
         try {
            // Proceed knowing you have a logged in user who's authenticated.
            return \_m\auth::$oauth['facebook']->api('/me');
          } catch (FacebookApiException $e) {
            return \_m::setError('Unable to access Facebook user data');
          }
    }
    
    public function setAccessCode()
    {
        if(isset($_GET['code']))
            \_m\auth::$oauth['facebook_code'] = $_GET['code'];
    }
    
    public function api($data)
    {
        return \_m\auth::$oauth['facebook']->api($data);
    }
}

?>
