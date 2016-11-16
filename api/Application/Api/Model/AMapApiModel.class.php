<?php
namespace Api\Model;
/**
 * 高德地图API调用
 *
 * @author kezhen.yi <2016年5月13日 下午2:48:40>
 * @copyright Copyright (C) 2015 mywork99.com
 * @version 1.0
 */
use Common\Util\HttpCurl;
class AMapApiModel{
    
    
    /**
     * 检索附近影人
     * 
     * @author kezhen.yi
     */
    public function searchAroundUser($params=array()){
        $url = "http://yuntuapi.amap.com/datasearch/around";
        
        $postdata = array();
        $postdata['tableid'] = C('amap_table_user');
        $postdata['key'] = C('amap_key');
        $url .='?'.http_build_query(array_merge($postdata,$params));
        
        $data = HttpCurl::get($url);
        $data = json_decode($data,true);
        
        //判断
        if(1==$data['status']){
        	return $data['datas'];
        }else{
        	//log
            \Think\Log::record('Amap_API调用错误：【incode:'.$data['infocode'].';info:'.$data['info'].'】');
            
            return false;
        }
        
    }
    
    /**
     * 创建影人位置
     *
     * @author kezhen.yi
     */
    public function createUser($data){
        $url = 'http://yuntuapi.amap.com/datamanage/data/create';
        $postdata = array();
        $postdata['key'] = C('amap_key');
        $postdata['tableid'] = C('amap_table_user');
        $postdata['data'] = json_encode($data);
        
        $data = HttpCurl::post($url,$postdata);
        $data = json_decode($data,true);
        
        if($data['status']==1){//成功返回创建的ID
            return $data['_id'];
        }else{//错误处理
            //log
            \Think\Log::record('Amap_API调用错误：【incode:'.$data['infocode'].';info:'.$data['info'].'】');
            return false;
        }
        
    }
    
    /**
     * 更新影人位置
     *
     * @author kezhen.yi
     */
    public function updateUser($mapID,$data){
        
        $url = 'http://yuntuapi.amap.com/datamanage/data/update';
        $postdata = array();
        $postdata['key'] = C('amap_key');
        $postdata['tableid'] = C('amap_table_user');
        $mapData = array();
        $mapData['_id'] = $mapID;
        $postdata['data'] = json_encode(array_merge($mapData,$data));
        
        $data = HttpCurl::post($url,$postdata);
        $data = json_decode($data,true);
        
        if($data['status']==1){//成功返回创建的ID
            return $data;
        }else{//错误处理
            //log
            \Think\Log::record('Amap_API调用错误：【incode:'.$data['infocode'].';info:'.$data['info'].'】');
            return $data;
        }
    }
    
} 