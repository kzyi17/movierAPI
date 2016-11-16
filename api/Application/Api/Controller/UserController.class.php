<?php
/**
 * 用户相关API
 * 
 * @author kezhen.yi <2015年12月16日 下午6:46:33>
 * @copyright Copyright (C) 2015 mywork99.com
 * @version 1.0 
 */
namespace Api\Controller;
class UserController extends CommonController {
    
    /**
     * 获取注册短信验证码
     * 
     * @author kezhen.yi                  
     * @date 2016年2月23日 下午12:59:16        
     *
     */
    public function regSmsCode(){
        //检查参数
        $this->checkParam('mobile',true);
        
        $data = D('Users')->getRegSmsCode($this->params);
        $this->jsonReturn($data);
    }
    
    /**
     * 用户注册
     *
     * @author kezhen.yi
     * @date 2016年2月24日 下午3:11:51
     *
     */
    public function register(){
        //检查参数
        $this->checkParam('mobile',true);
        $this->checkParam('code',true);
    
        $data = D('Users')->reg($this->params);
        $this->jsonReturn($data);
    }
    
    /**
     * 修改用户信息
     * 
     * @author kezhen.yi                  
     * @date 2016年2月28日 上午12:00:43        
     *
     */
    public function updateinfo(){
        //检查参数
        $this->checkParam('userid',true);
        
        $data = D('Users')->updateInfo($this->params);
        $this->jsonReturn($data);
    }
    
    /**
     * 用户登录
     * 
     * @author kezhen.yi                  
     * @date 2016年2月28日 下午12:48:53        
     *
     */
    public function login(){
        //检查参数
        $this->checkParam('mobile',true);
        $this->checkParam('password',true);
        
        $data = D('Users')->login($this->params);
        $this->jsonReturn($data);
    }
    
    /**
     * 自动登录
     * 
     * @author kezhen.yi                  
     * @date 2016年2月28日 下午12:48:53        
     *
     */
    public function autoLogin(){
        //检查参数
//      $this->checkParam('invalid_token',true);
//      $this->checkParam('password',true);
        
        $data = D('Users')->autoLogin($this->params);
        $this->jsonReturn($data);
    }
    
    /**
     * 获取用户基本信息
     * 
     * @author kezhen.yi                  
     * @date 2016年3月1日 上午2:23:31        
     *
     */
    public function userinfo(){
        //检查参数
        $this->checkParam('user_id',true);
        
        $data = D('Users')->getUserInfo($this->params);
        $this->jsonReturn($data);
        
    }
    
//  /**
//   * 更新用户位置
//   * 
//   */
//  public function updatepos(){
//      //检查参数
//      $this->checkParam('user_id',true);
//      $this->checkParam('map_id',true);
//      $this->checkParam('location',true);
//      
//      $position=$this->params['location'];
//      $location = $position['longitude'].','.$position['latitude'];
//      
//      $param = array('_id'=>$this->params['map_id'],'_name'=>$this->params['nickname'],'_location'=>$location);
//      
//      $data = D('AMapApi')->updateUser($param);
//      $this->jsonReturn($data);
//      
//  }
   
}