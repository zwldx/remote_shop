<?php
namespace app\admin\controller;
use think\Controller;
use think\facade\Request;
use app\admin\model\Business;
use  think\facade\Session;
class Suser extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    
    //析构一个实例化的函数  
    public function __construct(\think\App $app=null) {
        parent::__construct($app);
        $this->shopbusiness=new Business();//实例化
    }
    
    public function index()
    {
        //
    }

    //商家登录页面展示
    public function seller()
    {
        return $this->fetch('index/login');
    }

    //商家登录
    public function login()
    {       
        //判断未输入且不为空
        if(!empty($_POST['name'])){
            //接收浏览器中的值
            $name = Request::param('name');
            $password = Request::param('password');
            //print_r($name);
            
            //查数据库中的密码
            $paswd = $this->shopbusiness-> select_password_by_username($name);
            //转化为数组
            $paswd=\json_decode(json_encode($paswd),true);
            //$paswd[0]['password']);die;           
            if($paswd && $password==($paswd[0]['password'])){
                session::set('username',$name);
                //session::get('username');              
                $this->success('登录成功', '/admin/Index/index');die;
           }else{
               $this->error('登录失败', '/admin/Suser/login');
        }
        }
        return $this->fetch('/index/login');
    }

    //商家退出
    public function logout()
    {
        //清除session
        Session::clear();
        
        $this->success('退出成功', '/admin/Suser/login');
        return $this->fetch('/index/login');
        
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
