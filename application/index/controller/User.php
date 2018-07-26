<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class User extends Base
{
    //user model对象
    private $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new \app\index\model\User();
    }
    public function index()
    {
        return '';
    }

    public function toLogin()
    {
        return $this->fetch('login');
    }

    public function toRegist()
    {
        return $this->fetch('regist');
    }
    //打开用户安全页面
    public function memberSafe()
    {
        $this->chkLogin();
        return $this->fetch('order/member_safe');
    }

    //邮箱绑定ajax
    public function mailBind()
    {
        //登陆验证并获取登录id
        $userid = $this->chkLogin();
        //获取待绑定的邮箱地址
        $mail = request()->param('mail');
        //生成激活链接
        $url = $this->user->creatMailUrl($userid);
        //发送邮件
        return $this->user->sendActiveMail($mail,$url,$userid);
       
    }

    //邮箱激活
    public function mailActive(){ 
        $uid = $_GET['uid'];
        $time = $_GET['time'];
        //当前时间
        $cur_time = time();
        //用户签名
        $sign = $_GET['sign'];
        //服务端校验签名,返回结果为true 或者false
        $res_sign = $this->user->chkSign($uid,$sign,$time);
        if($res_sign){
            if((int)$time>$cur_time){
                $this->user->mailActive($uid);
                $this->success('激活成功','user/membersafe');
            }else{
                $this->error('激活链接已过期');
            }
        }else{
            $this->error('签名错误');
        }
    }
}
