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
    
   
    
}