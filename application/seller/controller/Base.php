<?php
/**
 * 卖家基本控制器
 * author:GT
 * time:2017/05/26
 */
 namespace app\seller\controller;
 use think\Controller;
 use think\Request;
 use think\Session;
 
 class Base extends Controller{
	 
	 function _initialize(){
		//登陆控制,强制登陆
		 $action=Request::instance()->action();
		 if($action!='login'){
			if(!Session::has('seller.login') || !Session::has('store_id')){
				//重定向到卖家登陆
				return $this->redirect("seller/Publicc/login2");
			}
		 }
	 }
 }
?>