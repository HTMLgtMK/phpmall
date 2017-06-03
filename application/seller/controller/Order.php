<?php
/**
 * 订单
 * author:GT
 * time:2017/05/28
 */
 namespace app\seller\controller;
 use \think\Request;
 use \think\Session;
 use app\seller\controller\Base;
 use app\common\model\Goods;
 use app\common\model\StoreTerms;
 
 class Order extends Base{
	 /**
	  * 订单列表
	  */
	 public function index(){
		 $filter=Request::instance()->get();
		 $this->order_list($filter);
		 return $this->fetch();
	 }
	 
	 /**
	  * 发货
	  */
	 public function fahuo(){
		 $order_id=Request::instance()->param('order_id');
		 if(empty($order_id)){
			 return $this->error("错误参数!");
		 }
		 $model_order=new \app\common\model\Order();
		 $res= $model_order->where(['id'=>"{$order_id}",'status'=>"2"])
					->update(['status'=>'3']);
		 if($res){
			 return $this->success("发货成功!");
		 }else{
			 $err= $model_order->getError();
			return $this->error("发货失败!".$err); 
		 }
	 }
	 
	 private function order_list($filter){
		 $where=array();
		 if(!empty($filter['term_id']) && $filter['term_id']!=0){
			 $where['b.term_id']=$filter['term_id'];
		 }
		 if(!empty($filter['keyword'])){
			 $where['b.name']=['like',"%{$filter['keyword']}%"];
		 }
		 if(!empty($filter['order_status']) && $filter['order_status']!= 0){
			 $where['a.status']=$filter['order_status'];
		 }
		 if(!empty($filter['datetime_start'])){
			 $where['a.c_time']=['EGT',"{$filter['datetime_start']}"];
		 }
		 if(!empty($filter['datetime_end'])){
			 $where['a.c_time']=['ELT',"{$filter['datetime_end']}"];
		 }
		 if(!empty($filter['goods_id'])){
			 $where['a.goods_id']=$filter['goods_id'];
			 unset($where['b.term_id']);
			 unset($where['b.name']);
		 }
		 $store_id=Session::get('store_id');
		 $where['a.store_id']="{$store_id}";
		 $orders=\app\common\model\Order::alias('a')
					->join(["__GOODS__"=>'b'],'a.goods_id=b.id')
					->field('a.*,b.name as `name`,b.cover as `cover`,b.term_id as `term_id`')
					->where($where)
					->select();
		 $this->assign("orders",$orders);
		 
		 $goods=Goods::where('store_id',$store_id)->select();
		 $this->assign("goods",$goods);
		 
		 $terms=StoreTerms::where('store_id',$store_id)->select();
		 $this->assign("terms",$terms);
	 }
	 
 }
?>