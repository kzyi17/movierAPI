<?php
namespace Api\Controller;
/**
 * API基类
 *
 * @author kezhen.yi <2015年12月16日 下午6:46:33>
 * @copyright Copyright (C) 2015 mywork99.com
 * @version 1.0
 */
use Think\Controller;
class CommonController extends Controller
{
    public $params = array();
    
    /**
     * 全局初始化
     * 
     * @author kezhen.yi                  
     * @date 2015年12月11日 上午11:28:14        
     *
     */
    public function _initialize(){
        //只允许post调用
        if(!IS_POST){
            $this->_empty();
        }
        
        //验证token
        $token = I('get.token');
        if($token!='asdqwe'){
            $this->error(1003, '非法调用');
        }
        
        //读取content
        $content = file_get_contents("php://input");
        $content = $this->analyJson($content);
        
          //$content = $_POST;
          //$this->success($content);
      
        //判断请求参数是否为json格式
        /*if(!$content){
            $this->error(1002, '参数格式错误');
        }*/
        
        //获POST参数
        $this->params = $content;
        
    }
    
    /**
     * 解析json串
     * @param type $json_str
     * @return type
     */
    public function analyJson($json_str) {
//      $json_str = str_replace('\\', '', $json_str);
////      \Think\Log::record('API：'.$json_str);
//      $out_arr = array();
//      preg_match('/{.*}/', $json_str, $out_arr);
//      if (!empty($out_arr)) {
//          $result = json_decode($out_arr[0], TRUE);
//      } else {
//          return FALSE;
//      }
//      return $result;
		$json_str = str_replace('\\', '', $json_str);
		$out_arr = array();
        preg_match('/{.*}/', $json_str, $out_arr);
		$result = json_decode($out_arr[0], TRUE);
		
		return $result;
    }
    
    /**
     * 检查参数
     * 
     * @author kezhen.yi                  
     * @date 2015年12月17日 上午3:51:13        
     *
     */
    protected function checkParam($param,$required=false,$type='string',$default=null){
    
        //判断参数是否必须
        if($required AND !isset($this->params[$param])){
            $this->error(1001, '缺少参数');
        }
    
        //判断参数类型是否符合
        if($type=="int" AND isset($this->params[$param]) AND !is_numeric($this->params[$param])){
            $this->error(1002, '参数格式错误');
        }
    
        //设置默认值
        if($default AND !isset($this->params[$param])){
            $this->params[$param] = $default;
        }
    
        //$this->error(100, 'test');
    
    }
    
    
    /**
     * 成功返回
     *
     * @author kezhen.yi
     * @date 2015年12月14日 下午8:09:32
     *
     */
    protected function success($data) {
        $this->ajaxReturn($data,'JSON');
    }
    
    /**
     * 错误返回
     *
     * @author kezhen.yi
     * @date 2015年12月14日 下午8:09:32
     *
     */
    protected function error($errcode,$errmsg) {
        $this->ajaxReturn(array('errcode'=>$errcode,'errmsg'=>$errmsg),'JSON');
    }
    
    /**
     * json
     * 
     * @author kezhen.yi                  
     * @date 2016年2月18日 下午1:43:52        
     *
     */
    protected function jsonReturn($arrayData) {
        $this->ajaxReturn($arrayData,'JSON');
    }

    /**
     * 空操作
     * 定义404输出
     *
     * @author kezhen.yi
     * @date 2015年9月9日 下午4:51:33
     *
     */
    public function _empty(){
        $this->error(404,'404 page not found');
    }
}