<?php

namespace _m\obj;

class video extends _content
{
    
    public $type = 'video';
    public $identifier = 'video_id';
    
    
    public function create($data=false,$tags=array())
    {
        if(!$data)
            return \_m::setError("No data provided.");
        
        if(is_array($data)&&(isset($data['title'])))
            $data['label'] = $this->makeLabel($data['title']);
        
        \_m::setError(print_r($data,true),true);
        
        $id = parent::create($data);
        
        if(!$id)
            return \_m::setError("Unable to create joke.");
            
        foreach($tags as $tag)
        {
            if(isset($tag['id']))
                $this->tag_apply($id,$tag['id']);
            else
                $this->tag_apply($id,$tag['title']);
        }
        
        return true;
        
    }
    
    public function getAPIData($url)
    {
        $type = self::determineType($url);
        if($type===false) return \_m::setError('Invalid Service');
        $id = self::determineID($url,$type);
        if($id===false) return \_m::setError('Invalid Video');
        
        switch($type)
        {
            case 1: //youtube
                $data= self::api_youtube($id);
                break;
            case 2: //vimeo
                $data= self::api_vimeo($id);
                break;
            default:
                $data = array();
                break;
        }
        
        if(!$data)
            return \_m::setError('Unable to retrieve video data.');
        
        \_m\data::set('video_submission',$data);
        return $data;
        
    }
    
    private function determineType($url)
    {
        //@TODO: improve logic
        if(strpos($url, 'youtube.com')) return 1;
        if(strpos($url, 'vimeo.com')) return 2;
        
        return 0;
    }
    
    private function determineID($url, $type)
    {
        //@TODO: improve logic
        switch($type)
        {
            case 1: //youtube
                $url_string = parse_url($url, PHP_URL_QUERY);
                 parse_str($url_string, $args);
                 return isset($args['v']) ? $args['v'] : false;
                break;
            case 2: //vimeo
                return substr($url,(1+strrpos($url,'/')));
                break;
            default:
                return false;
        }
    }
    
    private function api_vimeo($id)
    {
        $url = 'http://vimeo.com/api/v2/video/'.$id.".php";
        $videoDataRaw = @file_get_contents($url);
        $data =  @unserialize(trim($videoDataRaw));
        $data = $data[0];
        $retData = array();
        $retData['title'] = $data['title'];
        $retData['description'] = $data['description'];
        $retData['thumbnail'] = $data['thumbnail_medium'];
        $retData['foreign_type'] = 2;
        $retData['foreign_id'] = $id;
        $retData['label'] = \_m\obj\_content::makeLabel($data['title']);
        $retData['status'] = 1;
        return $retData;
    }
    
    private function api_youtube($id)
    {
        $url = 'http://gdata.youtube.com/feeds/api/videos/'.$id.'?alt=jsonc&v=2';
        $videoDataRaw = @file_get_contents($url);
        $data = json_decode(trim($videoDataRaw));
        $data = $data->data;
        
        $retData = array();
        $retData['title'] = $data->title;
        $retData['description'] = $data->description;
        $retData['thumbnail'] = $data->thumbnail->sqDefault;
        $retData['foreign_type'] = 1;
        $retData['foreign_id'] = $id;
        $retData['label'] = \_m\obj\_content::makeLabel($data->title);
        $retData['status'] = 1;
        
        return $retData;
    }
}

?>
