<?php

namespace app\admin\controller;

use think\Controller;
use think\facade\Request;
use app\admin\model\Category;
use think\Db;
class Categorya extends Controller
{
    //析构一个实例化的函数 
    public $category;
    public function __construct(\think\App $app=null) {
        parent::__construct($app);
        $this->category=new Category();//实例化
    }
    /**
     * 显示分类列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询
        $data=$this->category->select_categoryname();
        return $this->fetch('index/column',['data'=>$data]);
    }

    //调用添加分类页面
    public function addCategory()
    {
        $title=Request::param('title');
        if(!empty($title)){
        
        //print_r($title);
        $this->category->insert_category($title);
      
        
       // alert('添加成功');
       
        } return $this->fetch('index/add_cat');
    }
    //展示修改分类界面
    public function  showCategory(){
        $categoryid = Request::param('categoryid');
        $categoryname=$this->category->where('categoryid',$categoryid)->select();
         // dump($categoryname);
         return $this->fetch('index/edit_cat',['categoryname'=>$categoryname]);
        
    }

    //调用修改分类页面
    /*Category::where('news_id',1)->find();//只能用静态
                      
            $this->categoryid = $sort;
            $this->category_name = $title;
            return $this->save();
    }*/
    public function editCategory()
    {
        $title=Request::param('title');
        $categoryid = Request::param('categoryid');
        
        //print_r($title);
       
        if(!empty($title)){
            Db::name('Category')->where('categoryid',$categoryid)->update(['category_name' => $title]);
            
        }
       
    }
    //删除分类
    public function del(){
        //接收id
        //$id=$_GET['id'];
       $id=Request::param('id');
       $data= Db::table('shop_category')->where('categoryid',$id)->delete();     
       //echo $data;
        
            return 1;
        
        
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
        
    }
}
