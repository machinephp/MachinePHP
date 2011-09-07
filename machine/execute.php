<?php

namespace _m;

class execute
{
    private static $instance;

    private static $controller = false;

    private static $object = false;

    private static $procResult = false;
    private static $dataResult = false;
    private static $objResult = false;

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
        $mode = \_m::getMode();

        if($mode!='obj'&&$mode!='comp')
        {
            $controllerClass = \_m\context::getControllerClass();
            
            if($controllerClass)
                self::$controller = new $controllerClass;
            else
                self::$controller = new \_m\controller;

             self::$controller->rcvr();
        }
        
        switch($mode)
        {
            case 'data':
                //self::$dataResult = array(4,32,43);
                self::$dataResult = self::$controller->data();
                break;

            case 'proc':
                self::$procResult = self::$controller->proc();
                break;

            case 'obj':
                self::$objResult = self::callObjectMethod();
                break;
            
            case 'comp':
                break;
            
            default:
                self::$procResult = self::$controller->proc();
                self::$dataResult = self::$controller->data();
                break;
        }
        
    }

    public function __clone() { return false;}

    public function getProcResult()
    {
        return self::$procResult;
    }

    public function getDataResult()
    {
        return self::$dataResult;
    }

    public function getObjResult()
    {
        return self::$objResult;
    }

    private function callObjectMethod()
    {
         
        //@LAST: next step is to locate the object/method and execute it
        $parts = \_m\context::getExtParts();
        
        if(\_m\req::machine('obj_name'))
            $obj = \_m\req::machine('obj_name');
        else if(isset($parts[0]))
            $obj = $parts[0];
        else
            return \_m::setError('Object not defined',true);

        if(!self::checkObjectExists($obj))
            return \_m::setError('Object does not exist',true);

        if(\_m\req::machine('obj_method_name'))
            $method = \_m\req::machine('obj_method_name');
        else if(isset($parts[1]))
            $method = $parts[1];
        else
            return \_m::setError('Method not defined',true);

        if(!self::checkMethodExists(self::$object, $method))
            return \_m::setError('Object does not exist',true);

        
        if(\_m\request::machine('obj_params'))
            $params = \_m\request::machine('obj_params');
        else if(isset($parts[2]))
            $params = $parts[2];
        else
            $params = array();
        
        return call_user_func_array(array(self::$object,$method),$params);
        /*
        switch(sizeof($params))
        {
            case 0:
                return self::$object->$method();
            case 1:
                return self::$object->$method($params[0]);
            case 2:
                return self::$object->$method($params[0],$params[1]);
            case 3:
                return self::$object->$method($params[0],$params[1],$params[2]);
            case 4:
                return self::$object->$method($params[0],$params[1],$params[2],$params[3]);
            case 5:
                return self::$object->$method($params[0],$params[1],$params[2],$params[3],$params[4]);
            case 6:
                return self::$object->$method($params[0],$params[1],$params[2],$params[3],$params[4],$params[5]);
        }
         * */
         
        
    }

    private function checkObjectExists($name)
    {
        $objFileName = _M_FILE_ROOT.'obj/'.$name.'.php';
        
        if(!file_exists($objFileName))
            return false;

        require_once($objFileName);

        $objName = '_m\obj\\'.$name;

        self::$object = new $objName;

        return true;

    }

    private function checkMethodExists($object, $methodName)
    {
        if(!$object)
            return false;

        if(method_exists($object,$methodName))
            return true;

        return false;
    }
}



?>
