<?php
namespace Api\Model;
/**
 * 用户模型
 *
 * @author kezhen.yi <2015年12月18日 上午4:26:46>
 * @copyright Copyright (C) 2015 mywork99.com
 * @version 1.0
 */
use Think\Model;
class UsersModel extends Model{
    
    /**
	 * 获取注册短信验证码
	 * 
	 * @author kezhen.yi                  
	 * @date 2016年2月23日 下午1:05:13        
	 *
	 */
	public function getRegSmsCode($param){
	    //检查账号是否注册
        if($this->_checkMobile($param['mobile'])){
            return array('errcode'=>2002,'errmsg'=>'该手机号码已被注册');
        }else{
        	return D('Smscode')->getCode($param);
        }
	}
    
    /**
     * 用户登录
     * 
     * @author kezhen.yi                  
     * @date 2016年2月28日 下午12:41:11        
     *
     */
    public function login($param){
        
        $user = $this->where(array('mobile'=>$param['mobile']))->find();
        
        if(!$user || $user['password']!=encrypt($param['password'])){
            return array('errcode'=>5001,'errmsg'=>'用户或密码错误，请重新登录');
        }else{
            unset($user['password']);//隐藏密码字段
            return array('success'=>'登录成功','userInfo'=>$user);
        }
        
    }
    
    /**
     * 检查手机号是否已注册会员
     * 
     */
    private function _checkMobile($mobile){
        if($this->where("mobile=$mobile")->find()){
            return true;
        }else{
        	return false;
        }
    }
    
    /**
     * 用户注册
     *  --密码默认为手机号码
     * @author kezhen.yi
     * @date 2016年2月24日 下午5:44:33
     *
     */
    public function reg($param){
        if(!isMobile($param['mobile'])){
            return array('errcode'=>2001,'errmsg'=>'手机号码不正确，请重新输入');
        }
    
        //检查账号是否注册
        if($this->_checkMobile($param['mobile'])){
            return array('errcode'=>2002,'errmsg'=>'用户已存在,请重新注册');
        }
    
        //检查验证码
        if(!D('Smscode')->checkCode($param['mobile'],$param['code']) AND $param['code']!="20162016"){
            return array('errcode'=>2002,'errmsg'=>'验证码错误或已过期，请重新输入');
        }
    
    	$address = $param['userAddress'];
    	$position = $param['userPosition'];
    	
        $saveData = array(
            'username' => $param['mobile'],
            'mobile' => $param['mobile'],
            'password'  => encrypt($param['mobile']),//密码默认为手机号码
            'reg_time'  => time(),
        );
    
        $result = $this->add($saveData);
        if($result){
            
            //创建地图数据
            $position = $param['userPosition'];
            $location = $position['lng'].','.$position['lat'];
            $mapData = array('_name'=>$param['mobile'],'userid'=>$result,'_location'=>$location);
            $mapID = D('AMapApi')->createUser($mapData);
            if($mapID){
                $this->where("user_id=$result")->save(array('map_id'=>$mapID));
            }
            
            $userInfo = $this->getInfo($result);
            unset($userInfo['password']);//隐藏密码字段
            return array('successmsg'=>'注册成功','userInfo'=>$userInfo);
        }else{
            return array('errcode'=>5001,'errmsg'=>'注册失败，请重新注册');
        }
    
    }
    
    
    /**
     * 更新用户信息
     * 
     * @author kezhen.yi                  
     * @date 2016年2月28日 上午10:40:36        
     *
     */
    public function updateInfo($param){
        if(!isset($param['user_id']) || empty($param['user_id'])){
            return array('errcode'=>1001,'errmsg'=>'缺少参数');
        }
        
        $userId = $param['user_id'];
        $updata = array();
        
        //更新用户昵称
        if(isset($param['nickname'])){
            $updata['nickname'] = $param['nickname'];
        }
        
        //更新用户密码
        if(isset($param['password'])){
            $updata['password'] = encrypt($param['password']);
        }
        
        //更新性别
        if(isset($param['sex'])){
            $updata['sex'] = (int)$param['sex'];
        }
        
        //return $updata;
        
//         //更新字段
//         if(!empty($param[''])){
//             $updata[''] = $param[''];
//         }
        
        //检查是否是否不包含更新字段
        if (empty($updata)){
            return array('errcode'=>1001,'errmsg'=>'缺少参数');
        }
        
        //保存信息
        if($this->where("user_id=$userId")->save($updata)){
            $userInfo = $this->getInfo($userId);
            unset($userInfo['password']);//隐藏密码字段
            return array('success'=>"注册成功","userInfo"=>$userInfo);
        }else{
            return array('errcode'=>5001,'errmsg'=>'更新用户数据失败');
        }
    }
    
    //获取用户信息
    private function getInfo($userId){
        if(!$userId){
            return null;
        }
        return $this->where("user_id=$userId")->find();
    }
    
    /**
     * 获取用户基本信息
     * 
     * @author kezhen.yi                  
     * @date 2016年3月1日 上午2:26:53        
     *
     */
    public function getUserInfo($param){
        return $this->where("user_id=".$param['user_id'])->find();
    }
    
} 