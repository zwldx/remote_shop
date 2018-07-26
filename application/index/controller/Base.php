<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\facade\Session;
use think\Db;

class Base extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    //检查用户是否登录
    public function chkLogin()
    {
        //检查session中是否有username

        $result = Session::get('userid');
        if($result==false)
        {
            return $this->error('请先登录','/index/suser/login');
        }else{
            return $result;
        }
    }

    public function getCategoryList()
    {
        $category = Db::name('category')
        ->select();

        $this->assign('category',$category);
    }
}
