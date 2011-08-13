<?php

namespace _m\util;

class listing
{
    public $title;
    public $name;
    public $columns;
    public $data;
    public $total=0;
    public $per=false;
    
    public $usePaging = true;
    public $paging;

    public $content;

    function __construct($title=false,$per=false)
    {
        $this->title = $title;
        $this->per = $per;
        $this->paging = new \_m\util\paging(0,false,$this->per);
        //@TODO: create a name by converting the title
    }
    
    function usePaging($use=true)
    {
        $this->usePaging = true;
    }
    
    function setTotalRows($num=0)
    {
        $this->total = $num;
    }

    function getPaging()
    {
        $this->paging = new \_m\util\paging($this->total,false,$this->per);
        return $this->paging->getHTML();
    }
    
    
    function addColumn($name=false,$title='&nbsp;',$width=false)
    {
        if(!$name)
            return false;

        $columnData['title'] = $title;
        $columnData['width'] = $width;

        return $this->columns[$name] = $columnData;
    }

    function delColumn($name=false)
    {
        

        if(!$name)
            return false;

        unset($this->columns[$name]);

        return true;
    }

    function setData($data=false)
    {
        if(!$data)
            return false;

        $this->data = $data;

        return true;
    }

    function getListing()
    {
        


        if(!sizeof($this->columns))
            return "";
    
    	
    	$out = '
    	<div class="content-box">
	<div class="content-box-header">
		<h3>'.$this->title.'</h3>
		<div class="clear"></div>
	</div>
	<div class="content-box-content">
    	<table>	';
        $out .= '<thead><tr>';

        foreach($this->columns as $column)
        {
            $out.= '<th  scope="col" '.($column['width']?'width="'.$column['width'].'"':'').'>'.$column['title'].'</th>';
        }
        $out .= '</tr></thead>';
        
        
        //@TODO: something different for the footer

        $numCol = sizeof($this->columns);
        
        $out .= '<tfoot><tr><td colspan="'.$numCol.'"><div style="width:100%; text-align:center;"><div style="max-width:200px;">'.$this->getPaging().'</div></div></td></tr></tfoot>';
        
        
        
        $i=1;
        $out .= '<tbody>';
        foreach($this->data as $row)
        {
            $out .= '<tr>';
            foreach($this->columns as $name => $column)
            {
               $out.= '<td '.($i%2==1?'class="odd"':'').'>'.$this->getColumnContent($name,$row).'</td>';
            }
            $out .= '</tr>';

            $i++;
        }
        $out .='</tbody></div></div>';
        return $this->content = $out;
    }

    function getColumnContent($name,$row)
    {
        


        if(isset($row[$name]))
            $value = $row[$name];
        else
            $value = "";

        switch($name)
        {
            default:
                $out = $value;
                break;
        }

        return $out;
    }


}

?>
