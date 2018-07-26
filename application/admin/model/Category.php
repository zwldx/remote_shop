<?php
namespace app\admin\model;

use think\Model;

class Category extends Model{
    //展示分类
    public function select_categoryname(){
        
        return $this->select();
        
    }
    //新增分类
    public function insert_category($title){
        $data = [ 'category_name' => $title,'categoryid' =>null];
          $this->insert($data);
          
          //echo 222;
        /*$this->category_name=$title;
        $this->categoryid=$sort;
        
        return$this->save();
        
        */
        
    }
    
    
        
}
