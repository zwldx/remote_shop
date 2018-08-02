<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\facade\Session;
use think\Db;
use think\facade\Cookie;

class Base extends Controller
{
    public function __construct(\think\App $app=null)
    {
        parent::__construct($app);
        $this->redis = new \Redis;
        $this->redis->connect('127.0.0.1',6379);
    }
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
        // dump($result);die;
        // $result = false;
        
        if($result==false)
        {
            $user_id =  Cookie::get('userid');
            $user_sign = Cookie::get('sign');
            $user_expire_date = Cookie::get('expire_date');

            if($user_id&&$user_sign&&$user_expire_date&&(int)$user_expire_date>time()&&($user_sign==$this->redis->hget('newest_sign',$user_id))){

                $password = $this->redis->hget('user_pwd',$user_id);
                $user_agent = $_SERVER['HTTP_USER_AGENT'];//获取浏览器信息
                if($password&&(get_sign($user_id,$password,SECRET,$user_expire_date,$user_agent)==$user_sign)&&($this->redis->sismember('keep_login_users',$user_id))){
                    // echo $this->redis->hget('user_name',$user_id);die;
                    $result = $user_id;
                    session::set('username', $this->redis->hget('user_name',$user_id));
                    session::set('userid', $user_id);
                }else{
                     return $this->error('请先登录','/index/suser/login');
                }         
            }else{
                 return $this->error('请先登录','/index/suser/login');
            }            
            
           
        }else{
            $user_sign = Cookie::get('sign');
            if($user_sign!=$this->redis->hget('newest_sign',$result)){
                return $this->error('请先登录','/index/suser/login');
            }
        }

        return $result;
        
    }

    public function getCategoryList()
    {
        $category = Db::name('category')
        ->select();

        $this->assign('category',$category);
    }
}
