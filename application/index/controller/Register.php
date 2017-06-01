<?php
/**
 * 买家注册控制器
 * author:GT
 * time:2017/05/22
 */
 namespace app\index\Controller;
 use \think\Controller;
 use \think\Request;
 use app\common\model\Buyer;
 use app\index\model\BuyerActivate;
 
 class Register extends Controller{
	 
	 public function index(){
		 return $this->redirect('index/Register/step1');
	 }
	
	public function step1(){
		if(Request::instance()->isPost()){
			$post_data=Request::instance()->post();
			if(!captcha_check($post_data['verify'])){
				 //验证失败
				 $this->error("验证码错误!");
			}
			//1.查找提交的注册邮箱是否已经注册买家
			if((new Buyer())->isBuyerRegisted($post_data['email'])){
				$this->error("用户已经注册买家!");
			}else if((new BuyerActivate())->isUserApplied($post_data['email'])){
				$this->error("已经申请注册!请查看邮箱完成验证!");
			}else{
				//2.获取随机字符串
				$code=randStr(30);//数据库设置最多支持30个支付
				$test_num=10;
				while((new BuyerActivate())->isCodeExistInActivateTable($code) && $test_num>0){
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
					$time=date('Y-m-d H:i:s',time());
					$pwd=md5($post_data['password']);
					$name=$post_data['name'];
					$address=$post_data['address'];
					$data=array(
						"mail"=>"{$toAddress}",
						"pwd"=>"{$pwd}",
						"name"=>"{$name}",
						"address"=>"{$address}",
						"code"=>"{$code}",
						"time"=>"{$time}"
					);
					BuyerActivate::insert($data);//插入或更新
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
		$res=BuyerActivate::where("code","{$code}")->find();
		if(!$res){
			return $this->error("验证码已经失效!");
		}
		//1.写入已经具有的信息
		$name=$res['name'];
		$mail=$res['mail'];
		$address=$res['address'];
		$pwd=$res['pwd'];
		$time=date('Y-m-d H:i:s',time());
		$data=array(
			"name"=>"{$name}",
			"address"=>"{$address}",
			"mail"=>"{$mail}",
			"pwd"=>"{$pwd}",
			"c_time"=>"{$time}"
		);
		$exist=Buyer::where('mail',"{$mail}")->find();
		if(!$exist){
			Buyer::insert($data);
			BuyerActivate::where("id","{$res['id']}")->update(['status'=>'1']);
		}
		//2.完善个人信息
		$res=Buyer::where('mail',"{$mail}")->find();
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
		$address=$data['address'];
		$nickname=$data['nickname'];
		$tel=$data['tel'];
		$data=array(
			'nickname'=>"{$nickname}",
			'address'=>"{$address}",
			'tel'=>"{$tel}"
		);
		Buyer::where("id",$id)->update($data);
		//跳转到前台登陆
		return $this->success("完善信息成功",'index/login');
	}
 }
?>