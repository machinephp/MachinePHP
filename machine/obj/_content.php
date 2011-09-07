<?php

namespace _m\obj;

class _content
{
    
    public function search($query=false,$limit=false,$order=false,$type=false)
    {
        if($query==false&&!isset($_GET['query']))
            return self::listing($limit);
        
        if($query==false&&isset($_GET['query']))
            $query = $_GET['query'];
            
        $where = array( array(SQL_CUSTOM => "title LIKE '%".$query."%' OR content LIKE '%".$query."%'"));

        $where = self::whereWrapper($where,$type);

        return self::listing($limit,false,false,false,$where,$order);
    }
    
    public function listing($limit=false,$tags=false,$taxonomy=false,$vote=false,$where=false,$order=false,$fields=false,$type=false)
    {
        if($limit===false) $limit = array(0,10);
        if($order==false) $order = ' content.content_id DESC ';
        if($fields==false||$fields==null) $fields = 'content.*, '.($type?'content_'.$type.'.*,':'').'  user.nickname user_name';
        
        if(is_array($fields))
            $fields[0] = 'SQL_CALC_FOUND_ROWS '.$fields[0];
        else
            $fields = 'SQL_CALC_FOUND_ROWS '.$fields;

        $where = self::whereWrapper($where,$type);

        $tables = self::getTables($type);

        return \_m\db::select($tables,$fields,$where,$order,$limit);
    }
    
    public function get($identifier=false,$type=false)
    {
        if($identifier===false) return false;
        
        $where = self::whereFromIdentifier($identifier,'content');

        $where = self::whereWrapper($where,$type);
        
        $fields = 'content.*, '.($type?'content_'.$type.'.*,':'').'  user.nickname user_name';
        
        $tables = self::getTables($type);
        
        $data =  \_m\db::select($tables,$fields,$where);
        
        if(is_array($data)&&isset($data[0]))
            return $data[0];
        else
            return false;
    }
    
    public function update($identifier=false,$data=false,$type=false)
    {
        if($data===false) return false;
        
        $where = self::whereFromIdentifier($identifier);

        $where = self::whereWrapper($where,$type);

        return\_m\db::update('content',$data,$where);
        
    }
    
    public function delete($identifier=false,$where=false,$type=false)
    {
        
        if($identifier)
            $where = self::whereFromIdentifier($identifier);

        $where = self::whereWrapper($where,$type);

        //@TODO: write delete
    }
    
    public function create($data=false,$type=false)
    {
        if($data===false) return false;
        
        if(!isset($data['title'])) return false;
        if(!isset($data['label'])) $data['label'] = self::makeLabel($data['title']);
        
        //@TEMP: force admin id
        $data['creator'] = 1;
        //if(!isset($data['creator'])) $data['creator'] = \_m\auth::id();
        
        $data['type'] = $type;

        return \_m\db::insert('content',$data);
    }
    
    public function listing_tag($tag,$start=0,$total=10,$type=false)
    {
        //\_m\db::select(array('content c','content_tag ct'),'c.*',array(SQL_AND,array('',''),array('','')));
        
        $sql = "SELECT c.* 
                    FROM content c, content__tag ct, tag t 
                    WHERE c.content_id=ct.content_id 
                      AND t.tag_id=ct.tag_id 
                      AND t.label LIKE '".$tag."' 
                    LIMIT {$start},{$total};";
        
        $result = \_m\db::query($sql);
        
        return \_m\db::getResultArray($result);
        
    }
    
    public function listing_badge($badge,$start=0,$total=10,$type=false)
    {
        $sql = "SELECT c.* 
                    FROM content c, content__badge cb, badge b 
                    WHERE c.content_id=cb.joke_id 
                      AND b.badge_id=cb.badge_id 
                      AND b.label LIKE '".$badge."' 
                    LIMIT {$start},{$total};";
        
        $result = \_m\db::query($sql);
        
        return \_m\db::getResultArray($result);
    }
    
    
    //content id, (tag id or tag label)
    public function tag_apply($id=false,$tag=false)
    {
        if($id===false||$tag===false) return false;
        
        $tagData = \_m\obj\tag::determine($tag);
        if(!$tagData) return false;
        
        $data = \_m\db::select('content__tag','*',
                                array(array(SQL_AND => array(
                                    array('content_id'=> $id),
                                    array('tag_id'=> $tagData['tag_id'])))));
    
        if(sizeof($data)) return \_m::error("Tag already applied.");
        
        \_m\db::insert('content__tag',array('content_id'=> $id, 'tag_id'=> $tagData['tag_id']));
        
        return $tagData;
    }
    
    public function tag_remove($id=false,$tag=false)
    {
        if($id===false||$tag===false) return false;
        
        $tagData = \_m\obj\tag::determine($tag);
        if(!$tagData) return false;
        
        return \_m\db::delete('content__tag',
                        array(array(SQL_AND,array(
                                array('content_id'=>$id),
                                array('tag_id'=>$tagData['tag_id'])))));
        
    }
    
    public function tag_list($id=false,$type=false)
    {
        if($id===false) return false;

        $sql = "SELECT t.* 
                         FROM content c,
                                content__tag ct, 
                                tag t 
                        WHERE c.content_id=ct.content_id 
                          AND t.tag_id=ct.tag_id 
                          AND c.content_id='".$id."' LIMIT 0,20;";
       
        $result = \_m\db::query($sql);

        return \_m\db::getResultArray($result);
    }
    
    
    public function taxonomy_apply($id=false,$tag=false)
    {
        //@TODO: write
        
    }
    
    public function taxonomy_remove($id=false,$tag=false)
    {
        //@TODO: write
        
    }
    
    public function taxonomy_list($id=false)
    {
        //@TODO: write
        
    }
    
    public function badge_list($contentID=false)
    {
        if($contentID===false) return false; 
        
        $sql = 'SELECT cb.*, b.* FROM badge b
                            LEFT JOIN content__badge cb ON (b.badge_id=cb.badge_id
                                        AND cb.content_id='.$contentID.') WHERE b.status=1;';
        
        $result = \_m\db::query($sql); 
        
        if(!$result)
            return \_m::error('Badges do not exist');
        
        $badges_set = \_m\data::get(array('badge',"$contentID"));
        if(!$badges_set) $badges_set = array();
        
        $badgeData = array();
        while(($row=\_m\db::getRowAssoc($result)))
        {
            if(in_array($row['badge_id'],$badges_set))
                 $row['status'] = true;
            else
                 $row['status'] = false;
            
            $badgeData[] = $row;
        }
        
        return $badgeData;
    }
    
    public function badge_apply($contentID=false,$badgeID=false)
    {
        
        if($contentID===false||$badgeID===false) return false;
         
        $sql = "SELECT cb.num 
                        FROM content__badge cb 
                        WHERE cb.content_id='".$contentID."'
                          AND cb.badge_id='".$badgeID."';";
        
        $result = \_m\db::query($sql);
        
        if(!$result) return \_m::error('Badge could not be added.');
        
        $badges_set = \_m\data::get(array('badge',"$contentID"));
        if(!$badges_set) $badges_set = array();
        
        if(\_m\db::numRows($result))
        {
            //update
            $data = \_m\db::getRowAssoc($result);
            
            if(in_array($badgeID,$badges_set))
                    return false;
            
            $num = $data['num'] + 1;
            
            $sql = "UPDATE content__badge cb 
                                    SET cb.num = ".$num."
                                     WHERE 
                                        cb.content_id='".$contentID."'
                                    AND cb.badge_id='".$badgeID."';";
            
            $res = \_m\db::query($sql);
            
            if(!$res) return \_m::error('Badge could not be added.');
        }
        else
        {
            //insert
            $num = 1;
            $res = \_m\db::insert('content__badge',array('content_id' => $contentID,
                                               'badge_id' => $badgeID,
                                               'num' => $num));
            
            if(!$res) return \_m::error('Badge could not be added.');
        }
        
        $badges_set[] = $badgeID;
        
        \_m\data::set(array('badge',"$contentID"),$badges_set);
        
        return $num;
    }
    
    
    
    public function comment_list($identifier=false)
    {
        if($identifier===false) return false;
        /*
        $whereJoin = array('cc.content_id'=>'`c.content_id`');
        $whereTemp = self::whereFromIdentifier($identifier,'c');
        $whereID = array_merge($whereTemp[0],$whereJoin);
        $where = array(SQL_AND => $whereID);
         
         */
        return \_m\db::select('content__comment cc LEFT JOIN user u ON (cc.creator = u.id)','cc.*, u.nickname user_name','cc.content_id='.$identifier);
    }
    
    public function comment_post($contentID=false,$comment='')
    {
        if($contentID===false) return false;

        $userID = \_m\auth::check();
        if(!$userID) return false;
        
        $res = \_m\db::insert('content__comment',array('content_id' => $contentID,
                                               'creator' => $userID,
                                               'content' => $comment));
        
        return $res;
    }
    
    


    public function vote_up($contentID=false,$value=1)
    {
        
        if(!$userID) return \_m::setError('Invalid user.');

        //@TODO: logic to allow only admin to set dif value
        //$res = \_m\db::select();

        $res =  \_m\db::select('content__vote',false,array( array('content_id'=>$contentID),array('user_id'=>$userID)));

        if(sizeof($res)) return \_m::setError('Already voted.');



        //$res = \_m\db:insert();
    }

    public function vote_down($contentID=false,$value=-1)
    {
        //@TODO: write
        //@TODO: logic to allow only admin to set dif value

    }
    /*
    public function whereFromIdentifier($identifier,$tablePrefix=false)
    {
        if($tablePrefix) $tablePrefixString = $tablePrefix.'.';
        else $tablePrefixString = '';
            
        if(is_array($identifier))
        {
            $where = array();
            foreach($identifier as $curIdentifier)
                $where[] =  self::whereFromIdenfier($curIdentifier,$tablePrefix);
            //@TODO: ensure array nesting is correct
        }
        else
        {
            if(is_numeric($identifier))
                $where = array($tablePrefixString.'content_id'=>$identifier);
            else
                $where = array($tablePrefixString.'label'=>$identifier);
        }
        
        return array($where);
    }
*/
    
    public function whereFromIdentifier($identifier,$tablePrefix=false)
    {
        //@TODO: restore original object based version
        
        if($tablePrefix) $tablePrefixString = $tablePrefix.'.';
        else $tablePrefixString = '';
        
        if(is_numeric($identifier))
            $where = $tablePrefixString.'content_id = '.$identifier;
        else
            $where = $tablePrefixString.'label = '.$identifier;
        
        return $where;
    }
    
    public function whereWrapper($where='',$type=false,$tablePrefix=false)
    {
        if(!$type) return $where;
        if($tablePrefix) $tablePrefixString = $tablePrefix.'.';
        else $tablePrefixString = 'content.';

        if(is_array($where))
        {
            $where = array(SQL_AND => array($where,array($tablePrefixString.'type' => $type)));
            $where = array(SQL_AND => array($where,array('`'.$tablePrefixString.'content_id`' => '`content_'.$type.'.content_id`')));
        }
        elseif($where=='')
        {
            $where = $tablePrefixString.'type = '."'".$type."' AND ".$tablePrefixString.'content_id = content_'.$type.'.content_id';
        }
        else
        {
            $where .= ' AND '.$tablePrefixString.'type = '."'".$type."' AND ".$tablePrefixString.'content_id = content_'.$type.'.content_id';
        }



        return $where;
    }
    
    public function makeLabel($title)
    {
        //@TODO: make better
        $title = str_replace(' ', '-', strtolower($title));
        $title = preg_replace('/[^a-zA-Z0-9_-]/', '-', $title);
        
        return $title;
    }
    
    public function submitToCalais($content=false)
    {
        //$content = 'The super bowl was a good game, overall I enjoy football. I wish the Giants were playing.';
      
        if(!$content)
            return \_m::setError('No content was provided.');
        
        $contentTest = trim(str_replace('<br>','',$content));
        
        if($contentTest=='')
            return \_m::setError('No content was provided.');
        
        if(!\_m::inc('opencalais.php'))
            return \_m::setError('Calais tagging service is not available','Calais include file not found');
        
        $apiKey = \_m\settings::get(array('calais','api_key'));

        if(!$apiKey)
            return \_m::setError('Calais tagging service is not available','API Key Not found');
            
        $entities = array();
        try
        {
            $calais = new \OpenCalais($apiKey);
            $entities = $calais->getEntities($content);
        }
        catch(\OpenCalaisException $e)
        {
            return \_m::setError('Calais tagging service is not available',$e->getMessage());
        }

	//@TODO:cross reference against existing tags
	//		if tags already exist, attach ID, else use text only
        
        $retEntities = array();
        
        foreach($entities as $group => $tags)
        {
            foreach($tags as $title)
            {
                $retEntities[] = \_m\obj\tag::create($title,$group);
            }
        }
        
        \_m::setError(print_r($retEntities,true));
        
        return $retEntities;

    }

    public function getTables($type=false)
    {
        if(!$type||$type=='')
            return array('content LEFT JOIN user ON (content.creator=user.id)');
        else
            return array('content LEFT JOIN user ON (content.creator=user.id)','content_'.$type);
    }
}

?>
