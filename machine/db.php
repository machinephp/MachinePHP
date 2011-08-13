<?php
namespace _m;

define('SQL_AND',' AND ');
define('SQL_OR',' OR ');
define('SQL_IN',' IN ');
define('SQL_CUSTOM',9999);

class db
{
    private static $instance;

    private static $dataLabel = '__db__';

    public static $connections = array();
    public static $primary = false;

    public static $sql = '';

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
        $data = \_m\settings::get(self::$dataLabel);

        if(isset($data['primary']))
            self::$primary = $data['primary'];

        foreach($data['connections'] as $label => $connection)
            self::connect($connection,$label);
    }

    public function connect($data,$label)
    {
   
        //@TODO: from here down to self::con
        // name, type, server, user, pass, (db,collection)
        if(!isset($data['type']))
            return \_m::setError('Database type not defined.');


        $typeClass = '\_m\db\\'.$data['type'];
        try { $obj = new $typeClass;}
        catch(Exception $e) { return \_m::setError('Database type "'.$data['type'].'" does not exist.');}

        if(!$obj->connect($data))
                return  \_m::setError('Unable to connect to the database server.');

        self::$connections[$label] = $obj;
        
    }

    public function isConnected()
    {
        //@TODO: re-write for multiple connections
        if(!self::$connections[self::$primary])
            return false;
        else
            return true;
    }

    public function __clone() { return false;}


    //@TODO: how do you handle from here down?
    public function close()
    {
        foreach(self::$connections as $connection)
            $connection->close();
    }

    public function transBegin()
    {
        if(mysql_query("BEGIN"))
            return true;
        else
            return false;
    }

    public function transCommit()
    {
        if(mysql_query("COMMIT"))
            return true;
        else
            return false;
    }

    public function transRollback()
    {
         mysql_query("ROLLBACK");
         //@TODO: decide how to deal with return value
         return false;
    }

    
    
    
    

    public function query($query,$connection=false)
    {
        if($connection===false) $connection = self::$primary;
        if($connection===false) return false;
        if(!isset(self::$connections[$connection])) return false;
        
        //@TODO: add SQL test
        return self::$connections[$connection]->query($query);

    }



    public function numRows($result=false,$connection=false)
    {
        if($connection===false) $connection = self::$primary;
        if($connection===false) return false;
        if(!isset(self::$connections[$connection])) return false;
        if(!method_exists(self::$connections[$connection],'numRows')) return false;

        return self::$connections[$connection]->numRows();
    }
    
    public function foundRows($result=false,$connection=false)
    {
        if($connection===false) $connection = self::$primary;
        if($connection===false) return false;
        if(!isset(self::$connections[$connection])) return false;
        if(!method_exists(self::$connections[$connection],'foundRows')) return false;

        return self::$connections[$connection]->foundRows();
    }
    
    public function getRowAssoc($result=false,$connection=false)
    {
        if($connection===false) $connection = self::$primary;
        if($connection===false) return false;
        if(!isset(self::$connections[$connection])) return false;
        if(!method_exists(self::$connections[$connection],'getRowAssoc')) return false;

        return self::$connections[$connection]->getRowAssoc($result);
    }
    
    public function getResultArray($result=false,$connection=false)
    {   
        
        if($connection===false) $connection = self::$primary;
        if($connection===false) return false;
        if(!isset(self::$connections[$connection])) return false;
        if(!method_exists(self::$connections[$connection],'getResultArray')) return false;
        return self::$connections[$connection]->getResultArray($result);
    }

    public function lastID($connection=false)
    {
        if($connection===false) $connection = self::$primary;
        if($connection===false) return false;
        if(!isset(self::$connections[$connection])) return false;
        if(!method_exists(self::$connections[$connection],'lastID')) return false;

        return self::$connections[$connection]->lastID($result);
    }
    
    
    
    public function prepData($data)
    {
        if(is_array($data))
        {
            foreach($data as $key => $value)
            {
                $data[$key] = $this->prepData($value);
            }
        }
        else
        {
            $data = stripslashes($data);
        }

        return $data;

    }

    public function cleanData($data)
    {
        if(is_array($data))
        {
            foreach($data as $key => $value)
            {
                $data[$key] = $this->cleanData($value);
            }
        }
        else
        {
            $data = addslashes(trim($data));
        }

        return $data;

    }

    
    

    //@TODO: these work, but need to be limited to databases that support SQL

    public function insert($table=false,$data=false,$connection=false)
    {
        if(!$table)
            return false;

        $insertSQL = 'INSERT INTO '.$table.' SET ';


        $insertFields = array();
        foreach($data as $name => $value)
            $insertFields[] = " ".$name."='".$value."' ";

        $insertSQL .= implode(' , ',$insertFields);

        self::$sql =  $insertSQL;
        
        //if(!self::query($insertSQL,$connection)) return false;
        //@DEV
        if(!self::query($insertSQL,$connection)) return \_m::setError($insertSQL,true);
        
        return (self::lastID($connection)?self::lastID($connection):true);
    }
    
    public function delete($table=false,$where=false,$limit=false,$connection=false)
    {
        $deleteSQL = "DELETE FROM ".$table." ";
        
        if(!is_array($where))
            $deleteSQL .= " WHERE ".strtolower($table)."_id=".$where;
        else
            $deleteSQL .= " WHERE ".self::whereBuilder($where);

        self::$sql =  $deleteSQL;

        
        return self::query($deleteSQL,$connection);
    }

    public function update($table=false,$data=false,$where=false,$limit=false,$connection=false)
    {
        if(!$table)
            return false;

        $updateSQL = 'UPDATE '.$table.' SET ';

        $updateFields = array();
        foreach($data as $name => $value)
            $updateFields[] = " ".$name."='".$value."' ";

        $updateSQL .= implode(' , ',$updateFields);
        
        
        $whereSQL = '';
        if($where)
        {
            $whereSQL = ' WHERE ';
            if(is_array($where))
            {
                $whereSQL .= self::whereBuilder($where);
            }
            else
                $whereSQL .= $where;
        }
        
        $updateSQL .= " ".$whereSQL;
        
        $limitSQL = '';
        if($limit)
            $limitSQL = 'LIMIT '.$limit;
        
        $updateSQL .= " ".$limitSQL;
        
        self::$sql =  $updateSQL;
    
        return self::query($updateSQL,$connection);
    }

    public function select($table=false,$fields=false,$where=false,$order=false,$limit=false,$connection=false)
    {
        //@TODO: handle both array and single variables for all
        
        if(!$table)
            return false;
        
        if(is_array($table))
        {
            $tableSQL = $table[0];
            for($i=1;$i<sizeof($table);$i++)
                $tableSQL .= ', '.$table[$i];
        }
        else
            $tableSQL = $table;

        $fieldsSQL = '*';
        if($fields)
        {
            if(is_array('fields'))
            {
                $fieldsSQL = ' ';
                foreach($fields as $key => $value)
                    $fieldsSQL .= ($key?', ':' ').$table.'.'.$value.' ';
            }
            else
                $fieldsSQL = $fields;
        }

        $whereSQL = '';
        if($where)
        {
            $whereSQL = ' WHERE ';
            if(is_array($where))
            {
                $whereSQL .= self::whereBuilder($where);
            }
            else
                $whereSQL .= $where;
        }

        $orderSQL = '';
        if($order)
        {
            $orderSQL = ' ORDER BY ';
            if(is_array($order))
            {
                foreach($order as $key => $value)
                    $orderSQL .= ($key?', ':' ').$value.' ';
            }
            else
                $orderSQL .= $order;
        }

        $limitSQL = '';
        if(is_array($limit))
            $limitSQL = 'LIMIT '.$limit[0].', '.$limit[1];
        elseif($limit)
            $limitSQL = 'LIMIT '.$limit;

        $selectSQL = 'SELECT '.$fieldsSQL.' FROM '.$tableSQL.' '.$whereSQL.' '.$orderSQL.' '.$limitSQL.';';

        self::$sql =  $selectSQL;
        //echo $selectSQL;
        if(!self::query($selectSQL,$connection)) return false;

        return self::getResultArray();
    }

    public function getSQL()
    {
        return self::$sql;
    }
    
    private function whereBuilder($where=false,$op=SQL_AND)
    {
        if(!$where) return '';
        
        $whereSQL = array();
        foreach($where as $element)
        {
            foreach($element as $key => $value)
            {
                if($key==SQL_AND||$key==SQL_OR)
                    $whereSQL[] = ' ( '.self::whereBuilder($value,$key).' ) ';
                elseif($key==SQL_IN)
                {
                    $name = $value[0];
                    $data = $value[1];
                    foreach($data as $key => $value)
                        $data[$key] = "'".$value."'";
                    $dataStr = implode(', ',$data);
                    $whereSQL[] = $name.' IN ('.$dataStr.')';
                }
                elseif($key==SQL_CUSTOM)
                    $whereSQL[] = ' '.$value.' ';
                else
                {
                    if(strpos($value,'`')!==false)
                        $whereSQL[] = " ".$key."=".str_replace('`','',$value)." ";
                    else
                        $whereSQL[] = " ".$key."='".$value."' ";
                }
            }
        }
                
        return implode(' '.$op.' ',$whereSQL);
    }




}

?>
