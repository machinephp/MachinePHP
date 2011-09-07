<?php

namespace _m;

class context
{
    private static $instance;

    private static $fullPath;               //the path information provided by the client [with query string removed]
    private static $queryString = false;    //Full query string.

    private static $path;                       //the path at which a suitable view/controller was found
    private static $pathParts;                  //      --split into an array
    private static $ext;                        //the remainder of the fullPath after the path
    private static $extParts;                   //      --split into an array


    private static $name;                       //TBD
    private static $label;                      // ie: /test/great becomes testGreat , may not have a purpose shortly
    private static $viewFile = false;           //Full path of the view file
    private static $templateFile = false;       //Full path of the associated template file
    private static $scriptFile = false;         //Full path of the script file
    private static $controllerFile = false;     //Full path of the controller file *not neded due to namespaces & autoload

    private static $controllerClass = false;

    private static $info;
    private static $current;

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
        self::$fullPath = self::determineModeAndFormat(self::parse());
        self::$pathParts = self::determinePathParts(self::$fullPath);

        if(\_m::$mode=='obj')
            list($files, $ext, $path, $class) = self::determineObj(self::$fullPath);
        else
            list($files, $ext, $path, $class) = self::determine(self::$fullPath);

        self::$viewFile = $files['view'];
        self::$controllerFile = $files['controller'];   //may be useless
        self::$scriptFile = $files['script'];
        self::$controllerClass = $class;
        self::$ext = $ext['string'];
        self::$extParts = $ext['obj'];
        self::$path = $path;
        self::$label = self::pathToLabel($path);        //may be useless

        self::readContextInfo();
        self::setCurrentContextInfo();

        //@TODO: handle root dir '/'
    }

    public function __clone() { return false;}

    private function parse()
    {
        self::$path = $_SERVER['REQUEST_URI'];
        if($_SERVER['QUERY_STRING']!='')
            self::$queryString = $_SERVER['QUERY_STRING'];
        if(self::$queryString)
            self::$path = str_replace('?'.self::$queryString,'',self::$path);

        self::$pathParts = self::determinePathParts(self::$path);

        return self::$path;
    }

    public function determinePathParts($path=false)
    {
        if(!$path) return false;

        $arr = explode('/',$path);
        array_shift($arr);
        return $arr;
    }

    public function determine($path=false)
    {
        if(!$path)
            $pathParts = self::getPathParts();
        else
            $pathParts = self::determinePathParts($path);

        
        $files['controller'] = false;
        $files['view'] = false;
        $files['script'] = false;
        $cpSize = sizeof($pathParts);
        $cpOriginalSize = $cpSize;

        $class = false;
        $classTemp = '_m\app';
        $path = '';
        $pathTemp = '';
        $dir = '';

        
        //loop on the path parts which comprise the directory
        //@TODO: add index hack (if someone types /admin, it will look for and use /admin/index.php as a priorty over /admin.php)
        for($e=0;$e<$cpSize;$e++)
        {
            
            $pathTemp .= '/'.lcfirst($pathParts[$e]);
            $classTemp  .= '\\'.lcfirst($pathParts[$e]);

            if($e!=0)
                $dir .= $pathParts[($e-1)].'/';

            $file = lcfirst($pathParts[$e]);

            $temp['controller'] = false;
            $temp['view'] = false;
            $temp['script'] = false;
            
            if((file_exists(_M_APP_ROOT.$dir.$file.'.php')||file_exists(APP_ROOT.$dir.$file.'.con.php')||file_exists(APP_ROOT.$dir.$file.'.js'))||
                 ((file_exists(_M_APP_ROOT.$dir.$file.'/index.php')||file_exists(APP_ROOT.$dir.$file.'/index.con.php')||file_exists(APP_ROOT.$dir.$file.'/index.js')))     )
            {
                 if(file_exists(_M_APP_ROOT.$dir.$file.'.con.php'))
                    $temp['controller'] = _M_APP_ROOT.$dir.$file.'.con.php';
                 elseif(file_exists(_M_APP_ROOT.$dir.$file.'/index.con.php'))
                    $temp['controller'] = _M_APP_ROOT.$dir.$file.'/index.con.php';

                 if(file_exists(_M_APP_ROOT.$dir.$file.'.php'))
                    $temp['view'] = _M_APP_ROOT.$dir.$file.'.php';
                 elseif(file_exists(_M_APP_ROOT.$dir.$file.'/index.php'))
                    $temp['view'] = _M_APP_ROOT.$dir.$file.'/index.php';

                 if(file_exists(_M_APP_ROOT.$dir.$file.'.js'))
                    $temp['script'] = _M_APP_ROOT.$dir.$file.'.js';
                 elseif(file_exists(_M_APP_ROOT.$dir.$file.'/index.js'))
                    $temp['script'] = _M_APP_ROOT.$dir.$file.'/index.js';
            }
            
            if($temp['controller']||$temp['view'])
            {
                $files = $temp;
                $path = $pathTemp;
                if($temp['controller'])
                    $class = $classTemp;
                $cpSizeNew = $e;
            }

         }
        $cpSize = $cpSizeNew;
        $ext['obj'] = array();
        $ext['string'] = '';
        
        if($cpSize!=$cpOriginalSize)
        {
            $cpDiff = $cpOriginalSize - $cpSize;
            for($i=($cpOriginalSize-$cpDiff+1);$i<$cpOriginalSize;$i++)
            {
                $ext['obj'][] = $pathParts[$i];
                unset($pathParts[$i]);
            }

            if(is_array($ext['obj']))
                $ext['string'] = implode('/',$ext['obj']);
            else
                $ext['string'] = $ext['obj'];
        }
        
        return array($files,$ext,$path,$class);
    }

    public function determineObj($path=false)
    {
        if(!$path) return false;

        $pathParts = self::determinePathParts($path);

        $class = '_m\obj';
        $path = '';
        $ext['obj'] = $pathParts;
        $ext['string'] = $path;
        $files['controller'] = false;
        $files['view'] = false;
        $files['script'] = false;
        
        return array($files,$ext,$path,$class);
    }

    private function determineModeAndFormat($path)
    {

        $parts = self::determinePathParts($path);

        //$var = preg_grep("/_-_(.*?)_-_/",$parts[0]);
        //@TODO: use regular expression for both components

        switch($parts[0])
        {
            case '_-_proc_-_':  //process
                \_m::$mode = 'proc';
                array_shift($parts);
                break;
            case '_-_data_-_':
                \_m::$mode='data';
                array_shift($parts);
                break;
            case '_-_obj_-_':
                \_m::$mode='obj';
                array_shift($parts);
                break;
            case '_-_view_-_':
                \_m::$mode='view';
                array_shift($parts);
                break;
            case '_-_comp_-_':
                \_m::$mode = 'comp';
                array_shift($parts);
                break;
        }

        switch($parts[0])
        {
            case '_-_json_-_':  //process
                \_m::$format = 'json';
                array_shift($parts);
                break;
        }

        return '/'.implode('/',$parts);
    }

    private function readContextInfo()
    {
        if(!file_exists(_M_SETTINGS_ROOT.'context.php'))
            throw new mException('Context file does not exist.');

        include _M_SETTINGS_ROOT.'context.php';

        self::$info = $context;
    }

    private function setCurrentContextInfo()
    {
        //-load settings for root
        if(isset(self::$info['/']))
                foreach(self::$info['/'] as $key => $info)
                    self::$current[$key] = $info;

        //- iterate through path from lowest to highest folder
        foreach(self::getPathParts() as $contextPart)
        { 
            $curContext .= '/'.$contextPart;
            
            if(isset(self::$info[$curContext]))
                foreach(self::$info[$curContext] as $key => $info)
                    self::$current[$key] = $info;
        }

        self::setTemplate();
    }

    public function pathToLabel($path=false)
    {
        
        if(!$path)
            return '';

        $parts = self::determinePathParts($path);

        $label = strtolower(array_shift($parts));
        
        foreach($parts as $part)
            $label .= ucfirst(strtolower($part));

        return $label;
    }

    //accessor methods
    public function getPath()
    {
        return self::$path;
    }

    public function getPathParts($pos=false)
    {
        if($pos===false)
            return self::$pathParts;
        
        if(isset(self::$pathParts[$pos]))
            return self::$pathParts[$pos];

        return false;
    }

    public function getControllerClass()
    {
        return self::$controllerClass;
    }

    public function getExt()
    {
        return self::$ext;
    }

    public function getExtParts($pos=false)
    {
        if($pos===false)
            return self::$extParts;
        
        if(isset(self::$extParts[$pos]))
            return self::$extParts[$pos];

        return false;
    }

    public function getViewFile()
    {
        return self::$viewFile;
    }

    public function getControllerFile()
    {
        return self::$controllerFile;
    }

    public function getScriptFile()
    {
        return self::$scriptFile;
    }

    public function getTemplateFile()
    {
        //@TODO: all starting in _templates dir, check exist, return full filename
        return self::$templateFile;
    }

    public function getQueryString()
    {
        return self::$queryString;
    }

    public function getInfo($path=false)
    {
        if(!$path) return self::$current;
        
        if(isset(self::$info[$path]))
            return self::$info[$path];
        else
            return false;

        //@TODO: use iterative include logic to get hierarchal information
    }

    public function getAllInfo()
    {
        return self::$info;
    }

    public function setTemplate($name=false)
    {
        //@TODO: this method never allows the template to be set false.
        if(!$name&&isset(self::$current['template']))
            $name = self::$current['template'];

        if($name===false)
            return self::$templateFile = false;

        $templateFile = _M_APP_ROOT.'_templates/'.$name.'.php';

        if(!file_exists($templateFile))
            return self::$templateFile = false;

        return self::$templateFile = $templateFile;
    }

    public function setPathParts($parts=false)
    {
        if(!$parts)
            return false;

        return self::$pathParts = $parts;
    }

}

?>
