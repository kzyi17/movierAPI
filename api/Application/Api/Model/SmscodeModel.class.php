<?php
namespace Api\Model;
/**
 * 短信验证码模型
 * @author Muke
 *
 */
use Think\Model;
use Common\Extend\CCPRestSDK;
class SmscodeModel extends Model{
	
	/**
	 * 获取短信验证码
	 * 
	 * @author kezhen.yi                  
	 * @date 2016年2月23日 下午1:05:13        
	 *
	 */
	public function getCode($param){
	    
	    if(isMobile($param['mobile'])){
	       
	       //检查是否重复申请短信验证码    
	       $used = $this->where("mobile=".$param['mobile'])->find();
	       
	       if($used AND !$this->_checkCodeInterval($used['request_time'])){
	           return array('errcode'=>2002,'errmsg'=>'短信发送间隔限制，不能重复发送短信');
	       }
	       
	       $code = generate_rand(4,true);
	       //发送短信
	       $sendResult = $this->sendSms($param['mobile'],array($code),C('SMS_TEMPID_COMM'));
	       if(isset($sendResult['error'])){
	           //return $sendResult;
	           return array('errcode'=>2002,'errmsg'=>'短信通道出错！');
	       }
	       
	       //更新数据库
	       $saveData = array('code'=>$code,'request_time'=>time());
	       $where['mobile'] = $param['mobile'];
	       if($used){
	           $this->where($where)->save($saveData);
	       }else{
	           $saveData['mobile'] = $param['mobile'];
	           $this->add($saveData);
	       }
	       
	       return array('success'=>'短信验证码已成功发送');
//	       return $saveData;
	        
	    }else{
	        return array('errcode'=>2001,'errmsg'=>'手机号码不正确，请重新输入');
	    }
	}
	
	/**
	 * 发送短信
	 * 
	 * @author kezhen.yi                  
	 * @date 2016年2月23日 下午3:28:30        
	 *
	 */
	public function sendSms($to,$datas,$tempId){
	    //主帐号
	    $accountSid = C('SMS_ACCOUNT_SID');
	    //主帐号Token
	    $accountToken = C('SMS_ACCOUNT_TOKEN');
	    //应用Id
	    $appId = C('SMS_APP_ID');
	    //请求地址
	    $serverIP = C('SMS_SERVER_IP');
	    //请求端口
	    $serverPort = C('SMS_SERVER_PORT');
	    //REST版本号
	    $softVersion = C('SMS_SOFT_VERSION');

	    // 初始化REST SDK
	    $rest = new CCPRestSDK($serverIP,$serverPort,$softVersion);
	    $rest->setAccount($accountSid,$accountToken);
	    $rest->setAppId($appId);
	     
	    // 发送模板短信
	    $result = $rest->sendTemplateSMS($to,$datas,$tempId);
	    
	    if($result == NULL ) {
	        return  array("error"=>"短信通道出错");
	    }
	    
	    if($result->statusCode!=0) {
	        //TODO 添加错误处理逻辑
            return array("error"=>"errcode：".$result->statusCode.".errmsg：$result->statusMsg");
	    }
	    
	    return true;
	}
	
	/**
	 * 检查短信是否超过发送间隔限制
	 * 
	 * @author kezhen.yi                  
	 * @date 2016年2月23日 下午2:55:57        
	 * @param int $time 
	 */
	private function _checkCodeInterval($time){
	    $interval = C('SMS_INTERVAL')?C('SMS_INTERVAL'):60;//获取配置的短信发送时间，如没有设置则默认为1分钟
	    
	    if(time()>(intval($time) + $interval)){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	/**
	 * 检查验证码是否超过有效期
	 * 
	 * @author kezhen.yi                  
	 * @date 2016年2月23日 下午3:08:37        
	 *
	 */
	private function _checkCodeValidity($time){
	    $validity = C('SMS_VALIDITY')?C('SMS_VALIDITY'):60;//获取短信配置的有效时间，如没有设置则默认为1分钟
	     
	    if(time()>(intval($time) + $validity)){
	        return false;
	    }else{
	        return true;
	    }
	}
	
	/**
	 * 检查验证码
	 * 
	 * @author kezhen.yi                  
	 * @date 2016年2月24日 下午4:21:49        
	 *
	 */
	public function checkCode($mobile,$code){
	    if(empty($mobile) OR empty($code)){
	        return false;
	    }
	    
	    $codIfo = $this->where(array('mobile'=>$mobile,'code'=>$code))->find();
	    //检查是否在有效期内
	    if(!$codIfo){
	        return false;
	    }else{
	        if(!$this->_checkCodeValidity($codIfo['request_time'])){
	            return false;
	        }
	    }
	    
	    return true;
	}
	
}