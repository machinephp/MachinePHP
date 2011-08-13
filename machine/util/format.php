<?php

namespace _m\util;

class format
{
    public $input = false;
    public $output = false;
    public $options;


    public function __construct($input=false,$options=array())
    {
        $this->input = $input;
        $this->options = $options;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        $this->process();
        return $this->output;
    }

    public function truncate($content,$len=250)
    {
        //@TODO: make work with the function used that minds HTML
        return substr($content,0,$len);
    }

    protected function process()
    {
        $this->output = self::packageResult($this->input);
    }


    protected function packageResult($return)
    {
        $result->data = $return;
        $result->error = \_m::getError();
        $result->message = \_m::getMessage();
        //@DBUG:
        $result->internal = \_m::getError(test);

        return $result;
    }
}

?>
