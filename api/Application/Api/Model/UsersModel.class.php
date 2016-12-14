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
use Common\Util\HttpCurl;
use Common\Extend\Base64Upload;
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
     * 用户注册
     *  --密码默认为手机号码
     * @author kezhen.yi
     * @date 2016年2月24日 下午5:44:33
     *
     */
    public function reg($param){
        //检查手机号码
        if(!isMobile($param['mobile']))
            return array('errcode'=>2001,'errmsg'=>'手机号码不正确，请重新输入');
        //检查账号是否注册
        if($this->_checkMobile($param['mobile']))
            return array('errcode'=>2002,'errmsg'=>'用户已存在,请重新注册');
        //检查验证码
        if(!D('Smscode')->checkCode($param['mobile'],$param['code']) AND $param['code']!="20162016")
            return array('errcode'=>2002,'errmsg'=>'验证码错误或已过期，请重新输入');
    	
        //创建用户数据
        $userID = uniqid();//生成一个唯一的 ID//generate_rand(30);
        $saveData = array(
        	'userid' => $userID,
            'username' => $param['mobile'],
            'mobile' => $param['mobile'],
            'password'  => encrypt($param['mobile']),//密码默认为手机号码
            'reg_time'  => time(),
            'last_login'  => time(),
            'last_ip'  => get_client_ip(),
        );
    
        if($this->add($saveData)){
            //创建地图数据
            $position = $param['location'];
            $location = $position['lng'].','.$position['lat'];
            $this->_createUserLocation($userID,$location);
            
            //查询用户数据
            $userInfo = $this->_getUserInfo($userID);
            //取得invalid_token,并更新数据库里的token
            $invalid_token = $this->_updateInvalidToken($userID);
            
            return array('successmsg'=>'注册成功', 'userInfo'=>$userInfo, 'invalid_token'=>$invalid_token);
        }else{
            return array('errcode'=>5001,'errmsg'=>'注册失败，请重新注册');
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
        	//更新最后登陆
        	$this->_updateLastLogin($user['userid']);
        	//取得invalid_token,并更新数据库里的token
        	$invalid_token = $this->_updateInvalidToken($user['userid']);
        	//更新用户定位
        	$position = $param['location'];
            $location = $position['lng'].','.$position['lat'];
            $this->_updateUserLocation($user['userid'],$location);
            //取得用户信息
        	$userInfo = $this->_getUserInfo($user['userid']);
        	
            return array('success'=>'登录成功','userInfo'=>$userInfo,'invalid_token'=>$invalid_token);
        }
    }
    
    /**
     * 自动登录
     * 
     */
    public function autoLogin($param){
    	$token = $param['token'];
    	$timestamp = $param['timestamp'];
    	$userID = $param['userid'];
    	
    	if(!empty($userID) && !empty($token) && !empty($timestamp)){//用户自动登陆
    		//验证invalid_token
    		$invalid_token = M('invalid_token')->where(array('token'=>$token,'userid'=>$userID))->find();
    		if(!$invalid_token){
    			return array('errcode'=>5001,'errmsg'=>'自动登陆信息错误');
    		}else{
    			//验证自动登录信息是否过时
    			$interval = 3600*24*7;//设置token过时时间
    			
			    if(time()>(intval($invalid_token['timestamp']) + $interval)){
			        return array('errcode'=>5001,'errmsg'=>'自动登陆信息过时');
			    }else{
			    	//更新最后登录时间
			    	$this->_updateLastLogin($userID);
			    	//更新定位
			    	$position = $param['location'];
		            $location = $position['lng'].','.$position['lat'];
		            $this->_updateUserLocation($userID,$location);
			    	
			        return array('success'=>'自动登录成功','userInfo'=>$this->_getUserInfo($userID));
			    }
    		}
    	}else{//游客登陆
    		//TODO 记录游客信息
    		return array('errcode'=>5001,'errmsg'=>'游客登录');
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
        if(!isset($param['userid']) || empty($param['userid'])){
            return array('errcode'=>1001,'errmsg'=>'缺少参数');
        }
        
        $userID = $param['userid'];
        $userInfo = $this->where(array('userid'=>$userID))->find();
        if(!$userInfo)
        	return array('errcode'=>5001,'errmsg'=>'更新失败');
        	
        //修改密码
        if(isset($param['oldpwd'])&&isset($param['newpwd'])){
            if($userInfo['password']!=encrypt($param['oldpwd'])){
            	return array('errcode'=>5001,'errmsg'=>'原密码输入错误');
            }
            $updata['password'] = encrypt($param['newpwd']);
            if($this->_saveUserInfo($userID,$updata)){
            	return array('success'=>"修改成功");
            }
            return array('errcode'=>5001,'errmsg'=>'密码修改失败');
        }
        
        //完善资料
        if(isset($param['updataType'])&&$param['updataType']=='ALL'){
			
			$updata = array();
			//更新用户昵称
			if(isset($param['nickname'])) $updata['nickname'] = $param['nickname'];
			//更新用户密码
	        if(isset($param['password'])) $updata['password'] = encrypt($param['password']);
	        //更新性别
	        if(isset($param['sex'])) $updata['sex'] = (int)$param['sex'];
			//上传头像
			if(isset($param['file'])){
				$upload = new Base64Upload();
		        $upload->rootPath  = C('upload_rootPath').'uploads/';
		        $upload->savePath  = 'usericon/';
		        $upload->subName  = array('date', 'Ymd');
		        $imgInfo = $upload->upload($param['file']);
		        $updata['usericon'] = $imgInfo['savepath'].$imgInfo['savefullname'];
			}
			//更新职业
			if(isset($param['job']))
				$updata['job'] = (int)$param['job'];
			//更新地区
	        if(isset($param['province_id'])&&isset($param['city_id'])&&isset($param['area_id'])){
	        	$updata['province_id'] = (int)$param['province_id'];
	        	$updata['city_id'] = (int)$param['city_id'];
	        	$updata['area_id'] = (int)$param['area_id'];
	        }
			
			$updata['update_time'] = time();
			//保存信息
	        if($this->where(array('userid'=>$userID))->save($updata)){
	            return array('success'=>"保存成功","userInfo"=>$this->_getUserInfo($userID));
	        }else{
	            return array('errcode'=>5001,'errmsg'=>'保存资料失败');
	        }
        }
        
    }
    
    /**
     * 根据手机号码获取userid
     */
    public function getUserId($mobile){
    	return $this->where("mobile=$mobile")->getField('userid');
    }
    
    /**
     * 更新资料
     */
    private function _saveUserInfo($userID,$saveData){
    	if(empty($saveData) || !is_array($saveData))
    		return false;
    		
    	return $this->where(array('userid'=>$userID))->save($saveData);
    }
    
    /**
     * 创建地图数据并更新User数据
     * 
     */
    private function _createUserLocation($userID,$location){
        $userInfo = $this->_getUserInfo($userID);
        $mapData = array('_name'=>$userInfo['nickname']?$userInfo['nickname']:$userInfo['mobile'],
        				'userid'=>$userID,
        				'mobile'=>$userInfo['mobile'],
        				'_location'=>$location);
        $mapID = D('AMapApi')->createUser($mapData);
        if($mapID){
            return $this->save(array('userid'=>$userID,'map_id'=>$mapID));
        }else{
        	return false;
        }
    }
    
    /**
     * 更新用户定位
     * 
     */
    private function _updateUserLocation($userID,$location){
        $userInfo = $this->_getUserInfo($userID);
        $mapID = $userInfo['map_id'];
		if($mapID){//用户map_id不为空时更新地图数据
			$mapData = array('_location'=>$location,
							'_name'=>$userInfo['nickname']?$userInfo['nickname']:$userInfo['mobile'],
							'mobile'=>$userInfo['mobile']);	
			$data = D('AMapApi')->updateUser($mapID,$mapData);	
			if($data['status']==1){
				return true;
			}else{
				if($data['infocode']=="32001"){//_map_id不存在时重新创建地图数据
		            return $this->_createUserLocation($userID,$location);//创建地图数据	
				}else{
	            	return false;
				}
			}	
       	}else{//用户map_id为空时重新创建地图数据
            return $this->_createUserLocation($userID,$location);//创建地图数据
       	}
    }
    
    /**
	 * 更新数据库里的invalid_token
	 * 
	 * @param userid
	 * @return Array token,timestamp
	 */
	private function _updateInvalidToken($userID){
		$model = M('invalid_token');
		$timestamp = time();
      	$token = encrypt($userID.$timestamp);
    	$saveData = array('userid'=>$userID,
    					'timestamp'=>$timestamp,
    					'token'=>$token,
    					'ip'=>get_client_ip());
    	
		if($model->where(array('userid'=>$userID))->find()){
			$model->save($saveData);
		}else{
			$model->add($saveData);
		}
		
		return array('token'=>$token,'timestamp'=>$timestamp);
	}
    
    /**
     * 获取用户信息
     * 
     * @return
     */
    private function _getUserInfo($userID){
        if(!$userID)
            return null;
        $userInfo = $this->field(array('u.userid','u.nickname','u.mobile','u.usericon','u.sex','u.start','u.map_id','u.last_login','u.province_id','u.city_id','u.area_id',
        						'p.area_name'=>'province_name','c.area_name'=>'city_name','a.area_name'=>'area_name',
        						'u.job'=>'job_id',
        						'f.filter_name'=>'job'))
        			->alias('u')
        			->join('areas as p on p.area_id = u.province_id','LEFT')
        			->join('areas as c on c.area_id = u.city_id','LEFT')
        			->join('areas as a on a.area_id = u.area_id','LEFT')
        			->join('filter as f on f.filter_id = u.job','LEFT')
        			->where(array('u.userid'=>$userID))
        			->find();
        $userInfo['usericon'] = C('WEB_STATICS').$userInfo['usericon'];		
        return $userInfo;			
    }
    
    /**
     * 更新最后登录
     * 
     */
    private function _updateLastLogin($userID){
    	if(!$userID)
            return null;
        return $this->where(array('userid'=>$userID))
        			->save(array('last_login'=> time(),'last_ip'=>get_client_ip()));
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
    
   
    
} 