<?php

namespace _m;

class out
{
    private static $instance;

    private static $view;
    private static $out;

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
        self::$view = '';
        self::$out = '';
        switch(\_m::getMode())
        {

            case 'data':
                self::$out = self::processFormat(self::loadData());
                self::$out = 'false';
                break;
            case 'proc':
                self::$out = self::processFormat(self::loadProc());
                break;
            case 'obj':
                self::$out = self::processFormat(self::loadObj());
                break;
            case 'comp':
                //@TODO: write logic for component
                self::loadComponent();
                break;
            case 'view':
                self::loadView();
                self::implementScripts(true);
                break;
            default:
                self::loadView();
                self::loadTemplate();
                self::implementScripts();
                break;
        }
        echo self::$out; 
    }

    public function __clone() { return false;}

    private function processFormat($input)
    {
        switch(\_m::getFormat())
        {
            default:
                $formatter = new \_m\util\format\json($input);
                break;
        }
        return $formatter->getOutput();
    }

    private function loadTemplate($fileName=false)
    {
        if(!$filename)
            $fileName = \_m\context::getTemplateFile();
        
        if(!$fileName)
            return false;           //- if no template just return false

        self::$out = self::includeContent($fileName);
    }

    //currently only for loading the primary view
    private function loadView()
    {
        
        if(!$filename)
            $fileName = \_m\context::getViewFile();

        if(!$fileName)
            throw new \mException('The requested view does not exist.');

        \_m\script::get(\_m\context::getPath(),\_m\context::getScriptFile());

        self::$out = self::$view = self::includeContent($fileName);
    }
    
    private function loadComponent()
    {
         self::$out = self::$view = self::component(\_m\req::machine('name'),\_m\req::machine('comp_name'),\_m\req::get(),false);
    }

    private function loadData()
    {
        return \_m\execute::getDataResult();
    }

    private function loadProc()
    {
        return \_m\execute::getProcResult();
    }

    private function loadObj()
    {
        return \_m\execute::getObjResult();
    }

    private function includeContent($fileName,$params=array())
    {
        if(file_exists($fileName))
        {
            ob_start();
            include $fileName;
            return ob_get_clean();
        }
        else
            return '';
    }

    private function getComponentFileName($componentName)
    {
        $parts = explode('@',$componentName);
        
        $context='';
        if(sizeof($parts)>1)
            $context = $parts[1];
        else
            $context = substr(\_m\context::getPath(),1);

        $component  = str_replace('/','--',$parts[0]);

        $file = str_replace('//','/',_M_APP_ROOT.((substr($context,0,1)!='/')?$context:'_components'.$context).'/--'.$component.'.php');
       
        if(!file_exists($file))
            $file = str_replace('//','/',_M_APP_ROOT.((substr($context,0,1)!='/')?$context:'_components'.$context).'--'.$component.'.php');
        
        if(!file_exists($file))
            return false;   //@ERR: Component file not found
        
        return $file;

    }
    
    public function area()
    {
        //@TODO: write... similar to component... may not be needed
    }

    public function component($name, $componentName=false, $params=array(), $includeDiv=true, $class=false, $style=array())
    {
        //@TODO: style as an associative array which applies to the div tag
        if($params==array()) $params = \_m\req::get();
        $content = self::includeContent(self::getComponentFileName($componentName),$params);
        
        //@TODO: retrieve component - file mapping from context data


        $styleText = 'style="';
        if(sizeof($style))
            foreach($style as $row)
                $styleText .= $row.";";
        $styleText .= '";';

        $classText = '';
        if($class)
            $classText = 'class="'.$class.'"';

        if($includeDiv)
            return '<div id="_m_'.$name.'" '.$styleText.' '.$classText.'>'.$content.'</div>';
        else
            return $content;
    }

    public function comp($name, $fileName=false, $style=array())    //- shortcut to component
    {   return self::component($name, $fileName, $style);}

    public function view($params=array())
    {
        //@TODO: style as an associative array which applies to the div tag
        //$name=false, $fileName=false, $style=array()
        //@TODO: handle multiple views (view div naming & filename loading) -later edition

        $content = self::$view;

        $paramString = '';
        foreach($params as $name =>$value)
            $paramString = ' '.$name.'="'.$value.'" ';

        return '<div id="_m__view" '.$paramString.'>'.$content.'</div>';
    }

    public function implementScripts($isView=false)
    {
        $scriptContent = \_m\script::getOutput($isView);
        
        if(strpos(self::$out,'<head>')!=false)
            self::$out = str_replace("<head>","<head>\r\n".$scriptContent,self::$out);
        else
            self::$out .= $scriptContent;
    }

}



?>
