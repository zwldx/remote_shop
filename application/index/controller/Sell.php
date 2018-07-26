<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\facade\Session;

class Sell extends Controller
{
    public function __construct(\think\App $app=null)
    {
        parent::__construct($app);
        $this->sell = new \app\index\model\Sell();//实例化
    }

    /**
     * 促销商品展示
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch('sell/sell');
    }

    /**
     * 抢购功能
     */
    public function buyLimit(){
        $userid = Session::get('userid');
        $this->sell->saveToRedis($userid);
        return '抢购成功';
    }

    //促销订单生成
    public function SellOrder(Request $request){
        //获取促销商品id
        // $id = $request->param('id');
        $id = 1;//模拟id
        $this->sell->readFromRedis($id);
    }

    public function showSellGoods(){
        return $this->fetch('sell/selldetails');
    }
}
