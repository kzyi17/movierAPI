<?php
namespace Common\Extend;
/**
 * base64编码图片上传处理类
 * 
 * @author kezhen.yi 
 * @copyright Copyright (C) 2015 mywork99.com
 * 
 */
class Base64Upload{
    /**
     * 默认上传配置
     * @var array
     */
    private $config = array(
//         'mimes'         =>  array(), //允许上传的文件MiMe类型
        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
        'exts'          =>  array(), //允许上传的文件后缀
        'autoSub'       =>  true, //自动子目录保存文件
        'subName'       =>  array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath'      =>  './Uploads/', //保存根路径
        'savePath'      =>  '', //保存路径
        'saveName'      =>  array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'       =>  '', //文件保存后缀，空则使用原后缀
        'filedata'      =>  null, //文件数据
//         'replace'       =>  false, //存在同名是否覆盖
//         'hash'          =>  true, //是否生成hash编码
        'callback'      =>  false, //文件保存成功后的回调函数，返回文件信息
//         'driver'        =>  '', // 文件上传驱动
//         'driverConfig'  =>  array(), // 上传驱动配置
    );
    
     /**
     * 上传错误信息
     * @var string
     */
    private $error = ''; //上传错误信息
    
    // 上传成功的文件信息
    private $uploadFileInfo ;
    
    /**
     * 架构函数
     * @access public
     * @param array $config  上传参数
     */
    public function __construct($config=array()) {
        if(is_array($config)) {
            $this->config   =   array_merge($this->config,$config);
        }
    }
    
    public function getConfig(){
        return $this->config;
    }
    
    /**
     * 使用 $this->name 获取配置
     * @param  string $name 配置名称
     * @return multitype    配置值
     */
    public function __get($name){
        if(isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }
    
    // 设置配置项
    public function __set($name,$value){
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }
    
    // 注销配置项
    public function __isset($name){
        return isset($this->config[$name]);
    }
    
    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError(){
        return $this->error;
    }
    
    /**
     * base64图片解码
     * @param string $base64_content
     * @return multitype:unknown Array |boolean 编码后的图片信息
     */
    private function base64ImageDecode($base64_content){
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_content, $result)){
            $file = array();
            $file['ext'] = $result[2];
            $file['filedata'] = base64_decode(str_replace($result[1], '', $base64_content));
            $file['name'] = date('YmdHis').rand(1000,9999);//产生一个随机文件名（因为你base64上来没有文件名）
            return  $file;
        }else{
            $this->error = '图片数据不正确';
            return false;
        }
    }
    
    //base64 解码
    private function base64FileDecode($base64_content){
        if (preg_match('/(?<=\/)[^\/]+(?=\;)/',$base64_content,$pregR)){
            $file = array();
            $file['ext'] = $pregR[0];
            $file['name'] = date('YmdHis').rand(1000,9999);//产生一个随机文件名（因为你base64上来没有文件名）
            //处理base64文本，用正则把第一个base64,之前的部分砍掉
            preg_match('/(?<=base64,)[\S|\s]+/',$base64_content,$streamForW);
            $file['filedata'] = base64_decode($streamForW[0]);
        }else{
            $this->error = '非法文件数据';
            return false;
        }
    }
    
    /**
     * 保存文件
     * @param  array   $file    保存的文件信息
     * @param  boolean $replace 同名文件是否覆盖
     * @return boolean          保存状态，true-成功，false-失败
     */
    private function save($file='') {
       if(empty($file)){
           $this->error = '上传文件为空！';
           return false;
       }
       $filename = $this->rootPath . $file['savepath'] . $file['savefullname'];
       
       $fp = @fopen($filename, 'wb');
       if (!$fp){
           $this->error = '文件上传保存错误！';
           return false;
       }
       
       if(!fwrite($fp,$file['filedata'])){
           $this->error = '文件上传保存错误！';
           @fclose($fp);
           return false;
       }
       
       @fclose($fp);
       return true;
    }
    
    
    /**
     * 上传一个文件
     * @param string $base64_image_content
     * @param function $callback
     */
    public function upload($base64_image_content){
        
        /* 检测上传根目录 */
        if(!$this->checkRootPath($this->rootPath)){
            $this->error = $this->getError();
            return false;
        }
        
        /* 检查上传目录 */
        if(!$this->checkSavePath($this->savePath)){
            $this->error = $this->getError();
            return false;
        }
        
        $info    =  null;
        
        /* base64解码 */
        $file = $this->base64ImageDecode($base64_image_content);
        if (!$file){
            $this->error = $this->getError();
            return false;
        }
        
        /* 生成保存文件名 */
        $savename = $this->getSaveName($file);
        if(false == $savename){
            continue;
        } else {
            /* 文件保存后缀，支持强制更改文件后缀 */
            $tmpext = empty($this->config['saveExt']) ? $file['ext'] : $this->saveExt;
            $file['savename'] = $savename;
            $file['savefullname'] = $savename . '.' . $tmpext;
        }
        
        /* 检测并创建子目录 */
        $subpath = $this->getSubPath($file['name']);
        if(false === $subpath){
            continue;
        } else {
            $file['savepath'] = $this->savePath . $subpath;
        }
        
        //TODO 对图像文件进行严格检测 
        /* 对图像文件进行严格检测 */
        /* $ext = strtolower($file['ext']);
        if(in_array($ext, array('gif','jpg','jpeg','bmp','png','swf'))) {
            $imginfo = getimagesize($file['tmp_name']);
            if(empty($imginfo) || ($ext == 'gif' && empty($imginfo['bits']))){
                $this->error = '非法图像文件！';
                continue;
            }
        } */
        
        
        /* 保存文件 并记录保存成功的文件 */
        if ($this->save($file)) {
            unset($file['filedata']);
            $info = $file;
            //if($this->callback) call_user_func($this->callback, $file);
            
        } else {
            $this->error = $this->getError();
        }
        
        return empty($info) ? false : $info;
    }
    
    
    /**
     * 检测上传根目录
     * @param string $rootpath   根目录
     * @return boolean true-检测通过，false-检测失败
     */
    public function checkRootPath($rootpath){
        if(!(is_dir($rootpath) && is_writable($rootpath))){
            $this->error = '上传根目录不存在！请尝试手动创建:'.$rootpath;
            return false;
        }
        $this->rootPath = $rootpath;
        return true;
    }
    
    /**
     * 检测上传目录
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkSavePath($savepath){
        /* 检测并创建目录 */
        if (!$this->mkdir($savepath)) {
            return false;
        } else {
            /* 检测目录是否可写 */
            if (!is_writable($this->rootPath . $savepath)) {
                $this->error = '上传目录 ' . $savepath . ' 不可写！';
                return false;
            } else {
                return true;
            }
        }
    }
    
    
    /**
     * 创建目录
     * @param  string $savepath 要创建的穆里
     * @return boolean          创建状态，true-成功，false-失败
     */
    public function mkdir($savepath){
        $dir = $this->rootPath . $savepath;
        if(is_dir($dir)){
            return true;
        }
    
        if(mkdir($dir, 0777, true)){
            return true;
        } else {
            $this->error = "目录 {$savepath} 创建失败！";
            return false;
        }
    }
    
    /**
     * 根据上传文件命名规则取得保存文件名
     * @param string $file 文件信息
     */
    private function getSaveName($file) {
        $rule = $this->saveName;
        if (empty($rule)) { //保持文件名不变
            $savename = $file['name']; 
        } else {
            $savename = $this->getName($rule, $file['name']);
            if(empty($savename)){
                $this->error = '文件命名规则错误！';
                return false;
            }
        }
        
        return $savename ;
    }
    
    /**
     * 获取子目录的名称
     * @param array $file  上传的文件信息
     */
    private function getSubPath($filename) {
        $subpath = '';
        $rule    = $this->subName;
        if ($this->autoSub && !empty($rule)) {
            $subpath = $this->getName($rule, $filename) . '/';
    
            if(!empty($subpath) && !$this->mkdir($this->savePath . $subpath)){
                $this->error = $this->getError();
                return false;
            }
        }
        return $subpath;
    }
    
    /**
     * 根据指定的规则获取文件或目录名称
     * @param  array  $rule     规则
     * @param  string $filename 原文件名
     * @return string           文件或目录名称
     */
    private function getName($rule, $filename){
        $name = '';
        if(is_array($rule)){ //数组规则
            $func     = $rule[0];
            $param    = (array)$rule[1];
            foreach ($param as &$value) {
                $value = str_replace('__FILE__', $filename, $value);
            }
            $name = call_user_func_array($func, $param);
        } elseif (is_string($rule)){ //字符串规则
            if(function_exists($rule)){
                $name = call_user_func($rule);
            } else {
                $name = $rule;
            }
        }
        return $name;
    }
    
    
}