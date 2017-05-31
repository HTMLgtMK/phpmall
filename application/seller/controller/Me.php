<?php
/**
 * 卖家个人中心
 * author:GT
 * time:2017/05/31
 */
 namespace app\seller\controller;
 use \think\Request;
 use \think\Session;
 use app\seller\controller\Base;
 use app\seller\model\Seller;
 use app\common\model\Store;
 
 class Me extends Base{
	
	public function index(){
		$store_id=Session::get("store_id");
		$store=Store::where('id',"{$store_id}")->find();
		$model_seller=new Seller();
		$seller=$model_seller->alias('b')
					->join(["__BUYER__"=>'a'],'a.id=b.buyer_id')
					->field('a.name as name,a.mail as mail,b.*')
					->where('b.id',"{$store['seller_id']}")
					->find();
		
		$this->assign("seller",$seller);
		$this->assign("store",$store);
		return $this->fetch();
	}
	
	/**
	 * 修改密码
	 */
	public function modifyPwd(){
		
	}
	
	/*登出*/
	public function exitStore(){
		$publicc=controller('Publicc');
		$publicc->logout();
	}
 }
?>