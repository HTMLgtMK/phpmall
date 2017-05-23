<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * Url 生成 
 * 主要是为了方便模板中使用
 * @param string $url 路由地址
 * @param string|array $value 变量
 * @param bool|string $suffix 前缀
 * @param bool|string $fdomain 域名
 * return string
 */
 function url($url='',$vars='',$suffix=true,$domain=false){
	 return \think\Url::build($url,$vars,$suffix,$domain);
 }
 
 
 /**
  * 发送邮件
  * @param string $toAddress 对方邮箱地址
  * @param string $subject 主题
  * @param string $body 正文
  * @param bool
  */
 function sendEmail($toAddress,$subject,$body){
	include_once(EXTEND_PATH."PHPMailer/PHPMailerAutoload.php");
	
	$meEmail=\think\Config::get('Email');
	
	$mail=new \PHPMailer();
	
	$mail->SMTPDebug=3;//打印smtp信息
	
	$mail->isSMTP();
	$mail->Host=$meEmail['host'];
	$mail->SMTPAuth=true;
	$mail->Username=base64_decode($meEmail['username']);
	$mail->Password=base64_decode($meEmail["password"]);
	$mail->SMTPSecure="ssl";
	$mail->Port=$meEmail['port'];
	$mail->Charset="utf-8";
	
	$mail->setFrom(base64_decode($meEmail['username']),$meEmail['name']);
	$mail->addAddress($toAddress);
	
	$mail->isHTML(true);
	
	$mail->Subject="=?utf-8?B?".base64_encode($subject)."?=";
	$mail->Body=$body;
	
	if($mail->send()){
		return true;
	}else{
		echo $mail->ErrorInfo();
		return false;
	}
 }
 
 /**
  * 生成随机字符串
  * @param string $length 字符串长度
  */
 function randStr($length=4){
	$str = null;
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
	$max = strlen($strPol) - 1;
	for ($i = 0; $i < $length; $i++) {
		$str .= $strPol[rand(0, $max)];
		//rand($min,$max)生成介于min和max两个数之间的一个随机整数
	}
	return $str;
 }