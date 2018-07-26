<?php

namespace app\index\model;

use think\Model;
use Redis;
use think\Db;

/**
 * 商品促销模型类
 */
class Sell extends Model
{
    public function __construct(\think\App $app=null)
    {
        parent::__construct($app);
        $this->redis = new Redis();//实例化
        $this->redis->connect('127.0.0.1',6379);
    }
    //把用户id存入redis链表
    public function saveToRedis($uid){        
        $this->redis->lpush('buyer',$uid);
        //下面为测试数据
        // $this->redis->lpush('buyer',rand(1, 100));
        // $this->redis->lpush('buyer',1);
        // $this->redis->lpush('buyer',3);
        // $this->redis->lpush('buyer',4);
        // $this->redis->lpush('buyer',5);
        // $this->redis->lpush('buyer',6);
    }

    public function notifyUser($userid){
        $result = Db::name('order')->where('userid',$userid)
        ->find();
    }

    //从redis链表中读取
    public function readFromRedis($sellGoodsId){
        //获取商品库存量
        $num = Db::name('goods_onsale')->find($sellGoodsId)['storage'];
        // print_r($num);
        while(true){
             $uid = $this->redis->rpop('buyer');
            //  echo $uid;
            if($uid>0){
                $num--;
                echo "已经生成订单,用户id为{$uid}\r\n";
            }else{
                echo "没有取到对应的用户id\r\n";
            }
            if($num<=0){
                echo "所有订单处理完毕\r\n";
                break;
            }            
            sleep(2);
        }
    }

    // //生成订单
    // public function generateOrder(){
    //     $data = [];
    //     Db::name('order_detail')
    //     ->
    // }
}
