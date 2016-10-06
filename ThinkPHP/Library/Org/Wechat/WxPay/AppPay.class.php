<?php
namespace Org\Wechat\WxPay;
/**
 *
 * App支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成App支付客户端调用所需的参数
 *
 * 
 *
 *
 */
class AppPay
{
    
    /**
     *
     *生成App支付客户端调用所需的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     *
     * @return json数据，可直接填入js函数作为参数
     */
    public function GetAppPayParameters($UnifiedOrderResult)
    {
        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            throw new WxPayException("参数错误");
        }
        
        
        $appPay = new WxPayAppPay();
        $appPay->SetAppid($UnifiedOrderResult["appid"]);
        $appPay->SetMch_id($UnifiedOrderResult["mch_id"]);
        $timeStamp = time();
        $appPay->SetTimeStamp("$timeStamp");
        $appPay->SetNonceStr($UnifiedOrderResult["nonce_str"]);
        $appPay->SetPrepayid($UnifiedOrderResult["prepay_id"]);
        $appPay->SetPackage("Sign=WXPay");
        $appPay->SetPaySign($appPay->MakeSign());
        //$parameters = json_encode($appPay->GetValues());
        return $appPay->GetValues();
    }


}