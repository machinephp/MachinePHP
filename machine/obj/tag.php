<?php

namespace _m\obj;

class tag extends _base
{
    
    private $type = 'tag';
    private $identifier = 'tag_id';
    
    // given a tag ID or label, this determines if it exists, if not it creates it and returns the full tag object
    public function determine($tag)
    {
        
        if(!is_numeric($tag))
            $tagData = self::find($tag);
        else
            $tagData['tag_id'] = $tag;
         
        if(!$tagData)
            $tagData = self::create($tag);
        
        if(!$tagData)
            return \_m::error('Tag could not be created :"'.$tag.'"');
        
        return $tagData;
    }
    
    public function create($tag,$group='')
    {
        $data = \_m\db::select('tag',false," LOWER(title) LIKE '".strtolower($tag)."'");
        
        if(\_m\db::numRows())
            return $data[0];
        
        $id = \_m\db::insert('tag',array('title' => $tag, '`group`' => $group,'label' => self::makeLabel($tag)));
        
        $data = \_m\db::select('tag',false,array(array('tag_id'=>$id)));
        
        if($data)
            return $data[0];
        
        return false;
    }
    
    public function delete($identifier=false)
    {   
        \_m::setError('ok');
        return \_m\db::delete('tag',$identifier);
    }
    
    public function listing($query=false,$start=0,$total=10)
    {
        //@TODO: query;
        $limit = array($start,$total);
        return \_m\db::select('tag',false,false,false,$limit);
    }
    
    public function update($identifier=false,$data=false)
    {
        //@TODO: improve
        return \_m\db::update('tag',$data);
    }
    
    public function find($tag=false,$group=false)
    {
        if(!$tag) return false;

        $data = \_m\db::select('tag','title label'," LOWER(title) LIKE '".strtolower($tag)."%'");

        return $data;
    }

    public function find_label($label=false)
    {
        if(!$label) return false;

        $data = \_m\db::select('tag','tag_id, title, label'," LOWER(label) LIKE '".strtolower($label)."'");

        return $data;
    }
    
    public function makeLabel($tag)  
    {
        return str_replace(array(' '),array('-'),strtolower($tag));
    }



    public function listing_top($timeframe='day',$count=10)
    {
        //@TODO: join with top table
        $sql  = 'SELECT t.* FROM tag t LEFT JOIN tag__stats ts ON (t.tag_id=ts.tag_id) WHERE ts.label = "views-'.$timeframe.'" ORDER BY VALUE DESC LIMIT '.$count;
        $res  = \_m\db::query($sql);
        $data = \_m\db::getResultArray($res);

        return $data;
    }

    public function update_stats($timeFrame='day')
    {
        
         if(!\_m::inc('ga.php'))
            throw new mException('No Google Analytics Installed');

          $data = array();
          $filterText = '/hottopic/';
          $maxResults= 300;



          try {

              $oAnalytics = new \analytics('matt.webdeveloper@gmail.com', 'ASDFasdf5');

              $oAnalytics->setProfileByName('DailyComedy.com');

              $oAnalytics->setDateRange(date("Y-m-d", strtotime("-1 ".$timeFrame."s")), date('Y-m-d'));

              $data = $oAnalytics->getData(array(   'dimensions' => 'ga:pagePath',

                                                    'metrics'    => 'ga:pageviews',

                                                    'sort'       => '-ga:pageviews',

                                                    'filters'    => 'ga:pagePath=~^'.$filterText.'.*',

                                                    'max-results' => $maxResults));


          } catch (Exception $e) {

             throw new mException($e);

          }

          self::clear_stats($timeFrame);
          echo '<pre>'.print_r($data,true).'</pre>';


              foreach($data as $label => $count)
              {
                  $label = trim(strtolower(str_replace(array($filterText,'_'),array('','-'),$label)));
                  if($label=='') continue 1;

                  $tag = self::find_label($label);

                  echo $timeFrame.' : '.$label.' : '.$count.' -- '.print_r($tag,true).'<br>';

                  if($tag) \_m\db::insert('tag__stats',array('tag_id'=>$tag[0]['tag_id'],'label'=>'views-'.$timeFrame,'value'=>$count));

              }
          }

        public function clear_stats($timeFrame='day')
        {
            \_m\db::delete('tag__stats',array(array('label'=>'views-'.$timeFrame)));
        }

    
}

?>
