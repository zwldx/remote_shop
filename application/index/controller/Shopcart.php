<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
class Shopcart extends Base
{
    public function index()
    {
         //登录检验并从session接受用户id
         $userid = $this->chkLogin();       
        

         //查询shop_cart表中是否已存在商品
         $result = Db::name('cart')
         ->where('userid',$userid)
         ->select();
 
          //根据用户id查询shop_cart和shop_goodsinfo表
        $data = Db::table('shop_cart')
        ->alias('c')
        ->join('shop_goodsinfo g','c.goodsid = g.goodsid')
        ->where('c.userid ='.$userid)
        ->field('cartid,thumb,goods_name,price,num,price*num subtotal')
        ->select();

        //计算用户购物总价
        $total = Db::table('shop_cart')
        ->alias('c')
        ->join('shop_goodsinfo g','c.goodsid = g.goodsid')
        ->where('c.userid ='.$userid)
        ->field('sum(c.num*g.price) as total')
        ->find();

        if($result)
        {
            //获取分类列表并赋给模板
            $this->getCategoryList();
            return $this->fetch('buycar',['data'=>$data,'total'=>$total['total']]);
         }else{
            return $this->error('购物车为空','/index/index/index');
         }
    }   


    //订单确认
    public function orderSure(Request $request){
        
        //登录检验并从session中取$userid
        $userid = $this->chkLogin();

        //登录检验并从session接受用户地址id
        $addrid = $request->addrid;

        //定义一个orderid和total变量，在匿名函数外面需要调用
        $orderid = 0;
        $total = 0;

        //执行事务
        Db::transaction(function() use($userid,$addrid,&$orderid,&$total){
            //计算商品总价
            $total = Db::table('shop_cart')
            ->alias('c')
            ->join('shop_goodsinfo g','c.goodsid = g.goodsid')
            ->where('c.userid ='.$userid)
            ->field('sum(c.num*g.price) as total')
            ->select();
    
            //待插入的数据，先插入order表,再插入order_detail表
            $data_o  = ['total_price'=>$total[0]['total'],'status'=>0,'addrid'=>$addrid,'userid'=>$userid];
            // $data_od  = ;
            //插入数据并获得自增后的id
            $orderid = Db::name('order')->insertGetId($data_o);

            //获取购物车中的商品
            Db::execute("insert into shop_order_detail(`num`,`goodsid`,`orderid`) select `num`,`goodsid`,$orderid from shop_cart where userid=$userid");

            //删除已提交订单的购物车商品记录
            Db::name('cart')
            ->where('userid',$userid)
            ->delete();
        });

        //如果$orderid表示表插入成功且返回最新自增id
        if($orderid>0 && $total>0){
             //获取分类列表并赋给模板
             $this->getCategoryList();
            return $this->fetch('buycar_three',['orderid'=>$orderid,'total'=>$total]);
        }else{
            return $this->error('订单生成失败');
        }
    } 
    //清空购物车
    public function clearShopCart()
    {
        //登录检验并从session中获取userid
        $userid = $this->chkLogin();
        $result = Db::name('cart')
        ->where('userid',$userid)
        ->delete();
        //返回删除的行数
        return $result;

    }

    //根据商品id从购物车移除商品
    public function removeById()
    {
        //登录检验并从session中获取userid
        $userid = $this->chkLogin();
        $cartid = $_GET['cartid'];
        $result = Db::name('cart')
        ->where('cartid',$cartid)
        ->delete();

        if($result){
            //查询商品总价
            $total = Db::table('shop_cart')
            ->alias('c')
            ->join('shop_goodsinfo g','c.goodsid = g.goodsid')
            ->where('c.userid ='.$userid)
            ->field('sum(c.num*g.price) as total')
            ->find();
            //查询商品价格
            $price_num = Db::name('cart')
            ->alias('c')
            ->join('goodsinfo g','c.goodsid=g.goodsid')
            ->where('c.cartid',$cartid)
            ->field('g.price,c.num')
            ->find();
            $result = json(['total'=>$total['total'],'price'=>$price_num['price'],'num'=>$price_num['num']]);
        }
        
        return $result;
    }

    //修改商品数量
    public function modifyNumber()
    {
        
        //登录检验从session中获取userid
        $userid = $this->chkLogin();
        //获取数量
        $num = $_GET['num'];
        $cartid = $_GET['cartid'];

        // $result = Db::name('cart')
        // ->where('cartid',$cartid)
        // ->delete();

        //更新购物车商品数目
        $result = Db::name('cart')
        ->where('cartid',$cartid)
        ->update(['num'=>$num]);

        if($result){
            //查询商品总价
            $total = Db::table('shop_cart')
            ->alias('c')
            ->join('shop_goodsinfo g','c.goodsid = g.goodsid')
            ->where('c.userid ='.$userid)
            ->field('sum(c.num*g.price) as total')
            ->find();
            //查询商品价格
            $price_num = Db::name('cart')
            ->alias('c')
            ->join('goodsinfo g','c.goodsid=g.goodsid')
            ->where('c.cartid',$cartid)
            ->field('g.price,c.num')
            ->find();
            $result = json(['total'=>$total['total'],'price'=>$price_num['price'],'num'=>$price_num['num']]);
        }
        
        return $result;

    }

    //添加商品到购物车
    public function addToCart()
    {
        //登录检验并从session接受用户id
        $userid = $this->chkLogin();
        //获取商品id
        $goodsid = $_GET['goodsid'];
        //查询购物车的商品是否已存在
        $cartid = Db::name('cart')
        ->where('userid',$userid)
        ->where('goodsid',$goodsid)
        ->field('cartid')
        ->find();

        //如果购物车不存在该商品，执行插入操作,否则执行更新操作
        if(empty($cartid))
        {
            $data = ["userid"=>$userid,"num"=>1,"goodsid"=>$goodsid];
            $cartid = $result = Db::name('cart')
            ->insertGetId($data);
        }else{
            //当购物车商品存在时，点击添加到购物车按钮，则商品数量加1
            $result = Db::name('cart')
            ->where('cartid',$cartid['cartid'])
            ->setInc('num',1);
        }

        if($result)
        {
            // return json(['num'=>$num,'cartid'=>$cartid,'price'=>$price['price'],'total'=>$total[0]['total']]);
            return true;
        }else{
            return false;
        }

    }

    //统计商品数量进入订单确认页面
    public function account()
    {

        //登录检验并从session接受用户id
        $userid = $this->chkLogin();

        //根据用户id查询shop_cart和shop_goodsinfo表
        $data = Db::table('shop_cart')
        ->alias('c')
        ->join('shop_goodsinfo g','c.goodsid = g.goodsid')
        ->where('c.userid ='.$userid)
        ->field('cartid,thumb,goods_name,price,num,price*num subtotal')
        ->select();

        //计算用户购物总价
        $total = Db::table('shop_cart')
        ->alias('c')
        ->join('shop_goodsinfo g','c.goodsid = g.goodsid')
        ->where('c.userid ='.$userid)
        ->field('sum(c.num*g.price) as total')
        ->find();

        $addr = Db::name('addr')
        ->where('userid',$userid)
        ->order('ctime desc')
        ->field('addrid,addr,consigner,phone')
        ->find();

         //获取分类列表并赋给模板
         $this->getCategoryList();
        return $this->fetch('buycar_two',['data'=>$data,'total'=>$total['total'],'addr'=>$addr]);
    }
}
