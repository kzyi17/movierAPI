<?php
/**
 * 附近相关API
 * 
 * @author kezhen.yi <2015年12月16日 下午6:46:33>
 * @copyright Copyright (C) 2016 mywork99.com
 * @version 1.0 
 */
namespace Api\Controller;
class NearbyController extends CommonController {
    
    /**
     * 附近影人
     *
     */
    public function movier(){
        //检查参数
        $this->checkParam('location',true);
        $this->checkParam('page',false,'int',1);
        $this->checkParam('limit',false,'int',10);
        
        $param = $this->params;
        $mapParams = array();
        //$location = $param['location'];
        $mapParams['center'] = $param['location'];//$location['longitude'].','.$location['latitude'];   //中心点
        $mapParams['radius'] = $param['radius'];   //范围
        //$mapParams['filter'] = $param[''];   //过滤
        //$mapParams['sortrule'] = $param['']; //排序
        $mapParams['limit'] = $param['limit'];
        $mapParams['page'] = $param['page'];
        
        //调用地图接口获取数据
        $data = D('AMapApi')->searchAroundUser($mapParams);
        
        $return = array();
        //匹配数据
        foreach($data as $k=>$v){
        	$userInfo = D('Users')->getUserInfo(array('user_id'=>$v['userid']));
        	$userInfo['distance'] = $v['_distance'];
        	$userInfo['location'] = $v['_location'];
        	$return[]=$userInfo;
        }
        
        $this->jsonReturn($return);
    }
    
    /**
     * 附近影人--地图是用
     *
     */
    public function movierMap(){
        //检查参数
        $this->checkParam('location',true);
        $this->checkParam('page',false,'int',1);
        $this->checkParam('limit',false,'int',10);
        
        $param = $this->params;
        $mapParams = array();
        //$location = $param['location'];
        $mapParams['center'] = $param['location'];//$location['longitude'].','.$location['latitude'];   //中心点
        $mapParams['radius'] = $param['radius'];   //范围
        //$mapParams['filter'] = $param[''];   //过滤
        //$mapParams['sortrule'] = $param['']; //排序
        $mapParams['limit'] = $param['limit'];
        $mapParams['page'] = $param['page'];
        
        //调用地图接口获取数据
        $data = D('AMapApi')->searchAroundUser($mapParams);
        
        $return = array();
        //匹配数据
        foreach($data as $k=>$v){
        	$userInfo = D('Users')->getUserInfo(array('user_id'=>$v['userid']));
        	//$userInfo['distance'] = $v['_distance'];
        	//$userInfo['location'] = $v['_location'];
        	$p = explode(',',$v['_location']);
        	$userInfo['lng'] = $p[0];
        	$userInfo['lat'] = $p[1];
        	$return[]=$userInfo;
        }
        
        $this->jsonReturn($return);
    }
    
    /**
     * 附近团队
     * 
     */
    public function team(){
        //检查参数
        $this->checkParam('center',true);
    
    
        $data = D('AMapApi')->searchAroundUser($this->params);
        $this->jsonReturn($data);
    }
    
    /**
     * 附近闲器
     *
     */
    public function equipment(){
        //检查参数
        $this->checkParam('center',true);
    
    
        $data = D('AMapApi')->searchAroundUser($this->params);
        $this->jsonReturn($data);
    }
    
     
    
    
    
//     public function createUser(){
//         //检查参数
// //         $this->checkParam('_location',true);
        
//         $data = D('AMapApi')->createUser($this->params);
//         $this->jsonReturn($data);
//     }
    
   
}