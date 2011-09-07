<?php

namespace _m\util\format;

class json extends \_m\util\format
{
    protected function process()
    {
        $this->output = json_encode($this->packageResult($this->input));
    }
}

?>
