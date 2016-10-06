<?php
/**
 * 空控制器
 * 定义404
 * 
 * @author kezhen.yi <2015年12月16日 下午6:46:33>
 * @copyright Copyright (C) 2015 mywork99.com
 * @version 1.0
 */
namespace Api\Controller;

use Think\Controller\RestController;
class EmptyController extends RestController{
    
    function index() {
        $this->response('404 page not found','html',404);
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
        $this->response('404 page not found','json',404);
    }
    
}