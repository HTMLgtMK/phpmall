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
	
	function _initialize(){
		parent::_initialize();
		$this->db=Db::connect();
		$database=Config::get('database');
		$this->tb_buyer=$database['prefix'].$database['TB_BUYER'];
	}
	
	public function step1(){
		if(Request::instance()->isPost()){
			echo "is post!";
			if(!captcha_check($post_data['verify'])){
				 //验证失败
				 $this->error("验证码错误!");
			}
		}else{
			return $this->fetch();
		}
	}
	
	
	
 }
?>