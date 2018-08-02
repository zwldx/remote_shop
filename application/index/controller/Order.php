<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\facade\Cookie;


class Order extends Base
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }   

    public function userQuery()
    {
        $this->chkLogin();
        return $this->fetch('order/member_user');
    }
    public function orderDetail()
    {
        //登录检验并从session中取$userid
        $userid = $this->chkLogin();
        //根据用户id查订单表和地址表，查到该用户对应订单下的收货地址，获得地址和该用户下的订单id
        $data = Db::table('shop_order')->join('shop_addr','shop_order.addrid=shop_addr.addrid')
                ->where('shop_order.userid',$userid)->select();
        $orderlist = [];
       
        foreach ($data as $v) {
           //跟据订单该用户的订单编号查订单详情的到具体购买的商品id和购买数量
            $res = Db::table('shop_order_detail')->where('orderid',$v['orderid'])->select();
           // dump($res);           
            $goods = [];
            $goodslist = [];
            foreach ($res as $val) {
                //跟据该订单下的商品id查询具体的商品,得到商品名价格
                $goods_res = Db::table('shop_goodsinfo')->where('goodsid',$val['goodsid'])->select();
                //dump($goods_res);
                // $orderlist[]=[$goods_res[0]['goods_name']];
                if (!empty($goods_res)) {
                    $goods['thumb']=$goods_res[0]['thumb'];
                    $goods['goodsname']=$goods_res[0]['goods_name'];
                    $goods['price']=$goods_res[0]['price'];
                    $goods['goodsnum']=$val['num'];
                    $goods['tal']=$val['num']*$goods_res[0]['price'];
                    $goodslist[] = $goods;
                }
                /*
                 $goods[] = [
                     'thumb'=>$goods_res[0]['thumb'],
                     'goodsname'=>$goods_res[0]['goods_name'],
                     'price'=>$goods_res[0]['price'],
                     'goodsnum'=>$val['num'],
                     'tal'=> $val['num']*$goods_res[0]['price']
                         ];
                 
                 */
                //$goodslist[] = $goods_res[0]['goods_name'];
               //$goodsnum[] = $val['num'];
               //$price[] = $goods_res[0]['price'];
               //$tal[] = $val['num']*$goods_res[0]['price'];             
               //$thumb[] = $goods_res[0]['thumb'];
            }
             //$v[]=['goodslist'=>$goodslist,'goodsnum'=>$goodsnum,'price'=>$price,'tal'=>$tal,'thumb'=>$thumb];
               $v['goods']=$goodslist;
               $orderlist[]=[$v];     
            
        }
        //echo'<pre>';
        //print_r($orderlist);
        //dump($orderlist);
        $expire_date =Cookie::get('expire_date');
        return $this->fetch('order/member_order',['orderlist'=>$orderlist,'ex_date'=>$expire_date]);        
    }

    public function checkOut()
    {
        
    }

    public function address(){
        return $this->fetch('order/member_addr');
    }
    
     public function confirmgoods(Request $request){
         //登录检验并从session中取$userid
         $userid = $this->chkLogin();
         $orderid = ($request->orderid);
         Db::table('shop_order')->where('orderid',$orderid)->update(['status'=>3]);
         return 1;
     }
     //取消订单
     public function delorder(Request $request){
         //登录检验并从session中取$userid
         $userid = $this->chkLogin();
         $orderid = ($request->orderid);
         Db::table('shop_order')->where('orderid',$orderid)->update(['status'=>2]);
         return 1;
     }
     //添加收货地址
     
     //查询物流信息
     public function getLogMsg(){
        header("Content-Type:text/html;charset=UTF-8");
        date_default_timezone_set("PRC");
        //物流接口相关数据
        //应用id
        $showapi_appid = '68704';
        //获取物流单号
        $nu = request()->param('logNum');
        //物流公司
        $com = 'yuantong';
        //生成密钥用的参数
        $showapi_secret = "05f00d15568446eabdc52c39161902c2";

        $paramArr = array(
            'showapi_appid'=> $showapi_appid,
                'com'=> $com,
                'nu'=> $nu
            );
       
        //接口地址及参数
        $param = createParam($paramArr,$showapi_secret); 
        $url = 'http://route.showapi.com/64-19?'.$param;
        
        $data = file_get_contents($url);

        return json_decode($data);
     }
}
