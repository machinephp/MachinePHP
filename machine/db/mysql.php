<?php

namespace _m\db;

class mysql extends _base
{

    public function connect($data)
    {

        $this->con = mysql_connect($data['host'],$data['user'],$data['pass']);

        if(!$this->con) return false;

        if(!mysql_select_db($data['schema'],$this->con)) return false;

        return $this->con;
    }

    public function query($query=false)
    {
        if(!$query) return false;

        $this->result = mysql_query($query,$this->con);
        return $this->result;
    }

    public function getResultArray($result=false)
    {
        
        if($result===false) $result = $this->result;
        if($result===false) return false;

        $resultArray = array();

        
        while(($row=$this->getRowAssoc($result)))
            $resultArray[] = $row;
        
        return $resultArray;

    
    }

    public function getRowAssoc($result=false)
    {
        
        if($result===false) $result = $this->result;
        if($result===false) return false;
        return mysql_fetch_assoc($result);
    }

    public function numRows($result=false)
    {
        if($result===false) $result = $this->result;
        if($result===false) return false;
        
        return mysql_num_rows($result);
    }
    
    public function foundRows($result=false)
    {
        $res = mysql_query("SELECT FOUND_ROWS() AS `found_rows`;",$this->con);
        if($res===false) return 0;
        if(!mysql_num_rows($res)) return 0;
        $row = mysql_fetch_assoc($res);
        return $row['found_rows'];
    }
    
    
    
    
    public function lastID()
    {
        return mysql_insert_id($this->con);
    }

}

?>
