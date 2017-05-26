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
 use \think\Url;
 
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
				 return $this->error("验证码错误!");
			}
			//1.查找提交的注册邮箱是否已经注册卖家
			if($this->isSellerRegisted($post_data['email'])){
				return $this->error("用户已经注册卖家!");
			}else if($this->isUserApplied($post_data['email'])){
				return $this->error("已经申请注册!请查看邮箱完成验证!");
			}else{
				//2.获取随机字符串
				$code=randStr(30);//数据库设置最多支持30个支付
				$test_num=10;
				while($this->isCodeExistInActivateTable($code) && $test_num>0){
					$code=randStr(30);
					$test_num=$test_num-1;//防止死循环
				}
				//2.发送邮件到注册的邮箱
				$link=\think\Url::build("seller/register/step2",['code'=>$code],false,true);
				$toAddress=$post_data['email'];
				$subject="卖家注册";
				$body="欢迎注册卖家,请点击下面链接完成注册:</br>
					<a href='$link' >$link</a> ";
				$res=sendEmail($toAddress,$subject,$body);
				if($res){
					//3.写入激活数据表
					$time=time();
					$pwd=md5($post_data['password']);
					$data=array(
						"mail"=>"$toAddress",
						"pwd"=>"$pwd",
						"code"=>"$code",
						"time"=>"$time"
					);
					$this->db
						->table($this->tb_activate)
						->insert($data);//插入或更新
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
		$request=Request::instance()->param();
		if(empty($request['code'])){
			return $this->error("错误参数!");
		}
		$code=$request['code'];
		$res=$this->db
				->table($this->tb_activate)
				->where(array("code"=>"$code"))
				->find();
		if(!$res){
			return $this->error("验证码已经失效!");
		}
		$mail=$res['mail'];
		$pwd=$res['pwd'];
		$time=time();
		
		$buyer=$this->db->table($this->tb_buyer)->where(['mail'=>"$mail"])->find();
		if(!$buyer){
			return $this->error("请先注册为买家!");
		}
		//1.写入已经具有的信息
		$buyer_id=$buyer['id'];
		
		$data=array(
			'buyer_id'=>"$buyer_id",
			"pwd"=>"$pwd",
			"c_time"=>"$time"
		);
		$exist=$this->db->table($this->tb_seller)->where(['buyer_id'=>"$buyer_id"])->find();
		if(!$exist){
			$this->db->startTrans();//启用事务
			$this->db->table($this->tb_seller)->insert($data);
			$this->db->table($this->tb_activate)->where("id",$res['id'])->update(['status'=>'1']);
			$this->db->commit();//提交事务
		}
		$redirect_url=Url::build("seller/Publicc/login",array("mail"=>"$mail"));
		$content="卖家身份验证成功!<br/>即将跳转...
					<script type='text/javascript'>
						window.setTimeout(function(){
							window.location='$redirect_url';
						},2000);
					</script>";
		return $this->display($content);
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
	 * 判断是否已经申请注册
	 */
	private function isUserApplied($email){
		$res=$this->db
				->table($this->tb_activate)
				->alias('a')
				->where(array('a.mail'=>"$email"))
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