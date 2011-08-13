<?php

namespace _m\util;

class form
{
	public $title;
	public $name;
	public $content;
	public $elementData;
	public $action;
        public $urlSuccess;
        public $actionObject = false;
        public $actionMethod = false;

	public $html;
	public $script;

	public $built=false;

	public function __construct($name=false,$action=false,$method='POST',$title=false,$urlSuccess=false,$onSubmit=false)
	{


		$this->script = array();
		$this->script['load'] = '';
		$this->script['submit'] = '';
		$this->script['ext'] = '';

                if($name)
                    $this->name = $name;

                if(is_array($action))
                {
                    $this->action = '/obj';
                    $this->actionObject = $action[0];
                    $this->actionMethod = $action[1];
                }
                elseif($action)
                    $this->action = $action;
                else
                    $this->action = \_m\context::getPath();

                if($method)
                    $this->method = $method;

                if($title)
                    $this->title = $title;

                if($urlSuccess)
                    $this->urlSuccess = $urlSuccess;

	}

	public function name($name=false)
	{


                if($name)
                    $this->name = $name;

                return $this->name;

	}

        public function action($action=false)
	{
                if(is_array($action))
                {
                    $this->action = '/obj';
                    $this->actionObject = $action[0];
                    $this->actionMethod = $action[1];
                }
                elseif($action)
                    $this->action = $action;
                else
                    $this->action = \_m\context::getPath(); //@TODO: make work

                return $this->action;
	}

	public function method($method=false)
	{
                if($method)
                    $this->method = $method;

                return $this->method;
	}

	public function title($title=false)
	{
                if($title)
                    $this->title = $title;

                return $this->title;
	}

        public function urlSuccess($urlSuccess=false)
	{
                if($urlSuccess)
                    $this->urlSuccess = $urlSuccess;

                return $this->urlSuccess;

	}


        public function setOrder($order=array())
        {
            //@TODO: write
            //      Provide the object with an array of the labels of the fields
            //      if there are fields given that do not exist, ignore
            //      if there are additional fields, place them after the ordered ones in any order
        }

        public function set($hidden=array())
        {
            //@TODO: write
            //      Provide the object with an array of the labels of the fields
            //      if there are fields given that do not exist, ignore
            //      if there are additional fields, place them after the ordered ones in any order
        }

        public function addElement($formElement)
        {
            //@TODO: add logic for checking formElement's validity

            $this->elementData[$formElement->name] = $formElement;

            $this->built = false;

        }


	//returns the entire form (script and html)
	public function getForm()
	{
		$this->build();

		$out = ''."\r\n";
		$out .= '<div class="formTitle">'.$this->title.'</div>'."\r\n";
		$out .= '<form name="'.$this->name.'" method="POST" action="'.$this->action.'">'."\r\n";
		$out .= $this->getHTML()."\r\n";
                $out .= '<br><div><button onClick="return _m.form.'.$this->name.'.submit();">SUBMIT</button> - <button onClick="window.history.back();">CANCEL</button></div>'."\r\n";
		$out .= '</form>'."\r\n";
		$out .= '<script>'."\r\n";
		$out .= $this->getScript()."\r\n";
                $out .= '_m.form.'.$this->name.' = new machine_form_'.$this->name.'_js();'."\r\n";
                $out .= "$(document).ready(function(){_m.form.".$this->name." = new machine_form_".$this->name."_js(); _m.form.".$this->name.".load();});"."\r\n";
		$out .= '</script>'."\r\n";

                return $this->content = $out;
	}

	//get the form itself in html
	//
	public function getHTML($formTags=true)
	{
		$this->build();

                return $this->html;
	}

	//get just the javascript
	public function getScript($classWrapper=true)
	{

		$this->build();

		$out = '';

		if($classWrapper)
			$out .= 'function machine_form_'.$this->name.'_js() { '."\r\n";

		$out .= 'this.load = function ()'."\r\n";
		$out .= '{'."\r\n";
		$out .= $this->script['load']."\r\n";
		$out .= '}'."\r\n";
		$out .= ''."\r\n";
		$out .= 'this.submit = function ()'."\r\n";
		$out .= '{'."\r\n";
                $out .= 'var params = new Object();'."\r\n";
               // $out .= 'params.submit=1;'."\r\n";
		$out .= $this->script['submit']."\r\n";
                $out .= 'var parameters = new Array();'."\r\n";
                $out .= 'parameters[0] = params;'."\r\n";
                if($this->action=='/obj')
                    $out .= 'var result = _m.obj("'.$this->actionObject.'","'.$this->actionMethod.'" ,parameters);';
                else
                    $out .= 'var result = _m.proc("'.$this->action.'",params);';

                if($this->urlSuccess)
                    $out .= 'if(result) _m.load("'.$this->urlSuccess.'");'."\r\n";
                else
                    $out .= 'if(result) alert("Update successful.");';
                $out .= 'else alert("Update failed.");';
                $out .= 'return false;'."\r\n";
                $out .= '}'."\r\n";
                $out .= $this->script['ext']."\r\n";
		$out .= ''."\r\n";

		if($classWrapper)
			$out .= '}'."\r\n";

                return $out;

	}

	private function build()
	{


		if($this->built)
			return true;

		$this->html = "";
		$this->script = array();
		$this->script['load'] = '';
		$this->script['submit'] = '';
		$this->script['ext'] = '';

		foreach($this->elementData as $element)
		{
			$this->html .= $this->getElementHTML($element);

                        $scriptData = $this->getElementScript($element);

                        $this->script['load'] .= $scriptData['load'];
                        $this->script['submit'] .= $scriptData['submit'];
                        $this->script['ext'] .= $scriptData['ext'];
		}
	}

        private function wrapElementInTemplate($label,$field,$name)
        {


            //@TODO: get external template


            $html = '<div style="padding-top:10px;">';
            if($label)
                $html .=  '<label for="'.$name.'">'.$label.'</label>';
            $html .=  $field.'</div>';

            return $html;
        }

	public function getElementHTML($element)
	{


		$out = '';

		switch($element->type)
		{
                        case 'content':
				$fieldHTML = $element->content."\r\n";
				break;
			case 'text':
				$fieldHTML = '<input type="text" name="'.$element->name.'" id="'.$element->name.'" value="'.$element->value.'" '.$this->getParamString($element).'>'."\r\n";
				break;
			case 'textarea':
				$fieldHTML = '<textarea name="'.$element->name.'" id="'.$element->name.'" '.$this->getParamString($element).'>'.$element->value.'</textarea>'."\r\n";
				break;
			case 'html':
				$fieldHTML = '<textarea name="'.$element->name.'" id="'.$element->name.'" '.$this->getParamString($element).'>'.$element->value.'</textarea>'."\r\n";
				break;
			case 'radio':
				$fieldHTML = $this->getElementHTML_radio($element);
				break;
			case 'check':
				$fieldHTML = $this->getElementHTML_check($element);
				break;
			case 'date':
				$fieldHTML = '<input type="text" name="'.$element->name.'" id="'.$element->name.'" value="'.$element->value.'" '.$this->getParamString($element).'>'."\r\n";
				break;
			case 'time':
				$fieldHTML = $this->getElementHTML_time($element);
				break;
			case 'multi':
				$fieldHTML = $this->getElementHTML_multi($element);
				break;
                        case 'dropdown':
				$fieldHTML = $this->getElementHTML_dropdown($element);
				break;
                                /*
			case 'datetime':
				$fieldHTML = $this->getElementHTML_datetime($element);
				break;
                                 */
			case 'file':
                                $fieldHTML = '<input type="text" value="'.$element->value.'" name="image_'.$element->name.'" id="image_'.$element->name.'" style="width:350px;"  onChange="machine.util.imageChange(this.id)"  onClick="mcImageManager.open(\''.$this->name.'\',\'image_'.$element->name.'\',\'\',\'\',{document_base_url : \'http://a.b.c/\', remember_last_path : 1});"  '.$this->getParamString($element).'>'."\r\n";
                                break;
                        case 'hidden':
				$fieldHTML = '<input type="hidden" name="'.$element->name.'" id="'.$element->name.'" value="'.$element->value.'" '.$this->getParamString($element).'>'."\r\n";
				break;
			default:
				$fieldHTML = '';

		}

		return $this->wrapElementInTemplate($element->label,$fieldHTML,$element->name);

	}

	public function getElementScript($element)
	{


		switch($element->type)
		{
			case 'text':
				$scriptData = $this->getElementScript_text($element);
				break;
			case 'textarea':
				$scriptData = $this->getElementScript_textarea($element);
				break;
			case 'html':
				$scriptData = $this->getElementScript_html($element);
				break;
			case 'radio':
				$scriptData = $this->getElementScript_radio($element);
				break;
			case 'check':
				$scriptData = $this->getElementScript_check($element);
				break;
			case 'date':
				$scriptData = $this->getElementScript_date($element);
				break;
			case 'time':
				$scriptData = $this->getElementScript_time($element);
				break;
			case 'multi':
				$scriptData = $this->getElementScript_multi($element);
				break;
			case 'datetime':
				$scriptData = $this->getElementScript_datetime($element);
				break;
                        case 'dropdown':
				$scriptData = $this->getElementScript_dropdown($element);
				break;
			case 'file':
				$scriptData = $this->getElementScript_file($element);
				break;
                        case 'hidden':
				$scriptData = $this->getElementScript_hidden($element);
				break;
			default:
				$scriptData = '';
		}

		return $scriptData;

	}

        private function getParamString($element)
        {


            $params = $element->params;

            $out = "";
            foreach($params as $param)
                $out .= ' '.$param['name'].'="'.$param['value'].'" ';

            return $out;
        }


        //////////////// GET ELEMENT HTML FUNCTIONS   //////////
        private function getElementHTML_radio($element)
        {


            $options = $element->options;
            $name = $element->name;
            $value = $element->value;

            $out = '';


            $i = 1;
            foreach($options as $option)
            {
                if(!is_array($option))
                {
                    $oldOption = $option;
                    $option = array();
                    $option['label'] = $oldOption;
                    $option['value'] = $oldOption;
                }

                $selected = ($option['value']==$value)?'checked':'';

                $out .= '<input type="radio" name="'.$name.'" id="'.$name.'_'.$i.'" value="'.$option['value'].'" '.$selected.'  '.$this->getParamString($element).'/> '.$option['label'].'<br>';
                $i++;

            }

            return $out;
        }

        private function getElementHTML_check($element)
        {

            //@TODO: write;

            $options = $element->options;
            $name = $element->name;
            $value = $element->value;

            $out = '';
            $i=1;
            foreach($options as $option)
            {
                if(!is_array($option))
                {
                    $oldOption = $option;
                    $option = array();
                    $option['label'] = $oldOption;
                    $option['value'] = $oldOption;
                }


                $selected = ($option['value']==$value)?'checked':'';


                $out .= '<input type="checkbox" name="'.$name.'" id="'.$name.'_'.$i.'" value="'.$option['value'].'" '.$selected.'  '.$this->getParamString($element).'/> '.$option['label'].'<br>';

                $i++;
            }

            return $out;
        }

        private function getElementHTML_time($element)
        {


            $time = $element->value;
            $idBase = $element->name;

            if($time)
            {
                $timeData = explode(':',$time);
                //@TODO: need to handle 24h time convert properly
                $hour = $timeData[0] % 12;
                $min = $timeData[1];
                $ampm = (($timeData[0]>11)?2:1);
            }
            else
                {$hour=false;$min=false;$ampm=false;}

            $labels = array();
            $vals = array();
            for($i=1;$i<=12;$i++)
            {
                $labels[] = $i;
                $vals[] = $i;
            }
            $out = \_m\util\display::dropdown($idBase.'_hour',$hour,$labels,$vals);

            $labels = array();
            $vals = array();
            for($i=0;$i<=59;$i++)
            {
                $labels[] = ((strlen($i)==1)?'0'.$i:$i);
                $vals[] = $i;
            }

            $out .= \_m\util\display::dropdown($idBase.'_min',$min,$labels,$vals);

            $labels = array('AM','PM');
            $vals = array(1,2);
            $out .= \_m\util\display::dropdown($idBase.'_ampm',$ampm,$labels,$vals);

            return $out;

        }

        private function getElementHTML_dropdown($element)
        {


            $options = $element->options;
            $name = $element->name;
            $value = $element->value;

            $out = '';

            //integrate params  '.$this->getParamString($element).'
            $out = \_m\util\display::dropdown($name,$value,$options,$options);

            return $out;
        }

        private function getElementHTML_multi($element)
        {
            //@TODO: Write


        }




        //////////////// GET ELEMENT SCRIPT FUNCTIONS   //////////

        private function getElementScript_text($element)
        {


            $scriptData['load'] = "";

            $scriptData['submit'] = "params.".$element->name." = document.getElementById('".$element->name."').value;"."\r\n";

            $scriptData['ext'] = "";

            return $scriptData;
        }

        private function getElementScript_hidden($element)
        {


            $scriptData['load'] = "";

            $scriptData['submit'] = "params.".$element->name." = document.getElementById('".$element->name."').value;"."\r\n";

            $scriptData['ext'] = "";

            return $scriptData;
        }

        private function getElementScript_textarea($element)
        {


            $scriptData['load'] = "";

            $scriptData['submit'] = "params.".$element->name." = document.getElementById('".$element->name."').value;"."\r\n";

            $scriptData['ext'] = "";

            return $scriptData;
        }

        private function getElementScript_html($element)
        {


            //$scriptData['load'] = "CKEDITOR.replace( '".$element->name."' );";
            //$scriptData['load'] = "";
            //$scriptData['submit'] =
            //$scriptData['submit'] = "params.".$element->name." = CKEDITOR.instances.".$element->name.".getData();"."\r\n";

            $scriptData['load'] = "$('#".$element->name."').wysiwyg();";
            $scriptData['submit'] = "params.".$element->name." = $('#".$element->name."').val();";
            
            $scriptData['ext'] = "";

            return $scriptData;
        }

        private function getElementScript_radio($element)
        {


            $options = $element->options;
            $name = $element->name;
            $value = $element->value;

            $scriptData['load'] = "";

            //@TODO: get value for radio button

            $out = '';

            $scriptData['submit'] = "";

            $i=1;
            foreach($options as $option)
            {
                $scriptData['submit'] .= "if(document.getElementById('".$element->name."_".$i."').checked)
                                                params.".$element->name." = document.getElementById('".$element->name."_".$i."').value;"."\r\n";

                $i++;
            }
            $scriptData['ext'] = "";

            return $scriptData;
        }

        private function getElementScript_check($element)
        {


            $scriptData['load'] = "";

            //@TODO: write in the manner of the radio button
            $scriptData['submit'] = "dropdownIndex = document.getElementById('".$element->name."').selectedIndex;
                                     listID = document.getElementById('".$element->name."').options[dropdownIndex].value;
                                     params.".$element->name." = listID;"."\r\n";

            $scriptData['ext'] = "";

            return $scriptData;
        }

        private function getElementScript_dropdown($element)
        {


            $scriptData['load'] = "";

            $scriptData['submit'] = "dropdownIndex = document.getElementById('".$element->name."').selectedIndex;
                                     listID = document.getElementById('".$element->name."').options[dropdownIndex].value;
                                     params.".$element->name." = listID;"."\r\n";

            $scriptData['ext'] = "";

            return $scriptData;
        }

        private function getElementScript_date($element)
        {


            $scriptData['load'] = '$("#'.$element->name.'").datepicker();
                                    document.getElementById("'.$element->name.'").value = machine.util.convertDateForDisplay(document.getElementById("'.$element->name.'").value);';

            $scriptData['submit'] = "params.".$element->name." = machine.util.convertDateForUpdate(document.getElementById('".$element->name."').value);"."\r\n";

            $scriptData['ext'] = "";

            return $scriptData;
        }

        private function getElementScript_time($element)
        {


            $name = $element->name;

            $scriptData['load'] = "";

            $scriptData['submit'] = "
                    params.".$name."_hour = document.getElementById('".$name."_hour').value;
                    params.".$name."_min = document.getElementById('".$name."_min').value;
                    params.".$name."_ampm = document.getElementById('".$name."_ampm').value;
                    var hour24 = (parseInt(params.".$name."_hour) + parseInt((params.".$name."_ampm -1) * 12));
                    params.".$name." = hour24 + ':' + params.".$name."_min + ':00';";

            $scriptData['ext'] = "";

            return $scriptData;
        }

        private function getElementScript_datetime($element)
        {


            $scriptData['load'] = "";

            $scriptData['submit'] = "";

            $scriptData['ext'] = "";

            return $scriptData;
        }


        private function getElementScript_multi($element)
        {


            $scriptData['load'] = "";

            $scriptData['submit'] = "";

            $scriptData['ext'] = "";

            return $scriptData;
        }


        private function getElementScript_file($element)
        {


            $scriptData['load'] = "";

            $scriptData['submit'] = "";

            $scriptData['ext'] = "";

            return $scriptData;
        }

}

?>
