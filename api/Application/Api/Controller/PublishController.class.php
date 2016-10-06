<?php
/**
 * 发布相关API
 * 
 * @author kezhen.yi <2015年12月16日 下午6:46:33>
 * @copyright Copyright (C) 2015 mywork99.com
 * @version 1.0 
 */
namespace Api\Controller;

class PublishController extends CommonController {
    
    
    /**
     * 发布内容
     *
     * @author kezhen.yi
     * @date 2016年2月24日 下午3:11:51
     *
     */
    public function create(){
        //检查参数
        $this->checkParam('user_id',true);
        $this->checkParam('publishtype',true);
        $this->checkParam('title',true);
        $this->checkParam('description',true);
    
        $data = D('Publish')->addPublish($this->params);
        $this->jsonReturn($data);
    }
    
    
    /**
     * 获取发布列表
     * 
     * @author kezhen.yi                  
     * @date 2016年3月21日 上午3:41:09        
     *
     */
    public function getlist(){
        //检查参数
//         $this->checkParam('user_id',true);
        $this->checkParam('page',false,'int',1);
        $this->checkParam('limit',false,'int',10);
    
        $data = D('Publish')->getPublishList($this->params);
        $this->jsonReturn($data);
    }
    
    /**
     * 获取发布详情
     *
     * @author kezhen.yi
     * @date 2016年3月21日 上午3:41:09
     *
     */
    public function getdetail(){
        //检查参数
        $this->checkParam('publish_id',true);
    
        $data = D('Publish')->getPublishDetail($this->params);
        $this->jsonReturn($data);
    }
    
    
    /**
     * 获取评论
     * 
     * @author kezhen.yi                  
     * @date 2016年5月13日 下午2:43:25        
     *
     */
    public function getCommentList(){
        //检查参数
        $this->checkParam('publish_id',true);
    
        $data = D('Publish')->getPublishDetail($this->params);
        $this->jsonReturn($data);
    }
    
    /**
     * 发表评论
     * 
     * @author kezhen.yi                  
     * @date 2016年5月13日 下午2:43:41        
     *
     */
    public function addComment(){
        //检查参数
        $this->checkParam('publish_id',true);
    
        $data = D('Publish')->getPublishDetail($this->params);
        $this->jsonReturn($data);
    }

    /**
     * 评论点赞
     * 
     * @author kezhen.yi                  
     * @date 2016年5月13日 下午2:43:55        
     *
     */
    public function addLikeToComment(){
        //检查参数
        $this->checkParam('publish_id',true);
    
        $data = D('Publish')->getPublishDetail($this->params);
        $this->jsonReturn($data);
    }
    
    /**
     * 取消评论点赞
     *
     * @author kezhen.yi
     * @date 2016年5月13日 下午2:43:55
     *
     */
    public function delLikeToComment(){
        //检查参数
        $this->checkParam('publish_id',true);
    
        $data = D('Publish')->getPublishDetail($this->params);
        $this->jsonReturn($data);
    }
    
    
//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-//
//     /**
//      * 发布作品
//      *
//      * @author kezhen.yi
//      * @date 2016年2月24日 下午3:11:51
//      *
//      */
//     public function works(){
//         //检查参数
//         $this->checkParam('user_id',true);
//         $this->checkParam('title',true);
    
//         $data = D('Works')->addWorks($this->params);
//         $this->jsonReturn($data);
//     }
    
//     /**
//      * 发布活动
//      *
//      * @author kezhen.yi
//      * @date 2016年2月24日 下午3:11:51
//      *
//      */
//     public function event(){
//         //检查参数
//         $this->checkParam('user_id',true);
//         $this->checkParam('title',true);
    
//         $data = D('Event')->addEvent($this->params);
//         $this->jsonReturn($data);
//     }
    
//     /**
//      * 发布需求
//      *
//      * @author kezhen.yi
//      * @date 2016年2月24日 下午3:11:51
//      *
//      */
//     public function demand(){
//         //检查参数
//         $this->checkParam('user_id',true);
//         $this->checkParam('title',true);
    
//         $data = D('Demand')->addDemand($this->params);
//         $this->jsonReturn($data);
//     }
    
//     /**
//      * 发布交易
//      *
//      * @author kezhen.yi
//      * @date 2016年2月24日 下午3:11:51
//      *
//      */
//     public function trade(){
//         //检查参数
//         $this->checkParam('user_id',true);
//         $this->checkParam('title',true);
    
//         $data = D('Trade')->addTrade($this->params);
//         $this->jsonReturn($data);
//     }
    
   
}