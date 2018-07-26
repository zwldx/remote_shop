<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\index\model\Category;
use app\index\controller\Goods;

class Index extends Controller
{
    public $getcategory;
    public $goods;
    public function __construct(\think\App $app = null) {
        parent::__construct($app);
        $this->getcategory = new Category();
        $this->goods=new Goods();
    }
    public function index()
    {
        $category = $this->getcategory->select();
        $data = $this->goods->index();
        $categorylist=[];
        foreach ($category as $v){
           $res = Db::table('shop_goodsinfo')->where('categoryid',$v['categoryid'])->limit(6)->select();
          $categorylist[]=[$v['category_name'],$res];
        }
        
        //促销商品数据
        $data_sale = Db::name('goods_onsale')
        ->alias('go')
        ->join('shop_goodsinfo gi','go.goodsid=gi.goodsid')
        // ->where('begin_time < now()')
        ->where('end_time > now()')
        ->field('go.goodsid,go.price,go.storage,gi.thumb,gi.goods_name,go.id')
        ->select();    
        // print_r($data_sale);
        // die;    
        return $this->fetch('index/index',['category'=>$category,'data'=>$data,'categorylist'=>$categorylist]);
    }   

}
