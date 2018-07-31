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
    //检测抢购是否开始
    public function chkSaleStart($saleId){
        $begin_time = $this->redis->hget($saleId,'begin_time'); 
        $end_time = $this->redis->hget($saleId,'end_time'); 
        $now = time();
        if($now > strtotime($begin_time)&& $now < strtotime($end_time)&&$this->redis->exists("{$saleId}_start")){
            return true;
        }else{
            return false;
        }
    }

    //把用户id存入redis链表，并通知用户
    public function saveToRedis($uid,$saleId){    
        if(!$this->chkSaleStart($saleId)){
            echo 0;//0表示抢购失败
            return;
        }    

        $this->redis->lpush('buyer',$uid);
        
        //下面为测试数据
        // $this->redis->lpush('buyer',rand(1, 100));
        // $this->redis->lpush('buyer',1);
        // $this->redis->lpush('buyer',3);
        // $this->redis->lpush('buyer',4);
        // $this->redis->lpush('buyer',5);
        // $this->redis->lpush('buyer',6);
        //获取商品库存量
        $num = $this->redis->hget('sell_limit',$saleId);
        while(true){
            if($this->redis->sismember('users_got',$uid)){
                echo 1;//抢购成功
                break;
            }else{
                // echo $this->redis->scard('users_got');//问题排查，原来是users_got写成了user_got
                // echo '<br>';
                // echo $num;
                if($this->redis->scard('users_got')>=$num){
                    
                    // echo $this->redis->scard('users_got');
                    echo 0;//抢购失败
                    break;
                }else{
                    // echo -1;//排队中
                    // echo str_repeat(' ',64*1024);
                    // flush();//不知道为什么输出缓冲区总是不行
                    // if(!$this->redis->get('$')){
                        // echo -1;
                        // break;
                    // }
                    sleep(2);//加了这个，结果总是跟预期不一样，不知道为什么。
                    // break;
                }
            }
            // sleep(2);
        }
    }

    //从redis链表中读取
    public function readFromRedis($saleId){
        //抢购开始标记在内存
        $this->redis->set("{$saleId}_start",1);
        //获取商品库存量
        $num = $this->redis->hget('sell_limit',$saleId);
        // print_r($num);
        while(true){
             $uid = $this->redis->rpop('buyer');
            //  echo $uid;
            if($uid>0){
                $num--;
                // $users_got = $this->redis->hget('users_got',$saleId);
                // $this->redis->hset('users_got',$saleId,$users_got+1);

                $this->redis->sadd('users_got',$uid);//为的是统计处理的用户数
                echo "已经生成订单,用户id为{$uid}\r\n";
            }else{
                echo "没有取到对应的用户id\r\n";
            }
            if($num<=0){
                echo "所有订单处理完毕\r\n";
                break;
            }            
            sleep(1);
        }
        $this->redis->del("{$saleId}_start");//删除开始标记，防止抢购结束后，用户再次抢购
        sleep(3);//时间大于saveToRedis中的sleep(2)，否则最后一个用户无法取到，时间可以设置更久一些
        $this->redis->del('buyer');//清空抢购用户队列
        $this->redis->del('users_got');//清空抢购用户集合
        
    }

    // //生成订单
    // public function generateOrder(){
    //     $data = [];
    //     Db::name('order_detail')
    //     ->
    // }

    // //查询
    // public function getStorage(){
    // }

    //抢购商品初始化
    public function initSell($saleId){
        $storage = Db::name('goods_onsale')->find($saleId)['storage'];
        $arr_time = Db::name('goods_onsale')
        ->where("id = {$saleId}")
        ->field('begin_time,end_time')
        ->find();
        $this->redis->hset($saleId,'begin_time',$arr_time['begin_time']);
        $this->redis->hset($saleId,'end_time',$arr_time['end_time']);
        $r = $this->redis->hset('sell_limit',$saleId,$storage);//抢购商品数量限制
        $this->redis->del('buyer');//初始化抢购用户队列
        $this->redis->del('users_got');//初始化抢购用户集合
        $this->redis->del("{$saleId}_start");//初始化抢购开始标记
        
        // $this->redis->hset('users_got',$saleId,0);//抢购用户量初始化

        return $r;
    }
}
