<?php
/**
 * 卖家控制器
 * author:GT
 * time:2017/05/20
 */
 namespace app\seller\controller;
 use \think\Controller;
 use \think\Db;
 use \think\Config;
 use \think\Request;
 
 class Register extends Controller{
	 
	protected $db;
	protected $tb_seller;
	protected $tb_buyer;
	
	function _initialize(){
		parent::_initialize();
		$this->db = Db::connect();
		$database = Config::get("database");
		$this->tb_seller=$database['prefix'].$database['TB_SELLER'];//没有C()了。。config()也可以
		$this->tb_buyer=$database['prefix'].$database['TB_BUYER'];
	}
	
	function step1(){
		if(Request::instance()->isPost()){
			$post_data = Request::instance()->post();
			//var_dump($post_data);
			//1.查找提交的注册邮箱是否已经注册卖家
			if($this->isSellerRegisted($post_data['email'])){
				$this->error("用户已经注册卖家!");
			}else{
				//2.发送邮件到注册的邮箱
				$link=\think\Url::build("seller/register/step2",['code'=>'123456'],true,true);
				$toAddress=$post_data['email'];
				$subject="卖家注册";
				$body="欢迎注册卖家,请点击下面链接完成注册:</br>
					<a href='$link' >$link</a> ";
				$res=sendEmail($toAddress,$subject,$body);
				if($res){
					return $this->success("邮件已经发送!");
				}else{
					return $this->error("邮件发送失败!");
				}
			}
		}else{
			return $this->fetch("step1");
		}
	}
	
	private function isSellerRegisted($email){
		$res=$this->db
				->table($this->tb_seller)
				->alias('seller')
				->join(array($this->tb_buyer=>'`buyer`'),"seller.buyer_id=buyer.id")
				->where(array('buyer.mail'=>"$email"))
				->find();
		return $res;
	}
	
	public function index(){
		$res = $this->db
				->table($this->tb_seller)
				->select();
		var_dump($res);
	}
 }
?>