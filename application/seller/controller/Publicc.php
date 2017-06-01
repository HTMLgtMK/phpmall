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
		 $store = $this->db->table($this->tb_store)->where("seller_id",$seller_id)->find();
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