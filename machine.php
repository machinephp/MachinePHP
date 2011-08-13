<?php

class _m
{

    private static $instance;

    public static $settings;
    public static $context;

    public static $mode = false;   //normal(false), proc, data, view, component //@TODO: implement mode check
    public static $format = false; //json, also to be used for custom output

    public static $error = array();
    public static $errorInternal = array();
    public static $message = array();
    
    public static $title = '';

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

        try
        {
            \_m\settings::load();

            \_m\session::load();

            \_m\data::load();

            \_m\debug::load();      //- later edition

            \_m\service::load();

            \_m\client::load();
            
            \_m\req::load();

            \_m\context::load();

            \_m\cache::load();      //- later edition

            \_m\db::load();

            \_m\auth::load();

            \_m\script::load();
            
            \_m\execute::load();

            \_m\out::load();
            
            \_m\session::saveData();
        }
        catch(mException $e)
        {
            //@TODO: include default template on total error
            //temporary error message
            echo '<div style="padding:40px; text-align:center;border: solid black 2px; margin:20px; background-color:red; color: white; font-size:12px;">
                    <div style="font-weight:bold; font-size:14px;">MACHINE EXECUTION FAIL</div><br>
                    <i>'.$e->getMessage().'</i>
                        <br>
                        </div>
                        ';
        }
    }

    

    public function getMode()
    {
        return self::$mode;
    }

    public function getFormat()
    {
        return self::$format;
    }

    public function data($name=false,$value=false)
    {
            if($name!==false&&$value!==false)
                return _m\data::set($name,$value);
            else if($name!==false)
                return _m\data::set($name,$value);
            else
                return false;
    }

    
    public function __clone() { return false;}


    
    public function title($title=false)
    {
        if($title) self::$title = $title;
        return self::$title;
    }
    
    //error and message handiling
    
    //alias for setError
    public function error($error=false,$internal=false)
    {
        return self::setError($error,$internal);
    }
    
    //alias for setMessage
    public function message($message=false,$internal=false)
    {
        return self::setMessage($message,$internal);
    }
    
    public function setError($error=false,$internal=false)
    {
        if($error&&$internal)
            self::$errorInternal[] = $error;
        else if($error)
            self::$error[] = $error;
        else if($internal)
            self::$internal = array();
        else
            self::$error = array();

        //Always returns false to allow for one line fail case statements
        return false;
    }

    public function setMessage($message=false)
    {
        if($message)
            self::$message[] = $message;
        else
            self::$message = array();
    }

    //@TODO: remove internal
    public function getError($internal=false,$format=false)
    {
        if($internal)
        {
            $errorInternal = self::$errorInternal;
            self::$errorInternal = array();
            $ret = $errorInternal;
        }
        else
        {
            $error = self::$error;
            self::$error = array();
            $ret =  $error;
        }
        
        if($format=='html')
        {
            $html = '';
            foreach($ret as $line)
                $html .= $line.'<br>';

            return $html;
        }

        if($format=='text')
        {
            $text = '';
            foreach($ret as $line)
                $text .= $line."\n";

            return $text;
        }

        return $ret;
    }


    
    
    public function getMessage($format=false)
    {
        if(self::$message==array())
            return array();

        $message = self::$message;
        self::$message = array();

        if($format=='html')
        {
            $html = '';
            foreach($message as $line)
                $html .= $line.'<br>';

            return $html;
        }

        if($format=='text')
        {
            $text = '';
            foreach($message as $line)
                $text .= $line."\n";

            return $text;
        }

        return $message;
    }

    public function go($url=false)
    {
        if(!$url) return false;
        //@TODO: write so that it changes context more intelligently

        //@TODO: write that if it is a proc or data call that the information be passed back to the machine
        //           JS Class so that the context is changed via the front controller
        header('Location: '.$url);
    }


    public function inc($filename)
    {
        if(!file_exists(_M_FILE_ROOT.'include/'.$filename))
            return false;
        
        include _M_FILE_ROOT.'include/'.$filename;
        
        return true;
    }


}

//-  Autoload method override
function __autoload($fullClassName)
{

    //exception for machine exception
    if($fullClassName=='_m\mException')
        return true;
    
    $classNameParts = explode("\\",$fullClassName);

    if($classNameParts[0]=='')
        array_shift($classNameParts);
    
    $size = sizeof($classNameParts);

    $file = _M_FILE_ROOT;
    $file2 = _M_FILE_ROOT;
    

    if($size>1)
    {
        if($classNameParts[0]=='_m')
            $classNameParts[0]='machine';

        
        if($classNameParts[1]=='app')
        {
            array_shift($classNameParts);
            $size = sizeof($classNameParts);
        }
        
        if($classNameParts[1]=='obj')
        {
            for($i=0;$i<($size-1);$i++)
                $file2 .= $classNameParts[$i].'/';
            
            $file2 .= $classNameParts[($size-1)].'.php';
            
            array_shift($classNameParts);
            $size = sizeof($classNameParts);
        }

        for($i=0;$i<($size-1);$i++)
            $file .= $classNameParts[$i].'/';
        
        $file .= $classNameParts[($size-1)].(($classNameParts[0]=='app')?'.con':'').'.php';
        
        
    }
    else
        $file = $classNameParts[0].'.php';


    //@DEBUG: echo $fullClassName.' '.$file.'<br>';

    if(file_exists($file))
        require_once $file;
    elseif(($file2!=_M_FILE_ROOT)&&file_exists($file2))
        require_once $file2;
    else
        throw new mException('REQUIRE FAIL '.$file);

    //@TODO: throw error if file not found
    //@TODO: include classes without the machine class name from another dir
}

class mException extends Exception
{
    //@TODO: write
}


//@TODO: functionality

?>
