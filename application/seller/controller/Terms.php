<?php
/**
 * 分类管理
 * author:GT
 * time:2017/05/26
 */
 namespace app\seller\Controller;
 use \app\seller\Controller\Base;
 use \think\Request;
 use \think\Session;
 use \app\common\model\StoreTerms;
 
 class Terms extends Base{
	 
	 function _initialize(){
		 parent::_initialize();
		  if(!Session::has('store_id')){
			 Session::clear();
			 $url=\think\Url::build("seller/Publicc/login",true,true);
			 return $this->error("登陆超时!",$url);
		 }
	 }
	 
	 public function index(){
		 $term_id=Request::instance()->param('term_id');
		 if(!isset($term_id)) {
			 $term_id=0;
		 }
		 $terms_path=(new StoreTerms())->terms_path($term_id);
		 $this->assign("terms_path",$terms_path);
		 $terms_child=StoreTerms::where('parent_id',$term_id)->select();
		 $this->assign('terms_child',$terms_child);
		 return $this->fetch();
	 }
	 
	 public function add(){
		 if(Request::instance()->isPost()){
			 $store_id=Session::get("store_id");
			
			 $post_data=Request::instance()->post();
			 if(!isset($post_data['term_parent'])){
				 return $this->error("请选择父级分类!");
			 }
			 if(!isset($post_data['term_name'])){
				 return $this->error("请输入分类名称!");
			 }
			 if(!isset($post_data['term_status'])){
				 $post_data['status']='1';
			 }
			 $model_store_terms=new StoreTerms();
			 if($model_store_terms->isExistTerm($store_id,$post_data['term_name'])){
				 return $this->error("已经存在该分类!");
			 }
			 $timestamp=date('Y-m-d H:i:s',time());
			 $data=array(
				'parent_id'=>"{$post_data['term_parent']}",
				'name'=>"{$post_data['term_name']}",
				'store_id'=>"{$store_id}",
				'status'=>"{$post_data['term_status']}",
				'c_time'=>"{$timestamp}"
			 );
			 $res=$model_store_terms->insert($data);
			 if(!$res){
				 $err=$model_store_terms->getError();
				 return $this->error("添加失败!".$err);
			 }else{
				 return $this->success("添加成功!");
			 }
		 }else{
			 $term_id=Request::instance()->param('term_id');
			 $store_id=Session::get('store_id');
			 $terms=StoreTerms::where('store_id',$store_id)->select();
			 $this->assign("terms",$terms);
			 $this->assign("term_id",$term_id);
			 return $this->fetch();
		 }
	 }
	 
	 public function edit(){
		 if(Request::instance()->isPost()){
			 $store_id=Session::get("store_id");
			 $post_data=Request::instance()->post();
			 if(!isset($post_data['term_parent'])){
				 return $this->error("请选择父级分类!");
			 }
			 if(!isset($post_data['term_name'])){
				 return $this->error("请输入分类名称!");
			 }
			 if(!isset($post_data['term_status'])){
				 $post_data['status']='1';
			 }
			 $model_store_terms=new StoreTerms();
			 if($model_store_terms->isExistTerm($store_id,$post_data['term_name'])){
				 return $this->error("已经存在该分类!");
			 }
			 $timestamp=date('Y-m-d H:i:s',time());
			 $data=array(
				'parent_id'=>"{$post_data['term_parent']}",
				'name'=>"{$post_data['term_name']}",
				'status'=>"{$post_data['term_status']}",
				'store_id'=>"{$store_id}"
			 );
			 $res=$model_store_terms->where("id","{$post_data['id']}")
						->update($data);
			 if(!$res){
				 $err=$model_store_terms->getError();
				 return $this->error("修改失败!".$err);
			 }else{
				 return $this->success("修改成功!");
			 }
		 }else{
			 $term_id=Request::instance()->param('term_id');
			 if(!isSet($term_id)){
				 return $this->error("错误参数!");
			 }
			 $term=StoreTerms::where("id",$term_id)->find();
			 if(!$term){
				 return $this->error("没有该分类!");
			 }
			 $store_id=Session::get('store_id');
			 $terms=StoreTerms::where('store_id',$store_id)->select();
			 $this->assign("terms",$terms);
			 $this->assign("term",$term);
			 // var_dump($term);exit;
			 return $this->fetch();
		 }
	 }
	 
	 public function delete(){
		$term_id=Request::instance()->param('term_id');
		$res=StoreTerms::delete(['id'=>"{$term_id}"]);
		if(!$res){
			$err=StoreTerms::getError();
			return $this->error("删除失败!".$err);
		}
		return $this->success("删除成功!");
	 }
 }
?>