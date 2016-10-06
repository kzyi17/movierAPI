<?php
namespace Api\Controller;

use Think\Controller;
class TestsController extends Controller{
    
    public function index(){
        $arrayData = $_POST;
        
        //读取content
        $content = file_get_contents("php://input");
//         $content = $this->analyJson($content);
        
        
        print_r($content);die;
//         $this->jsonReturn($arrayData);
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
     * 解析json串
     * @param type $json_str
     * @return type
     */
    public function analyJson($json_str) {
        $json_str = str_replace('\\', '', $json_str);
        $out_arr = array();
        preg_match('/{.*}/', $json_str, $out_arr);
        if (!empty($out_arr)) {
            $result = json_decode($out_arr[0], TRUE);
        } else {
            return FALSE;
        }
        return $result;
    }
}