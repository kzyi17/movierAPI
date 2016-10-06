<?php
namespace Api\Model;
/**
 * 发布模型
 *
 * @author kezhen.yi <2015年12月18日 上午4:26:46>
 * @copyright Copyright (C) 2015 mywork99.com
 * @version 1.0
 */
use Think\Model;
use Common\Extend\Base64Upload;
class PublishModel extends Model{
    
    
    /**
     * 发布内容
     * 
     * @author kezhen.yi                  
     * @date 2016年2月28日 下午12:41:11        
     *
     */
    public function addPublish($param){
        
        $updata = array();
        $userId = $param['user_id'];
        $publishType = $param['publishtype']; 
        
        $updata['publishtype'] = $publishType;
        
        switch ($updata['publishtype']){
            case "works":
                $this->_works($updata,$param);    
                break;
            case "trade":
                $this->_trade($updata,$param);
                break;
            case "event":
                $this->_event($updata,$param);
                break;
            case "demand":
                $this->_demand($updata,$param);
                break;
            default:
                return array('errcode'=>1002,'errmsg'=>"'publishtype'不支持的类型");
        }
        
        //通用字段
        $updata['user_id'] = $userId;
        $updata['create_time'] = time();
        $updata['title'] = $param['title'];                 //标题
        $updata['description'] = $param['description'];     //详情描述
        
        $result = $this->add($updata);
        //return $result;
        
        //上传图片
        $upload = new Base64Upload();
        $upload->rootPath  = C('upload_rootPath').'uploads/';
        $upload->savePath  = 'publish/';
        $imgModel = M('photos');
        
        if($result){
            foreach ($param['files'] as $v){
                $imgInfo = $upload->upload($v);
                if($imgInfo){
                    $photo = array(
                        'photo_name'    => $imgInfo['savename'],
                        'photo_folder'  => $imgInfo['savepath'],
                        'photo_type'    => $imgInfo['ext'],
                        'user_id'       => $userId,
                        'source_type'   => $updata['publishtype'],
                        'source_id'     => $result,
                        'create_time'   => time(),
                    );
                    $imgModel->add($photo);//TODO 这里可以改成批量入库
                }
            }
            return array('successmsg'=>'发布成功');
        }else{
            return array('errcode'=>3001,'errmsg'=>'发布失败,请重新发布');
        }
        
    }
    
    /**
     * 获取发布列表
     * 
     * @author kezhen.yi                  
     * @date 2016年3月21日 上午3:42:57        
     *
     */
    public function getPublishList($param){
        $usrsTb = 'users';
        $selfTb = $this->trueTableName;
        
        $field = "$selfTb.*";
        $field .= ",$usrsTb.nickname,$usrsTb.sex,$usrsTb.headpic,$usrsTb.start";
        $field .= ",'".C('WEB_STATICS')."/uploads/' as _url";
        $this->field($field);
        
        //查询条件
        if($param['filter']){
            $this->where($param['filter']);
        }
        
        //排序
        if($param['sortrule']){
            $sortrule = $param['sortrule'];
        }else{
            $sortrule = 'publish_id desc';
        }
        
        $this->order($sortrule);
        
        //分页
        if($param['page']){
            $this->page($param['page'],$param['limit']);
        }
        
        $this->join("$usrsTb on $usrsTb.user_id =$selfTb.user_id",'LEFT');
        
        $result = $this->select();
        
        //获取相册
        $imgModel = M('photos');//定义图片模型
        foreach ($result as $k=>$v){
            $result[$k]['photos'] = $imgModel->field('photo_folder,photo_name,photo_type')->where('source_id='.$v['publish_id'])->limit(6)->select();
        }
        
        return $result;
    }
    
    /**
     * 获取发布详情
     *
     * @author kezhen.yi
     * @date 2016年3月21日 上午3:42:57
     *
     */
    public function getPublishDetail($param){
        $info = $this->where("publish_id=".$param['publish_id'])->find(); 
        if($info){
            $imgModel = M('photos');//定义图片模型
            $userModel = M('users');//定义用户模型
            $return = array();
            
            $_url = C('WEB_STATICS').'/uploads/';
            
            $return['userInfo'] = $userModel->field('nickname,mobile,sex,headpic,start')->where("user_id=".$info['user_id'])->find();
            $return['imgInfo'] = $imgModel->field("photo_folder,photo_name,photo_type,'$_url' as _url")->where('source_id='.$info['publish_id'])->select();
            $return['_url'] = C('WEB_STATICS').'/uploads/';
            
            return $return;
        }else{
            return array('errcode'=>1004,'errmsg'=>'查无记录');
        }
    }
    
    
    /**
     * 作品字段
     * @param array &$updata
     * @param array $param
     */
    private function _works(&$updata,$param){
        //风格
        if(isset($param['style'])) $updata['style'] = $param['style'];
        //价格
        if(isset($param['cost'])) $updata['cost'] = $param['cost'];
        //单位
        if(isset($param['unit'])) $updata['unit'] = $param['unit'];
        //来源
        if(isset($param['from'])) $updata['from'] = $param['from'];
    }
    
    /**
     * 需求字段
     * @param array &$updata
     * @param array $param
     */
    private function _demand(&$updata,$param){
        //联系方式
        if(isset($param['contact'])) $updata['contact'] = $param['contact'];
        //来源
        if(isset($param['from'])) $updata['from'] = $param['from'];
    }
    
    /**
     * 活动字段
     * @param array &$updata
     * @param array $param
     */
    private function _event(&$updata,$param){
        //来源
        if(isset($param['from'])) $updata['from'] = $param['from'];
        //活动时间
        if(isset($param['event_time'])){
            $updata['event_time'] = strtotime($param['event_time']);
        }
        //活动性质
        if(isset($param['attribute'])) $updata['attribute'] = $param['attribute'];
        //活动地点
        if(isset($param['location'])) $updata['location'] = $param['location'];
        //性别要求
        if(isset($param['preference_sex'])) $updata['preference_sex'] = $param['preference_sex'];
        //会员要求
        if(isset($param['preference_member'])) $updata['preference_member'] = $param['preference_member'];
        //职业要求
        if(isset($param['preference_job'])) $updata['preference_job'] = $param['preference_job'];
        //联系方式
        if(isset($param['contact'])) $updata['contact'] = $param['contact'];
        //报名费用
        if(isset($param['cost'])) $updata['cost'] = (float)$param['cost'];
    }
    
    /**
     * 交易字段
     * @param array &$updata
     * @param array $param
     */
    private function _trade(&$updata,$param){
        //联系方式
        if(isset($param['contact'])) $updata['contact'] = $param['contact'];
        //来源
        if(isset($param['from'])) $updata['from'] = $param['from'];
    }
} 