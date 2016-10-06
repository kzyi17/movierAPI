<?php
/**
 * 短信验证码相关配置
 */
return array(
    'SMS_VALIDITY'  =>  600,        //短信验证码有效期，单位为秒
    'SMS_INTERVAL'  =>  60,        //短信发送间隔时间，单位为秒
    //短信接口配置
    'SMS_ACCOUNT_SID'           => 'aaf98f8947473c1301474f35d9b70225',  //主帐号
    'SMS_ACCOUNT_TOKEN'	        => '394a532d03de4bf49b2d6994a51180ff',  //主帐号Token
    'SMS_APP_ID'	            => '8a48b55152f73add015319c11acd4066',  //应用Id
    'SMS_SERVER_IP'				=> 'app.cloopen.com',                   //请求地址
    'SMS_SERVER_PORT'			=> '8883',                              //请求端口
    'SMS_SOFT_VERSION'			=> '2013-12-26',                        //版本号
    'SMS_TEMPID_COMM'			=> '69199',                             //短信模板ID
    
);