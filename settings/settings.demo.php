<?php

$settings = array();

////////////////////////////////    General       ////////////////////////////////
$settings['domain_name'] = 'www.site.net';
$settings['folder_root'] = false;               //- if subdir (ie: test.com/subdir/)



////////////////////////////////    OpenCalais    ////////////////////////////////
$settings['calais']['api_key'] = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx';


////////////////////////////////    Facebook    ////////////////////////////////
$settings['facebook']['id'] = '1234123412341324';
$settings['facebook']['secret'] = 'xxx12312341234xxxx';
$settings['facebook']['file_upload'] = true;
$settings['facebook']['redirect_url'] = false;
$settings['facebook']['scope'] = 'email,user_photos,friends_photos,user_status,user_photo_video_tags,friends_photo_video_tags';



//--------------------OLD-----------------
////		to be deleted
 
define('PAGE_404',APP_ROOT.'404.php');
define('DEBUG_FILENAME',FILE_ROOT."output.txt");

?>
