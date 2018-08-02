<?php
define('SECRET','wakaka');
//生成n位随机字母
function getRandStr($len = 6){
    $code = '';
    for($i=1;$i<=$len;$i++){
        $code .= chr(rand(97,122));
    }
    return strtoupper($code);
}

function get_sign($user_id,$password,$secret,$user_expire_date,$user_agent){
   return md5($user_id.$password.$secret.$user_expire_date.$user_agent);
}