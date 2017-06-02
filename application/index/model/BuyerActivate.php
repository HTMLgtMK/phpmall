<?php
/**
 * 买家激活模型
 * author:GT
 * time:2017/06/01
 */
 namespace app\index\model;
 use think\Model;
 
 class BuyerActivate extends Model{
	 
	 /**
	 * 判断是否已经申请注册
	 * @return bool
	 */
	public function isUserApplied($email){
		$res=$this->where('mail',"{$email}")->find();
		return $res;
	}
	
	/**
	 * 判断是否存在code
	 */
	public function isCodeExistInActivateTable($code){
		$res=$this->where('code',"{$code}")->find();
		return $res;
	}
	 
 }
?>