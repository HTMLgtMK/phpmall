<?php
/**
 * 商店商品分类
 * author:GT
 * time:2017/05/28
 */
 
 namespace app\common\model;
 use think\Model;
 
 class StoreTerms extends Model{
	 
	 /**
	  * 商品分类路径
	  * @access public
	  * @param term_id 分类id
	  * @return $terms_path array
	  */
	 public function terms_path($term_id,$store_id){
		 $terms_path=array();
		 while($term_id!=0){
			 $term=$this->where([
						'id'=>"{$term_id}",
						'store_id'=>"{$store_id}"
					])->find();
			 $terms_path[]=$term;
			 $term_id=$term['parent_id'];
		 }
		 //翻转数组
		 $terms_path=array_reverse($terms_path,true);
		 return $terms_path;
	 }
	 
	 /** 
	  * 判断是否已经存在某个分类
	  * @param $store_id 商店id
	  * @param $term_name 分类名称
	  */
	 public function isExistTerm($store_id,$term_name){
		 $res=$this ->where("store_id",$store_id)
					->where("name",$term_name)
					->find();
		 return $res?true:false;
	 }
	 
 }


?>