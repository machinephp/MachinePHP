<?php

namespace _m\obj;

class badge extends _base
{
    
    private $type = 'badge';
    private $identifier = 'badge_id';
    
    public function create($badge,$description,$group='')
    {
        $data = \_m\db::select('badge',false," LOWER(title) LIKE '".strtolower($badge)."'");
    
        if(\_m\db::numRows())
            return $data[0];
        
        $id = \_m\db::insert('badge',array('title' => $badge, 'description' => $description, 'label' => $this->makeLabel($badge)));
        
        $data = \_m\db::select('badge',false," badge_id = '".$id."'");
        
        if($data)
            return $data[0];
        
        return false;
    }

    public function deactivate($id=false)
    {
        if(!$id) return false;
        return \_m\db::update('badge',array('status'=>0),array(array('badge_id'=>$id)));
    }

    public function reactivate()
    {

        if(!$id) return false;
        return \_m\db::update('badge',array('status'=>1),array(array('badge_id'=>$id)));
    }
    
    public function delete($identifier=false)
    {   
        \_m::setError('ok');
        return \_m\db::delete('badge',$identifier);
    }
    
    public function listing($limit)
    {
        //@TODO: query;
        //$limit = array($start,$total);
        //return \_m\db::select('badge',false,false,false,$limit);


        $limitSQL = '';

        if($limit&&is_array($limit))
            $limitSQL = ' LIMIT '.$limit[0].','.$limit[1].' ';
        elseif($limit)
            $limitSQL = ' LIMIT '.$limit;

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM badge ORDER BY badge_id ASC ".$limitSQL.";";

        if(!($result = \_m\db::query($sql)))
            return false;

        return \_m\db::getResultArray();
    }
    
    public function update($identifier=false,$data=false)
    {
        //@TODO: improve
        return \_m\db::update('badge',$data);
    }
    
    public function find($badge=false,$group=false)
    {
        if(!$tag) return false;

        $data = \_m\db::select('badge','title label'," LOWER(title) LIKE '".strtolower($badge)."%'");

        return $data;
    }
    
    public function makeLabel($badge)
    {
        return str_replace(array(' '),array('-'),strtolower($badge));
    }
    
    
}

?>
