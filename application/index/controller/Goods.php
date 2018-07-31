<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\index\model\Category;
use think\request;
use app\index\model\Goodsinfo;
class Goods extends Controller
{  
    //首页显示商品
    public function index()
    { 
       return $data = Db::table('shop_goodsinfo')->order('goodsid')->select();
    }

    //显示商品详情
    public function showDetail(Request $request)
    {
        $goodsid = $request->param('goodsid');
        //echo $goodsid;
        $goodsinfo = Db::table('shop_goodsinfo')->where('goodsid',$goodsid)->select();
        //dump($goodsinfo);
         $category = Db::table('shop_category')->select();
        return $this->fetch('goods/product',['goodsinfo'=>$goodsinfo,'category'=>$category]);
    }

    //根据关键词查询商品
    public function searchGoods(Request $request)
    {  $search = $request->param('search');
       //echo $search;
       $categorylist = Db::table('shop_goodsinfo')->where('goods_name','like',"%$search%")->select();
        //dump($categorylist);
         $category = Db::table('shop_category')->select();//查询数组分类
        return $this->fetch('goods/categorylist_1',['categorylist'=>$categorylist,'category'=>$category]);
    }

    //分类页面详情
    public function categoryList(Request $request)
    {
       $categoryid = $request->param('categoryid');
      // echo $categoryid;
       $categorylist = Db::table('shop_goodsinfo')->where('categoryid',$categoryid)->paginate(1);
       //dump($categorylist);
       $category = Db::table('shop_category')->select();//查询数组分类
       
        //print_r($category);
       // return $this->fetch('index/index',['category'=>$category]);//返回到首页，并将分类传给首页
        return $this->fetch('goods/categorylist',['category'=>$category,'categorylist'=>$categorylist]);
       
      // return $this->fetch('goods/categorylist');
      
    }

    public function category()
    {
        return $this->fetch('goods/category');
    }

}
