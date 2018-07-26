<?php

namespace app\admin\model;

use think\Model;


class Business extends Model
{
    //根据商家输入的用户名查询密码
    public function select_password_by_username($name){
    return $this->where('username',$name)->field('password')->select();
   
    }
    
}


