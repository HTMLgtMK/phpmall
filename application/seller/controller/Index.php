<?php
/**
 * 商品管理
 * author:GT
 * time:2017/05/23
 */
 namespace app\seller\Controller;
 use \app\seller\Controller\Base;
 use \think\Request;
 use \think\Session;
 use \app\common\model\StoreTerms;
 use \app\common\model\Goods;
 
 class Index extends Base{
	 
	 public function index(){
		 $store_id = Session::get('store_id');
		 $this->goods_list($store_id);
		 return $this->fetch();
	 }
	 
	 public function add(){
		 if(Request::instance()->isPost()){
			 $store_id=Session::get("store_id");
			 $post_data=Request::instance()->post();
			 //保存封面文件
			 $file=Request::instance()->file("cover");
			 $info = $file->move(ROOT_PATH.'public'.DS.'static'.DS.'image');
			 if(!$info){
				// 上传失败获取错误信息
				$err=$file->getError();
				return $this->error("上传失败!".$err);
			 } 
			 $timestamp=date("Y-m-d H:i:s",time());
			 $data=array(
				'store_id'		=> "{$store_id}",
				'name'			=> "{$post_data['name']}",
				'description'	=> "{$post_data['description']}",
				'price'			=> "{$post_data['price']}",
				'stock'			=> "{$post_data['stock']}",
				'cover'			=> "{$info->getSaveName()}",
				'term_id'		=> "{$post_data['term_id']}",
				'c_time'		=> "{$timestamp}"
			 );
			 $model_goods=new Goods();
			 $res= $model_goods->insert($data);
			 if($res){
				 return $this->success("添加成功!");
			 }else{
				 $err=$model_goods->getError();
				 return $this->error("添加失败!".$err);
			 }
		 }else{
			$term_id=Request::instance()->param('term_id');
			if(!isSet($term_id)){
				return $this->error("错误参数!");
			}
			$store_id=Session::get("store_id");
			$terms=StoreTerms::where('store_id',$store_id)->select();
			$this->assign("terms",$terms);
			$this->assign("term_id",$term_id);
			return $this->fetch();
		 }
	 }
	 
	 /**
	  * 商品详情
	  */
	 public function detail(){
		 $goods_id=Request::instance()->param('goods_id');
		 if(!isSet($goods_id)){
			 return $this->error("错误参数!");
		 }
		 $goods=Goods::where('id',$goods_id)->find();
		 $this->assign("goods",$goods);
		 //分类路径
		 $terms_path=(new StoreTerms())->terms_path($goods['term_id']);
		 $this->assign("terms_path",$terms_path);
		 return $this->fetch();
	 }
	 
	 public function edit(){
		 if(Request::instance()->isPost()){
			 $store_id=Session::get("store_id");
			 $post_data=Request::instance()->post();
			 //保存封面文件
			 $file=Request::instance()->file("cover");
			 $info = $file->move(ROOT_PATH.'public'.DS.'static'.DS.'image');
			 if(!$info){
				// 上传失败获取错误信息
				$err=$file->getError();
				return $this->error("上传失败!".$err);
			 } 
			 $data=array(
				'store_id'		=> "{$store_id}",
				'name'			=> "{$post_data['name']}",
				'description'	=> "{$post_data['description']}",
				'price'			=> "{$post_data['price']}",
				'stock'			=> "{$post_data['stock']}",
				'cover'			=> "{$info->getSaveName()}",
				'term_id'		=> "{$post_data['term_id']}"
			 );
			 $model_goods=new Goods();
			 $res= $model_goods->where('id',$post_data['id'])->update($data);
			 if($res){
				 return $this->success("修改成功!");
			 }else{
				 $err=$model_goods->getError();
				 return $this->error("修改失败!".$err);
			 }
		 }else{
			$goods_id=Request::instance()->param('goods_id');
			$goods=Goods::where('id',$goods_id)->find();
			$this->assign('goods',$goods);
			$store_id=Session::get("store_id");
			$terms=StoreTerms::where([
							'store_id'=>"{$store_id}",
							'status'=>'1'
						])->select();
			$this->assign("terms",$terms);
			return $this->fetch();
		 }
	 }
	 
	 public function delete(){
		 $goods_id=Request::instance()->param('goods_id');
		 $res=Goods::where('id',$goods_id)->delete();
		 if($res){
			 return $this->success("删除成功!");
		 }else{
			 return $this->error("删除失败!");
		 }
	 }
	 
	 /**
	  * 获取商店内全部商品
	  */
	 private function goods_list($store_id){
		 //1.获取分类信息
		 $terms=StoreTerms::where(['store_id'=>"{$store_id}",'status'=>1])
							->select();
		 //2.获取每种类的商品列表
		 $list=array();
		 $index=0;
		 foreach($terms as $term){
			$term_id=$term['id'];
			$list[$index]['term']=$term;
			$list[$index]['goods']=Goods::where([
								'term_id'=>"$term_id",
								'store_id'=>"$store_id"
								])
								->select();
			$index=$index+1;
		 }
		 $this->assign("list",$list);
	 }
 }
?>