<?php
namespace Api\Controller;
/**
 * 共用API
 * 
 * @author kezhen.yi <2015年12月16日 下午6:46:33>
 * @copyright Copyright (C) 2015 mywork99.com
 * @version 1.0 
 */
class PublicController extends CommonController {
    
    /**
     * 获取短信验证码
     * 
     * @author kezhen.yi                  
     * @date 2016年2月23日 下午12:59:16        
     *
     */
    public function getsmscode(){
        
        //检查参数
        $this->checkParam('mobile',true);
        
        $data = D('Smscode')->getCode($this->params);
        $this->jsonReturn($data);
    }
    
    /**
     * 校验短信验证码(忘记密码)
     * 
     */
    public function checkSmsCode(){
        //检查参数
        $this->checkParam('mobile',true);
        $this->checkParam('code',true);
        
        if('forgotpwd'==$this->params['checktype']){
        	if(D('Smscode')->checkCode($this->params['mobile'],$this->params['code'])){
        		$userId = D('Users')->getUserId($this->params['mobile']);
        		if(empty($userId)){
        			$this->error(2001,'该号码还未注册账号，请先注册！'); 
        		}else{
        			$this->success(array('successmsg'=>'验证通过','userid'=>$userId));
        		}
        	}else{
            	$this->error(2002,'验证码错误或已过期，请重新输入'); 
            }	
        }else{
        	$this->error(1001,'缺少参数'); 
        }
    }
    
    
   	/**
     * 获取图片验证码
     * 
     * 
     */
	public function getimgcode(){
        $config =    array(
		    'fontSize'    =>    30,    // 验证码字体大小
		    'length'      =>    3,     // 验证码位数
		    'useNoise'    =>    false, // 关闭验证码杂点
		);
        $Verify =     new \Think\Verify($config);
		$Verify->entry();
//      $data = $Verify->entry();
//      $this->jsonReturn($data);
    }
    
}