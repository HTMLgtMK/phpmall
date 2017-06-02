<?php
namespace app\index\controller;
use \think\Controller;
use \think\Request;
use \think\Session;

use app\common\model\Buyer;
use app\common\model\Order;
use app\common\model\Goods;

class Index extends Controller{
	protected $tb_slides;//表中添加了order字段，表示图片滚动的顺序
	protected $tb_site_terms;
	protected $tb_goods;
	protected $tb_cart;//此处修改了原有的购物车表，新增商品总额字段
	protected $tb_order;

	function _initialize(){
		parent::_initialize();
		$this->tb_slides=db("slides");
		$this->tb_site_terms=db("site_terms");
		$this->tb_goods=db("goods");
		$this->tb_cart=db("cart");
		$this->tb_order=db("order");
	}

	//thinkshop首页
	public function index(){
		//获取轮播图，获取商品类别
		$slide = $this->tb_slides->select();
		$goodType = $this->tb_site_terms->select();
		$goods = $this->tb_goods->limit(9)->select();//limit限制首次加载显示商品数量
		$this->assign("goods",$goods);
		$this->assign("goodType",$goodType);
		$this->assign("slide",$slide);
		return $this->fetch();
	}

	//我的主页
	public function main(){
		if(!Session::has('buyer.login')){
			return $this->redirect('index/index/login');
		}
		//买家信息
		$buyer_id=Session::get('buyer.id');
		$buyer=Buyer::where('id',$buyer_id)->find();
		$this->assign('buyer',$buyer);
		//买家订单
		$orders=Order::alias('`a`')
						->join(['__GOODS__'=>'`b`'],'a.goods_id=b.id')
						->field('a.*,b.name,b.id as goods_id')
						->where('buyer_id',$buyer_id)->select();
		$this->assign('orders',$orders);
		return $this->fetch();
	}
	
	//商品详情
	public function detail($id){
		$good = $this->tb_goods->where("id",$id)->find();
		$this->assign("good",$good);
		return $this->fetch();
	}

	//商品列表类
	public function goodList(){
		$keyword=Request::instance()->param('keyword');
		// $str_keyword=implode('',$keyword);
		$this->assign('keyword',$keyword);
		$this->search();
		return $this->fetch();
	}
	
	public function search(){
<<<<<<< HEAD
		$keyword=Request::instance()->param('keyword');
		$goods=Goods::where('name','like',"%{$keyword}%")->select();
		$this->assign("goods",$goods);
		// ajaxReturn($data,$info='',$status=1,$type='') 没有了。。
=======
		//查询
		$keyword=Request::instance()->param('keyword');
		$page=Request::instance()->param('page');
		if(!isSet($page)){
			$page=1;
		}
		$goods=Goods::alias('`a`')
						->join(['__STORE_TERMS__'=>'`b`'],'a.term_id=b.id')
						->field('a.*,b.name as terms_name')
						->where('a.name','like',"%{$keyword}%")
						->whereOr('a.description','like',"%{$keyword}%")
						->whereOr('b.name','like',"%{$keyword}%")
						->where('b.status','1')
						->order('a.sell_count DESC')//按销量排序
						->page($page)//分页
						->select();
		$this->assign("goods",$goods);
		// ajaxReturn($data,$info='',$status=1,$type='') 没有了。。
		//分页
		$count=Goods::alias('`a`')
						->join(['__STORE_TERMS__'=>'`b`'],'a.term_id=b.id')
						->field('a.*,b.name as terms_name')
						->where('a.name','like',"%{$keyword}%")
						->whereOr('a.description','like',"%{$keyword}%")
						->whereOr('b.name','like',"%{$keyword}%")
						->where('b.status','1')
						->count();//计数
		$this->assign('page',$page);
		$this->assign('count',$count);
		$list_rows=\think\Config::get('paginate.list_rows');
		$page_count=$count/$list_rows;
		if($count%$list_rows!=0){
			$page_count+=1;
		}
		$this->assign('page_count',$page_count);
>>>>>>> a18df754164942e29efc0823fab6ee4ac4c0a9aa
		return json($goods);
	}

	//商品购买
	public function buy($id){
		if(!Session::has('buyer.login')){
			return $this->redirect('index/index/login');
		}
		//存储订单数据
		$good = $this->tb_goods->where('id',$id)->find();
		$data=['buyer_id'=>Session::get('buyer.id'),
			"goods_id"=>$id,
			"store_id"=>$good["store_id"],
			"num"=>1,
			"total"=>$good["price"],
			"message"=>"",
			"status"=>2,
			"status_message"=>"",
			"c_time"=>date("Y-m-d H:i:s")];
		$this->tb_order->insert($data);
		$this->tb_goods->where('id',$id)->update(["sell_count"=>(int)($good["sell_count"])+1,"stock"=>(int)($good["stock"])-1]);
<<<<<<< HEAD
		return $this->redirect('index/index/index');
=======
		return $this->success("剁手成功!",'index/index/index');
	}
	
	public function qianshou(){
		$order_id=Request::instance()->param('order_id');
		if(!isSet($order_id)){
			return $this->error("错误参数!");
		}
		$data=[
			'id'=>"{$order_id}",
			'status'=>'4'
		];
		$res=$this->tb_order->where('id',$order_id)->update($data);
		if($res){
			return $this->success("签收成功!");
		}else{
			return $this->success("签收失败!");
		}
>>>>>>> a18df754164942e29efc0823fab6ee4ac4c0a9aa
	}

	//买家登陆
	public function login(){
		if(Request::instance()->isPost()){
			$post_data=Request::instance()->post();
			if(!captcha_check($post_data['verify'])){
				 //验证失败
				 return $this->error("验证码错误!");
			}
			$res=Buyer::where('mail',"{$post_data['email']}")->find();
			if(!$res){
				return $this->error("未注册买家!");
			}
			if($res['pwd']!=md5($post_data['password'])){
				return $this->error("密码错误!");
			}
			//登陆成功,记录登陆信息
			Session::set("buyer.login",true);
			Session::set("buyer.id",$res['id']);
			Session::set("buyer.login_time",time());
			$url=\think\Url::build("index/Index/main",false,true);
			return $this->success("登陆成功",$url);
		 }else{
			 $request=Request::instance()->param();
			 if(!empty($request) && !empty($request['email'])){
				 $this->assign("res",['mail'=>$request['email']]);
			 }else{
				 $this->assign("res",['mail'=>'']);
			 }
			 return $this->fetch();
		 }
	}
<<<<<<< HEAD
=======
	
	public function logout(){
		Session::clear();
		return $this->redirect('index/index/index');
	}
>>>>>>> a18df754164942e29efc0823fab6ee4ac4c0a9aa
}
