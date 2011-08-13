<?php

namespace _m\util;

class formElement
{
	public $name;
	public $label;
	public $type;
	public $content;
	public $value;
        public $options;
        public $params;

	public function __construct($type=false,$label=false,$name=false,$value=false,$content=false,$options=false)
	{
		
                $this->type = $type;
                $this->label = $label;
                $this->name = $name;
                $this->value = $value;
                $this->content = $content;
                $this->options = $options==false?array():$options;

                $this->params = array();
	}

	//by default the class will convert the label
	public function name($name)
	{
		
		$this->name = $name;

	}

	public function label($label)
	{
		
		$this->label = $label;
	}

	public function type($type)
	{
		
		$this->type = $type;

	}


	//for custom content
	public function content($content)
	{
		
		$this->content = $content;
	}

	//the existing value(s)
	//by default the value will be set to the data in the context that matches the title
	public function value($value)
	{
		
		$this->value = $value;
	}


	//function name for button object
	//i.e.: 'delete' would result in the onClick event of the button going to 'eng_FORMNAME.delete()'
	public function action($action)
	{
		
		$this->action = $action;
	}


        public function options($options)
	{
		
		$this->options = $options;
	}


        public function addParam($name,$value)
        {
            

            $cur = array();
            $cur['name'] = $name;
            $cur['value'] = $value;

            $this->params[] = $cur;
        }
        
        public function params($params)
        {
            $this->params = $params;
        }

        public function getHtml()
        {
            $form = new \_m\util\form();

            return $form->getElementHTML($this);
        }

        public function getScript()
        {

        }



	/*
		element type

		text input		text
		text field		textfield
		HTML Field		html
		radio buttons		radio
		checkboxes		check
		date			date
		time			time
                file                    file
                dropdown		dropdown
		multiselect		multi
		datetime		datetime
		button			button
                content                 content             //custom content
                hidden                  hidden
	*/

}
?>