<?php

namespace _m;

class script
{
    private static $instance;

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

    }

    public function __clone() { return false;}

    public function loadApp()
    {
        
    }

    public function get($context, $fileName=false, $label=false)
    {
        //@TODO:: if fileName=false, determine from context
        
        if(!$label)
                $label = \_m\context::pathToLabel($context);
        
        if(file_exists($fileName))
            $scriptContent = file_get_contents($fileName);
        else
            $scriptContent = '';
        
        //@TODO: initialize subspaces i.e: .comp and .video
        
        $out = 'function _m_'.$label.'()
                {
                    '.$scriptContent.'
                }

                

                _m.app = new _m_'.$label.'();';

        self::$out[$context] = $out;

        return $out;
    }

    public function getOutput($isView=false)
    {
        $out = '';

        if(!$isView)
            $out .= self::getStandardScripts();

        if(!sizeof(self::$out))
            return $out;

        $out .= '<script>';

        foreach(self::$out as $block)
        {
            $out .= $block."\r\n";
        }

        $out .= '</script>';

        return $out;
    }

    //onyl needs to be called for the template load, not the view
    private function getStandardScripts()
    {
        //@TODO: factor in scrip directory or at least webroot
        return '<script src="/source/jquery.js" type="text/javascript"></script>
                <script src="/source/json2.js" type="text/javascript"></script>
                <script src="/source/machine.js" type="text/javascript"></script>
                <script src="/source/php.min.js" type="text/javascript"></script>';

    }
}

?>
