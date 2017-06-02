<?php
/**
 * 卖家登陆
 * author:GT
 * time:2017/05/23
 */
 namespace app\seller\Controller;
 use \think\Controller;
 use \think\Config;
 use \think\Request;
 use \think\Db;
 use \think\Session;
 
 use app\common\model\Store;
 use app\seller\model\Seller;
 use app\common\model\Buyer;
 
 class Publicc extends Controller{
	 
	 protected $db;
	 protected $tb_buyer;
	 protected $tb_seller;
	 protected $tb_store;
	 
	 function _initialize(){
		 parent::_initialize();
		 $this->db=Db::connect();
		 $database=Config::get('database');
		 $this->tb_buyer=$database['prefix'].$database['TB_BUYER'];
		 $this->tb_seller=$database['prefix'].$database['TB_SELLER'];
		 $this->tb_store=$database['prefix'].$database['TB_STORE'];
	 }
	 
	 public function c_shop(){
		 if(Request::instance()->isPost()){
			 $post_data=Request::instance()->post();
			 $c_time=date('Y-m-d H:i:s',time());
			 $data=[
				'name'=>"{$post_data['name']}",
				'seller_id'=>"{$post_data['seller_id']}",
				'c_time'=>"{$c_time}"
			 ];
			 $res=Store::insert($data);
			 if(!$res){
				 return $this->error('创建店铺失败!');
			 }
			 $buyer_id=Seller::field('buyer_id')->where('id',$post_data['seller_id'])->find();
			 $buyer_id=$buyer_id['buyer_id'];
			 $url=\think\Url::build('seller/Publicc/login2',['buyer_id'=>"{$buyer_id}"],true,true);
			 return $this->redirect($url);
		 }else{
			 return $this->fetch();
		 }
	 }
	 
	 /**
	  * 自动登陆
	  */
	 public function login2(){
		 $buyer_id=Session::get('buyer.id');
		 if(!isSet($buyer_id)){
			 return $this->error('错误参数!');
		 }
		 //判断是否已经是卖家
		 $seller=Seller::where('buyer_id',$buyer_id)->find();
		 if(!$seller){
			 //添加新卖家
			 $buyer=Buyer::where('id',$buyer_id)->find();
			 $c_time=date("Y-m-d H:i:s",time());
			 $data=[
				'buyer_id'=>"{$buyer_id}",
				'pwd'=>"{$buyer['pwd']}",
				'c_time'=>"{$c_time}"
			 ];
			 $res=Seller::insert($data);
			 if(!$res){
				 return $this->error('创建卖家失败!');
			 }
			 $seller=Seller::where('buyer_id',$buyer_id)->find();
		 }
		 /*登陆卖家成功*/
		 Session::set("seller.login",true);
		 Session::set("seller.id",$seller['id']);
		 Session::set("seller.login_time",time());
		 //判断是否已经有店铺
		 $res=Store::where('seller_id',$seller['id'])->find();
		 if(!$res){
			 //创建店铺
			 return $this->fetch('c_shop');
		 }
		 //登陆店铺
		 $this->loginStore();
		 //跳转卖家后台
		 $url=\think\Url::build("seller/Index/index",array('seller_id'=>$res['seller_id']),false,true);
		 return $this->redirect($url);
	 }
	 
	 public function logout2(){
		  Session::clear('seller');//清空seller控制
		 return $this->redirect("index/Index/main");
	 }
	 
	 /**
	  * 登陆卖家
	  */
	 public function login(){
		 if(Request::instance()->isPost()){
			 $post_data=Request::instance()->post();
			if(!captcha_check($post_data['verify'])){
				 //验证失败
				 return $this->error("验证码错误!");
			}
			$email=$post_data['email'];
			$res=$this->db->table(["{$this->tb_seller}"=>"a"])
					->join(["{$this->tb_buyer}"=>"b"],"a.buyer_id=b.id")
					->field([
						'a.pwd'=>'seller_pwd',
						'a.id'=>'seller_id'
					])
					->where(array('b.mail'=>"{$email}"))
					->find();
			if(!$res){
				return $this->error("未注册卖家!");
			}
			if($res['seller_pwd']!=md5($post_data['password'])){
				return $this->error("密码错误!");
			}
			//登陆成功,记录登陆信息
			Session::set("seller.login",true);
			Session::set("seller.id",$res['seller_id']);
			Session::set("seller.login_time",time());
			$this->loginStore();
			$url=\think\Url::build("seller/Index/index",array('seller_id'=>$res['seller_id']),false,true);
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
	 
	 /*为了方便直接在这个方法中选择商店*/
	 private function loginStore(){
		 $seller_id = Session::get('seller.id');
		 $store = Store::where("seller_id",$seller_id)->find();
		 Session::set('store_id',$store['id']);
	 }
	 
	 /**
	  * 登出卖家
	  */
	 public function logout(){
		 Session::clear();//清空
		 return $this->redirect("seller/publicc/login");
	 }
 }
?>