<?php
/**
 * 商品管理
 * author:GT
 * time:2017/05/23
 */
 namespace app\seller\Controller;
 use \app\seller\Controller\Base;
 use \think\Config;
 use \think\Request;
 use \think\Db;
 use \think\Session;
 
 class Index extends Base{
	 
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
		 $this->goods_list($seller_id);
		 return $this->fetch();
	 }
	 
	 private function goods_list($store_id){
		 //1.获取分类信息
		 $terms=$this->db->table($this->tb_store_terms)->select();
		 //2.获取每种类的商品列表
		 $list=array();
		 foreach($terms as $term){
			$term_id=$term['id'];
			$list[]['term']=$term;
			$list[]['goods']=$this->db
							->table($this->tb_goods)
							->where([
								'term_id'=>"$term_id",
								'store_id'=>"$store_id"
							])
							->select();
		 }
		 $this->assign("list",$list);
	 }
 }
?>