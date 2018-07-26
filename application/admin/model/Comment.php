<?php

namespace app\admin\model;

use think\Model;
use think\Db;
class Comment extends Model
{
    //
    public function getAllComm(){
        $data = Db::table('shop_comment')->alias('c')->join('shop_user u','c.uid=u.userid')->field('u.username,c.id,c.title')->order('c.uid')->select();
        return $data;
    }
    
    public function getCommStr($data){
        
        $strs = [];//保存数组分组后的评论
        
        foreach ($data as $v){
            $strs[$v['username']][] = ['title'=>$v['title'],'id'=>$v['id']];
        }

        $str = '';//输出最终的字符串
        foreach($strs as $k => $v){
            $str.=$k.'发表过的评论标题有:';
            foreach ($v as $v_2){
                $str.="(评论id:{$v_2['id']})";
                $str.= "标题:{$v_2['title']}";                         
            }
            $str.="<hr>";
        }
        return $str;
    }
}
