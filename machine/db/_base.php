<?php

namespace _m\db;

class _base
{

    private $con = false;
    private $supportsSQL = true;
    private $result = false;

    public function connect($data)
    {

    }

    public function close()
    {
        
    }

    public function query($query)
    {
        
    }





    public function getCon()
    {
        return $con;
    }

    public function getResult()
    {
        return $result;
    }

    public function supportsSQL()
    {
        return $supportsSQL;
    }

}

?>
