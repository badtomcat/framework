<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/21
 * Time: 17:10
 */
function isMobile(){
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $keywords = array("android", "iphone", "ipod", "ipad", "windows phone", "mqqbrowser", "symbian", "blackberry", "ucweb", "linux; u;" ) ;
    foreach($keywords as $kw){
        if(strpos($userAgent, $kw) !== false)return true;
    }
    return false;
}