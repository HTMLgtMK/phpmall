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
		 $term_id=Request::instance()->param('term_id');
		 if(empty($term_id)) {
			 $term_id=0;
		 }
		 $this->terms_path($term_id);
		 $this->terms_child($term_id);
		 return $this->fetch();
	 }
	 
	 /**
	  * 获取该分类下的所有分类
	  * @param $term_id 分类id
	  */
	 private function terms_child($term_id=0){
		 $terms_child=$this->db
						->table($this->tb_store_terms)
						->where('parent_id',$term_id)
						->select();
		 $this->assign('terms_child',$terms_child);
	 }
	 
	 /*
	  * 获取当前分类的路径
	  */
	 private function terms_path($term_id=0){
		 $terms_path=array();
		 while($term_id!=0){
			 $term=$this->db
						->table($this->tb_store_terms)
						->where('id',$term_id)
						->find();
			 $terms_path[]=$term;
			 $term_id=$term['parent_id'];
		 }
		 //反转数组
		 $terms_path=array_reverse($terms_path,true);
		 $this->assign('terms_path',$terms_path);
	 }
 }
?>