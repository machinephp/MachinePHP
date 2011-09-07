<?php

namespace _m;

class request
{
    private static $instance;

    private static $get = array();
    private static $post = array();

    private static $data = array();

    private static $machine = array();

    private static $auth = array();

    public function load()
    {
        if (isset(self::$instance))
            return false; //@TODO: throw error

        $c = __CLASS__;
        self::$instance = new $c;

        return self::$instance;
    }

    private function __construct()
    {
        //@TODO: install data cleaning methods
        self::$get = $_GET;
        self::$post = $_POST;

        //request favors posted parameters
        foreach(self::$get as $name => $value)
           self::$data[$name] = $value;

        foreach(self::$post as $name => $value)
            self::$data[$name] = $value;

        foreach(self::$data as $name =>$value)
            if(strpos($name,'_m_')!==false&&strpos($name,'_m_')==0)
                self::$machine[str_replace('_m_','',$name)] = $value;
            
        //this is logic to handle sub machine data (auth, etc)
        foreach(self::$data as $name =>$value)
            if(strpos($name,'_auth_')!==false&&strpos($name,'_auth_')==0)
                self::$auth[str_replace('_auth_','',$name)] = $value;

    }

    public function get($label=false,$type=false)
    {
        if(!$type)
        {
            if(!$label)
                return self::$data;
            return self::$data[$label];
        }
        else if($type=='post')
        {
            if(!$label)
                return self::$post;
            return self::$post[$label];
        }
        else if($type=='get')
        {
            if(!$label)
                return self::$get;
            return self::$get[$label];
        }
    }

    public function machine($label=false)
    {
        //@TODO: array logic for retrival of multi-dim
        
        if(!$label)
            return self::$machine;
        
        return self::$machine[$label];
    }

    function auth()
    {
        return self::$auth;
    }

    public function __clone() { return false;}
}

?>
