<?php

namespace _m\util;

class paging
{
    public $items_total;
    public $per=10;
    public $current = 1;
    public $ajax = false;
    public $distance = 3;
    public $url = "";
    
    public $pages = array();
    public $prev = false;
    public $next = false;
    public $min = 0;
    public $max = 0;
    public $total = 0;

    public function __construct($totalItems=0,$currentPage=false,$itemsPerPage=10,$url="",$useAJAX=false,$pagesDistance=3)
    {
        $this->items_total = $totalItems;
        if($itemsPerPage)
            $this->per = $itemsPerPage;
        $this->path = $path;
        $this->ajax = $useAJAX;
        $this->distance = $pagesDistance;
        
        if(!$currentPage&&isset($_GET['page']))
            $this->current = $_GET['page'];
        elseif(!$currentPage)
            $this->current = 1;
        else
            $this->current = $currentPage;
        
    }
    
    public function build()
    {
        if(!$this->items_total) return false;
        
        $this->total = ceil($this->items_total/$this->per);
        
        if($this->current>$this->total)
            $this->current = $this->total;
       
        if($this->current>1) $this->prev = $this->current-1;
        if($this->current<$this->total) $this->next = $this->current+1;
        
        $this->min = (($this->current-$this->distance)>1)?($this->current-$this->distance):1;
        $this->max = (($this->current+$this->distance)<$this->total)?($this->current+$this->distance):$this->total;
        
        $this->pages = array();
        
        for($i=$this->min;$i<=$this->max;$i++)
            $this->pages[] = $i;
        
        
    }

    public function getHTML()
    {
        $this->build();
        
        //@TODO: handle AJAX
        
        $html = '<table class="paging_table"><tr>';
        
        if($this->prev==1)
                $html .= '<td><a href="'.$this->getLink(1).'">&lt;&lt;</a></td>';
        elseif($this->prev)
                $html .= '<td><a href="'.$this->getLink(1).'">&lt;&lt;</a></td>
                          <td><a href="'.$this->getLink($this->prev).'">&lt;</a></td>';
        
        foreach($this->pages as $cur)
        {
            if($cur!=$this->current)
                $html .= '<td><a href="'.$this->getLink($cur).'">'.$cur.'</a></td>';
            else
                $html .= '<td>'.$cur.'</td>';
                
        }
        
        if($this->next==$this->total)
                $html .= '<td><a href="'.$this->getLink($this->total).'">&gt;&gt;</a></td>';
        elseif($this->next)
                $html .= '<td><a href="'.$this->getLink($this->next).'">&gt;</a></td>
                          <td><a href="'.$this->getLink($this->total).'">&gt;&gt;</a></td>';
        
        $html .= '</tr></table>';
        
        return $html;
        
    }
    
    private function getLink($num)
    {
        if($ajax)
            return 'paging_goto('.$num.');';
        
        if($this->url=="")
                $this->url = $_SERVER['REQUEST_URI'];
        
      
         $url_data = parse_url($this->url );
         $params = array();
          
         parse_str($url_data['query'], $params);
         
         $params['page'] = $num;   
         
         $params_str = http_build_query($params);
         
         return  $url_data['path'].'?'.$params_str;
         
    }
 
}

?>
