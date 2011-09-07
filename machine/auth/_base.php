<?php

namespace _m\auth;

class _base
{

    public function login($data=false)
    {    
        if(!$data)
        {
            $data = \_m\req::auth();
            if(!sizeof($data))
                return false;
        }
        
        if(\_m\auth::check())
            return \_m::setError("User already logged in.");

        if(!\_m\db::isConnected())
             return \_m::setError("Connection to the authentication server could not be made.");

       
        

        if(!isset($data['login']))
            return  \_m::setError("No login credentials were provided.");
        
        //@TODO: validate password
        
        //@TODO: check for password
        $sql = 'SELECT * FROM user WHERE login LIKE "'.$data['login'].'"';    //@TODO: write actual SQL
        $res = \_m\db::query($sql);
        $dbInfo = \_m\db::getRowAssoc($res);
        
        if(!$dbInfo)
            return  \_m::setError("User not in the system.");
        
        foreach($dbInfo as $key => $row)
        {
            if($key!='password'&&$key!='pass')
                \_m\auth::setData($key,$row);
        }
        
        
    }

    public function logout($url='/login')
    {
        \_m\auth::delData();
        \_m::go($url);
    }
    
    public function register($data)
    {
        
        
        
    }
    
    public function check()
    {
        
    }

}

?>
