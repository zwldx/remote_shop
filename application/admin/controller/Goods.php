<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
//商品信息类

class Goods extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $goodslist = Db::table('shop_goodsinfo')->select();
        //dump($goodslist);
        return $this->fetch('index/goods_manager',['goodslist'=>$goodslist]);
    }

    //显示添加页面
    public function toAdd()
    {
       $category = Db::table('shop_category')->select();
       //dump($category);
        return $this->fetch('index/add',['category'=>$category]);
    }

    //添加商品
    public function addGoods(Request $request)
    {
         $file = request()->file('afile') ;
        $goodsname = ($request->goodsname);
        $price = ($request->price);
        //$thumb = ($request->file);
        $cid = ($request->cid);
        $detail = ($request->content1);
         $info = $file->move('/static/index/images');
         //dump($info);
        $data =[
            'goodsid'=>null,
            'goods_name'=>$goodsname,
            'thumb'=>$info->getSaveName(),
            'price'=>$price,
            'detail'=>$detail,
            'categoryid'=>$cid
        ] ;
        
        Db::table('shop_goodsinfo')->insert($data);
        

    }

    //通过id删除和下架商品
        public function deleteGoodsById(Request $request)
    {
        $id=($request->id);
        $data= Db::table('shop_goodsinfo')->where('goodsid',$id)->delete();     
       //echo $data;
            return 1;
        }

    //打开商品修改页面
    public function toModifyGoods(Request $request)
    {
          $goodsid = ($request->goodsid);
       $category = Db::table('shop_category')->select();
       $goodsinfo = Db::table('shop_goodsinfo')->where('goodsid',$goodsid)->select();
       //dump($goodsinfo);
        return $this->fetch('index/edit',['category'=>$category,'goodsinfo'=>$goodsinfo]);
    }

    //修改商品信息
    public function modifyGoods(Request $request)
    {
       $goodsid = ($request->goodsid);
       $th = Db::name('goodsinfo')->where('goodsid',$goodsid)->field('thumb')->find();
       $file = $request->file('afile') ;
       //dump($file);die;
        $goodsname = ($request->goodsname);
        $price = ($request->price);
        //$thumb = ($request->file);
        $cid = ($request->cid);
        $detail = ($request->content1);
        $info='';
        if($file){
         $info = $file->move('/static/index/images');
        }
         //dump($info);
        $data =[
          
            'goods_name'=>$goodsname,
            'thumb'=>$info?$info->getSaveName():$th['thumb'],
            'price'=>$price,
            'detail'=>$detail,
            'categoryid'=>$cid
        ] ;
        
      Db::table('shop_goodsinfo')->where('goodsid',$goodsid)->update($data);
        

    }

    //删除成功提示
    public function tips()
    {
        return $this->fetch('index/tips');
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
