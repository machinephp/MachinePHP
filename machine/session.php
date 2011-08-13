<?php

namespace _m;

class session
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
        //@TODO: start the session if does not exist
        //  use secure method to generate key
        //  if key defined in request, utilize that
        session_start();
    }

    public function loadData()
    {
        self::$data = self::decryptData($_SESSION['__machine__']);
        //print_r(self::$data);
        return self::$data['__machine__']['data'];
    }

    public function saveData()
    {
        self::$data['__machine__']['data'] = \_m\data::getPersist();
        $_SESSION['__machine__'] = self::encryptData(self::$data);
        session_write_close();
    }

    public function get($name=false)
    {
        //@TODO: use array logic for multiple dimentions requests
        if(!$name) return false;

        if(isset(self::$data[$name]))
            return self::$data[$name];
        return false;
    }

    public function set($name=false,$value=false)
    {
        //@TODO: use array logic for multiple dimentions requests
        if(!$name) return false;

        return self::$data[$name] = $value;
    }

    public function getMachineData($type)
    {
        return self::$data['__machine__'];
    }

    private function encryptData($data)
    {
        //@TODO: encrypt the data being placed in the session
        return $data;
    }

    private function decryptData($data)
    {
        //@TODO: decrypt the data returning from the session
        return $data;
    }

    public function __clone() { return false;}
}

?>
