<?php

//NOTE: all data persists, until further notice

namespace _m;

class data
{
    private static $instance;
    
    private static $data;
    private static $persist;

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
        self::setPersist();
    }

    public function __clone() { return false;}

    public function get($name=false)
    {
        
        if($name===false)
            return self::$data;
            //return false; //@TODO: in the future, return false, do not dump entire obj

        //@TODO: find a more efficent way then copyting the entire data object.
        if(is_array($name))
        {
            $data = self::$data;
            foreach($name as $part)
                $data = $data[$part];
            
            return $data;
        }

        if(isset(self::$data[$name]))
            return self::$data[$name];

        return false;
    }

    public function set($name=false,$value=false,$persist=true)
    {
        if(is_array($name))
        {
            //if($persist)
            //    return self::setVar(self::$data,$name,$value,self::$persist);
            //else
                return self::setVar(self::$data,$name,$value,false);
        }
        elseif($name!==false)
        {
            //self::$persist[$name] = true;
            return self::$data[$name] = $value;
        }
        else
            return false;
    }

    public function setVar(&$var,$name=false,$value) //,&$persist
    {
       if($name==false)
            return $var = $value;
        elseif(is_array($name))
        {
            $cur = array_shift($name);
            if(sizeof($name)==0)
            {
                //if($persist)
                //    $persist[$cur] = true;
                return $var[$cur] = $value;
            }
            elseif(sizeof($name)>0)
            {
                if(!isset($var[$cur]))
                    $var[$cur] = array();
                //if($persist)
                //{
                //    $persist[$cur] = array();
                //    return self::setVar($var[$cur],$name,$value,$persist[$cur]);
                //}
                //else
                    return self::setVar($var[$cur],$name,$value); //,false
            }
        }

        return false;
    }

    public function del($name=false)
    {
        if($name===false)
            return false;

        unset(self::$data[$name]);
        unset(self::$persist[$name]);
        
        return true;
    }
    

    public function persist($name=false,$persist=true)
    {
        if($name===false)
            return false;

        //if(!isset(self::$data[$name]))    //this logic does not set persis when no data exists
        //    return false;                 //do not see a reason for this test anymore

        if($persist)
            self::$persist[$name] = true;
        else
            unset(self::$persist[$name]);

        return true;
    }

    
    public function getPersist($data=false,$persist=false)
    {
        return self::$data; //@TODO: short circuit
        if(!$data) $data = self::$data;
        if(!$persist) $persist = self::$persist;
        
        $return = array();
        foreach($persist as $name => $value)
        {
            if(isset($data[$name]))
                $return[$name] = $data[$name];
           // elseif(is_array($persist[$name]))
           //     $return[$name] = self::getPersist($data,$persist);
        }
         
        return $return;
    }

    public function setPersist($data=false)
    {
        if(!$data)
            $data = \_m\session::loadData();

        if(!is_array($data)) return false;

        self::$data = $data;
        return true; //@TODO: short circuit
        
        
        
        foreach($data as $name => $value)
        {
            self::$persist[$name] = true;
            self::$data[$name] = $value;
        }

        return true;
    }

    public function _dx_allData()
    {
        return self::$data;
    }
    
}

?>
