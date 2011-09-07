<?php

namespace _m;

class cache
{
    private static $instance;
    
    private static $dataLabel = '__cache__';

    private static $memcache = false;
    
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
        if(self::getSetting(array('memcache','active')))
                self::connect_memcache();
          
    }

    public function __clone() { return false;}

    public function view_get($context,$ext='',$get='')
    {
        $key = view_generate_key($obj,$method,$params);
        if($key===false) return false;
        return self::get($key,$data);
    }
    
    public function view_set($context,$ext='',$get='')
    {
        $key = view_generate_key($obj,$method,$params);
        if($key===false) return false;
        return self::set($key,$data);
    }
    
    public function view_del($context,$ext='',$get='')
    {
        $key = view_generate_key($obj,$method,$params);
        if($key===false) return false;
        return self::del($key,$data);
    }
    
    public function view_valid($context,$ext='',$get='')
    {
        $key = view_generate_key($obj,$method,$params);
        if($key===false) return false;
        return self::valid($key,$data);
    }
    
    
    public function obj_get($obj,$method,$params)
    {
        $key = obj_generate_key($obj,$method,$params);
        if($key===false) return false;
        return self::get($key,$data);
    }
    
    public function obj_set($obj,$method,$params,$data)
    {
        $key = obj_generate_key($obj,$method,$params);
        if($key===false) return false;
        return self::set($key,$data);
    }
    
    public function obj_del($obj,$method,$params)
    {
        $key = obj_generate_key($obj,$method,$params);
        if($key===false) return false;
        return self::del($key,$data);
    }
    
    public function obj_valid()
    {
        $key = obj_generate_key($obj,$method,$params);
        if($key===false) return false;
        return self::valid($key,$data);
    }
    
    public function obj_generate_key($obj,$method,$params)
    {
        $key = md5('obj:'.$obj.':'.$method.':'.serialize($params));
        
        if(sizeof($key)>250) return false;  //key too large to store
        
        return $key;
    }
    
    
    public function view_generate_key($context,$ext='',$get='')
    {
        $key = md5('view:'.$context.':'.$ext.':'.$get);
        
        if(sizeof($key)>250) return false;  //key too large to store
        
        return $key;
    }
    
    public function set($key,$data)
    {
        if(self::$memcache!==false)
            return self::set_memcache($key,$data);
        else 
            return self::set_filesystem($key,$data);
    }
    
    public function get($key)
    {
        if(self::$memcache!==false)
            return self::get_memcache($key);
        else 
            return self::get_filesystem($key);
    }
    
    public function del($key)
    {
        if(self::$memcache!==false)
            return self::del_memcache($key);
        else 
            return self::del_filesystem($key);
    }
    
    public function valid($key)
    {
        if(self::$memcache!==false)
            return self::valid_memcache($key);
        else 
            return self::valid_filesystem($key);
    }
    
    private function get_filesystem($key)
    {
        //@TODO: write
    }
    
    private function set_filesystem($key,$data)
    {
        //@TODO: write
    }
    
    private function del_filesystem($key)
    {
        //@TODO: write
    }
    
    private function valid_filesystem($key)
    {
        //@TODO: write
    }
    
    private function get_memcache($key)
    {
        if(!self::$memcache) return false;
        
        return self::$memcache->get($key);
    }
    
    private function set_memcache($key,$data=false,$expiration=false)
    {
        if(!self::$memcache) return false;
        if($expiration===false&&self::getSetting(array('memcache','expiration')))
                $expiration = self::getSetting(array('memcache','expiration'));
        
        return self::$memcache->set($key,$data,false,$expiration);
    }
    
    private function del_memcache($key)
    {
        if(!self::$memcache) return false;
        
        return self::$memcache->delete($key);
    }
    
    private function valid_memcache($key)
    {
        if(!self::$memcache) return false;
        
        if(self::$memcache->get($key)===false) return false;
        
        return true;
    }
    
    private function connect_memcache()
    { 
        $host = self::getSetting(array('memcache','host'));
        $port = self::getSetting(array('memcache','port'));
        
        if($host===false||$port===false) return false;
       
        $mmc = new \Memcache();
        $con = $mmc->addServer($host, $port);
         
        if($con) self::$memcache = $mmc;
        else self::$memcache = false;
        
        return self::$memcache;
    }
    
    public function get_memcache_object()
    {
        return self::$memcache;
    }
    
    
    public function getSetting($name=false)
    {
        if(is_array($name))
            array_unshift($name,self::$dataLabel);
        elseif($name!==false)
            $name = array(self::$dataLabel,$name);
        else
            return false;
        
        return \_m\settings::get($name);
    }
    
}

?>
