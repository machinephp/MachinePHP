<?php

namespace _m;

class auth
{
    private static $instance;

    private static $dataLabel = '__auth__';
    
    public static $oauth;
    
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
        \_m\data::persist(self::$dataLabel);
        
        $settings = \_m\settings::get(self::$dataLabel);
    }
    
    public function __clone() { return false;}

    public function check($group=false,$id=false)
    {
        if(!self::getData('type')) return false;
        
        if(self::getData('type')=='standard')
             return \_m\auth\standard::check();
         else
         {
             $class = '\_m\auth\\'.self::getData('type');
             return $class::check($group,$id);
         }
        
        return self::getData('id');
    }

    public function login($type=false,$data=false)
    {    
         if(!$type)
         {
             if(\_m\auth\standard::login($data))
             {
                 \_m\auth::setData('type','standard');
                 return true;
             }
         }
         else
         {
             $class = '\_m\auth\\'.$type;
             if($class::login($data))
             {
                 \_m\auth::setData('type',$type);
                 return true;
             }
         }
    }

    public function logout($type=false,$url=false)
    {
        if(!$type) $type=self::getData('type');
        //@TODO: catch return
        if(!$type)
             \_m\auth\_base::logout($url);
         else
         {
             $class = '\_m\auth\\'.$type;
             $class::logout($data);
         }
         
         self::delData();
         
    }
    
    public function connect($type=false,$url=false)
    {
        if(!$type)
             return \_m\auth\_baseOAuth::connect($url);
         else
         {
             $class = '\_m\auth\\'.$type;
             return $class::connect($data);
         }
         self::delData();
    }


    public function getData($name=false)
    {
        if(is_array($name))
            array_unshift($name,self::$dataLabel);
        elseif($name!==false)
            $name = array(self::$dataLabel,$name);
        else
            $name = self::$dataLabel;

        return \_m\data::get($name);
    }

    public function setData($name=false,$value=false)
    {
        if(is_array($name))
            array_unshift($name,self::$dataLabel);
        elseif($name!==false)
            $name = array(self::$dataLabel,$name);

        return \_m\data::set($name,$value);
    }

    public function delData($name=false)
    {
        if(is_array($name))
            array_unshift($name,self::$dataLabel);
        elseif($name!==false)
            $name = array(self::$dataLabel,$name);
        else
            $name = self::$dataLabel;

        return \_m\data::del($name);
    }

    public function getGroupInfo($groupID=false)
    {
        if(!$groupID)
            return false;
        
        //@TODO: get from auth settings

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

    //@TODO: handle information retrival

}

?>
