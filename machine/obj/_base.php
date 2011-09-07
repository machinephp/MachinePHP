<?php
namespace _m\obj;

class _base
{
    private $type;
    private $identifier;
    var $table = false;
    
    public function __construct()
    {

    }

    

    public function update($identifier=false,$data=false,$table=false)
    {

    }

    public function delete($identifier=false,$where=false,$table=false)
    {

    }

    public function create($data=false,$table=false)
    {

    }

    public function listing($where=false,$order=false,$type=false)
    {

    }

    //future

    public function select($params = array())
    {

    }
    
    public function whereFromIdentifier($identifier,$identifierField=false)
    {
        if($this->identifier==false&&$this->type==false)
                $this->identifier = 'id';
        elseif($this->identifier==false)
                $this->identifier = $this->type.'_id';
                
        if(is_array($identifier))
        {
            $where = array();
            foreach($identifier as $curIdentifier)
                $where[] =  self::whereFromIdenfier($curIdentifier);
            //@TODO: ensure array nesting is correct
        }
        else
        {
            if(is_numeric($identifier))
            {
                if(!$identifierField)
                    $where = array('id'=>$identifier);
                else
                    $where = array($identifierField=>$identifier);
            }
            else
                $where = array('label'=>$identifier);
        }
        
        return $where;
    }

   

}
?>