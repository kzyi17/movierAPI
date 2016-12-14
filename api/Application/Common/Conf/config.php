<?php
return array(
	//'配置项'=>'配置值'
    'MULTI_MODULE'       	=>  false,  			// 关闭多模块访问
    'DEFAULT_MODULE'     	=> 'Api', 				// 默认模块
    'DEFAULT_CONTROLLER' 	=> 'Index', 			// 默认控制器名称
    'URL_MODEL'             => 2,
    'LOAD_EXT_CONFIG'       => 'database,sms,upload,map',
    'AUTHTOKEN'             => 'mywork99.com',
    //'URL_404_REDIRECT'      =>  '404.html', // 404 跳转页面 部署模式有效
    //'ERROR_PAGE'            =>  '/Public/error.html',
    'WEB_STATICS'               =>  $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"].'/mres/',//'http://172.20.10.2/movier/statics',
    
    
    
);