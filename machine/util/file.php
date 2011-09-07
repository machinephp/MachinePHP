<?php
namespace _m\util;

class file
{


    // $fileTypesBlock - if true, only block the provided fileTypes
    public function handleUpload($fieldName,$destination=false,$fileName=false,$maxSize=20000000,$fileTypes=array(),$fileTypesBlock=false)
    {
        
        if(!isset($_FILES[$fieldName]))
            return \_m::setError('File upload failed.');
        
        if($_FILES[$fieldName]['error']>0)
            return \_m::setError('File upload failed.','File upload error code:'.$_FILES[$fieldName]['error']);
       
        if(sizeof($fileTypes))
        {   //@TODO: write filetype4 check
            //if($fileTypesBlock)
        }
        /*
        if (($_FILES[$fieldName]["type"] != "image/gif")
                    && ($_FILES[$fieldName]["type"] != "image/jpeg")
                    && ($_FILES[$fieldName]["type"] != "image/pjpeg")
                    && ($_FILES[$fieldName]["type"] != "image/png")
                    && ($_FILES[$fieldName]["type"] != "image/bmp"))
                    {
                            $message = 'File uploaded not an image.';
                            return false;
                    }
            */
        
            if($_FILES[$fieldName]["size"] > $maxSize) //20MB limit
            {
                    $message = 'File uploaded too large.';
                            return false;
            }

            if ($_FILES[$fieldName]["error"] > 0)
            {
                    $message = 'Error in the upload process.';
                    return false;
            }

            if(!$name) $name = $_FILES[$fieldName]["name"];
            
            if(move_uploaded_file($_FILES[$fieldName]["tmp_name"],$destination.'/'.$name))
                return $name;
            return false;
    }

    public function uploadForm($params)
    {
        $divID = $params['name'];
        $loadingHTML = 'loading';
        $errorHTML = 'error';

        $name = $params['name'];

        $scriptURL = '/admin/upload?filename='.$params['name'].'&object_type='.$params['object_type'].'&object_id='.$params['object_id'].'&label='.$params['label'];
        $maxSize = 99999999;   //in Bytes
        $relPath = '/admin/upload';
        $fullPath = 'http://'.DOMAIN_NAME.'/admin/upload';
        
        
       ?>
        <form>
        <p>
            <label for="<?=$params['name']?>"><?=$params['name']?></label>
            <input type="file" name="<?=$params['name']?>" onchange="machine.upload.ajaxUpload(this.form,'<?=$scriptURL?>','<?=$name?>','<?=$loadingHTML?>','<?=$errorHTML?>'); return false;" /></p>
        </form>
        <div id="<?=$params['name']?>">&nbsp;</div>
        <?php
    }
}

?>
