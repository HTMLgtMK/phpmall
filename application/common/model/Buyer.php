<?php
/**
 * 买家模型
 * author:GT
 * time:2017/05/31
 */
 
 namespace app\common\model;
 use think\Model;
 
 class Buyer extends Model{
	 
	 /*
	 * 判断注册买家是否已经注册
	 * @return bool
	 */
	public function isBuyerRegisted($email){
		$res=$this->where('mail',"{$email}")->find();
		return $res;
	}
	
 }

?>