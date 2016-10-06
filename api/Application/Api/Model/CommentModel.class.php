<?php
namespace Api\Model;
/**
 * 评论模型
 *
 * @author kezhen.yi <2016年5月13日 下午2:48:40>
 * @copyright Copyright (C) 2015 mywork99.com
 * @version 1.0
 */
use Think\Model;
class CommentModel extends Model{
    
    /**
     * 获取评论列表
     * 
     * @author kezhen.yi                  
     * @date 2016年5月13日 下午2:48:40        
     *
     */
    public function getList($param){
               
        //分页
        if($param['page']){
            $this->page($param['page'],$param['limit']);
        }
        
        return $this->select();
    }
    
    /**
     * 发布评论
     *
     * @author kezhen.yi
     * @date 2016年5月13日 下午2:48:40
     *
     */
     public function addComment($param){
          
     }
    
    /**
     * 点赞
     *
     * @author kezhen.yi
     * @date 2016年5月13日 下午2:48:40
     *
     */
     public function addLike($param){
          
     }
    
    /**
     * 取消点赞
     *
     * @author kezhen.yi
     * @date 2016年5月13日 下午2:48:40
     *
     */
     public function delLike($param){
          
     }
    
} 