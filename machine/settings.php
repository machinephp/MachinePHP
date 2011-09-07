<?php

namespace _m;

class settings
{
    private static $instance;

    private static $data;

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
        self::readSettings();
        self::setConstants();
    }

    private function readSettings()
    {
        //include general settings
        if(!file_exists(_M_SETTINGS_ROOT.'settings.php'))
            throw new mException('Settings [main] file does not exist.');

        require _M_SETTINGS_ROOT.'settings.php';

        self::$data = $settings;

        //include auth settings
        if(!file_exists(_M_SETTINGS_ROOT.'auth.php'))
            throw new mException('Settings [authentication] file does not exist.');

        require _M_SETTINGS_ROOT.'auth.php';
        
        self::$data['__auth__']['tables']       = $tables;
        self::$data['__auth__']['groups']       = $groups;
        self::$data['__auth__']['types']        = $types;
        self::$data['__auth__']['users']        = $users;

        if(!file_exists(_M_SETTINGS_ROOT.'db.php'))
            throw new mException('Settings [database] file does not exist.');

        require _M_SETTINGS_ROOT.'db.php';

        self::$data['__db__']['connections']    = $connections;
        self::$data['__db__']['primary']        = $primary;
        
        
        if(!file_exists(_M_SETTINGS_ROOT.'cache.php'))
            throw new mException('Settings [cache] file does not exist.');

        require _M_SETTINGS_ROOT.'cache.php';

        self::$data['__cache__']['memcache']    = $memcache;
        self::$data['__cache__']['filesystem']  = $filesystem;
        
    }



    private function setConstants()
    {
        define('_M_APP_ROOT',_M_FILE_ROOT.'app/');
        define('_M_EXTENSION_ROOT',_M_FILE_ROOT."extensions/");
        define('_M_OBJECT_ROOT',_M_FILE_ROOT."objects/");
        define('_M_SOURCE_ROOT',_M_FILE_ROOT."webroot/source/");
        
        define('DOMAIN_NAME',self::get('domain_name'));

        if(self::get('folder_root'))
            define('FOLDER_ROOT',self::get('folder_root').'/');
    }

    

    public function __clone() { return false;}






    public function get($name=false)
    {
        if($name===false)
            return false;

        $data = self::$data;
        
        if(is_array($name))
        {
            while(sizeof($name)>0)
            {
                $cur = array_shift($name);
                if(isset($data[$cur]))
                        $data = $data[$cur];
                else
                    return false;

                if(!sizeof($name)) return $data;
            }
        }

        if(isset($data[$name]))
            return $data[$name];

        return false;
    }

    public function set($name=false,$value=false)
    {
        //@TODO: handle nested requests by parsing name/name
        
        if($name!==false)
            return self::$data[$name] = $value;
        else
            return false;

    }

    public function del($name=false)
    {
        //@TODO: handle nested requests by parsing name/name
        
        if($name===false)
            return false;

        unset(self::$data[$name]);

        return true;
    }

    public function _dx_allData()
    {
        return self::$data;
    }
}

?>
