<?php
namespace app\index\controller;
use \think\Controller;

class Index extends Controller{
	//thinkshop首页
	public function index(){
		return $this->fetch();
	}

	//我的主页
	public function main(){
		return $this->fetch();
	}
	
}
