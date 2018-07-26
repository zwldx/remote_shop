<?php

namespace app\index\model;

use think\Model;

class Category extends Model
{
   
    public function getCategory()
    {
     $data = $this->select();//查询数组分类
       // echo '<br>';
        //print_r($category);
        //return $this->fetch('index/index',['category'=>$category]);//返回到首页，并将分类传给首页
       $this->assign('category',$data);
    }

    
}
