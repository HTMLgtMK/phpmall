<?php
/**
 * 分类管理
 * author:GT
 * time:2017/05/26
 */
 namespace app\seller\Controller;
 use \app\seller\Controller\Base;
 use \think\Config;
 use \think\Request;
 use \think\Db;
 use \think\Session;
 
 class Terms extends Base{
	 
	 protected $db;
	 protected $tb_buyer;
	 protected $tb_seller;
	 protected $tb_store_terms;
	 protected $tb_store;
	 protected $tb_goods;
	 
	 function _initialize(){
		 parent::_initialize();
		 $this->db=Db::connect();
		 $database=Config::get('database');
		 $this->tb_buyer=$database['prefix'].$database['TB_BUYER'];
		 $this->tb_seller=$database['prefix'].$database['TB_SELLER'];
		 $this->tb_store_terms=$database['prefix'].$database['TB_STORE_TERMS'];
		 $this->tb_store=$database['prefix'].$database['TB_STORE'];
		 $this->tb_goods=$database['prefix'].$database['TB_GOODS'];
	 }
	 
	 public function index(){
		 $seller_id=Session::get('seller.id');
		 return $this->fetch();
	 }
 }
?>