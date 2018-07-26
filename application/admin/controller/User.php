<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
// use app\admin\model\User;

class User extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
        //user model对象
    private $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new \app\admin\model\User();
    }

    public function sendOrderCount(){
        //订单统计
        $detail = $this->user->countOrder();
        // echo $detail;
        $userAddress = "teo2018@qq.com";
        //发送邮件
        $this->user->sendMail($userAddress,$detail);
    }

    public function index()
    {
        //
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
