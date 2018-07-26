<?php
namespace app\admin\controller;

//进入后台以后每个页面也需要判断是否登陆
class Base extends Controller{
    public function have_login(){
    if(empty($_SESSION['username'])){
        return ture;
    }else{
        return false;
    }
}
//判断页面是否登录
}
if( have_login('true')){
    $this->success('您未登录','/admin/Suser/login');
}