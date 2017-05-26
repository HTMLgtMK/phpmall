<?php
/**
 * 买家注册控制器
 * author:GT
 * time:2017/05/22
 */
 namespace app\index\Controller;
 use \think\Controller;
 use \think\Db;
 use \think\Config;
 use \think\Request;
 
 class Register extends Controller{
	
	protected $db;
	protected $tb_buyer;
	protected $tb_activate;
	
	function _initialize(){
		parent::_initialize();
		$this->db=Db::connect();
		$database=Config::get('database');
		$this->tb_buyer=$database['prefix'].$database['TB_BUYER'];
		$this->tb_activate=$database['prefix'].$database['TB_BUYER_ACTIVATE'];
	}
	
	public function step1(){
		if(Request::instance()->isPost()){
			$post_data=Request::instance()->post();
			if(!captcha_check($post_data['verify'])){
				 //验证失败
				 $this->error("验证码错误!");
			}
			//1.查找提交的注册邮箱是否已经注册买家
			if($this->isBuyerRegisted($post_data['email'])){
				$this->error("用户已经注册买家!");
			}else if($this->isUserApplied($post_data['email'])){
				$this->error("已经申请注册!请查看邮箱完成验证!");
			}else{
				//2.获取随机字符串
				$code=randStr(30);//数据库设置最多支持30个支付
				$test_num=10;
				while($this->isCodeExistInActivateTable($code) && $test_num>0){
					$code=randStr(30);
					$test_num=$test_num-1;//防止死循环
				}
				//2.发送邮件到注册的邮箱
				$link=\think\Url::build("index/register/step2",['code'=>$code],false,true);
				$toAddress=$post_data['email'];
				$subject="买家注册";
				$body="欢迎注册买家,请点击下面链接完成注册:</br>
					<a href='$link' >$link</a> ";
				$res=sendEmail($toAddress,$subject,$body);
				if($res){
					//3.写入激活数据表
					$time=time();
					$pwd=md5($post_data['password']);
					$name=$post_data['name'];
					$data=array(
						"mail"=>"$toAddress",
						"pwd"=>"$pwd",
						"name"=>"$name",
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
			return $this->fetch();
		}
	}
	
	/*经过邮箱验证*/
	public function step2(){
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
		//1.写入已经具有的信息
		$name=$res['name'];
		$mail=$res['mail'];
		$pwd=$res['pwd'];
		$time=time();
		$data=array(
			"name"=>"$name",
			"mail"=>"$mail",
			"pwd"=>"$pwd",
			"c_time"=>"$time"
		);
		$exist=$this->db->table($this->tb_buyer)->where(['mail'=>"$mail"])->find();
		if(!$exist){
			$this->db->startTrans();//启用事务
			$this->db->table($this->tb_buyer)->insert($data);
			$this->db->table($this->tb_activate)->where("id",$res['id'])->update(['status'=>'1']);
			$this->db->commit();//提交事务
		}
		
		//2.完善个人信息
		$res=$this->db->table($this->tb_buyer)->where(['mail'=>"$mail"])->find();
		$this->assign("info",$res);
		return $this->fetch("step2");
	}
	
	/*完善个人信息*/
	public function step3(){
		$data=Request::instance()->post();
		if(empty($data['id'])){
			return $this->error("参数错误!");
		}
		$id=$data['id'];
		$nickname=$data['nickname'];
		$tel=$data['tel'];
		$data=array(
			'nickname'=>"$nickname",
			'tel'=>"$tel"
		);
		$this->db->table($this->tb_buyer)->where("id",$id)->update($data);
		//跳转到前台登陆
		return $this->success("完善信息成功",'Index/login');
	}
	
	/*
	 * 判断注册买家是否已经注册
	 */
	private function isBuyerRegisted($email){
		$res=$this->db
				->table($this->tb_buyer)
				->alias('buyer')
				->where(array('buyer.mail'=>"$email"))
				->find();
		return $res;
	}
	
	/**
	 * 判断注册买家的code是否已经存在
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
 }
?>