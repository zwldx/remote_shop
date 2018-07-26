<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Tools extends Controller
{
    private $mem;
    public function __construct(\think\App $app = null) {
        parent::__construct($app);
        $this->mem = new \Memcache;
        $this->mem->connect('127.0.0.1',11211);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //ba
    }
        /**
     * 翻译方法
     *
     * @return \think\Response
     */
    public function translate()
    {        
        //待翻译字符串
        $q = $this->request->param('source');
        //查询缓存是否存在
        $r = $this->mem->get($q);
        if(!empty($r)){
            print_r($r);
            return;
        }
        
        $from = 'auto';
        $to = 'zh';
        $appid = "20180723000187986";
        //密钥
        $secret = "xJ_J8ihT1GxEDXYX5Z_Q";
        $salt = time()+rand(1, 100);
        
        $sign = md5($appid.$q.$salt.$secret);
        $url = "https://fanyi-api.baidu.com/api/trans/vip/translate?q=".urlencode($q)."&from={$from}&to={$to}&appid={$appid}&salt={$salt}&sign={$sign}";
        $data = file_get_contents($url);
        $this->mem->set($q,$data,0,60);
        print_r($data);
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
