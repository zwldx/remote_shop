<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;


class Order extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch('index/order_manager');
    }

    //商家查询订单
    public function sellQuery()
    {
    
        //查订单表和地址表，查到该用户对应订单下的收货地址，获得地址和该用户下的订单id
        $data = Db::table('shop_order')->join('shop_addr','shop_order.addrid=shop_addr.addrid')->select();
//dump($data);
        $orderlist = [];
       
        foreach ($data as $v) {
           //跟据订单该用户的订单编号查订单详情的到具体购买的商品id和购买数量
            $res = Db::table('shop_order_detail')->where('orderid',$v['orderid'])->select();
            //dump($res);           
            $goods = [];
            $goodslist = [];
            foreach ($res as $val) {
                //跟据该订单下的商品id查询具体的商品,得到商品名价格
                $goods_res = Db::table('shop_goodsinfo')->where('goodsid',$val['goodsid'])->select();
                //dump($goods_res);
                 //$orderlist[]=[$goods_res[0]['goods_name']];
                if(!empty($goods_res)){
                $goods['thumb']=$goods_res[0]['thumb'];
                 $goods['goodsname']=$goods_res[0]['goods_name'];
                  $goods['price']=$goods_res[0]['price'];
                   $goods['goodsnum']=$val['num'];
                    $goods['tal']=$val['num']*$goods_res[0]['price'];
                    $goodslist[] = $goods;
                }
                
            }
             
               $v['goods']=$goodslist;
            $orderlist[]=[$v];
            
            
        }
       // echo '<pre>';
        //print_r($orderlist);
        
        return $this->fetch('index/order_manager',['orderlist'=>$orderlist]);

    }

    //商家删除订单
    public function deleteOrder()
    {
        
    }

    //取消订单
    public function cansel(Request $request){
         $orderid = ($request->orderid);
         
         Db::table('shop_order')->where('orderid',$orderid)->update(['status'=>2]);
         return 1;
     }
     
     //接收订单
    public function reorder(Request $request){
         $orderid = ($request->orderid);
         $log_num = ($request->log_num);
         
         Db::table('shop_order')->where('orderid',$orderid)->update(['log_num'=>$log_num]);
          Db::table('shop_order')->where('orderid',$orderid)->update(['status'=>1]);
         return 1;
     }





    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
