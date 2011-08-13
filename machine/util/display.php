<?php
namespace _m\util;

class display
   {

    public function dropdown($id='',$selected=false,$labels=false,$vals=false,$blankFirst=true)
    {
        global $m;

        $out = '<select name="'.$id.'" id="'.$id.'">';
        if($blankFirst)
            $out .= '<option value="" '.(($selected=="")?'SELECTED':'').'></option>';
        foreach($vals as $key => $val)
            $out .= '<option value="'.$val.'" '.(($selected==$val)?'SELECTED':'').'>'.$labels[$key].'</option>';

        $out .= '</select>';

        return $out;
    }

    public function buildDropdownList($name,$defaultValue,$valueList,$haveBlank=false,$onChange='')
    {
        global $m;
        $m->d->m('engineUtility::buildDropdownList($name,$defaultValue,$valueList)');

       
        if(isset($valueList[0]['dropdown_value']))
            $valueField = 'dropdown_value';
        elseif(isset($valueList[0]['value']))
            $valueField = 'value';
        elseif(isset($valueList[0]['id']))
            $valueField = 'id';

        if(isset($valueList[0]['dropdown_label']))
            $labelField = 'dropdown_label';
        elseif(isset($valueList[0]['name']))
            $labelField = 'name';
        elseif(isset($valueList[0]['label']))
            $labelField = 'label';

        $html = '';

        $html .= '<SELECT NAME="'.$name.'" ID="'.$name.'" onChange="'.$onChange.'">';

        if($haveBlank)
            $html .=  '<OPTION VALUE=""></OPTION>';

        foreach($valueList as $row)
        {
            if(trim($defaultValue)==trim($valueField))
                $selected = 'SELECTED';
            else
                $selected = '';
            $html .= '<OPTION VALUE="'.$valueField.'" '.$selected.'>'.$row[$labelField].'</OPTION>';
        }

        $html .= '</SELECT>';
        return $html;
    }


    public function inputTime($idBase,$time=false)
    {


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
        $out = $this->dropdown($idBase.'_hour',$hour,$labels,$vals,false);

        $labels = array();
        $vals = array();
        for($i=0;$i<=59;$i++)
        {
            $labels[] = ((strlen($i)==1)?'0'.$i:$i);
            $vals[] = $i;
        }

        $out .= $this->dropdown($idBase.'_min',$min,$labels,$vals,false);

        $labels = array('AM','PM');
        $vals = array(1,2);
        $out .= $this->dropdown($idBase.'_ampm',$ampm,$labels,$vals,false);

        return $out;

    }


    public function time($time)
    {
            $timeData = explode(':',$time);
            //@TODO: need to handle 24h time convert properly
            $hour = $timeData[0] % 12;
            $min = $timeData[1];
            $ampm = (($timeData[0]>11)?2:1);
            //@FIX: AM-PM
        return $hour.':'.$min.' PM';
    }

    

    public function date($date,$type=1,$format=false)
    {
        // type: 1- full 2 -mid 3- small 4 - custom
        // format: the actual PHP date format (for type 4)

        if($type==4&&$format)
        {
            $dateFormat = $format;
        }
        else
        {
            $dateFormat = "M jS, Y";
        }

        return date($dateFormat,strtotime($date));
    }
    
    public function datetime($datetime,$type=1,$format=false)
    {
        $parts = explode(" ",$datetime);
        $date = $parts[0];
        $time = $parts[1];

        return $this->date($date).' '.$this->time($time);
        
        
    }

    public function money($value)
    {
        // type: 1- full 2 -mid 3- small 4 - custom
        // format: the actual PHP date format (for type 4)
        return $value;
    }

    
    public function inputDate($idBase,$date=false)
    {
    	if($date)
        {
            $dateData = explode('-',$date);

            $year = $dateData[0];
            $month = $dateData[1];
            $day = $dateData[2];
        }
        else
            {$year=false;$month=false;$day=false;}

        $labels = array();
        $vals = array();
        for($i=(date('Y')-10);$i<=(date('Y')+2);$i++)
        {
            $labels[] = $i;
            $vals[] = $i;
        }
        $out = $this->dropdown($idBase.'_year',$year,$labels,$vals);


        $labels = array('January','February','March','April','May','June','July','August','September','October','November','December');
        $vals = array(1,2,3,4,5,6,7,8,9,10,11,12);

        $out .= $this->dropdown($idBase.'_month',$month,$labels,$vals);


        $labels = array();
        $vals = array();
        for($i=1;$i<=31;$i++)
        {
            $labels[] = ((strlen($i)==1)?'0'.$i:$i);
            $vals[] = $i;
        }

        $out .= $this->dropdown($idBase.'_day',$day,$labels,$vals);

        return $out;
    }
     
     

    public function inputDatePopup($name,$date='')
    {

        if($date)
        {
            $dateData = explode('-',$date);

            $year = $dateData[0];
            $month = $dateData[1];
            $day = $dateData[2];

            $date = $month .'/'. $day .'/'. $year;
        }
        else
            {$year=false;$month=false;$day=false; $date = '';}



        $out = '<script type="text/javascript">
	$(function() {
		$("#'.$name.'").datepicker();
	});
	</script><input id="'.$name.'" type="text" value="'.$date.'">';

        echo $out;

    }

    public function autoComplete($name,$default,$values)
    {


        echo json_encode($values);
        $out = '<script type="text/javascript">
            var availableTags = ["ActionScript", "AppleScript", "Asp", "BASIC", "C", "C++", "Clojure", "COBOL", "ColdFusion", "Erlang", "Fortran", "Groovy", "Haskell", "Java", "JavaScript", "Lisp", "Perl", "PHP", "Python", "Ruby", "Scala", "Scheme"];
                $(function() {
                        $("#tags").autocomplete({
                                source: availableTags
                        });
                });
                </script>
        <input id="tags">';

    }




    public function listLimiter($fieldData=false,$useDate=false)
    {
    	    $out = '';

    	    if($useDate)
    	    {
    	    	    $out.= ' <span>Start Date </span>';
		    $out.= $this->inputDate('date_start',date('Y-m-d'));
		    $out.= ' <span>End Date </span>';
		    $out.= $this->inputDate('date_end');
    	    }

    	    if($fieldData)
    	    {
    	    	    $out.= '<span>Order By </span>';
    	    	    $orderBy = false;
    	    	    if(isset($_POST['list_order_field']))
    	    	    	    $orderBy = $_POST['list_order_field'];

    	    	    $orderDir = false;
    	    	    if(isset($_POST['list_order_direction']))
    	    	    	    $orderDir = $_POST['list_order_direction'];

    	    	    $out .= $this->dropdown('list_order_field',$orderBy,$fieldData[1],$fieldData[0]);
    	    	    $out .= $this->dropdown('list_order_direction',$orderBy,array('ASC','DESC'),array('Ascending','Decending'));

    	    }

    	    $out .= '<input type="button" name="listLimiterSet" value="GO" onClick="eng.listLimiter();">';

    	    return $out;
    }

    

    public function isChecked($val)
    {
         global $m;
        $m->d->m('engineUtility::isChecked($val)');

        if($val)
            return ' CHECKED ';
    }


   }


    ?>