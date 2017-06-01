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
 use app\common\model\Order;
 use app\common\model\Goods;
 
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
		$this->today();
		$this->month();
		return $this->fetch();
	}
	
	private function today(){
		//今日订单
		$store_id=Session::get('store_id');
		$begin_time=date('Y-m-d 00:00:00',time());//凌晨时间
		$end_time=date('Y-m-d 00:00:00',strtotime('+1 day'));//明日凌晨时间
		$count=Order::field('count(*) as order_num,count(total) as total')
					->where(['store_id'=>"{$store_id}"])
					->where(['c_time'=>['lt',"{$end_time}"]])
					->where(['c_time'=>['gt',"{$begin_time}"]])
					->select();
		$count=$count[0];
		$today['sell_count']=$count['order_num'];
		$today['total']=$count['total'];
		$this->assign("today",$today);
	}
	
	private function month(){
		//月销量
		$store_id=Session::get('store_id');
		$end_time=date('Y-m-d 00:00:00',strtotime(' +1 day'));//明日凌晨时间
		$begin_time=date('Y-m-1 00:00:00',time());//当月开始时间
		$count=Order::field('count(*) as order_num,count(total) as total')
					->where(['store_id'=>"{$store_id}"])
					->where(['c_time'=>['lt',"{$end_time}"]])
					->where(['c_time'=>['gt',"{$begin_time}"]])
					->select();
		$count=$count[0];
		$month['sell_count']=$count['order_num'];
		$month['total']=$count['total'];
		//月销量详情
		$duration_begin=$begin_time;
		$duration_end=date('Y-m-d 00:00:00',strtotime($duration_begin." +1 day"));
		$index=1;
		while($duration_end<=$end_time){
			$count=Order::field('count(*) as order_num,count(total) as total')
					->where(['store_id'=>"{$store_id}"])
					->where(['c_time'=>['lt',"{$duration_end}"]])
					->where(['c_time'=>['gt',"{$duration_begin}"]])
					->select();
			$count=$count[0];
			$month['day'][$index]['sell_count']=$count['order_num'];
			$month['day'][$index]['total']=$count['total'];
			$index++;
			$duration_begin=$duration_end;
			$duration_end=date('Y-m-d 00:00:00',strtotime($duration_begin."+1 day"));
		}
		$this->assign('month',$month);
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