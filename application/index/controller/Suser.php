<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\facade\Request;
use app\index\model\User;
use  think\facade\Session;

class Suser extends Base
{
    //析构一个函数，下面的类都可以使用
    public function __construct(\think\App $app=null)
    {
        parent::__construct($app);
        $this->shopuser=new User();//实例化
    }
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }
       
    //ajax检测用户名是否已存在
    public function checkUsername()
    {
        //接收数据
        $username=$_GET['user'];
        
        //查询并对比shop_user中的数据
        $res=Db::table('shop_user')->where('username', $username)->select();
        
        if ($res) {
            echo  1;
        } else {
            echo  0;
        }
    }
    
    //用户注册
    public function register()
    {
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $user=Request::param('username');
            //print_r($user);die;
            $paswd=Request::param('password');
            //print_r($paswd);
            $confirm=Request::param('confirm_password');
            //print_r($confirm);
            $phone=Request::param('phone');
            //print_r($phone);
            $password=Db::table('shop_user')->where('username', $user)->select();
            if ($password) {
                $this->error('用户名已存在', '/index/suser/register');
            } elseif ($paswd==$confirm) {
                $userid=$this->shopuser->insert_to_shopuser($user, $paswd, $phone);
                session::set('username', $user);
                session::set('userid',$userid);
                // session::set('password', $password);
                // session::set('phone', $phone);
                $this->success('注册成功', '/index/Index/index');
            }
        }
        return $this->fetch('register');
    }
    
    //用户登录
    public function login()
    {
        //判断是否为空，才可进入
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $user=Request::param('username');
            //print_r($user);
            $paswd=Request::param('password');
            //print_r($paswd);die;
            $result=$this->shopuser->get_password_by_username($user);
            // $password=\json_decode(json_encode($password),true);

            //用户id和密码
            $userid = $result['userid'];
            $password = $result['password'];

            //print_r($password);
            if (!empty($password) && ($password==$paswd)) {
                //session开始函数
                session::set('username', $user);
                session::set('userid', $userid);
                //原生的session
                //session_start();
                //$_SESSION['username']=$user;
                //$_SESSION['password']=$password[0]['password'];
//            dump($_SESSION);
                $this->success('登录成功', '/index/index/index');
//            $show_msg='登录成功';//提示信息
//            $this->redirect('shop.com/index/Index/index',302);//目标地址
            } elseif (!isset($password) or empty($password)) {
                //返回注册页面
                $this->error('您还未注册，请先注册', '/index/suser/register');
            } else {
                //返回登录页面
                $this->error('登录失败', '/index/suser/login');
            }
        }
        return $this->fetch('login');//,['user'=>$user,'password'=>$paswd]
    }
    
    //修改密码
    public function forgetpaswd()
    {
        if (!empty($_POST['username']) && !empty($_POST['originpaswd']) && !empty($_POST['newpaswd'])) {
            $user=Request::param('username');
            //print_r($user);
            $originpaswd=Request::param('originpaswd');
            //print_r($originpaswd);die;
            $newpaswd=Request::param('newpaswd');
            //查询数据库中的密码
            $password=$this->shopuser->get_password_by_username($user) ;
            //转为数组
            $password=\json_decode(json_encode($password), true);
            //print_r($password);die;
            //输入的原始密码与数据库中密码相同，才可以更改
            if ($originpaswd == $password[0]['password']) {
                //将新密码写入
                $this->shopuser->update_by_username($user, $newpaswd);
                //写入session
                session::set('username', $user);
                //跳转
                $this->success('修改成功,请重新登录', '/index/suser/login');
            } else {
                $this->error('修改失败', '/index/suser/login');
            }
        }
        return $this->fetch('forgetpaswd');
    }
    //用户退出
    public function logout()
    {
        //清除session
        Session::clear();
        //session::get('username');
        $this->success('退出成功', '/index/Index/index');
        return $this->fetch('login');
    }
}
