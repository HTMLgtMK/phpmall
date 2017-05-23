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
	protected $tb_activate;
	
	function _initialize(){
		parent::_initialize();
		$this->db = Db::connect();
		$database = Config::get("database");
		$this->tb_seller=$database['prefix'].$database['TB_SELLER'];//没有C()了。。config()也可以
		$this->tb_buyer=$database['prefix'].$database['TB_BUYER'];
		$this->tb_activate=$database['prefix'].$database['TB_SELLER_ACTIVATE'];
	}
	
	/**
	 * 步骤1
	 */
	function step1(){
		if(Request::instance()->isPost()){
			$post_data = Request::instance()->post();
			//var_dump($post_data);
			if(!captcha_check($post_data['verify'])){
				 //验证失败
				 $this->error("验证码错误!");
			}
			//1.查找提交的注册邮箱是否已经注册卖家
			if($this->isSellerRegisted($post_data['email'])){
				$this->error("用户已经注册卖家!");
			}else{
				//2.获取随机字符串
				$code=randStr(30);//数据库设置最多支持30个支付
				$test_num=10;
				while($this->isCodeExistInActivateTable($code) && $test_num>0){
					$code=randStr(30);
					$test_num=$test_num-1;//防止死循环
				}
				//2.发送邮件到注册的邮箱
				$link=\think\Url::build("seller/register/step2",['code'=>$code],true,true);
				$toAddress=$post_data['email'];
				$subject="卖家注册";
				$body="欢迎注册卖家,请点击下面链接完成注册:</br>
					<a href='$link' >$link</a> ";
				$res=sendEmail($toAddress,$subject,$body);
				if($res){
					//3.写入激活数据表
					$time=time();
					$pwd=$post_data['password'];
					$data=array(
						"mail"=>"$toAddress",
						"pwd"=>"$pwd",
						"code"=>"$code",
						"time"=>"$time"
					);
					$this->db
						->table($this->tb_activate)
						->insert($data,true);//插入或更新
					return $this->success("邮件已经发送!");
				}else{
					return $this->error("邮件发送失败!");
				}
			}
		}else{
			return $this->fetch("step1");
		}
	}
	
	/**
	 * 步骤2
	 */
	function step2(){
		$code=Request::instance()->get('code');
		$res=$this->db
				->where(array('code'=>$code))
				->find();
		if(!$res){
			$this->error("验证码已经过期!需要重新申请注册!");
		}else{
			//添加到卖家表
			
		}
	}
	
	/*
	 * 判断注册卖家是否已经注册
	 */
	private function isSellerRegisted($email){
		$res=$this->db
				->table($this->tb_seller)
				->alias('seller')
				->join(array($this->tb_buyer=>'`buyer`'),"seller.buyer_id=buyer.id")
				->where(array('buyer.mail'=>"$email"))
				->find();
		return $res;
	}
	
	/**
	 * 判断注册卖家的code是否已经存在
	 */
	private function isCodeExistInActivateTable($code){
		$res=$this->db
				->table($this->tb_activate)
				->alias('a')
				->where(array('a.code'=>"$code"))
				->find();
		return $res;
	}
	
	/**
	 * 
	 */
	public function index(){
		$res = $this->db
				->table($this->tb_seller)
				->select();
		var_dump($res);
	}
 }
?>