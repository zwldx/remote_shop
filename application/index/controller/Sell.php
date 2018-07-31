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
    public function buyLimit(Request $request){
        
        $saleId = $request->param('saleId');
        $userId = Session::get('userid');
        $userId = $request->param('userId');//仅为方便模拟多用户，项目完成后需要注释掉
        $this->sell->saveToRedis($userId,$saleId);
        // return '抢购成功';
    }

    //促销订单生成
    public function sellOrder(Request $request){
        //获取促销商品id
        $saleId = $request->param('saleId');
        // $saleId = 1;//模拟id
        $this->sell->readFromRedis($saleId);
    }

    //展示抢购商品具体信息
    public function showSellGoods(){
        return $this->fetch('sell/selldetails');
    }

    //通知用户是否抢购成功
    // public function notifyResult(Request $request){
    //     $userId;  
    //     $saleId;
    //     return $this->sell->notifyUser($userId,$saleId);
    // }

    //抢购前初始化
    public function initSell(Request $request){
        $saleId = 1;//模拟抢购商品的id
        $saleId = $request->param('saleId');//抢购商品的id
        $r = $this->sell->initSell($saleId);
        if($r){
            echo '设置抢购商品数量成功';
        }else{
            echo '抢购商品数量数值已覆盖';
        }
    }
}
